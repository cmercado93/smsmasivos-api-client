# smsmasivos-api-client

Librería para el uso de la API de SmsMasivos Argentina

Esta librería cuenta con el funcionamiento básico de la API de acuerdo con la documentación de [Sms Masivos](https://smsmasivos.com.ar).

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

# Requerimientos
- [PHP 5.2 o mayor](https://www.php.net/)

# Ejemplos

### Registro de credenciales:
Antes de comenzar a utilizar las funciones principales del paquete se tienen que registrar las credenciales que Sms Masivos les proporciona. Para realizar esto tiene que llamar a la clase *"SmsmasivosCredentials"* y luego al método estático *"setUserAndPassword"* como se muestra a continuación.

```php
<?php

require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosCredentials.php';

SmsmasivosCredentials::setUserAndPassword("DEMO500", "DEMO500");

```

## Envió de SMS en tiempo real:
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

## Envío de mensajes en bloque:
Esta función te permite enviar múltiples SMS's en una sola peticion.

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
