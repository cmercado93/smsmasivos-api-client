<?php

require_once dirname(__FILE__) . '/SmsmasivosException.php';

class SmsmasivosCredentialsException extends SmsmasivosException
{
    public function __construct()
    {
        parent::__construct("Username and password were not configured.", 100);
    }
}
