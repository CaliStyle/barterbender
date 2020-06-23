<?php
;

if(Phpfox::isModule('directory')){
    $aUsers = phpfox::getService('foxfavorite')->getUserInfoToSendNotification($aItem['user_id']);
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {
            
        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addbusiness', $aItem['business_id'], $aUser['user_id'], $aItem['user_id']);
        }
    }
}

;
?>