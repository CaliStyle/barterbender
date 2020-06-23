<?php

if ($aUpdate['is_approved'])
{
    $aUsers = phpfox::getService('foxfavorite')->getUserInfoToSendNotification($aCoupon['user_id']);
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {
            
        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addcoupon', $aCoupon['coupon_id'], $aUser['user_id'], $aCoupon['user_id']);
        }
    }
}
