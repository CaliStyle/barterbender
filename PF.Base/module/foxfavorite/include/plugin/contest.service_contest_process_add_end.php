<?php

$iId = $iContestId;

$aCheckItem = $this->database()->select('contest_status, is_published, is_approved, privacy')->from(Phpfox::getT('contest'))->where('contest_id = '.$iId)->execute('getSlaveRow');

if ($aCheckItem['contest_status'] == 4 && $aCheckItem['is_published'] == 1 && $aCheckItem['privacy'] == 0) // && $aCheckItem['is_approved'] == 1
{
    $aUsers = Phpfox::getService('foxfavorite')->getUserInfoToSendNotification();
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {

        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addcontest', $iId, $aUser['user_id']);
        }
    }

    Phpfox::getService('foxfavorite.log.process')->setNotifiedFollower('contest', $iId);
}

?>