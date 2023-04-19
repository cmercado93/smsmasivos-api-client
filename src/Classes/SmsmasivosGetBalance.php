<?php

require_once dirname(__FILE__) . '/../SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosEndpoints.php';

class SmsmasivosGetBalance
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

        $res = $r->get(SmsmasivosEndpoints::GET_BALANCE, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        return false;
    }

    protected function parseApiResponse($response)
    {
        if (is_numeric($response)) {
            return (int) $response;
        }

        return -1;
    }
}
