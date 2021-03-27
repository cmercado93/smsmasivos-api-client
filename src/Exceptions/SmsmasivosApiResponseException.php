<?php

require_once dirname(__FILE__) . '/SmsmasivosException.php';

class SmsmasivosApiResponseException extends SmsmasivosException
{
    public function __construct(array $data)
    {
        parent::__construct("Error response data.", 102, $data);
    }
}
