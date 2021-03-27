# smsmasivos-api-client

Librería para el uso de la API de SMS masivos Argentina

Esta librería cuenta con el funcionamiento básico de la API de acuerdo con la documentación de [SMS masivos](https://smsmasivos.com.ar).

## Funciones de la libreria:

__*Funciones que requieren autenticación.*__
- Envío de SMS en tiempo real.
- Envío de SMS en bloque.
- Comprobación de mensajes enviados mediante bloque.
- Consulta de saldo (Cuentas prepago).
- Consulta de vencimiento del paquete contratado (Cuentas prepago).
- Consulta de cantidad de mensajes enviados en el mes.

__*Funciones que no requieren autenticación.*__
- Consulta de la hora del servidor.

## Requerimientos
- [PHP 5.2 o mayor](https://www.php.net/)

## Ejemplos

### Registro de credenciales:
Antes de comenzar a utilizar las funciones principales del paquete se tienen que registrar las credenciales que SMS masivos les proporciona. Para realizar esto tiene que llamar a la clase *"SmsmasivosCredentials"* y luego al método estático *"setUserAndPassword"* como se muestra a continuación.

```php
<?php

require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosCredentials.php';

SmsmasivosCredentials::setUserAndPassword("DEMO500", "DEMO500");

```

### Envió de SMS en tiempo real:
Esta función te permite enviar SMS en tiempo real.

```php
<?php

require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosCredentials.php';
require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/Smsmasivos.php';

try {
    // Ingresamos los datos de autenticación.
    SmsmasivosCredentials::setUserAndPassword('DEMO500', 'DEMO500');

    // Enviamos un nuevo mensaje.
    Smsmasivos::sendMessage('1234567890', 'Mensaje a enviar', array(
        'test' => true,
        'internal_id' => 'Ab123',
        'send_date' => new DateTime('NOW'),
        'html' => '<p>Texto en <b>HTML</b></p>'
    ));
} catch (Exception $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;

    if (method_exists($e, 'getExtraData')) {
        print_r($e->getExtraData());
    }
}

```

### Envío de mensajes en bloque:
Esta función te permite enviar múltiples SMS en una sola peticion.

```php
<?php

require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosCredentials.php';
require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/Smsmasivos.php';

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
            'phone_number' => '1234567890',
            'internal_id' => 'Ab123', // opcional
        ),
        array(
            'message' => 'texto 2',
            'phone_number' => '1234567891',
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

```

### Verificación de bloque de mensajes enviados.
Con esta función podes verificar si un bloque de mensajes fue enviado.

```php
<?php

require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosCredentials.php';
require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/Smsmasivos.php';

try {
    // Ingresamos los datos de autenticación.
    SmsmasivosCredentials::setUserAndPassword('DEMO500', 'DEMO500');

    /****************************************************************
    Verificamos el estado de un mensaje en particular y lo marcamos como leído.
     ****************************************************************/

    // Enviamos el bloque de mensajes.
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


```

### Recibimos las respuestas a los mensajes que enviamos

```php
<?php

require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosCredentials.php';
require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/Smsmasivos.php';

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

```

### Licencia
Distribuido bajo la licencia MIT. Vea `LICENSE.md` para más información.

_Este software y sus desarrolladores no tienen ninguna relación con [SMS masivos](https://smsmasivos.com.ar)._
