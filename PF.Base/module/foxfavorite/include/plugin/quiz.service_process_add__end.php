<?php

if ($aVals['privacy'] == 0 && $aVals['view_id'] == 0)
{
    $aUsers = phpfox::getService('foxfavorite')->getUserInfoToSendNotification();
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {
            
        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addquiz', $iQuizId, $aUser['user_id']);
        }
    }
}
?>