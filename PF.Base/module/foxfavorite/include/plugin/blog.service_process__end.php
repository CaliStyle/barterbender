<?php

if ($aVals['privacy'] == 0 && $aVals['post_status'] == 1 && $aInsert['is_approved'] == 1)
{
    $aUsers = phpfox::getService('foxfavorite')->getUserInfoToSendNotification();
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {
            
        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addblog', $iId, $aUser['user_id']);
        }
    }
}
?>