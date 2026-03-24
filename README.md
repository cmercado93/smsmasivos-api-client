# smsmasivos-api-client

Libreria para el uso de la API de SMS masivos Argentina

Esta libreria cuenta con el funcionamiento basico de la API de acuerdo con la documentacion de [SMS masivos](https://smsmasivos.com.ar).

## Funciones de la libreria

**Funciones que requieren autenticacion:**
- Envio de SMS en tiempo real.
- Envio de SMS en bloque.
- Comprobacion de mensajes enviados mediante bloque.
- Recepcion de mensajes.
- Consulta de saldo (Cuentas prepago).
- Consulta de vencimiento del paquete contratado (Cuentas prepago).
- Consulta de cantidad de mensajes enviados en el mes.

**Funciones que no requieren autenticacion:**
- Consulta de la hora del servidor.

## Requerimientos

- [PHP 5.3.3 o mayor](https://www.php.net/)
- Extensiones: `curl`, `mbstring`
- [Composer](https://getcomposer.org/)

## Instalacion

```bash
composer require cmercado93/smsmasivos-api-client
```

## Uso

### Registro de credenciales

Antes de utilizar las funciones principales se tienen que registrar las credenciales. Se soportan dos metodos de autenticacion:

```php
<?php

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Smsmasivos;

// Opcion A: APIKEY (recomendada)
Credentials::setApiKey('MI_API_KEY');

// Opcion B: usuario/clave
Credentials::setUserAndPassword('DEMO500', 'DEMO500');
```

### Envio de SMS en tiempo real

```php
<?php

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Smsmasivos;

try {
    Credentials::setApiKey('MI_API_KEY');

    Smsmasivos::sendMessage('1234567890', 'Mensaje a enviar', array(
        'test' => true,
        'internal_id' => 'Ab123',
        'send_date' => new DateTime('NOW'),
        'html' => '<p>Texto en <b>HTML</b></p>',
    ));
} catch (\Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;
    print_r($e->getExtraData());
}
```

### Envio de mensajes en bloque

```php
<?php

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Smsmasivos;

try {
    Credentials::setApiKey('MI_API_KEY');

    $data = array(
        'configs' => array(
            'test' => true,                  // opcional
            'field_separator' => 'tab',      // opcional: 'tab' o 'comma' (default)
        ),
        'messages' => array(
            array(
                'message' => 'texto 1',
                'phone_number' => '1234567890',
                'internal_id' => 'Ab123',    // opcional
            ),
            array(
                'message' => 'texto 2',
                'phone_number' => '1234567891',
            ),
        ),
    );

    Smsmasivos::sendMessagesInBlock($data);
} catch (\Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;
    print_r($e->getExtraData());
}
```

### Verificacion de bloque de mensajes enviados

```php
<?php

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Smsmasivos;

try {
    Credentials::setApiKey('MI_API_KEY');

    // Por ID interno
    $res = Smsmasivos::checkMessageBlockSent('Ab123', 'internal_id', array(
        'mark_as_read' => 1,
    ));

    if ($res && count($res)) {
        foreach ($res as $m) {
            if ($m['sent']) {
                echo 'El mensaje "' . $m['internal_id'] . '" fue enviado.' . PHP_EOL;
            } else {
                echo 'El mensaje "' . $m['internal_id'] . '" no fue enviado: ' . $m['error'] . PHP_EOL;
            }
        }
    } else {
        echo 'No se encontraron datos.' . PHP_EOL;
    }

    // Por fecha
    $date = new DateTime('NOW');
    $res = Smsmasivos::checkMessageBlockSent($date, 'date');
} catch (\Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;
    print_r($e->getExtraData());
}
```

### Recepcion de mensajes

```php
<?php

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Smsmasivos;

try {
    Credentials::setApiKey('MI_API_KEY');

    $configs = array(
        'phone_number' => '1234567890',     // opcional: filtrar por numero
        'mark_as_read' => true,             // opcional
        'only_unread' => true,              // opcional
        'include_internal_id' => true,      // opcional (default: true)
        'format' => 'text',                 // opcional: 'text' (default) o 'excel'
    );

    $res = Smsmasivos::receiveMessages($configs);

    foreach ($res as $t) {
        echo 'Enviado por: ' . $t['phone_number'] . PHP_EOL;
        echo 'Mensaje: ' . $t['message'] . PHP_EOL;
        echo 'Fecha: ' . $t['date']->format('d-m-Y H:i:s') . PHP_EOL;
    }
} catch (\Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException $e) {
    echo 'code: ' . $e->getCode() . PHP_EOL;
    echo 'msg: ' . $e->getMessage() . PHP_EOL;
    print_r($e->getExtraData());
}
```

### Consultas de cuenta

```php
<?php

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Smsmasivos;

Credentials::setApiKey('MI_API_KEY');

// Saldo disponible (prepago)
$saldo = Smsmasivos::getBalance();

// Vencimiento del paquete (prepago)
$vencimiento = Smsmasivos::getPackageExpiration();

// Mensajes enviados en el mes (plan abierto)
$enviados = Smsmasivos::getNumberMessagesSent();

// Fecha del servidor (no requiere autenticacion)
$fecha = Smsmasivos::getCurrentDateServer();
```

## Implementacion HTTP personalizada

Se puede reemplazar la implementacion HTTP por defecto (cURL) con una propia:

```php
<?php

use Cmercado93\SmsmasivosApi\Smsmasivos;
use Cmercado93\SmsmasivosApi\Http\HttpRequestInterface;

class MiHttpClient implements HttpRequestInterface
{
    public function get($path, $params = array()) { /* ... */ }
    public function post($path, $params = array()) { /* ... */ }
}

Smsmasivos::setHttpRequest(new MiHttpClient());
```

## Excepciones

Todas las operaciones tiran excepciones en caso de error:

| Excepcion | Codigo | Cuando |
|---|---|---|
| `CredentialsException` | 100 | No se configuraron credenciales |
| `ValidationException` | 101 | Error de validacion en los datos de entrada |
| `ApiResponseException` | 102 | Error en la respuesta de la API o HTTP != 200 |

Todas extienden de `SmsmasivosException`, que a su vez extiende de `\Exception`.

```php
use Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException;
use Cmercado93\SmsmasivosApi\Exceptions\ValidationException;

try {
    Smsmasivos::sendMessage('123', 'Hello');
} catch (ValidationException $e) {
    // Error de validacion
    print_r($e->getExtraData());
} catch (SmsmasivosException $e) {
    // Cualquier otro error del SDK
    echo $e->getMessage();
}
```

## Codigos de respuesta de la API

La clase `ResponseCode` contiene constantes para todos los codigos:

```php
use Cmercado93\SmsmasivosApi\Common\ResponseCode;

ResponseCode::OK;                    //  0  - Mensaje entregado
ResponseCode::TEST_OK;               //  1  - Simulacro OK
ResponseCode::LANDLINE;              // -1  - Telefono fijo
ResponseCode::TOO_MANY_FAILURES;     // -2  - Muchos fallos previos
ResponseCode::UNSUBSCRIBED;          // -3  - Envio palabra BAJA
ResponseCode::DUPLICATE_MESSAGE;     // -4  - Mensaje identico reciente
ResponseCode::SPAM;                  // -5  - Posible SPAM
ResponseCode::MESSAGE_TOO_LONG;      // -6  - Texto muy largo
ResponseCode::INVALID_NUMBER_LENGTH; // -7  - Numero debe tener 10 digitos
ResponseCode::INVALID_NUMBER_CHARS;  // -8  - Caracteres invalidos en numero
ResponseCode::INVALID_AREA_CODE;     // -9  - Caracteristica invalida
ResponseCode::CARRIER_REJECTED;      // -10 - Rechazado por telefonica
ResponseCode::INVALID_MESSAGE_CHARS; // -11 - Caracteres invalidos en texto
ResponseCode::INVALID_NUMBER_PREFIX; // -12 - Numero debe comenzar con 1, 2 o 3
ResponseCode::DO_NOT_CALL_LIST;      // -14 - Registro "No Llame"
ResponseCode::OTHER;                 // -99 - Otro
```

## Tests

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpunit --testdox
```

## Licencia

Distribuido bajo la licencia MIT. Vea `LICENSE.md` para mas informacion.

_Este software y sus desarrolladores no tienen ninguna relacion con [SMS masivos](https://smsmasivos.com.ar)._
