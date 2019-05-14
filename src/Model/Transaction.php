<?php

namespace RedsysConsultasPHP\Model;

class Transaction extends BaseModel
{

    public static function fromXml(\SimpleXMLElement $xml)
    {
        $transaction_data = $xml->xpath('//Messages/Version/Message/Response');

        $transaction = new static();

        if (empty($transaction_data)) {
            // @TODO: custom exception!
            throw new \Exception('There is no transaction data in response');
        }

        $transaction_xml = $transaction_data[0];

        foreach (RedsysFields::getList() as $field) {
            $field_setter_method = 'set' . str_replace('_', '', $field);
            if (method_exists($transaction, $field_setter_method)) {
                $transaction->{$field} = isset($transaction_xml->{$field}) ? (string) $transaction_xml->{$field} : NULL;
            }
        }

        return $transaction;
    }

}
