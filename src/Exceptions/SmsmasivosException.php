<?php

namespace Cmercado93\SmsmasivosApiClient\Exceptions;

use Exception;

class SmsmasivosException extends Exception
{
    protected $data;

    public function __construct($message = "", $code = 1, array $data = [])
    {
        $this->setData($data);

        parent::__construct($message, $code);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
}
