<?php

$aUsers = phpfox::getService('foxfavorite')->getUserInfoToSendNotification();
foreach ($aUsers as $iKey => $aUser)
{
    if (isset($aUser['user_notification']) && ($aUser['user_notification']))
    {
        
    }
    else
    {
        Phpfox::getService('notification.process')->add('foxfavorite_updateStatus', $iId, $aUser['user_id']);
    }
}
?>