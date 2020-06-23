<?php

$iId = $iContestId;

$bIsNotified = Phpfox::getService('foxfavorite.log')->isNotifiedFollower('contest', $iId);

$aCheckItem = $this->database()->select('user_id, contest_status, is_approved, privacy')->from(Phpfox::getT('contest'))->where('contest_id = '.$iId)->execute('getSlaveRow');

if ($aCheckItem['contest_status'] == 4 && $aCheckItem['privacy'] == 0) // && $aCheckItem['is_approved'] == 1
{
    $aUsers = Phpfox::getService('foxfavorite')->getUserInfoToSendNotification($aCheckItem['user_id']);
    foreach ($aUsers as $iKey => $aUser)
    {
        if (isset($aUser['user_notification']) && ($aUser['user_notification']))
        {

        }
        else
        {
            Phpfox::getService('notification.process')->add('foxfavorite_addcontest', $iId, $aUser['user_id'], $aCheckItem['user_id']);
        }
    }

    Phpfox::getService('foxfavorite.log.process')->setNotifiedFollower('contest', $iId);
}

?>