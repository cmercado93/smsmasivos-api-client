<?php

require_once dirname(__FILE__) . '/SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/Common/SmsmasivosEndpoints.php';
require_once dirname(__FILE__) . '/SmsmasivosQueries/SmsmasivosGetBalance.php';
require_once dirname(__FILE__) . '/SmsmasivosQueries/SmsmasivosGetCurrentDateServer.php';
require_once dirname(__FILE__) . '/SmsmasivosQueries/SmsmasivosGetPackageExpiration.php';
require_once dirname(__FILE__) . '/SmsmasivosQueries/SmsmasivosGetNumberMessagesSent.php';

class SmsmasivosQueries
{
    /**
     * Se usa en los planes prepagos para saber la cantidad de SMS que aún podemos enviar connuestro usuario.
     * @return int|boolean
     */
    public static function getBalance()
    {
        $i = new SmsmasivosGetBalance;

        return $i->get();
    }

    /**
     * Se usa en los planes prepagos para saber la fecha de vencimiento del paquete contratado.
     * @return DateTime|boolean
     */
    public static function getPackageExpiration()
    {
        $i = new SmsmasivosGetPackageExpiration;

        return $i->get();
    }

    /**
     * Se usa en los planes abiertos para saber la cantidad de SMS que se han enviado en el mes
     * @return int|boolean
     */
    public static function getNumberMessagesSent()
    {
        $i = new SmsmasivosGetNumberMessagesSent;

        return $i->get();
    }

    /**
     * Se usa para consultar la fecha actual del servidor.
     * @return DateTime|boolean
     */
    public static function getCurrentDateServer()
    {
        $i = new SmsmasivosGetCurrentDateServer;

        return $i->get();
    }
}
