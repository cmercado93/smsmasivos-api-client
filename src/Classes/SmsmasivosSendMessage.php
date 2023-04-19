<?php

require_once dirname(__FILE__) . '/../SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosEndpoints.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosException.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosValidationException.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosApiResponseException.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosMessageValidation.php';

class SmsmasivosSendMessage
{
    public function __construct()
    {
        SmsmasivosCredentials::existsCredentials(true);
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

        $validator = new SmsmasivosMessageValidation;

        if (!$validator->validateMessageLength($data['message'])) {
            $errors['message'][] = array(
                'message' => 'El texto del mensaje es muy largo',
                'code' => -6,
            );
        } elseif (!$validator->validateMessageCharacters($data['message'])) {
            $errors['message'][] = array(
                'message' => 'El texto del mensaje contiene caractéres inválidos',
                'code' => -11,
            );
        }

        if (!$validator->validatePhoneNumberLength($data['phone_number'])) {
            $errors['phone_number'][] = array(
                'message' => 'El número telefonico debe tener al menos 10 dígitos',
                'code' => -7,
            );
        } elseif (!$validator->validatePhoneNumberCharacters($data['phone_number'])) {
            $errors['phone_number'][] = array(
                'message' => 'El número telefonico contiene caracteres inválidos',
                'code' => -8,
            );
        }

        if (isset($data['configs']['internal_id'])) {
            if (!$validator->validateInternalIdLength($data['configs']['internal_id'])) {
                $errors['configs']['internal_id'][] = array(
                    'message' => 'El ID interno es muy largo',
                    'code' => -99,
                );
            } elseif (!$validator->validateInternalIdCharacters($data['configs']['internal_id'])) {
                $errors['configs']['internal_id'][] = array(
                    'message' => 'El ID interno contiene caracteres inválidos',
                    'code' => -99,
                );
            }
        }

        if (isset($data['configs']['send_date'])) {
            if ($data['configs']['send_date'] instanceof DateTime == false) {
                $errors['configs']['send_date'][] = array(
                    'message' => 'La fecha de envio no es lo que se esperaba',
                    'code' => -99,
                );
            }
        }

        if (count($errors)) {
            throw new SmsmasivosValidationException($errors);
        }
    }

    protected function getDataToSend(array $data)
    {
        $credential = SmsmasivosCredentials::getUserAndPassword();

        $res = array(
            'usuario' => $credential['user'],
            'clave' => $credential['password'],
            'tos' => $data['phone_number'],
            'texto' => $data['message'],
            'api' => 1,
            'respuestanumerica' => 1,
        );

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
        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $data = array(
            'query' => $this->getDataToSend($data),
        );

        $res = $r->get(SmsmasivosEndpoints::URI_SEND_MESSAGE, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        $errors['api_response'][] = array(
            'message' => 'Error en la API (' . $res['code'] . '): ' . (string) $res['response'],
            'code' => -99,
        );

        throw new SmsmasivosApiResponseException($errors);
    }

    protected function parseApiResponse($response)
    {
        $errors = array();

        $r = explode(';', $response, 2);

        // valido si se respondio en el formato correcto
        if (!is_numeric($r[0])) {
            $errors['api_response'][] = array(
                'message' => utf8_encode($response),
                'code' => -99,
            );
        } elseif ((int)$r[0] == 0 || (int)$r[0] == 1) {
            return true;
        } else {
            $errors['api_response'][] = array(
                'message' => utf8_encode($r[1]),
                'code' => $r[0],
            );
        }

        throw new SmsmasivosApiResponseException($errors);
    }
}
