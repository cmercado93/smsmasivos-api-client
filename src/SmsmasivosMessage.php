<?php

require_once dirname(__FILE__) . '/SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/Http/SmsmasivosHttpRequest.php';
require_once dirname(__FILE__) . '/Common/SmsmasivosEndpoints.php';
require_once dirname(__FILE__) . '/Common/SmsmasivosMessageValidation.php';

class SmsmasivosMessage {

    /**
     * Numero de error respondido por la API
     * @var int
     */
    private $errorNumber;

    /**
     * Mensaje de error respondido por la API
     * @var string
     */
    private $errorMessage;

    /**
     * Numero telefónico al que se enviara el mensaje
     * @var string
     */
    private $phoneNumber;

    /**
     * Mensaje a enviar
     * @var string
     */
    private $message;

    /**
     * Bandera que permite realizar una prueba sin enviar el mensaje
     * @var boolean
     */
    private $test = false;

    /**
     * ID interno que se relacionara con el mensaje enviado
     * @var string
     */
    private $internalId;

    /**
     * Fecha de envío del mensaje
     * @var DateTime
     */
    private $sendDate;

    /**
     * Texto HTML que se incluirá dentro del mensaje
     * @var [type]
     */
    private $html;

    /**
     * Bandera que indica si el mensaje fue enviado en esta instancia
     * @var boolean
     */
    private $sent = false;

    public function __construct()
    {
        if (!SmsmasivosCredentials::existsCredentials()) {
            throw new Exception("No se ingresaron las credenciales necesarias");
        }
    }

    /**
     * Establezco el numero de teléfono
     * @param String
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Retorno el numero de teléfono
     * @return String
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Establezco el mensaje
     * @param String
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Retorno el mensaje
     * @return String
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Establezco el ID interno
     * @param string
     */
    public function setInternalId($id)
    {
        $this->internalId = $id;
    }

    /**
     * Retorno el ID interno
     * @return string
     */
    public function getInternalId()
    {
        return $this->internalId;
    }

    /**
     * Establezco la fecha en la que se enviara
     * @param DateTime
     */
    public function setSendDate(DateTime $date)
    {
        $this->sendDate = $date;
    }

    /**
     * Retorno la fecha en la que se enviara
     * @param DateTime|string
     */
    public function getSendDate($format = 'Y-m-d H:i:s')
    {
        if ($format === false) {
            return $this->sendDate;
        }

        return $this->sendDate instanceOf DateTime ? $this->sendDate->format($format) : null;
    }

    /**
     * Indico que el mensaje es de prueba
     * @return void
     */
    public function isTest()
    {
        $this->setIsTest();
    }

    /**
     * Indico que el mensaje es de prueba
     * @return void
     */
    public function setIsTest()
    {
        $this->test = true;
    }

    /**
     * @return boolean
     */
    public function getIsTest()
    {
        return (boolean)$this->test;
    }

    /**
     * Establezco el contenido HTML
     * @param string
     */
    public function setHtml($html)
    {
        return $this->html = $html;
    }

    /**
     * Retorno el contenido HTML
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Establezco el numero de error
     * @param int
     */
    private function setErrorNumber($number)
    {
        $this->errorNumber = (int) $number;
    }

    /**
     * Retorno el numero de error
     * @return int
     */
    public function getErrorNumber()
    {
        return (int) $this->errorNumber;
    }

    /**
     * Establezco el mensaje de error
     * @param string
     */
    private function setErrorMessage($message)
    {
        $this->errorMessage = $message;
    }

    /**
     * Retorno el mensaje de error
     * @param string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Retorno si se produjo un error
     * @return boolean
     */
    public function hasErrors()
    {
        return (int) $this->getErrorNumber() < 0;
    }

    /**
     * Establezco el numero y mensaje del error
     * @param int
     * @param string
     */
    public function setError($number, $message)
    {
        $this->setErrorNumber($number);
        $this->setErrorMessage($message);
    }

    /**
     * Limpiamos los posibles errores
     * @return void
     */
    private function clearError()
    {
        $this->setErrorNumber(null);
        $this->setErrorMessage(null);
    }

