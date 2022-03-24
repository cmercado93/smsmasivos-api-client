<?php

class SmsmasivosMessageValidation
{
    /**
     * Validamos si los caracteres del mensaje son validos
     * @param  string
     * @return boolean
     */
    public function validateMessageCharacters($value)
    {
        $re = '/^[A-Za-z0-9\!\?\#\$\%\(\)\*\+\-\.\/\:\;\=\@\ \,]+$/';

        return (bool) preg_match($re, $value);
    }

    /**
     * Validamos si el largo del mensaje es valido
     * @param  string
     * @return boolean
     */
    public function validateMessageLength($value)
    {
        return strlen((string)$value) <= 160;
    }

    /**
     * Validamos si los caracteres del numero telefónico son validos
     * @param  string
     * @return boolean
     */
    public function validatePhoneNumberCharacters($value)
    {
        $re = '/^[0-9]+$/';

        return (bool) preg_match($re, $value);
    }

    /**
     * Validamos si el largo del numero telefónico es valido
     * @param  string
     * @return boolean
     */
    public function validatePhoneNumberLength($value)
    {
        $re = '/^([0-9]{1,4})?[0-9]{10}$/';

        return (bool) preg_match($re, $value);
    }

    /**
     * Validamos los caracteres del ID interno
     * @param  string
     * @return boolean
     */
    public function validateInternalIdCharacters($value)
    {
        $re = '/^[A-Za-z0-9]+$/';

        return (bool) preg_match($re, $value);
    }

    /**
     * Validamos el largo del ID interno
     * @param  string
     * @return boolean
     */
    public function validateInternalIdLength($value)
    {
        return strlen($value) <= 50;
    }
}
