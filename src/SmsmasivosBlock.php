<?php

require_once dirname(__FILE__) . '/SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/Common/SmsmasivosEndpoints.php';

class SmsmasivosBlock {

    const FIELD_SEPARATOR_TAB = "\t";

    const FIELD_SEPARATOR_COMA = ",";

    const FIELD_SEPARATOR_STRING_TAB = "tab";

    const FIELD_SEPARATOR_STRING_COMA = "coma";

    /**
     * @var null|string
     */
    private $fieldSeparator = null;

    /**
     * @var null|string
     */
    private $fieldSeparatorString = null;

    /**
     * @var boolean
     */
    private $test = false;

    /**
     * @var array
     */
    private $messageList = array();

    /**
     * @var array
     */
    private $errors = array();

    public function __construct()
    {
        if (!SmsmasivosCredentials::existsCredentials()) {
            throw new Exception("No se ingresaron las credenciales necesarias");
        }
    }

    /**
     * @return void
     */
    public function useFieldSeparatorTab()
    {
        $this->fieldSeparator = self::FIELD_SEPARATOR_TAB;
        $this->fieldSeparatorString = self::FIELD_SEPARATOR_STRING_TAB;
    }

    /**
     * @return void
     */
    public function useFieldSeparatorComa()
    {
        $this->fieldSeparator = self::FIELD_SEPARATOR_COMA;
        $this->fieldSeparatorString = self::FIELD_SEPARATOR_STRING_COMA;
    }

    /**
     * @return string
     */
    public function getFieldSeparator()
    {
        return $this->fieldSeparator ? $this->fieldSeparator : self::FIELD_SEPARATOR_COMA;
    }

    /**
     * @return string
     */
    public function getFieldSeparatorString()
    {
        return $this->fieldSeparatorString ? $this->fieldSeparatorString : self::FIELD_SEPARATOR_STRING_COMA;
    }

    /**
     * @return void
     */
    public function isTest()
    {
        $this->setIsTest();
    }

    /**
     * @return void
     */
    public function setIsTest()
    {
        $this->test = true;
    }

    public function getIsTest()
    {
        return $this->test;
    }

    /**
     * @return void
     */
    public function addMessageToBlock(SmsmasivosMessage $message)
    {
        array_push($this->messageList, $message);
    }

    public function getMessageBlock()
    {
        return $this->messageList;
    }

    /**
     * @return void
     */
    private function addError($number, $message)
    {
        array_push($this->errors, array(
            'number' => $number,
            'message' => $message,
        ));
    }

    /**
     * @return boolean
     */
    public function hasErrors()
    {
        return (bool) count($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function clearErrors()
    {
        return $this->errors = array();
    }

    /**
     * @return boolean
     */
    public function send()
    {
        $this->clearErrors();

        if (!$this->validateDataToSend()) {
            return false;
        }

        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $data = array(
            'body' => $this->getDataToSend(),
        );

        $res = $r->post(SmsmasivosEndpoints::URI_SEND_MESSAGE_BLOCK, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        return false;
    }

    /**
     * @return array
     */
    private function getDataToSend()
    {
        $credential = SmsmasivosCredentials::getUserAndPassword();

        $data = array(
            'usuario' => $credential['user'],
            'clave' => $credential['password'],
            'bloque' => array(),
            'separadorcampos' => $this->getFieldSeparatorString(),
        );

        if ($this->getIsTest()) {
            $data['test'] = 1;
        }

        foreach ($this->parseMessageList() as $message) {
            $str = $message->getInternalId() . $this->getFieldSeparator() . $message->getPhoneNumber() . $this->getFieldSeparator() . $message->getMessage();

            array_push($data['bloque'], $str);
        }

        $data['bloque'] = implode(PHP_EOL, $data['bloque']);

        return $data;
    }

    /**
     * @return array
     */
    private function parseMessageList()
    {
        $block = array();

        foreach ($this->getMessageBlock() as $message) {
            if ($message->isValid(true) && !$message->itsSent()) {
                array_push($block, $message);
            } else {
                $this->addError($message->getErrorNumber(), $message->getErrorMessage());
            }
        }

        return $block;
    }

    /**
     * @return boolean
     */
    private function validateDataToSend()
    {
        if (count($this->parseMessageList())) {
            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function parseApiResponse($response)
    {
        return trim(strtoupper($response)) == 'OK';
    }
}
