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

class ReceiveMessages
{
    protected $configs = array();

    protected $http;

    public function __construct(HttpRequestInterface $http = null)
    {
        Credentials::existsCredentials(true);
        $this->http = $http ?: new HttpRequest(Endpoints::URL_GENERAL);
    }

    public function setConfigs(array $configs)
    {
        $this->validateConfigs($configs);

        $this->configs = $configs;
    }

    public function receive()
    {
        $data = array(
            'query' => $this->getDataToSend(),
        );

        $res = $this->http->get(Endpoints::GET_MESSAGES_INBOX, $data);

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
     * @param  string $response
     * @return array
     */
    protected function parseApiResponse($response)
    {
        if (isset($this->configs['format']) && $this->configs['format'] === 'excel') {
            return $response;
        }

        $includeInternalId = !isset($this->configs['include_internal_id']) || $this->configs['include_internal_id'];

        if ($includeInternalId) {
            $re = '/^([0-9]{8,14})\t(.+)\t([0-9\-\:\ ]+)\t([0-9]+)\t([A-Za-z0-9]+)/m';
        } else {
            $re = '/^([0-9]{8,14})\t(.+)\t([0-9\-\:\ ]+)\t([0-9]+)/m';
        }

        $math = array();

        $messages = array();

        preg_match_all($re, $response, $math, PREG_SET_ORDER, 0);

        foreach ($math as $mht) {
            $expectedGroups = $includeInternalId ? 6 : 5;
            if (count($mht) < $expectedGroups) {
                continue;
            }

            $tmp = array();

            $tmp['phone_number'] = $mht[1];
            $tmp['message'] = mb_convert_encoding($mht[2], 'UTF-8', 'ISO-8859-1');

            if (isset($this->configs['api_response_date']) && $this->configs['api_response_date'] == 'raw') {
                $tmp['date'] = $mht[3];
            } else {
                try {
                    $tmp['date'] = new \DateTime($mht[3]);
                } catch (\Exception $e) {
                    $tmp['date'] = $mht[3];
                }
            }

            $tmp['smsmasivos_id'] = $mht[4];

            if ($includeInternalId) {
                $tmp['internal_id'] = $mht[5];
            }

            array_push($messages, $tmp);
        }

        return $messages;
    }

    protected function validateConfigs($configs)
    {
        $errors = array();

        $validator = new MessageValidation();

        if (isset($configs['phone_number'])) {
            if (!$validator->validatePhoneNumberLength($configs['phone_number'])) {
                $errors['phone_number'][] = array(
                    'message' => 'El numero telefonico debe tener entre 8 y 14 digitos',
                    'code' => ResponseCode::INVALID_NUMBER_LENGTH,
                );
            }
            if (!$validator->validatePhoneNumberCharacters($configs['phone_number'])) {
                $errors['phone_number'][] = array(
                    'message' => 'El numero telefonico contiene caracteres invalidos',
                    'code' => ResponseCode::INVALID_NUMBER_CHARS,
                );
            }
        }

        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }

    protected function getDataToSend()
    {
        $auth = Credentials::getAuthParams();

        $res = $auth;

        if (!isset($this->configs['include_internal_id']) || $this->configs['include_internal_id']) {
            $res['traeridinterno'] = 1;
        }

        if (isset($this->configs['phone_number'])) {
            $res['origen'] = $this->configs['phone_number'];
        }

        if (isset($this->configs['only_unread'])) {
            $res['solonoleidos'] = 1;
        }

        if (isset($this->configs['mark_as_read'])) {
            $res['marcarcomoleidos'] = 1;
        }

        if (isset($this->configs['format']) && $this->configs['format'] === 'excel') {
            $res['formato'] = 'excel';
        }

        return $res;
    }
}
