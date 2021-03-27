<?php

require_once dirname(__FILE__) . '/../src/SmsmasivosCredentials.php';
require_once dirname(__FILE__) . '/../src/Smsmasivos.php';

try {
    // Ingresamos los datos de autenticación.
    SmsmasivosCredentials::setUserAndPassword('DEMO500', 'DEMO500');

    /****************************************************************
    Verificamos el estado de un mensaje en particular y lo marcamos como leído.
     ****************************************************************/

    $res = Smsmasivos::checkMessageBlockSent('Ab123', 'internal_id', array(
        'mark_as_read' => 1,
    ));

    if ($res && count($res)) {
        if ($res[0]['sent']) {
            echo 'El mensaje "' . $res[0]['internal_id'] . '" fue enviado.' . PHP_EOL;
        } else {
            echo 'El mensaje "' . $res[0]['internal_id'] . '" no fue enviado por esta razón: ' . $res[0]['error'] . PHP_EOL;
        }
    } else {
        echo 'No se encontraron datos.' . PHP_EOL;
    }

    /****************************************************************
    Verificamos si se enviaron los mensajes de un fecha particular.
     ****************************************************************/

    $date = new DateTime('NOW');
    $res = Smsmasivos::checkMessageBlockSent($date, 'date');

    if ($res && count($res)) {
        foreach ($res as $m) {
            if ($m['sent']) {
                echo 'El mensaje "' . $m['internal_id'] . '" fue enviado.' . PHP_EOL;
            } else {
                echo 'El mensaje "' . $m['internal_id'] . '" no fue enviado por esta razón: ' . $m['error'] . PHP_EOL;
            }
        }
    } else {
        echo 'No se encontraron datos.' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;

    if (method_exists($e, 'getExtraData')) {
        print_r($e->getExtraData());
    }
}
