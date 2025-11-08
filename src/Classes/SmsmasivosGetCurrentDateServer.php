<?php

require_once dirname(__FILE__) . '/../SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosEndpoints.php';

class SmsmasivosGetCurrentDateServer
{
    public function get()
    {
        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        // get_fecha.asp no requiere credenciales según la documentación
        $res = $r->get(SmsmasivosEndpoints::GET_CURRENT_DATE_SERVER);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        return false;
    }

    protected function parseApiResponse($response)
    {
        try {
            return new DateTime($response);
        } catch (Exception $e) {
            return false;
        }
    }
}
