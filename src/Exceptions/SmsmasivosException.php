<?php

class SmsmasivosException extends Exception
{
    protected $extraData;

    public function __construct($message = "", $code = 1, array $extraData = array())
    {
        $this->extraData = $extraData;
        parent::__construct($message, $code);
    }

    public function getExtraData()
    {
        return $this->extraData;
    }
}
