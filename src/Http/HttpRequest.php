<?php

namespace Cmercado93\SmsmasivosApi\Http;

use Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException;
use Cmercado93\SmsmasivosApi\Common\ResponseCode;

class HttpRequest implements HttpRequestInterface
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @param string $host
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    /**
     * @param  string $path
     * @param  array  $params
     * @return array
     */
    public function get($path, $params = array())
    {
        $data = array();
        $data['query'] = isset($params['query']) ? $params['query'] : array();

        return $this->exec($path, $data, 'GET');
    }

    /**
     * @param  string $path
     * @param  array  $params
     * @return array
     */
    public function post($path, $params = array())
    {
        $data = array();
        $data['query'] = isset($params['query']) ? $params['query'] : array();
        $data['body'] = isset($params['body']) ? $params['body'] : array();

        return $this->exec($path, $data, 'POST');
    }

    /**
     * @param  string $uri
     * @param  array  $data
     * @param  string $method
     * @return array
     */
    protected function exec($uri, $data, $method)
    {
        $parsed = parse_url($uri);
        $path = isset($parsed['path']) ? $parsed['path'] : $uri;

        $query = array();

        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $query);
        }

        if (isset($data['query'])) {
            $query = array_merge($query, $data['query']);
        }

        $url = $this->host . '/' . $path . (count($query) ? '?' . http_build_query($query) : '');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($method == 'POST') {
            $postBody = isset($data['body']) ? $data['body'] : array();

            if (count($postBody)) {
                $fields_string = http_build_query($postBody);

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            }
        }

        $output = curl_exec($ch);

        if ($output === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);

            throw new ApiResponseException(array(
                'api_response' => array(array(
                    'message' => 'cURL error (' . $errno . '): ' . $error,
                    'code' => ResponseCode::OTHER,
                )),
            ));
        }

        $output = (string) $output;
        $info = curl_getinfo($ch);

        curl_close($ch);

        return array(
            'code' => $info['http_code'],
            'response' => $output,
            'info' => $info,
        );
    }
}
