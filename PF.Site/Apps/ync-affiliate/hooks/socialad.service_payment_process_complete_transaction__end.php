<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/14/17
 * Time: 11:32
 */

if(Phpfox::isModule('yncaffiliate') && $aTransaction)
{
    if($method != 'pay_later'){
        $aPurchase = [
            'amount' => $aTransaction['transaction_amount'],
            'currency_id' => $aTransaction['transaction_currency']
        ];
        if($aTransaction['transaction_amount'] > 0)
            Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['transaction_user_id'],$aPurchase,'buy_social_ad_package','socialad');
    }
}
?>