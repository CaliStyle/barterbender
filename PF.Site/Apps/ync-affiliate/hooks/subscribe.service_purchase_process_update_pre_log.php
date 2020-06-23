<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/27/17
 * Time: 14:24
 */
if(Phpfox::isModule('yncaffiliate') && $iPurchaseId)
{
    $aPurchase = Phpfox::getService('subscribe.purchase')->getPurchase($iPurchaseId);
    if($sStatus == 'completed')
    {
        $iNewUserId = $aPurchase['user_id'];
        $aPurchase['amount'] = $aPurchase['price'];
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($iNewUserId,$aPurchase,'subscription','core');
    }
}
?>