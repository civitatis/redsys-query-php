<?php

namespace RedsysConsultasPHP\Client;

use GuzzleHttp\Psr7\Response;

class ResponseParser
{
    public static function parse(Response $response) {
        $body = (string) $response->getBody();
        $xml = simplexml_load_string(htmlspecialchars_decode($body));
        $operation_query_return_xpath = $xml->xpath('//consultaOperacionesReturn');
        return !empty($operation_query_return_xpath[0]) ? $operation_query_return_xpath[0] : NULL;
    }

}
