<?php
;

if(Phpfox::isModule($sType)){
    $aUsers = phpfox::getService('foxfavorite')->getUserInfoToSendNotification($aItem['user_id']);
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {
            
        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_add'.$sType, $aItem['product_id'], $aUser['user_id'], $aItem['user_id']);
        }
    }
}

;
?>