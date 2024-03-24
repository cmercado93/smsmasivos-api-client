<?php

namespace Cmercado93\SmsmasivosApiClient;

class Credentials
{
    protected static $credentials = [];

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey)
    {
        static::clear();

        static::$credentials['api_key'] = $apiKey;
    }

    /**
     * @param string $user
     * @param string $password
     */
    public static function setUserAndPassword(string $user, string $password)
    {
        static::clear();

        static::$credentials['user'] = $user;
        static::$credentials['password'] = $password;
    }

    /**
     * @return array
     */
    public static function getCredentials() : array
    {
        return static::$credentials;
    }

    /**
     * @return void
     */
    public static function clear()
    {
        static::$credentials = [];
    }
}
