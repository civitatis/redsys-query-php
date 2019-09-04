# Redsys query PHP

Allow query order transactions done into spanish gateway banks that uses Sermepa/Redsys.

This is used into webservice located in /apl02/services/SerClsWSConsulta.

The spanish documentation is located in 'Consultas SOAP HMAC SHA256 2.5', in root folder.

@IMPORTANT: Right now, the only service covered by this library is 'Transacción Simple'.

## Installation

Use composer:
```bash
composer require metadrop/redsys-query-php
```

## Examples

### Get single transaction
This service allows get the redsys transactions by id. 

Example (also available in examples/get_transaction.php:


```php

<?php
require './vendor/autoload.php';

use RedsysConsultasPHP\Client\Client;

$url = 'https://sis-t.redsys.es:25443/apl02/services/SerClsWSConsulta';
$client = new Client($url, 'Introduce your merchant password');

$order = 'Introduce your order';
$terminal = 'Introduce your terminal';
$merchant_code = 'Introduce your merchant code';
$response = $client->getTransaction($order, $terminal, $merchant_code);

print_r($response);

```

### Get monitor masiva
This service allows get the redsys monitor masiva by start date and end date.


```php

<?php
require './vendor/autoload.php';

use RedsysConsultasPHP\Client\Client;

$url = 'https://sis-t.redsys.es:25443/apl02/services/SerClsWSConsulta';
$client = new Client($url, 'Introduce your merchant password');

$fecha_inicio = 'Y-m-d-H.i.00.000000';
$fecha_fin = 'Y-m-d-H.i.59.000000';
$terminal = 'Introduce your terminal';
$merchant_code = 'Introduce your merchant code';
$response = $client->getMonitorMasiva($fecha_inicio, $fecha_fin, $terminal, $merchant_code);

print_r($response);

```
