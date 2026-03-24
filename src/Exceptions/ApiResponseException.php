<?php

namespace Cmercado93\SmsmasivosApi\Exceptions;

class ApiResponseException extends SmsmasivosException
{
    public function __construct(array $data)
    {
        $message = 'API error';

        if (isset($data['api_response'][0]['message'])) {
            $message = $data['api_response'][0]['message'];
        }

        parent::__construct($message, 102, $data);
    }
}
