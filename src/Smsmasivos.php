<?php

namespace Cmercado93\SmsmasivosApi;

use Cmercado93\SmsmasivosApi\Http\HttpRequestInterface;
use Cmercado93\SmsmasivosApi\Operations;

class Smsmasivos
{
    protected static $httpRequest = null;

    /**
     * @param HttpRequestInterface|null $http
     */
    public static function setHttpRequest(HttpRequestInterface $http = null)
    {
        self::$httpRequest = $http;
    }

    /**
     * @param  string $phoneNumber
     * @param  string $message
     * @param  array  $configs
     * @return bool
     */
    public static function sendMessage($phoneNumber, $message, array $configs = array())
    {
        $i = new Operations\SendMessage(self::$httpRequest);

        return $i->sendMessage($phoneNumber, $message, $configs);
    }

    /**
     * @param  array $data
     * @return bool
     */
    public static function sendMessagesInBlock(array $data)
    {
        $i = new Operations\SendMessagesInBlock(self::$httpRequest);

        $data['configs'] = isset($data['configs']) ? $data['configs'] : array();
        $data['messages'] = isset($data['messages']) ? $data['messages'] : array();

        $i->setConfigs($data['configs']);
        $i->setMessageBlock($data['messages']);

        return $i->send();
    }

    /**
     * @param  string $value
     * @param  string $filter
     * @param  array  $configs
     * @return false|array
     */
    public static function checkMessageBlockSent($value, $filter = 'internal_id', array $configs = array())
    {
        $i = new Operations\CheckMessageBlockSent(self::$httpRequest);

        $i->setFilter($filter, $value);

        $i->setConfigs($configs);

        return $i->check();
    }

    /**
     * @param  array  $configs
     * @return array
     */
    public static function receiveMessages(array $configs = array())
    {
        $i = new Operations\ReceiveMessages(self::$httpRequest);

        $i->setConfigs($configs);

        return $i->receive();
    }

    /**
     * @return int
     */
    public static function getBalance()
    {
        $i = new Operations\GetBalance(self::$httpRequest);

        return $i->get();
    }

    /**
     * @return \DateTime|false
     */
    public static function getPackageExpiration()
    {
        $i = new Operations\GetPackageExpiration(self::$httpRequest);

        return $i->get();
    }

    /**
     * @return int|false
     */
    public static function getNumberMessagesSent()
    {
        $i = new Operations\GetNumberMessagesSent(self::$httpRequest);

        return $i->get();
    }

    /**
     * @return \DateTime|false
     */
    public static function getCurrentDateServer()
    {
        $i = new Operations\GetCurrentDateServer(self::$httpRequest);

        return $i->get();
    }
}
