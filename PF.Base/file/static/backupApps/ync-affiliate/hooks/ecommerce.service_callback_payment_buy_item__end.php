<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/2/17
 * Time: 17:16
 */
if(Phpfox::isModule('yncaffiliate') && $aParams)
{
    $aPurchase = [
        'amount' => $aParams['total_paid'],
        'currency_id' => $sCurrencyId,
    ];
    $sType = '';
    $sModule = '';
    if($sOrderModule)
    {
        switch ($sOrderModule)
        {
            case 'auction':
                $sType = 'buy_auction';
                $sModule = 'auction';
                break;
            case 'ynsocialstore':
                $sType = 'buy_product';
                $sModule = 'ynsocialstore';
        }
    }
    else{
        return;
    }
    if($aParams['total_paid'] > 0)
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aUserBuyer['user_id'],$aPurchase,$sType,$sModule);

}
?>