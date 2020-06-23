<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/14/17
 * Time: 12:06
 */
if(Phpfox::isModule('yncaffiliate') && $aInvoice)
{
    if($iUserId)
    {
        $aPurchase = [
            'amount' => $fQuantity,
            'currency_id' => $sCurrency,
        ];
        if($fQuantity > 0)
            Phpfox::getService('yncaffiliate.commission.process')->handlePayment($iUserId,$aPurchase,'donate_donation','donation');
    }

}
?>