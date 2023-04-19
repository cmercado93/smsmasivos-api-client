<?php

require_once dirname(__FILE__) . '/../SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosEndpoints.php';

class SmsmasivosGetPackageExpiration
{
    public function __construct()
    {
        SmsmasivosCredentials::existsCredentials(true);
    }

    public function get()
    {
        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $credentials = SmsmasivosCredentials::getUserAndPassword();

        $data = array(
            'query' => array(
                'usuario' => $credentials['user'],
                'clave' => $credentials['password'],
            ),
        );

        $res = $r->get(SmsmasivosEndpoints::GET_PACKAGE_EXPIRATION, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        return false;
    }

    protected function parseApiResponse($response)
    {
        $re = '/^20[0-9]{2}\-[0-1][0-2]\-[0-3][0-9]$/';

        if (preg_match($re, $response)) {
            return new DateTime($response);
        }

        return false;
    }
}
