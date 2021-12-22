<?php

require_once dirname(__FILE__) . '/../SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosEndpoints.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosException.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosValidationException.php';
require_once dirname(__FILE__) . '/../Exceptions/SmsmasivosApiResponseException.php';
require_once dirname(__FILE__) . '/../Common/SmsmasivosMessageValidation.php';

class SmsmasivosCheckMessageBlockSent
{
    private $filter = 'internal_id';

    private $filterValue = null;

    private $configs = array();

    private $filters = array(
        'internal_id',
        'date',
    );

    public function __construct()
    {
        SmsmasivosCredentials::existsCredentials(true);
    }

    public function setFilter($filter, $value)
    {
        $this->validateFilter($filter, $value);

        $this->filter = $filter;

        $this->filterValue = $value;
    }

    public function setConfigs(array $data)
    {
        $this->configs = $data;
    }

    public function check()
    {
        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $data = array(
            'query' => $this->getDataToSend(),
        );

        $res = $r->post(SmsmasivosEndpoints::CHECK_SENT_BLOCK, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        $errors['api_response'][] = array(
            'message' => 'Error en la API (' . $res['code'] . '): ' . (string) $res['response'],
            'code' => -99,
        );

        throw new SmsmasivosApiResponseException($errors);
    }

    /**
     * Analizo los datos que me pasan
     * @param  string $response
     * @return boolean|array
     */
    private function parseApiResponse($response)
    {
        if (trim(strtoupper($response)) == 'PENDIENTE') {
            return false;
        }

        $re = '/(.*)\t([0-9]+)\t(.*)/m';

        $math = array();

        $messages = array();

        preg_match_all($re, $response, $math, PREG_SET_ORDER, 0);

        foreach ($math as $mht) {
            $tmp = array();

            $tmp['internal_id'] = $mht[1];

            if (isset($this->configs['api_response_date']) && $this->configs['api_response_date'] == 'raw') {
                $tmp['date'] = $mht[2];
            } else {
                $tmp['date'] = new DateTime($mht[2]);
            }

            if (trim(strtoupper($mht[3])) == 'OK') {
                $tmp['sent'] = true;
            } else {
                $tmp['sent'] = false;
                $tmp['error'] = utf8_encode($mht[3]);
            }

            array_push($messages, $tmp);
        }

        return $messages;
    }

    private function getDataToSend()
    {
        $credential = SmsmasivosCredentials::getUserAndPassword();

        $res = array(
            'usuario' => $credential['user'],
            'clave' => $credential['password'],
        );

        switch ($this->filter) {
            case 'internal_id':
                $res['idinterno'] = $this->filterValue;
                break;
            case 'date':
                $res['fecha'] = $this->filterValue instanceof DateTime ? $this->filterValue->format('YmdHis') : '';
                break;
        }

        if (isset($this->configs['only_unread'])) {
            $res['solonoleidos'] = 1;
        }

        if (isset($this->configs['mark_as_read'])) {
            $res['marcarcomoleidos'] = 1;
        }

        return $res;
    }

    private function validateFilter($filter, $value)
    {
        $errors = array();

        if (!in_array($filter, $this->filters)) {
            $errors['filter'] = array(
                array(
                    'message' => 'filter not valid',
                    'code' => -99,
                ),
            );
        }

        switch ($filter) {
            case 'internal_id':
                $validator = new SmsmasivosMessageValidation;

                if (!$validator->validateInternalIdLength($value)) {
                    $errors['filter_value'][] = array(
                        'message' => 'El valor del filtro ID interno es muy largo',
                        'code' => -99,
                    );
                } elseif (!$validator->validateInternalIdCharacters($value)) {
                    $errors['filter_value'][] = array(
                        'message' => 'El valor del filtro ID interno contiene caracteres inválidos',
                        'code' => -99,
                    );
                }
                break;
            case 'date':
                if ($value instanceof DateTime == false) {
                    $errors['filter_value'][] = array(
                        'message' => 'El valor del filtro fecha debe ser una instancia de DateTime',
                        'code' => -99,
                    );
                }
                break;
        }

        if (count($errors)) {
            throw new SmsmasivosValidationException($errors);
        }
    }
}
