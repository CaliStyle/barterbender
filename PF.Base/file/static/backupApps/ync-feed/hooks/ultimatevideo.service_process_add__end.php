<?php

$aInsert = [
    'module' => '',
    'type_id' => 'ultimatevideo_video',
    'table_prefix' => '',
    'item_id' => $iVideoId
];

Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aInsert, $aVals));