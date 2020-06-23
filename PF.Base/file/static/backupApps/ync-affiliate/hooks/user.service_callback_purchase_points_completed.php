<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/1/17
 * Time: 15:36
 */
if(Phpfox::isModule('yncaffiliate') && isset($aRow))
{
    if(count($aRow) && $aRow['price'])
    {
        $aPurchase = [
            'amount' => $aRow['price'],
            'currency_id' => $aRow['currency_id'],
        ];
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aRow['user_id'],$aPurchase,'purchase_activity_points','user');
    }
    return true;
}