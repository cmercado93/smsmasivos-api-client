<?php

require_once dirname(__FILE__) . '/../SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosEndpoints.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosException.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosValidationException.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosApiResponseException.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosMessageValidation.php';

class SmsmasivosReceiveMessages
{
    private $configs = array();

    public function __construct()
    {
        SmsmasivosCredentials::existsCredentials(true);
    }

    public function setConfigs(array $configs)
    {
        $this->validateConfigs($configs);

        $this->configs = $configs;
    }

    public function receive()
    {
        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $data = array(
            'query' => $this->getDataToSend(),
        );

        $res = $r->get(SmsmasivosEndpoints::GET_MESSAGES_INBOX, $data);

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
     * Analizo los datos que me pasan
     * @param  string $response
     * @return boolean|array
     */
    private function parseApiResponse($response)
    {
        $re = '/^([0-9]{10})\t([A-Za-z0-9\!\?\#\$\%\(\)\*\+\-\.\/\:\;\=\@\ ]+)\t([0-9\-\:\ ]+)\t([0-9]+)\t([A-Za-z0-9]+)/m';

        $math = array();

        $messages = array();

        preg_match_all($re, $response, $math, PREG_SET_ORDER, 0);

        foreach ($math as $mht) {
            $tmp = array();

            $tmp['phone_number'] = $mht[1];
            $tmp['message'] = utf8_encode($mht[2]);

            if (isset($this->configs['api_response_date']) && $this->configs['api_response_date'] == 'raw') {
                $tmp['date'] = $mht[3];
            } else {
                $tmp['date'] = new DateTime($mht[3]);
            }

            $tmp['smsmasivos_id'] = $mht[4];

            $tmp['internal_id'] = $mht[5];

            array_push($messages, $tmp);
        }

        return $messages;
    }

    private function validateConfigs($configs)
    {
        $errors = array();

        $validator = new SmsmasivosMessageValidation;

        if (isset($configs['phone_number'])) {
            if (!$validator->validatePhoneNumberLength($configs['phone_number'])) {
                $errors['phone_number'][] = array(
                    'message' => 'El número telefonico debe tener al menos 10 dígitos',
                    'code' => -7,
                );
            } elseif (!$validator->validatePhoneNumberCharacters($configs['phone_number'])) {
                $errors['phone_number'][] = array(
                    'message' => 'El número telefonico contiene caracteres inválidos',
                    'code' => -8,
                );
            }
        }

        if (count($errors)) {
            throw new SmsmasivosValidationException($errors);
        }
    }

    private function getDataToSend()
    {
        $credential = SmsmasivosCredentials::getUserAndPassword();

        $res = array(
            'usuario' => $credential['user'],
            'clave' => $credential['password'],
            'traeridinterno' => 1,
        );

        if (isset($this->configs['phone_number'])) {
            $res['origen'] = $this->configs['phone_number'];
        }

        if (isset($this->configs['only_unread'])) {
            $res['solonoleidos'] = 1;
        }

        if (isset($this->configs['mark_as_read'])) {
            $res['marcarcomoleidos'] = 1;
        }

        return $res;
    }
}
