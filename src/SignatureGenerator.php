<?php

namespace RedsysConsultasPHP;

/**
 * Creates a signature used in requests to the webservice.
 */
class SignatureGenerator
{

  /**
   * A specific key is generated per operation.
   *
   * To obtain the
   * derived key to be used in an operation you must perform a
   * 3DES encryption between the trade key, which must be
   * previously decoded in BASE 64, and the value of the number of
   * order of the operation (Ds_Order).
   *
   * @param int $ds_order
   *   Purchase order number of the operation.
   * @param string $ds_merchant_code
   *   Trade key.
   *
   * @return bool|string
   *   Specific key for the operation.
   */
	public function encrypt($ds_order, $ds_merchant_code)
    {
        $key = base64_decode($ds_merchant_code);
		$l = ceil(strlen($ds_order) / 8) * 8;
		return substr(openssl_encrypt($ds_order . str_repeat("\0", $l - strlen($ds_order)), 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, "\0\0\0\0\0\0\0\0"), 0, $l);
	}

    /**
    * Calculate HMAC SHA256 of <Version> element.
    *
    * The result obtained is coded in BASE 64, and the result of the
    * encoding will be the value of the element <Ds_Signature>.
    *
    * @param string $version
    *   Version element from xml. Example: <Version Ds_Version="0.0">...</Version>.
    * @param string $key
    *   Specific key to use in operation.
    *
    * @return string
    *   Encoding result. It will be used to compose the signature.
    *
    * @see SignatureGenerator::encrypt()
    *
    */
	public function hash($version, $key)
    {
		$res = hash_hmac('sha256', $version, $key, true);
		return $res;
	}

    /**
    * Signature Calculation.
    *
    * @param string $version
     *   Version element from xml. Example: <Version Ds_Version="0.0">...</Version>.
    * @param string $ds_order
    *   Order number
    * @param $ds_merchant_code
    *   Trade key.
    *
    * @return string
    *   Encoding result. It will be the value for element <Ds_Signature>.
    */
	public function createMerchantSignatureHostToHost($version, $ds_order, $ds_merchant_code)
    {
		$key = $this->encrypt($ds_order, $ds_merchant_code);

		$res = $this->hash($version, $key);

		return base64_encode($res);
	}

}
