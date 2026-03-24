<?php

namespace Cmercado93\SmsmasivosApi\Operations;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Http\HttpRequestInterface;
use Cmercado93\SmsmasivosApi\Http\HttpRequest;
use Cmercado93\SmsmasivosApi\Common\Endpoints;
use Cmercado93\SmsmasivosApi\Common\ResponseCode;
use Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException;

class GetPackageExpiration
{
    protected $http;

    public function __construct(HttpRequestInterface $http = null)
    {
        Credentials::existsCredentials(true);
        $this->http = $http ?: new HttpRequest(Endpoints::URL_GENERAL);
    }

    public function get()
    {
        $auth = Credentials::getAuthParams();

        $data = array(
            'query' => $auth,
        );

        $res = $this->http->get(Endpoints::GET_PACKAGE_EXPIRATION, $data);

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
        $response = trim($response);

        $re = '/^20[0-9]{2}\-(0[1-9]|1[0-2])\-(0[1-9]|[12][0-9]|3[01])$/';

        if (preg_match($re, $response)) {
            try {
                return new \DateTime($response);
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }
}
