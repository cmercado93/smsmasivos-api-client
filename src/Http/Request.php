<?php

namespace Cmercado93\SmsmasivosApiClient\Http;

use Cmercado93\SmsmasivosApiClient\Common\Endpoints;
use Cmercado93\SmsmasivosApiClient\Credentials;
use Cmercado93\SmsmasivosApiClient\Exceptions\ApiResponseException;
use Cmercado93\SmsmasivosApiClient\Exceptions\CredentialsException;
use Cmercado93\SmsmasivosApiClient\Http\Curl;

class Request
{
    /**
     * @param  string $path
     * @param  array  $params
     * @return array
     */
    public function get(string $path, array $params = [])
    {
        $data = [];

        $data['query'] = $params['query'] ?? [];

        return $this->exec('GET', $path, $data);
    }

    /**
     * @param  string $path
     * @param  array  $params
     * @return array
     */
    public function post(string $path, array $params = [])
    {
        $data = [];

        $data['query'] = $params['query'] ?? [];

        $data['body'] = $params['body'] ?? [];

        return $this->exec('POST', $path, $data);
    }

    protected function exec(string $method, string $path, array $data)
    {
        $curl = new Curl(Endpoints::GENERAL_URL);

        $response = [];

        $credentials = $this->getCredentials();

        $data['query']['api'] = 1;

        if ($method == 'POST') {
            $data['body'] = array_merge($data['body'], $credentials);
            $response = $curl->post($path, $data);
        }

        if ($method == 'GET') {
            $data['query'] = array_merge($data['query'], $credentials);
            $response = $curl->get($path, $data);
        }

        if ($response['code'] == 200) {
            return $response['response'];
        }

        throw new ApiResponseException($response);
    }

    /**
     * @throws Cmercado93\SmsmasivosApiClient\Exceptions\CredentialsException
     * @return array
     */
    protected function getCredentials() : array
    {
        $credentials = Credentials::getCredentials();

        if (empty($credentials)) {
            throw new CredentialsException();
        }

        $return = [];

        if (isset($credentials['api_key'])) {
            $return['apikey'] = $credentials['api_key'];
        } else {
            $return['usuario'] = $credentials['user'];
            $return['clave'] = $credentials['password'];
        }

        return $return;
    }
}
