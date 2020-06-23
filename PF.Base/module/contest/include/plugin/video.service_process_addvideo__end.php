<?php

if (Phpfox::getLib('request')->getInt('yncontestid') > 0) {
    Phpfox::getService('contest.contest')->handlerAfterAddingEntry($sType = 'video', $iItemId = $iId, 3);
}