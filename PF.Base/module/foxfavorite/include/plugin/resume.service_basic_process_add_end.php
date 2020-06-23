<?php

$aResume = $this->database()->select('privacy, status, is_published')->from($this->_sTable)->where('resume_id = '.$iId)->execute('getSlaveRow');

if ($aResume['privacy'] == 0 && $aResume['status'] == 'approved' && $aResume['is_published'] == 1)
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