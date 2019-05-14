<?php

namespace RedsysConsultasPHP\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use RedsysConsultasPHP\Model\Transaction;


class Client extends GuzzleClient
{
    const WEBSERVICE_URL_TESTING = 'https://sis-t.redsys.es:25443/apl02/services/SerClsWSConsulta';

    protected $webserviceUrl;
    protected $requestGenerator;

    public function __construct($webservice_url, $ds_merchant_code, array $config = [])
    {
        $this->webserviceUrl = $webservice_url;
        $this->requestGenerator = new RequestGenerator($ds_merchant_code);
        parent::__construct($config);
    }

    protected function defaultHeaders() {
        return [
            'Content-type' => 'text/xml;charset=utf-8',
            'Accept' => ' text/xml',
            'Cache-Control' => ' no-cache',
            'Pragma' => ' no-cache',
            'SOAPAction' => 'consultaOperaciones',
        ];
    }

    public function getTransaction($order_id, $terminal, $merchant_code, $transaction_type = 0) {
        $payload = $this->buildSoapBody($this->requestGenerator->transaction($order_id, $terminal, $merchant_code, $transaction_type));
        $response = $this->doRequest($payload);
        return Transaction::fromXml($response);
    }

    /**
     * @param $payload
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *   Response.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *   Exception if payload data is not okay.
     */
    private function doRequest($payload) {
        $headers = $this->defaultHeaders() + [
            'Content-length' => strlen($payload),
        ];
        try  {
            $response = $this->post($this->webserviceUrl, [RequestOptions::HEADERS => $headers, RequestOptions::BODY => $payload]);
            $response = ResponseParser::parse($response);
            if (count($response->xpath('//Messages/Version/Message/ErrorMsg/Ds_ErrorCode')) == 1) {
                list($error_code) = $response->xpath('//ErrorMsg/Ds_ErrorCode');
                // @TODO: wrap response into an exception class to show every exception code!
                throw new \Exception('Error ' . $error_code);
            }
        }
        catch (RequestException $e) {
            $response = NULL;
        }
        return $response;
    }

    protected function buildSoapBody($input) {
        $soap_request  = "<?xml version=\"1.0\"?>\n";
        $soap_request .= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://webservices.apl02.redsys.es">';
        $soap_request .= '<soapenv:Header/>';
        $soap_request .= '<soapenv:Body>';
        $soap_request .= '<web:consultaOperaciones>';
        $soap_request .= '<cadenaXML>';
        $soap_request .= '<![CDATA['.$input.']]>';
        $soap_request .= '</cadenaXML>';
        $soap_request .= '</web:consultaOperaciones>';
        $soap_request .= '</soapenv:Body>';
        $soap_request .= '</soapenv:Envelope>';

        return $soap_request;
    }

}
