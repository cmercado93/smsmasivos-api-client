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
require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosMessage.php';

SmsmasivosCredentials::setUserAndPassword("DEMO500", "DEMO500");

$newMessage = new SmsmasivosMessage();

// Ingreso el numero de teléfono al que enviare el mensaje
$newMessage->setPhoneNumber('1122223344');

// Ingreso el mensaje que enviare
$newMessage->setMessage('Mensaje a enviar');

// Marco este mensaje como una prueba para que SmsMasivos no lo envíe
$newMessage->isTest();

// Ingreso un ID con el que podre identificarlo desde mi sistema
$newMessage->setInternalId("ABC123");

// Genero una fecha en la cual quiero que el mensaje sea enviado desde Sms Masivos
$date = new DateTime("2020-01-12 13:12:00");
$newMessage->setSendDate($date);

// Genero texto en HTML que SmsMasivos utilizara para generar una pagina dinámica con esto
// Solo sera tomado en cuenta si en el mensaje a enviar se incluye este enlace "http://1rck.in/-000000"
$newMessage->setHtml('<p>Texto en <b>HTML</b></p>');

// Envio el mensaje
if ($newMessage->send()) {
    echo "Se envio el mensaje" . PHP_EOL;
} else {
    echo "No se pudo enviar el mensaje" . PHP_EOL;
}

// Compruebo si no se generaron errores
if ($newMessage->hasErrors()) {
    echo "Se encontraron errores:" . PHP_EOL . PHP_EOL;

    echo "Código de error: " . $newMessage->getErrorNumber() . PHP_EOL;
    echo "Mensaje de error: " . $newMessage->getErrorMessage() . PHP_EOL;
}

```

## Envío de SMS en bloque:
Esta función permite el envío de múltiples SMS mediante una única petición a la API.

```php
<?php

require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosCredentials.php';
require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosMessage.php';
require dirname(__FILE__) . '/cmercado93/smsmasivos-api-client/src/SmsmasivosBlock.php';

SmsmasivosCredentials::setUserAndPassword("DEMO500", "DEMO500");

//
// Generamos un mensaje básico
//

$newMessage = new SmsmasivosMessage();

// Ingreso el numero de teléfono al que enviare el mensaje
$newMessage->setPhoneNumber('1122223344');

// Ingreso el mensaje que enviare
$newMessage->setMessage('Mensaje a enviar');

// Ingreso un ID con el que podre identificarlo desde mi sistema
$newMessage->setInternalId("ABC123");

//
// iniciamos un nuevo bloque
//

$newBlock = new SmsmasivosBlock();

// Seteamos el separador del bloque, de forma predeterminada viene en "coma"
$newBlock->useFieldSeparatorComa();

// seteamos que se trata de una prueba
$newBlock->isTest();

// Agregamos el mensaje a enviar
$newBlock->addMessageToBlock($newMessage);

// enviamos el bloque
if ($newBlock->send()) {
    echo "Se envio el bloque de mensajes" . PHP_EOL;
} else {
    echo "No se pudo enviar el bloque de mensajes" . PHP_EOL;
}

// si hay errores los mostramos
if ($newBlock->hasErrors()) {
    echo "Se encontraron errores:" . PHP_EOL . PHP_EOL;

    foreach ($newBlock->getErrors() as $error) {
        echo sprintf("Codigo %s: %s", $error['number'], $error['message']) . PHP_EOL;
    }
}

```
