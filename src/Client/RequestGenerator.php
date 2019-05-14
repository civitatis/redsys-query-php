<?php


namespace RedsysConsultasPHP\Client;

use RedsysConsultasPHP\SignatureGenerator;

class RequestGenerator
{
    protected $signatureGenerator;
    protected $dsMerchantCode;

    public function __construct($ds_merchant_code)
    {
        $this->dsMerchantCode = $ds_merchant_code;
        $this->signatureGenerator = new SignatureGenerator();
    }

    public function buildTransaction() {

    }

    public function transaction($ds_order, $terminal, $merchant_code, $transaction_type = 0) {
        $payload = "<Version Ds_Version=\"0.0\"><Message><Transaction><Ds_MerchantCode>$merchant_code</Ds_MerchantCode>"
            . "<Ds_Terminal>$terminal</Ds_Terminal><Ds_Order>$ds_order</Ds_Order>"
            . "<Ds_TransactionType>$transaction_type</Ds_TransactionType></Transaction></Message></Version>";

        return $this->wrapPayload($payload, $ds_order);
    }

    public function wrapPayload($payload, $ds_order) {
        $signature = $this->signatureGenerator->createMerchantSignatureHostToHost($payload, $ds_order, $this->dsMerchantCode);
        return "<Messages>" . $payload . "<Signature>" . $signature . "</Signature><SignatureVersion>HMAC_SHA256_V1</SignatureVersion></Messages>";
    }

}
