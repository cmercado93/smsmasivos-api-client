<?php

namespace Cmercado93\SmsmasivosApi\Common;

class MessageValidation
{
    /**
     * @param  string $value
     * @return boolean
     */
    public function validateMessageCharacters($value)
    {
        if ($value === '' || $value === null) {
            return false;
        }

        $re = '/^[A-Za-z0-9\!\?\#\$\%\(\)\*\+\-\.\/\:\;\=\@\ \,]+$/';

        return (bool) preg_match($re, $value);
    }

    /**
     * @param  string $value
     * @return boolean
     */
    public function validateMessageLength($value)
    {
        return mb_strlen((string) $value, 'UTF-8') <= 160;
    }

    /**
     * @param  string $value
     * @return boolean
     */
    public function validatePhoneNumberCharacters($value)
    {
        $re = '/^[0-9]+$/';

        return (bool) preg_match($re, $value);
    }

    /**
     * @param  string $value
     * @return boolean
     */
    public function validatePhoneNumberLength($value)
    {
        $re = '/^([0-9]{1,4})?[0-9]{8,10}$/';

        return (bool) preg_match($re, $value);
    }

    /**
     * @param  string $value
     * @return boolean
     */
    public function validateInternalIdCharacters($value)
    {
        $re = '/^[A-Za-z0-9]+$/';

        return (bool) preg_match($re, $value);
    }

    /**
     * @param  string $value
     * @return boolean
     */
    public function validateInternalIdLength($value)
    {
        return strlen($value) <= 50;
    }
}
