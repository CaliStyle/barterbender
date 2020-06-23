<?php

if (!empty($aVals['feed_id'])) {
    $userId = Phpfox::getUserId();
    Phpfox::getLib('cache')->remove('ynfeed_extra_' . $aVals['feed_id'] . '_' . $userId);
}

// Add notification map
$aFeedCallback = [
    'module' => $aInsert['module'],
    'table_prefix' => $aInsert['table_prefix'],
    'item_id' => $aInsert['item_id']
];

if(isset($aVals['feed_id']) && (int) $aVals['feed_id']) {
    $iFeedId = (int) $aVals['feed_id'];
    $aFeed = db()->select('*')->from(Phpfox::getT($aFeedCallback['table_prefix'] . 'feed'), 'feed')
        ->where("feed_id = " . $iFeedId)
        ->execute('getSlaveRow');
} else{
    $aFeed = db()->select('*')->from(Phpfox::getT($aFeedCallback['table_prefix'] . 'feed'), 'feed')
        ->where("item_id = " . $aInsert['item_id'] . " AND type_id = '" . $aVals['type_id'] ."'" . " AND user_id = " . Phpfox::getUserId())
        ->order("feed_id DESC")
        ->execute('getSlaveRow');
    if($aFeed && isset($aFeed['feed_id'])) {
        $iFeedId = (int) $aFeed['feed_id'];
    } else {
        return;
    }
}
$aOldMap = db()->select('*')
    ->from(Phpfox::getT('ynfeed_notification_map'))
    ->where("item_id = " . $iFeedId . " AND callback = '" . json_encode($aFeedCallback) . "'")
    ->execute("getSlaveRow");

if(isset($aOldMap['map_id'])) {
    $iMapId = $aOldMap['map_id'];
} else {
    $iMapId = db()->insert(Phpfox::getT('ynfeed_notification_map'), array(
        'item_id' => $iFeedId,
        'callback' => json_encode($aFeedCallback)
    ));
}


// Get tags, mentions
$aMaps = db()->select('*')
    ->from(Phpfox::getT('ynfeed_feed_map'), 'fm')
    ->where("fm.item_id = " . $aVals['item_id'] . " AND fm.item_type = '" . $aVals['type_id'] . "'")
    ->execute("getSlaveRows");
$aReducedMaps =
    array_map(function($e) {
        return [
            'item_id' => $e['item_id'],
            'type_id' => $e['type_id'],
            'parent_user_id' => $e['parent_user_id'],
            'parent_user_type' => $e['parent_user_type']
        ];
    }, $aMaps);
foreach ($aMaps as $key=>$aMap) {
    if($aMap['type_id'] == 'mention') {
        if(array_search([
                'item_id' => $aMap['item_id'],
                'type_id' => 'tag',
                'parent_user_id' => $aMap['parent_user_id'],
                'parent_user_type' => $aMap['parent_user_type']
            ], $aReducedMaps) !== false)
            continue;
    }

//    if(isset($aFeed['parent_user_id']) && ($aFeed['parent_user_id'] == $aMap['parent_user_id']))
//        continue;
    $aOldNoti = db()->select('*')
        ->from(Phpfox::getT('notification'))
        ->where("type_id = '" . 'ynfeed_' . $aMap['type_id'] . "' AND item_id = " . $iMapId . " AND user_id = " . $aMap['parent_user_id'] . " AND owner_user_id = " . $aMap['user_id'])
        ->execute('getSlaveRow');
    if(!$aOldNoti && ($aMap['parent_user_id'] != $aFeed['user_id']))
        Phpfox::getService('notification.process')->add('ynfeed_' . $aMap['type_id'], $iMapId, $aMap['parent_user_id'], $aMap['user_id']);
}