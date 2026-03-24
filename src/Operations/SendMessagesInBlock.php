<?php

namespace Cmercado93\SmsmasivosApi\Operations;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Http\HttpRequestInterface;
use Cmercado93\SmsmasivosApi\Http\HttpRequest;
use Cmercado93\SmsmasivosApi\Common\Endpoints;
use Cmercado93\SmsmasivosApi\Common\MessageValidation;
use Cmercado93\SmsmasivosApi\Common\ResponseCode;
use Cmercado93\SmsmasivosApi\Exceptions\ValidationException;
use Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException;

class SendMessagesInBlock
{
    const FIELD_SEPARATOR_ENTER = "\r\n";

    const FIELD_SEPARATOR_COMA = ",";

    const FIELD_SEPARATOR_TAB = "\t";

    const FIELD_SEPARATOR_STRING_COMA = "coma";

    const FIELD_SEPARATOR_STRING_TAB = "tab";

    protected $configs = array();

    protected $messageBlock = array();

    protected $http;

    public function __construct(HttpRequestInterface $http = null)
    {
        Credentials::existsCredentials(true);
        $this->http = $http ?: new HttpRequest(Endpoints::URL_GENERAL);
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
        $data = array(
            'body' => $this->getDataToSend(),
        );

        $res = $this->http->post(Endpoints::URI_SEND_MESSAGE_BLOCK, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        $errors['api_response'][] = array(
            'message' => 'Error en la API (' . $res['code'] . '): ' . (string) $res['response'],
            'code' => ResponseCode::OTHER,
        );

        throw new ApiResponseException($errors);
    }

    /**
     * @return array
     */
    protected function getDataToSend()
    {
        $auth = Credentials::getAuthParams();

        $separator = isset($this->configs['field_separator']) && $this->configs['field_separator'] === 'tab'
            ? self::FIELD_SEPARATOR_STRING_TAB
            : self::FIELD_SEPARATOR_STRING_COMA;

        $fieldSep = $separator === self::FIELD_SEPARATOR_STRING_TAB
            ? self::FIELD_SEPARATOR_TAB
            : self::FIELD_SEPARATOR_COMA;

        $data = array_merge($auth, array(
            'bloque' => array(),
            'separadorcampos' => $separator,
        ));

        if (isset($this->configs['test']) && $this->configs['test']) {
            $data['test'] = 1;
        }

        foreach ($this->messageBlock as $block) {
            $internalId = isset($block['internal_id']) ? $block['internal_id'] : $block['phone_number'];

            $tmp = $internalId .
                $fieldSep .
                $block['phone_number'] .
                $fieldSep .
                $block['message'];

            array_push($data['bloque'], $tmp);
        }

        $data['bloque'] = implode(self::FIELD_SEPARATOR_ENTER, $data['bloque']);

        return $data;
    }

    protected function validateMessageBlock(array $data)
    {
        $validator = new MessageValidation();

        $errors = array(
            'messages' => array(),
        );

        foreach ($data as $key => $block) {
            $errorTmp = array();

            if (isset($block['message']) && ($block['message'] === '' || $block['message'] === null)) {
                $errorTmp['message'][] = array(
                    'message' => 'El texto del mensaje esta vacio',
                    'code' => ResponseCode::INVALID_MESSAGE_CHARS,
                );
            } else {
                if (!$validator->validateMessageLength($block['message'])) {
                    $errorTmp['message'][] = array(
                        'message' => 'El texto del mensaje es muy largo',
                        'code' => ResponseCode::MESSAGE_TOO_LONG,
                    );
                }
                if (!$validator->validateMessageCharacters($block['message'])) {
                    $errorTmp['message'][] = array(
                        'message' => 'El texto del mensaje contiene caracteres invalidos',
                        'code' => ResponseCode::INVALID_MESSAGE_CHARS,
                    );
                }
            }

            if (!$validator->validatePhoneNumberLength($block['phone_number'])) {
                $errorTmp['phone_number'][] = array(
                    'message' => 'El numero telefonico debe tener entre 8 y 14 digitos',
                    'code' => ResponseCode::INVALID_NUMBER_LENGTH,
                );
            }
            if (!$validator->validatePhoneNumberCharacters($block['phone_number'])) {
                $errorTmp['phone_number'][] = array(
                    'message' => 'El numero telefonico contiene caracteres invalidos',
                    'code' => ResponseCode::INVALID_NUMBER_CHARS,
                );
            }

            if (isset($block['internal_id'])) {
                if (!$validator->validateInternalIdLength($block['internal_id'])) {
                    $errorTmp['internal_id'][] = array(
                        'message' => 'El ID interno es muy largo',
                        'code' => ResponseCode::OTHER,
                    );
                }
                if (!$validator->validateInternalIdCharacters($block['internal_id'])) {
                    $errorTmp['internal_id'][] = array(
                        'message' => 'El ID interno contiene caracteres invalidos',
                        'code' => ResponseCode::OTHER,
                    );
                }
            }

            if (count($errorTmp)) {
                $errorTmp['key'] = $key;

                array_push($errors['messages'], $errorTmp);
            }
        }

        if (count($errors['messages'])) {
            throw new ValidationException($errors);
        }
    }

    /**
     * @param  string $response
     * @return boolean
     */
    public function parseApiResponse($response)
    {
        if (trim(strtoupper($response)) == 'OK') {
            return true;
        }

        $errors = array();

        $errors['api_response'][] = array(
            'message' => mb_convert_encoding($response, 'UTF-8', 'ISO-8859-1'),
            'code' => ResponseCode::OTHER,
        );

        throw new ApiResponseException($errors);
    }
}
