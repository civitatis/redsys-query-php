<?php

namespace RedsysConsultasPHP\Client;

use RedsysConsultasPHP\SignatureGenerator;

/**
 * Generate requests body for each webservice operation.
 *
 * @package RedsysConsultasPHP\Client
 */
class RequestGenerator
{
    /**
     * Generate signatures.
     *
     * @var SignatureGenerator
     */
    protected $signatureGenerator;

    /**
     * Trade key.
     *
     * @var string
     */
    protected $tradeKey;

    public function __construct($trade_key)
    {
        $this->tradeKey = $trade_key;
        $this->signatureGenerator = new SignatureGenerator();
    }

    /**
     * @param int $ds_order
     *   Order.
     * @param $terminal
     *   Terminal.
     * @param $merchant_code
     *   Merchant code.
     * @param int $transaction_type
     *   Transaction type.
     *
     * @return string
     *   transaction payload.
     */
    public function transaction($ds_order, $terminal, $merchant_code, $transaction_type = 0)
    {
        $payload = "<Version Ds_Version=\"0.0\"><Message><Transaction><Ds_MerchantCode>$merchant_code</Ds_MerchantCode>"
            . "<Ds_Terminal>$terminal</Ds_Terminal><Ds_Order>$ds_order</Ds_Order>"
            . "<Ds_TransactionType>$transaction_type</Ds_TransactionType></Transaction></Message></Version>";

        return $this->wrapPayload($payload, $ds_order);
    }

    /**
     * @param int $ds_order
     *   Order.
     * @param $terminal
     *   Terminal.
     * @param $merchant_code
     *   Merchant code.
     * @param int $transaction_type
     *   Transaction type.
     *
     * @return string
     *   transaction payload.
     */
    public function monitorMasiva($fecha_inicio, $fecha_fin, $ds_order, $terminal, $merchant_code)
    {
        $payload = "<Version Ds_Version=\"0.0\"><Message><MonitorMasiva><Ds_Order>$ds_order</Ds_Order><Ds_MerchantCode>$merchant_code</Ds_MerchantCode>"
            . "<Ds_Terminal>$terminal</Ds_Terminal><Ds_Fecha_inicio>$fecha_inicio</Ds_Fecha_inicio>"
            . "<Ds_Fecha_fin>$fecha_fin</Ds_Fecha_fin></MonitorMasiva></Message></Version>";

        return $this->wrapPayload($payload, $ds_order);
    }

    /**
     * Wrap any request payload into a common payload.
     *
     * This payload is formed by the request payload and the signature.
     *
     * @param $payload
     *   Xml payload.
     * @param $ds_order
     *   Order.
     *
     * @return string
     *   Request payload.
     */
    public function wrapPayload($payload, $ds_order)
    {
        $signature = $this->signatureGenerator->createMerchantSignatureHostToHost($payload, $ds_order, $this->tradeKey);
        return "<Messages>" . $payload . "<Signature>" . $signature . "</Signature><SignatureVersion>HMAC_SHA256_V1</SignatureVersion></Messages>";
    }
}
