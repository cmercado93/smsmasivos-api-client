<?php

require_once dirname(__FILE__) . '/Exceptions/SmsmasivosCredentialsException.php';

class SmsmasivosCredentials
{
    /**
     * Usuario valido en la API
     * @var string
     */
    protected static $user;

    /**
     * Clave de la API
     * @var string
     */
    protected static $password;

    public static function setUserAndPassword($user, $password)
    {
        self::$user = trim($user);
        self::$password = trim($password);
    }

    /**
     * Retorno las credenciales de autenticacion
     * @return array|boolean
     */
    public static function getUserAndPassword()
    {
        if (!self::$user || !self::$password) {
            return false;
        }

        return array(
            'user' => self::$user,
            'password' => self::$password,
        );
    }

    /**
     * Retorno si existe o no credenciales
     * @param  boolean $throw Si se envía este parámetro se enviara una excepción.
     * @return boolean
     */
    public static function existsCredentials($throw = false)
    {
        $b = (bool) self::getUserAndPassword();

        if (!$b && $throw) {
            throw new SmsmasivosCredentialsException();
        }

        return $b;
    }
}
