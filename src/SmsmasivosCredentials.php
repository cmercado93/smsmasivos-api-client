<?php

class SmsmasivosCredentials
{
    /**
     * Usuario valido en la API
     * @var string
     */
    private static $user;

    /**
     * Clave de la API
     * @var string
     */
    private static $password;

    public static function setUserAndPassword($user, $password)
    {
        self::$user = $user;
        self::$password = $password;
    }

    /**
     * Retorno las credenciales de autenticacion
     * @return array|boolean
     */
    public static function getUserAndPassword()
    {
        if (!self::$user) {
            return false;
        }

        return array(
            'user' => self::$user,
            'password' => self::$password,
        );
    }

    /**
     * Retorno si existe o no credenciales
     * @return boolean
     */
    public static function existsCredentials()
    {
        return (bool) self::getUserAndPassword();
    }
}
