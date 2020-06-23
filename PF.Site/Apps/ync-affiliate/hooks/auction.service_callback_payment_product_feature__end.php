<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/2/17
 * Time: 16:59
 */

if(Phpfox::isModule('yncaffiliate') && $aInvoice)
{
    $aPurchase = [
        'amount' => $featureFee,
        'currency_id' => $aInvoice['currency_id'],
    ];
    if($featureFee > 0)
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aInvoice['user_id'],$aPurchase,'feature_auction','auction');

}
?>