<?php

require '../vendor/autoload.php';

use RedsysConsultasPHP\Client\Client;

$url = 'https://sis-t.redsys.es:25443/apl02/services/SerClsWSConsulta';
$client = new Client($url, 'Introduce your merchant password');

$order = 'Introduce Ds_Order';
$terminal = 'Introduce Ds_terminal';
$merchant_code = 'Introduce your Ds_merchantCode';
$response = $client->getTransaction($order, $terminal, $merchant_code);

print_r($response);
