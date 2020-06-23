<?php

$bIsNotified = Phpfox::getService('foxfavorite.log')->isNotifiedFollower('resume', $iId);

if ($aResume['privacy'] == 0 && $aResume['is_published'] == 1 && !$bIsNotified)
{
    $aUsers = Phpfox::getService('foxfavorite')->getUserInfoToSendNotification($aResume['user_id']);
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {

        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addresume', $iId, $aUser['user_id'], $aResume['user_id']);
        }
    }

    Phpfox::getService('foxfavorite.log.process')->setNotifiedFollower('resume', $iId);
}

?>