<?php
if($aParams['status'] == "completed" && !empty($aParams['total_paid']))
{
    $affiliatePurchase = [
        'amount' => $aParams['total_paid'],
        'currency_id' => $aPurchase['currency_id'],
    ];
    Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aPurchase['user_id'], $affiliatePurchase, 'purchase_activity_points', 'user');
}