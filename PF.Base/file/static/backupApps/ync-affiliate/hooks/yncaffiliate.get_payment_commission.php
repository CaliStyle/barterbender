<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/14/17
 * Time: 15:07
 */
/**
 * Params :
 * - $aPurchaseDetail : optional, array, list payment with key: sModule, sRuleName, sCurrency, fTotalAmount, iUserId
 * - $sModule: string, module_id
 * - $sRuleName: string, rule_name in table yncaffiliate_rules
 * - $sCurrency: string, payment currency ex: USD
 * - $fTotalAmount: float, total amount user was paid
 * - $iUserId: int, user made payment
 **/
if(Phpfox::isModule('yncaffiliate'))
{

    if(isset($aPurchaseDetail) && is_array($aPurchaseDetail))
    {
        foreach ($aPurchaseDetail as $aDetail)
        {
            if($aDetail['fTotalAmount'])
                Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aDetail['iUserId'],['amount' => $aDetail['fTotalAmount'],'currency_id' => $aDetail['sCurrency']],$aDetail['sRuleName'],$aDetail['sModule']);
        }
    }
    elseif(isset($sModule) && isset($sRuleName) && $fTotalAmount > 0 && $iUserId)
    {
        $sCurrency = (isset($sCurrency)) ? $sCurrency : 'USD';
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($iUserId,['amount' => $fTotalAmount,'currency_id' => $sCurrency],$sRuleName,$sModule);
    }
}
?>