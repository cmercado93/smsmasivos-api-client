<?php

namespace Cmercado93\SmsmasivosApi\Exceptions;

class ValidationException extends SmsmasivosException
{
    public function __construct(array $data)
    {
        $count = 0;

        foreach ($data as $field => $errors) {
            if (is_array($errors)) {
                $count += count($errors);
            }
        }

        $message = "Validation failed with {$count} error(s).";

        parent::__construct($message, 101, $data);
    }
}
