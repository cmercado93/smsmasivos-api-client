<?php

namespace Cmercado93\SmsmasivosApiClient\Exceptions;

use Cmercado93\SmsmasivosApiClient\Exceptions\SmsmasivosException;

class CredentialsException extends SmsmasivosException
{
    public function __construct()
    {
        parent::__construct("Username and password were not configured.", 100);
    }
}
