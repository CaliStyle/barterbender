<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/14/17
 * Time: 09:02
 */
if(Phpfox::isModule('yncaffiliate') && $iTransactionId)
{
    $aTransaction = Phpfox::getService('coupon.transaction')->getTransactionById($iTransactionId);
    $sCurrency = $aTransaction['currency'];
    if($aTransaction['payment_type'] == 2)
    {
        $iFeatureFee = Phpfox::getService('yncaffiliate.helper')->getUserParam('coupon.how_much_user_feature_coupon',$aTransaction['user_id']);
        if($iFeatureFee)
            Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],['amount' => $iFeatureFee,'currency_id' => $sCurrency],'feature_coupon','coupon');

    }
}
?>