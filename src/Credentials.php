<?php

namespace Cmercado93\SmsmasivosApi;

use Cmercado93\SmsmasivosApi\Exceptions\CredentialsException;

class Credentials
{
    protected static $apikey;

    protected static $user;

    protected static $password;

    public static function setApiKey($apikey)
    {
        self::$apikey = trim($apikey);
        self::$user = null;
        self::$password = null;
    }

    public static function setUserAndPassword($user, $password)
    {
        self::$user = trim($user);
        self::$password = trim($password);
        self::$apikey = null;
    }

    /**
     * @return array|false
     */
    public static function getAuthParams()
    {
        if (self::$apikey !== null && self::$apikey !== '') {
            return array('apikey' => self::$apikey);
        }

        if (self::$user !== null && self::$user !== '' && self::$password !== null && self::$password !== '') {
            return array(
                'usuario' => self::$user,
                'clave' => self::$password,
            );
        }

        return false;
    }

    /**
     * @param  boolean $throw
     * @return boolean
     */
    public static function existsCredentials($throw = false)
    {
        $auth = self::getAuthParams();

        if (!$auth && $throw) {
            throw new CredentialsException();
        }

        return (bool) $auth;
    }

    public static function reset()
    {
        self::$apikey = null;
        self::$user = null;
        self::$password = null;
    }
}
