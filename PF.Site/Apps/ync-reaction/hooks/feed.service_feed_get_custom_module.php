<?php
if ($aReturn) {
    //Get last feed item
    $iLast = count($aFeeds) - 1;
    if ($iLast >= 0) {
        Phpfox::getService('yncreaction')->getReactionsPhrase($aFeeds[$iLast]);
    }
}