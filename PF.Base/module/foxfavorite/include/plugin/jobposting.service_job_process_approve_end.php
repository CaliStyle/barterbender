<?php

$iId = $aJob['job_id'];

$bIsNotified = Phpfox::getService('foxfavorite.log')->isNotifiedFollower('jobposting', $iId);

if ($aJob['privacy'] == 0 && $aJob['post_status'] == 1 && !$bIsNotified)
{
    $aUsers = Phpfox::getService('foxfavorite')->getUserInfoToSendNotification($aJob['user_id']);
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {

        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addjob', $iId, $aUser['user_id'], $aJob['user_id']);
        }
    }

    Phpfox::getService('foxfavorite.log.process')->setNotifiedFollower('jobposting', $iId);
}

?>