<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/2/17
 * Time: 17:38
 */
if(Phpfox::isModule('yncaffiliate') && $aInvoice && $aPackage)
{
    $aPurchase = [
        'amount' => $aPackage['feature_store_fee']*$aInvoice['invoice_data']['feature_days'],
        'currency_id' => $aInvoice['currency_id'],
    ];
    if($aPackage['feature_store_fee'] > 0)
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aInvoice['user_id'],$aPurchase,'feature_store','ynsocialstore');

}
?>