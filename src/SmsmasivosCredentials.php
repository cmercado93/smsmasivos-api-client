<?php

require_once dirname(__FILE__) . '/Exceptions/SmsmasivosCredentialsException.php';

class SmsmasivosCredentials
{
    /**
     * Usuario valido en la API (método legacy)
     * @var string
     */
    protected static $user;

    /**
     * Clave de la API (método legacy)
     * @var string
     */
    protected static $password;

    /**
     * APIKEY de la API (método recomendado - API v12)
     * @var string
     */
    protected static $apikey;

    /**
     * Establece el APIKEY para autenticación (método recomendado)
     * @param string $apikey APIKEY obtenida desde el panel web de SMS Masivos
     */
    public static function setApiKey($apikey)
    {
        self::$apikey = trim($apikey);
    }

    /**
     * Establece usuario y contraseña para autenticación (método legacy)
     * @param string $user Usuario de SMS Masivos
     * @param string $password Contraseña de SMS Masivos
     */
    public static function setUserAndPassword($user, $password)
    {
        self::$user = trim($user);
        self::$password = trim($password);
    }

    /**
     * Retorna el APIKEY si está configurado
     * @return string|boolean
     */
    public static function getApiKey()
    {
        if (!self::$apikey) {
            return false;
        }

        return self::$apikey;
    }

    /**
     * Retorna las credenciales de autenticacion (método legacy)
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
     * Retorna las credenciales apropiadas para la API
     * Prioridad: APIKEY > Usuario/Contraseña
     * @return array Array con 'apikey' o con 'usuario' y 'clave'
     */
    public static function getCredentialsForApi()
    {
        // Prioridad 1: APIKEY (método recomendado)
        if (self::$apikey) {
            return array(
                'apikey' => self::$apikey,
            );
        }

        // Prioridad 2: Usuario y contraseña (método legacy)
        if (self::$user && self::$password) {
            return array(
                'usuario' => self::$user,
                'clave' => self::$password,
            );
        }

        return false;
    }

    /**
     * Retorna si existe o no credenciales (APIKEY o Usuario/Contraseña)
     * @param  boolean $throw Si se envía este parámetro se enviara una excepción.
     * @return boolean
     */
    public static function existsCredentials($throw = false)
    {
        $b = (bool) self::getCredentialsForApi();

        if (!$b && $throw) {
            throw new SmsmasivosCredentialsException();
        }

        return $b;
    }
}
