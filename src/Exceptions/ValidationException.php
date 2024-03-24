<?php

namespace Cmercado93\SmsmasivosApiClient\Exceptions;

use Cmercado93\SmsmasivosApiClient\Exceptions\SmsmasivosException;

class ValidationException extends SmsmasivosException
{
    public function __construct(array $data)
    {
        parent::__construct("validation error.", 101, $data);
    }
}
