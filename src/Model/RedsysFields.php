<?php


namespace RedsysConsultasPHP\Model;


final class RedsysFields
{

    public static function getList() {
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
