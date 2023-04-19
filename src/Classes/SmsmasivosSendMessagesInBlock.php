<?php

require_once dirname(__FILE__) . '/../SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosEndpoints.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosException.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosValidationException.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosMessageValidation.php';

class SmsmasivosSendMessagesInBlock
{
    const FIELD_SEPARATOR_ENTER = "\n";

    const FIELD_SEPARATOR_COMA = ",";

    const FIELD_SEPARATOR_STRING_COMA = "coma";

    protected $configs = array();

    protected $messageBlock = array();

    public function __construct()
    {
        SmsmasivosCredentials::existsCredentials(true);
    }

    public function setConfigs(array $configs)
    {
        $this->configs = $configs;
    }

    public function setMessageBlock(array $data)
    {
        $this->validateMessageBlock($data);

        $this->messageBlock = $data;
    }

    public function send()
    {
        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $data = array(
            'body' => $this->getDataToSend(),
        );

        $res = $r->post(SmsmasivosEndpoints::URI_SEND_MESSAGE_BLOCK, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        $errors['api_response'][] = array(
            'message' => 'Error en la API (' . $res['code'] . '): ' . (string) $res['response'],
            'code' => -99,
        );

        throw new SmsmasivosApiResponseException($errors);
    }

    /**
     * @return array
     */
    protected function getDataToSend()
    {
        $credential = SmsmasivosCredentials::getUserAndPassword();

        $data = array(
            'usuario' => $credential['user'],
            'clave' => $credential['password'],
            'bloque' => array(),
            'separadorcampos' => self::FIELD_SEPARATOR_STRING_COMA,
        );

        if (isset($this->configs['is_test'])) {
            $data['test'] = 1;
        }

        foreach ($this->messageBlock as $block) {
            $internalId = isset($block['internal_id']) ? $block['internal_id'] : $block['phone_number'];

            $tmp = $internalId .
                self::FIELD_SEPARATOR_COMA .
                $block['phone_number'] .
                self::FIELD_SEPARATOR_COMA .
                $block['message'];

            array_push($data['bloque'], $tmp);
        }

        $data['bloque'] = implode(self::FIELD_SEPARATOR_ENTER, $data['bloque']);

        return $data;
    }

    protected function validateMessageBlock(array $data)
    {
        $validator = new SmsmasivosMessageValidation;

        $errors = array(
            'messages' => array(),
        );

        foreach ($data as $key => $block) {
            $errorTmp = array();

            if (!$validator->validateMessageLength($block['message'])) {
                $errorTmp['message'][] = array(
                    'message' => 'El texto del mensaje es muy largo',
                    'code' => -6,
                );
            } elseif (!$validator->validateMessageCharacters($block['message'])) {
                $errorTmp['message'][] = array(
                    'message' => 'El texto del mensaje contiene caractéres inválidos',
                    'code' => -11,
                );
            }

            if (!$validator->validatePhoneNumberLength($block['phone_number'])) {
                $errorTmp['phone_number'][] = array(
                    'message' => 'El número telefonico debe tener al menos 10 dígitos',
                    'code' => -7,
                );
            } elseif (!$validator->validatePhoneNumberCharacters($block['phone_number'])) {
                $errorTmp['phone_number'][] = array(
                    'message' => 'El número telefonico contiene caracteres inválidos',
                    'code' => -8,
                );
            }

            if (isset($block['internal_id'])) {
                if (!$validator->validateInternalIdLength($block['internal_id'])) {
                    $errorTmp['internal_id'][] = array(
                        'message' => 'El ID interno es muy largo',
                        'code' => -99,
                    );
                } elseif (!$validator->validateInternalIdCharacters($block['internal_id'])) {
                    $errorTmp['internal_id'][] = array(
                        'message' => 'El ID interno contiene caracteres inválidos',
                        'code' => -99,
                    );
                }
            }

            if (count($errorTmp)) {
                $errorTmp['key'] = $key;

                array_push($errors['messages'], $errorTmp);
            }
        }

        if (count($errors['messages'])) {
            throw new SmsmasivosValidationException($errors);
        }
    }

    /**
     * @return boolean
     */
    public function parseApiResponse($response)
    {
        if (trim(strtoupper($response)) == 'OK') {
            return true;
        }

        $errors = array();

        $errors['api_response'][] = array(
            'message' => utf8_encode($response),
            'code' => -99,
        );

        throw new SmsmasivosApiResponseException($errors);
    }
}
