<?php

require_once dirname(__FILE__) . '/Classes/SmsmasivosSendMessage.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosSendMessagesInBlock.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosCheckMessageBlockSent.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosGetBalance.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosGetCurrentDateServer.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosGetPackageExpiration.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosGetNumberMessagesSent.php';
require_once dirname(__FILE__) . '/Classes/SmsmasivosReceiveMessages.php';

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
     * Compruebo el estado actual del bloque de mensajes enviados.
     * @param  string $filter
     * @param  string $value
     * @param  array  $configs
     * @return bool
     */
    public static function checkMessageBlockSent($value, $filter = 'internal_id', array $configs = array())
    {
        $i = new SmsmasivosCheckMessageBlockSent();

        $i->setFilter($filter, $value);

        $i->setConfigs($configs);

        return $i->check();
    }

    /**
     * [receiveMessages description]
     * @param  array  $configs [description]
     * @return bool
     */
    public static function receiveMessages(array $configs = array())
    {
        $i = new SmsmasivosReceiveMessages;

        $i->setConfigs($configs);

        return $i->receive();
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
