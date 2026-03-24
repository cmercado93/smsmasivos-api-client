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

class SendMessage
{
    protected $http;

    public function __construct(HttpRequestInterface $http = null)
    {
        Credentials::existsCredentials(true);
        $this->http = $http ?: new HttpRequest(Endpoints::URL_GENERAL);
    }

    public function sendMessage($phoneNumber, $message, array $configs = array())
    {
        $data = array(
            'phone_number' => $phoneNumber,
            'message' => $message,
            'configs' => $configs,
        );

        $this->validate($data);

        return $this->send($data);
    }

    protected function validate(array $data)
    {
        $errors = array();

        $validator = new MessageValidation();

        if ($data['message'] === '' || $data['message'] === null) {
            $errors['message'][] = array(
                'message' => 'El texto del mensaje esta vacio',
                'code' => ResponseCode::INVALID_MESSAGE_CHARS,
            );
        } else {
            if (!$validator->validateMessageLength($data['message'])) {
                $errors['message'][] = array(
                    'message' => 'El texto del mensaje es muy largo',
                    'code' => ResponseCode::MESSAGE_TOO_LONG,
                );
            }
            if (!$validator->validateMessageCharacters($data['message'])) {
                $errors['message'][] = array(
                    'message' => 'El texto del mensaje contiene caracteres invalidos',
                    'code' => ResponseCode::INVALID_MESSAGE_CHARS,
                );
            }
        }

        if (!$validator->validatePhoneNumberLength($data['phone_number'])) {
            $errors['phone_number'][] = array(
                'message' => 'El numero telefonico debe tener entre 8 y 14 digitos',
                'code' => ResponseCode::INVALID_NUMBER_LENGTH,
            );
        }
        if (!$validator->validatePhoneNumberCharacters($data['phone_number'])) {
            $errors['phone_number'][] = array(
                'message' => 'El numero telefonico contiene caracteres invalidos',
                'code' => ResponseCode::INVALID_NUMBER_CHARS,
            );
        }

        if (isset($data['configs']['internal_id'])) {
            if (!$validator->validateInternalIdLength($data['configs']['internal_id'])) {
                $errors['configs']['internal_id'][] = array(
                    'message' => 'El ID interno es muy largo',
                    'code' => ResponseCode::OTHER,
                );
            }
            if (!$validator->validateInternalIdCharacters($data['configs']['internal_id'])) {
                $errors['configs']['internal_id'][] = array(
                    'message' => 'El ID interno contiene caracteres invalidos',
                    'code' => ResponseCode::OTHER,
                );
            }
        }

        if (isset($data['configs']['send_date'])) {
            if (!($data['configs']['send_date'] instanceof \DateTime)) {
                $errors['configs']['send_date'][] = array(
                    'message' => 'La fecha de envio no es lo que se esperaba',
                    'code' => ResponseCode::OTHER,
                );
            }
        }

        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }

    protected function getDataToSend(array $data)
    {
        $auth = Credentials::getAuthParams();

        $res = array_merge($auth, array(
            'tos' => $data['phone_number'],
            'texto' => $data['message'],
            'api' => 1,
            'respuestanumerica' => 1,
        ));

        if (isset($data['configs']['internal_id'])) {
            $res['idinterno'] = $data['configs']['internal_id'];
        }

        if (isset($data['configs']['test']) && $data['configs']['test']) {
            $res['test'] = 1;
        }

        if (isset($data['configs']['send_date'])) {
            $res['fechadesde'] = $data['configs']['send_date']->format('Y-m-d H:i:s');
        }

        if (isset($data['configs']['html'])) {
            $res['html'] = $data['configs']['html'];
        }

        return $res;
    }

    protected function send(array $data)
    {
        $requestData = array(
            'query' => $this->getDataToSend($data),
        );

        $res = $this->http->get(Endpoints::URI_SEND_MESSAGE, $requestData);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        $errors['api_response'][] = array(
            'message' => 'Error en la API (' . $res['code'] . '): ' . (string) $res['response'],
            'code' => ResponseCode::OTHER,
        );

        throw new ApiResponseException($errors);
    }

    protected function parseApiResponse($response)
    {
        $errors = array();

        $r = explode(';', $response, 2);

        if (!ctype_digit(ltrim($r[0], '-'))) {
            $errors['api_response'][] = array(
                'message' => mb_convert_encoding($response, 'UTF-8', 'ISO-8859-1'),
                'code' => ResponseCode::OTHER,
            );
        } elseif ((int) $r[0] == ResponseCode::OK || (int) $r[0] == ResponseCode::TEST_OK) {
            return true;
        } else {
            $msg = isset($r[1]) ? $r[1] : $response;
            $errors['api_response'][] = array(
                'message' => mb_convert_encoding($msg, 'UTF-8', 'ISO-8859-1'),
                'code' => (int) $r[0],
            );
        }

        throw new ApiResponseException($errors);
    }
}
