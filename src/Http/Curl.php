<?php

namespace Cmercado93\SmsmasivosApiClient\Http;

class Curl
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @param string $host
     */
    public function __construct(string $host)
    {
        $this->host = $host;
    }

    /**
     * @param  string $path
     * @param  array  $params
     * @return array
     */
    public function get(string $path, array $params = []) : array
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
    public function post(string $path, array $params = []) : array
    {
        $data = [];

        $data['query'] = $params['query'] ?? [];

        $data['body'] = $params['body'] ?? [];

        return $this->exec('POST', $path, $data);
    }

    /**
     * @param  string $uri
     * @param  array  $data
     * @param  string $method
     * @return array
     */
    protected function exec(string $method, string $uri, array $data) : array
    {
        $url = $this->parseUrl($uri, $data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url); 

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($method == 'POST') {
            $postBody = $data['body'] ?? [];

            if (count($postBody)) {
                $fields_string = http_build_query($postBody);

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            }
        }

        $output = (string) curl_exec($ch);

        $info = curl_getinfo($ch);

        curl_close($ch);

        return array(
            'code' => $info['http_code'],
            'response' => $output,
            'info' => $info,
        );
    }

    /**
     * @param  string $uri
     * @param  array  $data
     * @return string
     */
    public function parseUrl(string $uri, array $data = []) : string
    {
        $uriArray = parse_url($uri);

        $url = $this->parseHost();

        if (isset($uriArray['path'])) {
            $url .= '/' . trim($uriArray['path'], '/');
        }

        $query = $data['query'] ?? [];

        if (isset($uriArray['query'])) {
            parse_str($uriArray['query'], $output);

            $query = array_merge($output, $query);
        }

        if (count($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    /**
     * @return string
     */
    public function parseHost() : string
    {
        $urlArray = parse_url($this->host);

        $url = $urlArray['scheme'] ?? 'http';

        $url .= '://';

        $url .= $urlArray['host'];

        if (isset($urlArray['port'])) {
            $url .= ':' . $urlArray['port'];
        }

        return $url;
    }
}
