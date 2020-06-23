<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/14/17
 * Time: 09:02
 */
if(Phpfox::isModule('yncaffiliate') && $aTransaction)
{
    $sCurrency = $aTransaction['currency'];
    if($aInvoice['is_featured'])
    {
        $iFeatureFee = Phpfox::getService('yncaffiliate.helper')->getUserParam('coupon.how_much_user_feature_coupon',$aTransaction['user_id']);
        if($iFeatureFee)
            Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],['amount' => $iFeatureFee,'currency_id' => $sCurrency],'feature_coupon','coupon');

    }
    $iPublishFee = Phpfox::getService('yncaffiliate.helper')->getUserParam('coupon.how_much_user_publish_coupon',$aTransaction['user_id']);
    if($iPublishFee)
        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],['amount' => $iPublishFee,'currency_id' => $sCurrency],'publish_coupon','coupon');
}
?>