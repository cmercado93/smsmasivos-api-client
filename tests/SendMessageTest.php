<?php

require_once dirname(__FILE__) . '/../src/SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../src/Smsmasivos.php';

try {
    // Método 1: Autenticación con APIKEY (recomendado - API v12)
    // Descomenta la siguiente línea y reemplaza 'TU_APIKEY_AQUI' con tu APIKEY real
    // SmsmasivosCredentials::setApiKey('TU_APIKEY_AQUI');

    // Método 2: Autenticación con usuario y contraseña (legacy)
    SmsmasivosCredentials::setUserAndPassword('DEMO500', 'DEMO500');

    // Enviamos un nuevo mensaje.
    Smsmasivos::sendMessage('1234567890', 'Mensaje a enviar', array(
        'test' => true,
        'internal_id' => 'Ab123',
        'send_date' => new DateTime('NOW'),
        'html' => '<p>Texto en <b>HTML</b></p>'
    ));

    echo 'Mensaje enviado correctamente!' . PHP_EOL;
} catch (Exception $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;

    if (method_exists($e, 'getExtraData')) {
        print_r($e->getExtraData());
    }
}
