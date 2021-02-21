<?php

require_once dirname(__FILE__) . '/../SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosEndpoints.php';

class SmsmasivosGetCurrentDateServer
{
    public function __construct()
    {
        if (!SmsmasivosCredentials::existsCredentials()) {
            throw new Exception("No se ingresaron las credenciales necesarias");
        }
    }

    public function get()
    {
        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $credentials = SmsmasivosCredentials::getUserAndPassword();

        $res = $r->get(SmsmasivosEndpoints::GET_CURRENT_DATE_SERVER);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        return false;
    }

    private function parseApiResponse($response)
    {
        try {
            return new DateTime($response);
        } catch (Exception $e) {
            return false;
        }
    }
}
