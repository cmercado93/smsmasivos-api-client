<?php

class SmsmasivosHttpRequest
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @param string
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    /**
     * @param  string
     * @param  array
     * @return array
     */
    public function get($path, $params = array())
    {
        $data = array();

        $data['query'] = isset($params['query']) ? $params['query'] : array();

        return $this->exec($path, $data, 'GET');
    }

    /**
     * @param  string
     * @param  array
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
     * @param  string
     * @param  array
     * @param  string
     * @return array
     */
    protected function exec($uri, $data, $method)
    {
        $path = parse_url($uri, PHP_URL_PATH);

        $query = array();

        if ($tmp = parse_url($uri, PHP_URL_QUERY)) {
            parse_str($tmp, $query);
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

        $output = (string) curl_exec($ch);

        $info = curl_getinfo($ch);

        curl_close($ch);

        return array(
            'code' => $info['http_code'],
            'response' => $output,
            'info' => $info,
        );
    }
}
