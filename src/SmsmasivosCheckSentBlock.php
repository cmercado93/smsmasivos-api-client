<?php

require_once dirname(__FILE__) . '/SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/SmsmasivosMessage.php';
require_once dirname(__FILE__) . '/Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/Common/SmsmasivosEndpoints.php';

class SmsmasivosCheckSentBlock
{
    public function __construct()
    {
        if (!SmsmasivosCredentials::existsCredentials()) {
            throw new Exception("No se ingresaron las credenciales necesarias");
        }
    }

    /**
     * Busco mensajes por el ID interno
     * @param  string  $internalId
     * @param  boolean $onlyUnreadMessages
     * @param  boolean $markAsRead
     * @return array|boolean
     */
    public function checkByInternalId($internalId, $onlyUnreadMessages = false, $markAsRead = false)
    {
        return $this->send('internalId', $internalId, $onlyUnreadMessages, $markAsRead);
    }

    /**
     * Busco mensajes por la fecha de envio
     * @param  string  $internalId
     * @param  boolean $onlyUnreadMessages
     * @param  boolean $markAsRead
     * @return array|boolean
     */
    public function checkByDate(DateTime $date, $onlyUnreadMessages = false, $markAsRead = false)
    {
        return $this->send('date', $date, $onlyUnreadMessages, $markAsRead);
    }

    private function send($filter, $value, $onlyUnreadMessages = false, $markAsRead = false)
    {
        $credential = SmsmasivosCredentials::getUserAndPassword();

        $data = array(
            'usuario' => $credential['user'],
            'clave' => $credential['password'],
        );

        if ($onlyUnreadMessages) {
            $data['solonoleidos'] = 1;
        }

        if ($markAsRead) {
            $data['marcarcomoleidos'] = 1;
        }

        switch ($filter) {
            case 'internalId':
                $data['idinterno'] = $value;
                break;
            case 'date':
                $value = $value instanceOf DateTime ? $value->format('YmdHis') : date('YmdHis');
                $data['fecha'] = $value;
                break;
        }

        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $data = array(
            'query' => $data,
        );

        $res = $r->post(SmsmasivosEndpoints::CHECK_SENT_BLOCK, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        return false;
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
            $message = new SmsmasivosMessage;

            $message->setInternalId($mht[1]);

            $date = new DateTime($mht[2]);
            $message->setSendDate($date);

            if (trim(strtoupper($mht[3])) == 'OK') {
                $message->markMessageAsSent();
            } else {
                $message->setError(-99, $mht[3]);
            }

            array_push($messages, $message);
        }

        return $messages;
    }
}
