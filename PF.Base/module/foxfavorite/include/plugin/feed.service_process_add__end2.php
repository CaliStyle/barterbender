<?php

if ($aInsert['type_id'] == 'link' && $aInsert['privacy'] == 0)
{
    $aUsers = Phpfox::getService('foxfavorite')->getUserInfoToSendNotification();
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {
            
        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addlink', $aInsert['item_id'], $aUser['user_id']);
        }
    }
}
?>