<?php

namespace Cmercado93\SmsmasivosApi\Exceptions;

class CredentialsException extends SmsmasivosException
{
    public function __construct()
    {
        parent::__construct(
            "No authentication configured. Call Credentials::setApiKey() or Credentials::setUserAndPassword() first.",
            100
        );
    }
}
