<?php

namespace Cmercado93\SmsmasivosApi\Tests\Mock;

use Cmercado93\SmsmasivosApi\Http\HttpRequestInterface;

class MockHttpRequest implements HttpRequestInterface
{
    protected $responses = array();

    public function setNextResponse($code, $response)
    {
        $this->responses[] = array(
            'code' => $code,
            'response' => $response,
            'info' => array('http_code' => $code),
        );
    }

    public function get($path, $params = array())
    {
        return array_shift($this->responses);
    }

    public function post($path, $params = array())
    {
        return array_shift($this->responses);
    }
}
