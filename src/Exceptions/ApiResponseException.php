<?php

namespace Cmercado93\SmsmasivosApiClient\Exceptions;

use Cmercado93\SmsmasivosApiClient\Exceptions\SmsmasivosException;

class ApiResponseException extends SmsmasivosException
{
    public function __construct(array $data)
    {
        parent::__construct("Error response data.", 102, $data);
    }
}
