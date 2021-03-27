<?php

require_once dirname(__FILE__) . '/Classes/SmsmasivosSendMessage.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosSendMessagesInBlock.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosGetBalance.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosGetCurrentDateServer.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosGetPackageExpiration.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosGetNumberMessagesSent.php';

class Smsmasivos
{
    /**
     * Enviamos un mensaje directo
     * @param  string $phoneNumber Numero telefónico del destinatario
     * @param  string $message     Mensaje a enviar
     * @param  array  $configs     Configuraciones adicionales para el envío
     * @return bool
     */
    public static function sendMessage($phoneNumber, $message, array $configs = array())
    {
        $i = new SmsmasivosSendMessage;

        return $i->sendMessage($phoneNumber, $message, $configs);
    }

    /**
     * Enviamos un bloque de mensajes
     * @param  array $data
     * @return bool
     */
    public static function sendMessagesInBlock(array $data)
    {
        $i = new SmsmasivosSendMessagesInBlock;

        $data['configs'] = isset($data['configs']) ? $data['configs'] : array();
        $data['messages'] = isset($data['messages']) ? $data['messages'] : array();

        $i->setConfigs($data['configs']);
        $i->setMessageBlock($data['messages']);

        return $i->send();
    }

    /**
     * [checkStatusBlock description]
     * @param  [type] $filter  [description]
     * @param  [type] $value   [description]
     * @param  array  $configs [description]
     * @return [type]          [description]
     */
    public static function checkStatusBlock($filter, $value, array $configs = array())
    {
        return false;
    }

    /**
     * [receiveMessages description]
     * @param  array  $configs [description]
     * @return bool
     */
    public static function receiveMessages(array $configs = array())
    {
        return false;
    }

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
