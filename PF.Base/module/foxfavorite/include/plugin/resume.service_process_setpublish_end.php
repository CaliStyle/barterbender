<?php

$iId = $aResume['resume_id'];

$bIsNotified = Phpfox::getService('foxfavorite.log')->isNotifiedFollower('resume', $iId);

if ($aResume['privacy'] == 0 && $aResume['status'] == 'approved' && !$bIsNotified)
{
    $aUsers = Phpfox::getService('foxfavorite')->getUserInfoToSendNotification();
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {

        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addresume', $iId, $aUser['user_id']);
        }
    }

    Phpfox::getService('foxfavorite.log.process')->setNotifiedFollower('resume', $iId);
}

?>