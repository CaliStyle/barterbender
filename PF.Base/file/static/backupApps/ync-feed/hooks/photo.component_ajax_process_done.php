<?php

$aInsert = [
    'module' => '',
    'type_id' => 'photo',
    'table_prefix' => '',
    'item_id' => $aImages[$iKey]['photo_id']
];

/*Retrieve extra info from feed_values*/
if(isset($aVals['feed_values']) && !empty($aVals['feed_values'])) {
    $aModifiedVals = json_decode(json_encode($aVals['feed_values']), true);
} else{
    $aModifiedVals = $aVals;
}
Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aInsert, $aModifiedVals));