    /**
     * Retorno el error generado
     * @return array|boolean
     */
    public function getError()
    {
        if (!$this->hasErrors()) {
            return false;
        }

        return array(
            'number' => $this->getErrorNumber(),
            'message' => $this->getErrorMessage(),
        );
    }

    public function markMessageAsSent()
    {
        $this->sent = true;
    }

    public function itsSent()
    {
        return (bool) $this->sent;
    }

    /**
     * @return boolean
     */
    public function isValid($fullValid = false)
    {
        return $this->validateDataToSend($fullValid);
    }

    /**
     * Realizo el envio del mensaje
     * @return boolean
     */
    public function send()
    {
        $this->clearError();

        if (!$this->validateDataToSend()) {
            return false;
        }

        $r = new SmsmasivosHttpRequest(SmsmasivosEndpoints::URL_GENERAL);

        $data = array(
            'query' => $this->getDataToSend(),
        );

        $res = $r->get(SmsmasivosEndpoints::URI_SEND_MESSAGE, $data);

        if ($res['code'] == 200) {
            return $this->parseApiResponse($res['response']);
        }

        $this->setError(-99, 'Error en la API (' . $res['code'] . '): ' . (string) $res['response']);

        return false;
    }

    /**
     * Validamos los datos que se enviaran a la API
     * @return boolean
     */
    private function validateDataToSend($fullValid = false)
    {
        $validator = new SmsmasivosMessageValidation();

        if (!$validator->validateMessageCharacters($this->getMessage())) {
            $this->setError(-11, $this->getPhoneNumber() . ': El texto del mensaje contiene caractéres inválidos');
            return false;
        }

        if (!$validator->validateMessageLength($this->getMessage())) {
            $this->setError(-6, $this->getPhoneNumber() . ': El texto del mensaje es muy largo');
            return false;
        }

        if (!$validator->validatePhoneNumberCharacters($this->getPhoneNumber())) {
            $this->setError(-8, $this->getPhoneNumber() . ': El número contiene caracteres inválidos');
            return false;
        }

        if (!$validator->validatePhoneNumberLength($this->getPhoneNumber())) {
            $this->setError(-7, $this->getPhoneNumber() . ': El número debe tener al menos 10 dígitos');
            return false;
        }

        if ($this->getInternalId() || $fullValid == true) {
            if (!$validator->validateInternalIdCharacters($this->getInternalId())) {
                $this->setError(-99, $this->getPhoneNumber() . ': El ID interno contiene caracteres inválidos');
                return false;
            }

            if (!$validator->validateInternalIdCharacters($this->getInternalId())) {
                $this->setError(-99, $this->getPhoneNumber() . ': El ID interno es muy largo');
                return false;
            }
        }

        return true;
    }

    /**
     * Generamos la lista de datos que se enviaran a la API
     * @return array
     */
    private function getDataToSend()
    {
        $credential = SmsmasivosCredentials::getUserAndPassword();

        $data = array(
            'usuario' => $credential['user'],
            'clave' => $credential['password'],
            'tos' => $this->getPhoneNumber(),
            'texto' => $this->getMessage(),
            'api' => 1,
            'respuestanumerica' => 1,
        );

        if ($this->getIsTest()) {
            $data['test'] = 1;
        }

        if ($this->getSendDate()) {
            $data['fechadesde'] = $this->getSendDate();
        }

        if ($this->getHtml()) {
            $data['html'] = $this->getHtml();
        }

        return $data;
    }

    /**
     * Analizo la respuesta de la API
     * @param  string
     * @return boolean
     */
    private function parseApiResponse($response)
    {
        $r = explode(';', $response, 2);

        // valido si se respondio en el formato correcto
        if (!is_numeric($r[0])) {
            $this->setError(-99, $response);
            return false;
        }

        if ((int)$r[0] == 0 || (int)$r[0] == 1) {
            $this->markMessageAsSent();
            return true;
        }

        $this->setError($r[0], $r[1]);

        return false;
    }
}
