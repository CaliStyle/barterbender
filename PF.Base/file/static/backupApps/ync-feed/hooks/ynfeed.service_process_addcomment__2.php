<?php

$aInsert = [
    'module' => 'feed',
    'type_id' => 'feed_comment',
    'table_prefix' => '',
    'item_id' => $iStatusId
];


Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aInsert, $aVals));
