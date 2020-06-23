<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/14/17
 * Time: 12:18
 */
if(Phpfox::isModule('yncaffiliate') && $iTransactionId)
{
    $aTransaction = Phpfox::getService('fundraising.transaction')->getTransactionById($iTransactionId);
    $aInvoice = unserialize($aTransaction['invoice']);
    if(!$aInvoice['is_guest'])
    {
        $aPurchase = [
            'amount' => $aParam['total_paid'],
            'currency_id' => $aTransaction['currency'],
        ];
        $iUserId = (isset($aInvoice['user_id'])) ? $aInvoice['user_id'] : 0;
        if($aParam['total_paid'] > 0)
            Phpfox::getService('yncaffiliate.commission.process')->handlePayment($iUserId,$aPurchase,'donate_fundraising','fundraising');
    }

}
?>