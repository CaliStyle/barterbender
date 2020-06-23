<?php

$bIsNotified = Phpfox::getService('foxfavorite.log')->isNotifiedFollower('petition', $iId);

if ($aPetition['privacy'] == 0 && !$bIsNotified)
{
    $aUsers = Phpfox::getService('foxfavorite')->getUserInfoToSendNotification($aPetition['user_id']);
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {

        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addpetition', $iId, $aUser['user_id'], $aPetition['user_id']);
        }
    }

    Phpfox::getService('foxfavorite.log.process')->setNotifiedFollower('petition', $iId);
}

?>