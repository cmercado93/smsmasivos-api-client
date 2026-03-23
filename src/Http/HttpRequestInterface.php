<?php

namespace Cmercado93\SmsmasivosApi\Http;

interface HttpRequestInterface
{
    /**
     * @param  string $path
     * @param  array  $params  array('query' => array(...))
     * @return array           array('code' => int, 'response' => string, 'info' => array)
     */
    public function get($path, $params = array());

    /**
     * @param  string $path
     * @param  array  $params  array('query' => array(...), 'body' => array(...))
     * @return array           array('code' => int, 'response' => string, 'info' => array)
     */
    public function post($path, $params = array());
}
