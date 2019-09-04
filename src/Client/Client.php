<?php

namespace RedsysConsultasPHP\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use RedsysConsultasPHP\Model\Transaction;

/**
 * Client to make queries to redsys query webservice.
 *
 * @package RedsysConsultasPHP\Client
 */
class Client extends GuzzleClient
{
    /**
     * Url of redsys test environment.
     */
    const WEBSERVICE_URL_TESTING = 'https://sis-t.redsys.es:25443/apl02/services/SerClsWSConsulta';

    /**
     * Webservice URL.
     *
     * @var string
     */
    protected $webserviceUrl;

    /**
     * Request generator.
     *
     * @var RequestGenerator
     */
    protected $requestGenerator;

    /**
     * Client constructor.
     *
     * @param string $webservice_url
     *   Webservice url.
     * @param string $trade_key
     *   Trade key.
     * @param array $config
     *   Configuration.
     */
    public function __construct($webservice_url, $trade_key, array $config = [])
    {
        $this->webserviceUrl = $webservice_url;
        $this->requestGenerator = new RequestGenerator($trade_key);
        parent::__construct($config);
    }

    /**
     * This headers will be sent on every request.
     *
     * @return array
     *   List of headers, beng the most important 'SOAPAction'.
     */
    protected function defaultHeaders()
    {
        return [
            'Content-type' => 'text/xml;charset=utf-8',
            'Accept' => ' text/xml',
            'Cache-Control' => ' no-cache',
            'Pragma' => ' no-cache',
            'SOAPAction' => 'consultaOperaciones',
        ];
    }

    /**
     * Get a transaction.
     *
     * @param int $order_id
     *   Order id.
     * @param $terminal
     *   Terminal.
     * @param $merchant_code
     *   Merchant code.
     * @param int $transaction_type
     *   Transaction type.
     *
     * @return Transaction
     *   Transaction.
     */
    public function getTransaction($order_id, $terminal, $merchant_code, $transaction_type = 0)
    {
        $payload = $this->buildPayload($this->requestGenerator->transaction($order_id, $terminal, $merchant_code, $transaction_type));
        $response = $this->doRequest($payload);
        return !empty($response) ? Transaction::fromXml($response) : NULL;
    }


    /**
     * Do a MonitorMasiva request
     * @param $fecha_inicio string Fecha de inicio
     * @param $fecha_fin string Fecha de fin
     * @param $terminal string Número de terminal
     * @param $merchant_code string Código de comercio
     * @return Transaction|null
     * @throws \Exception
     */
    public function getMonitorMasiva($fecha_inicio, $fecha_fin, $terminal, $merchant_code)
    {
        $payload = $this->buildPayload($this->requestGenerator->monitorMasiva($fecha_inicio, $fecha_fin, "7086YlQL", $terminal, $merchant_code));
        $response = $this->doRequest($payload);
        return !empty($response) ? Transaction::fromXml($response) : NULL;
    }

    /**
     * Do request to webservice.
     *
     * @param $payload
     *   Payload.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *   Response.
     *
     * @throws \Exception
     */
    private function doRequest($payload)
    {
        $headers = $this->defaultHeaders() + [
            'Content-length' => strlen($payload),
        ];
        try {
            $response = $this->post($this->webserviceUrl, [RequestOptions::HEADERS => $headers, RequestOptions::BODY => $payload]);
            $response = ResponseParser::parse($response);
            if (count($response->xpath('//Messages/Version/Message/ErrorMsg/Ds_ErrorCode')) == 1) {
                list($error_code) = $response->xpath('//ErrorMsg/Ds_ErrorCode');
                // @TODO: wrap response into an exception class to show every exception code!
                throw new \Exception('Error ' . $error_code);
            }
        } catch (RequestException $e) {
            $response = null;
        }
        return $response;
    }

    /**
     * Transform the xml payload we will send to webservice into a soap request.
     *
     * @param string $payload
     *   Soap request payload.
     *
     * @return string
     *   Soap full payload.
     */
    protected function buildPayload($payload)
    {
        $soap_request  = "<?xml version=\"1.0\"?>\n";
        $soap_request .= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://webservices.apl02.redsys.es">';
        $soap_request .= '<soapenv:Header/>';
        $soap_request .= '<soapenv:Body>';
        $soap_request .= '<web:consultaOperaciones>';
        $soap_request .= '<cadenaXML>';
        $soap_request .= '<![CDATA['.$payload.']]>';
        $soap_request .= '</cadenaXML>';
        $soap_request .= '</web:consultaOperaciones>';
        $soap_request .= '</soapenv:Body>';
        $soap_request .= '</soapenv:Envelope>';

        return $soap_request;
    }
}
