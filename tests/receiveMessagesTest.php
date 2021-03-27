<?php

require_once dirname(__FILE__) . '/../src/SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../src/Smsmasivos.php';

try {
    // Ingresamos los datos de autenticación.
    SmsmasivosCredentials::setUserAndPassword('DEMO500', 'DEMO500');

    /*
    Recuperamos las respuesta de un numero en particular y lo marcamos como leido
     */

    // configuraciones opcionales
    $configs = array(
        'phone_number' => '1234567890',
        'mark_as_read' => true,
    );

    $res = Smsmasivos::receiveMessages($configs);

    if (count($res)) {
        foreach ($res as $t) {
            echo 'Enviado por: ' . $t['phone_number'] . PHP_EOL;
            echo 'Mensaje: ' . $t['message'] . PHP_EOL;
            echo 'Fecha de la respuesta: ' . $t['date']->format('d-m-Y H:i:s') . PHP_EOL;
            echo PHP_EOL;
        }
    } else {
        echo 'No se encontraron datos' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;

    if (method_exists($e, 'getExtraData')) {
        print_r($e->getExtraData());
    }
}
