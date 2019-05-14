<?php

namespace RedsysConsultasPHP\Client;

use GuzzleHttp\Psr7\Response;

/**
 * Parse redsys response to get the xml only with the returned data.
 *
 * @package RedsysConsultasPHP\Client
 */
class ResponseParser
{

    /**
     * Parse response from redsys.
     *
     * @param Response $response
     *   Response.
     *
     * @return \SimpleXMLElement|null
     *   Response parsed, null if there aren't any tag in 'consultaOperacionesReturn'.
     */
    public static function parse(Response $response)
    {
        $body = (string) $response->getBody();
        $xml = simplexml_load_string(htmlspecialchars_decode($body));
        $operation_query_return_xpath = $xml->xpath('//consultaOperacionesReturn');
        return !empty($operation_query_return_xpath[0]) ? $operation_query_return_xpath[0] : null;
    }
}
