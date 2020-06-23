<?php

if ($aInsert['privacy'] == 0 && $aInsert['post_status'] == 1 && $aInsert['is_approved'] == 1)
{
    $aUsers = Phpfox::getService('foxfavorite')->getUserInfoToSendNotification();
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {

        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addjob', $iId, $aUser['user_id']);
        }
    }

    Phpfox::getService('foxfavorite.log.process')->setNotifiedFollower('jobposting', $iId);
}

?>