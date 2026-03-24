<?php

namespace Cmercado93\SmsmasivosApi\Operations;

use Cmercado93\SmsmasivosApi\Http\HttpRequestInterface;
use Cmercado93\SmsmasivosApi\Http\HttpRequest;
use Cmercado93\SmsmasivosApi\Common\Endpoints;
use Cmercado93\SmsmasivosApi\Common\ResponseCode;
use Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException;

class GetCurrentDateServer
{
    protected $http;

    public function __construct(HttpRequestInterface $http = null)
    {
        $this->http = $http ?: new HttpRequest(Endpoints::URL_GENERAL);
    }

    public function get()
    {
        $data = array(
            'query' => array('iso' => 1),
        );

        $res = $this->http->get(Endpoints::GET_CURRENT_DATE_SERVER, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        throw new ApiResponseException(array(
            'api_response' => array(array(
                'message' => 'Error en la API (' . $res['code'] . '): ' . (string) $res['response'],
                'code' => ResponseCode::OTHER,
            )),
        ));
    }

    protected function parseApiResponse($response)
    {
        try {
            return new \DateTime(trim($response));
        } catch (\Exception $e) {
            return false;
        }
    }
}
