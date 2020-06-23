<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/2/17
 * Time: 17:37
 */
if(Phpfox::isModule('yncaffiliate') && $aInvoice)
{
    $aGlobalSetting =  Phpfox::getService('directory')->getGlobalSetting();
    $aPurchase = [
        'amount' => $aGlobalSetting[0]['default_feature_fee']*$aInvoice['invoice_data']['feature_days'],
        'currency_id' => $aInvoice['currency_id'],
    ];
    if($aGlobalSetting[0]['default_feature_fee'])
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aInvoice['user_id'],$aPurchase,'feature_business','directory');

}
?>