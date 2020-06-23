<?php

if ($aSql['privacy'] == 0 && $aSql['view_id'] == 0)
{
    $aUsers = phpfox::getService('foxfavorite')->getUserInfoToSendNotification();
    foreach ($aUsers as $iKey => $aUser) {
        if (empty($aUser['user_notification'])) {
            Phpfox::getService('notification.process')->add('foxfavorite_addfevent', $iId, $aUser['user_id']);
        }
    }
}
?>