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

class CheckMessageBlockSent
{
    protected $filter = 'internal_id';

    protected $filterValue = null;

    protected $configs = array();

    protected $filters = array(
        'internal_id',
        'date',
    );

    protected $http;

    public function __construct(HttpRequestInterface $http = null)
    {
        Credentials::existsCredentials(true);
        $this->http = $http ?: new HttpRequest(Endpoints::URL_GENERAL);
    }

    public function setFilter($filter, $value)
    {
        $this->validateFilter($filter, $value);

        $this->filter = $filter;

        $this->filterValue = $value;
    }

    public function setConfigs(array $data)
    {
        $this->configs = $data;
    }

    public function check()
    {
        $data = array(
            'query' => $this->getDataToSend(),
        );

        $res = $this->http->get(Endpoints::CHECK_SENT_BLOCK, $data);

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
     * @return false|array
     */
    protected function parseApiResponse($response)
    {
        if (trim(strtoupper($response)) == 'PENDIENTE') {
            return false;
        }

        $re = '/(.*)\t([0-9]+)\t(.*)/m';

        $math = array();

        $messages = array();

        preg_match_all($re, $response, $math, PREG_SET_ORDER, 0);

        foreach ($math as $mht) {
            if (count($mht) < 4) {
                continue;
            }

            $tmp = array();

            $tmp['internal_id'] = $mht[1];

            if (isset($this->configs['api_response_date']) && $this->configs['api_response_date'] == 'raw') {
                $tmp['date'] = $mht[2];
            } else {
                try {
                    $tmp['date'] = new \DateTime('@' . $mht[2]);
                } catch (\Exception $e) {
                    $tmp['date'] = $mht[2];
                }
            }

            if (trim(strtoupper($mht[3])) == 'OK') {
                $tmp['sent'] = true;
            } else {
                $tmp['sent'] = false;
                $tmp['error'] = mb_convert_encoding($mht[3], 'UTF-8', 'ISO-8859-1');
            }

            array_push($messages, $tmp);
        }

        return $messages;
    }

    protected function getDataToSend()
    {
        $auth = Credentials::getAuthParams();

        $res = $auth;

        switch ($this->filter) {
            case 'internal_id':
                $res['idinterno'] = $this->filterValue;
                break;
            case 'date':
                $res['fecha'] = $this->filterValue instanceof \DateTime ? $this->filterValue->format('YmdHis') : '';
                break;
        }

        if (isset($this->configs['only_unread'])) {
            $res['solonoleidos'] = 1;
        }

        if (isset($this->configs['mark_as_read'])) {
            $res['marcarcomoleidos'] = 1;
        }

        return $res;
    }

    protected function validateFilter($filter, $value)
    {
        $errors = array();

        if (!in_array($filter, $this->filters)) {
            $errors['filter'] = array(
                array(
                    'message' => 'filter not valid',
                    'code' => ResponseCode::OTHER,
                ),
            );
        }

        switch ($filter) {
            case 'internal_id':
                $validator = new MessageValidation();

                if (!$validator->validateInternalIdLength($value)) {
                    $errors['filter_value'][] = array(
                        'message' => 'El valor del filtro ID interno es muy largo',
                        'code' => ResponseCode::OTHER,
                    );
                }
                if (!$validator->validateInternalIdCharacters($value)) {
                    $errors['filter_value'][] = array(
                        'message' => 'El valor del filtro ID interno contiene caracteres invalidos',
                        'code' => ResponseCode::OTHER,
                    );
                }
                break;
            case 'date':
                if (!($value instanceof \DateTime)) {
                    $errors['filter_value'][] = array(
                        'message' => 'El valor del filtro fecha debe ser una instancia de DateTime',
                        'code' => ResponseCode::OTHER,
                    );
                }
                break;
        }

        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }
}
