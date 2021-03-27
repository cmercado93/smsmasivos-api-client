<?php

require_once dirname(__FILE__) . '/../src/SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../src/Smsmasivos.php';

try {
    // Ingresamos los datos de autenticación.
    SmsmasivosCredentials::setUserAndPassword('DEMO500', 'DEMO500');

    $data = array();

    $data['configs'] = array(
        'is_test' => true, // opcional
    );

    $data['messages'] = array(
        array(
            'message' => 'texto 1',
            'phone_number' => '3364333333',
            'internal_id' => 'Ab123', // opcional
        ),
        array(
            'message' => 'texto 2',
            'phone_number' => '3364333333',
        ),
    );

    // Enviamos el bloque de mensajes.
    Smsmasivos::sendMessagesInBlock($data);
} catch (Exception $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;

    if (method_exists($e, 'getExtraData')) {
        print_r($e->getExtraData());
    }
}
