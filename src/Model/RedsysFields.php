<?php

namespace RedsysConsultasPHP\Model;

/**
 * List of redsys fields.
 *
 * This is used to make a field mapping when creating a model from a xml response.
 *
 * @package RedsysConsultasPHP\Model
 */
final class RedsysFields
{

    /**
     * Redsys field names.
     *
     * @return array
     *   [Ds_terminal, Ds_date...]
     */
    public static function getList()
    {
        return [
            'Ds_MerchantCode',
            'Ds_Terminal',
            'Ds_Order',
            'Ds_TransactionType',
            'Ds_Date',
            'Ds_Hour',
            'Ds_Amount',
            'Ds_Currency',
            'Ds_SecurePayment',
            'Ds_State',
            'Ds_Response',
        ];
    }
}
