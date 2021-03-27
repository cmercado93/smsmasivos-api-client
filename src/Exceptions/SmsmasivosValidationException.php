<?php

require_once dirname(__FILE__) . '/SmsmasivosException.php';

class SmsmasivosValidationException extends SmsmasivosException
{
    public function __construct(array $data)
    {
        parent::__construct("validation error.", 101, $data);
    }
}
