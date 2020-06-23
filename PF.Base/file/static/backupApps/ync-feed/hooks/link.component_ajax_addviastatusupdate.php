<?php
/* Notify tagged users */

$iLinkId = db()->select('item_id')->from(Phpfox::getT((isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : '') . 'feed'))->where('feed_id = ' . $iId)->execute('getField');
if($iLinkId) {
    $aInsert = [
        'module' => (isset($aCallback['module']) ? $aCallback['module'] : 'feed'),
        'type_id' => 'link',
        'table_prefix' => (isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : ''),
        'item_id' => $iLinkId
    ];
//    d($aInsert);die;
    Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aInsert, $aVals));
}