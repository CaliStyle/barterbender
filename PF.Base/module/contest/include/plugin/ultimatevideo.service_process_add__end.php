<?php

$iId = $iVideoId;
if (Phpfox::getLib('request')->getInt('yncontestid') > 0) {
    Phpfox::getService('contest.contest')->handlerAfterAddingEntry($sType = 'video', $iItemId = $iId, 1);
}

