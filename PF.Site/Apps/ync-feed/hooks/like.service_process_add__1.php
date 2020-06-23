<?php

$aFeed = db()->select('*')
    ->from(Phpfox::getT($sTablePrefix . 'feed'), 'feed')
    ->where(array(
        'type_id' => $sType,
        'item_id' => $iItemId
    ))
    ->execute('getSlaveRow');
if(empty($aFeed))
    return;

$aFeedCallback = [];
if ($aVals['table_prefix'] != '') {
    $aFeedCallback['module'] = substr($sTablePrefix, 0, strlen($sTablePrefix) - 1);
    $aFeedCallback['table_prefix'] = $sTablePrefix;
    $aFeedCallback['item_id'] = $iItemId;
}
$aOldMap = db()->select('*')
    ->from(Phpfox::getT('ynfeed_notification_map'))
    ->where("item_id = " . $aFeed['feed_id'] . " AND callback = '" . json_encode($aFeedCallback) . "'")
    ->execute("getSlaveRow");

if (isset($aOldMap['map_id'])) {
    $iMapId = $aOldMap['map_id'];
} else {
    // Add notification map
    $iMapId = db()->insert(Phpfox::getT('ynfeed_notification_map'), array(
        'item_id' => $aFeed['feed_id'],
        'callback' => json_encode($aFeedCallback)
    ));
}

// Get tags, mentions
$aMaps = db()->select('*')
    ->from(Phpfox::getT('ynfeed_feed_map'), 'fm')
    ->where("fm.item_id = " . $iItemId . " AND fm.item_type = '" . $sType . "'")
    ->execute("getSlaveRows");
$aReducedMaps =
    array_map(function ($e) {
        return [
            'item_id' => $e['item_id'],
            'type_id' => $e['type_id'],
            'parent_user_id' => $e['parent_user_id'],
            'parent_user_type' => $e['parent_user_type']
        ];
    }, $aMaps);

foreach ($aMaps as $key => $aMap) {
    if ($aMap['type_id'] == 'mention') {
        if (array_search([
                'item_id' => $aMap['item_id'],
                'type_id' => 'tag',
                'parent_user_id' => $aMap['parent_user_id'],
                'parent_user_type' => $aMap['parent_user_type']
            ], $aReducedMaps) !== false
        )
            continue;
    }
    Phpfox::getService('notification.process')->add('ynfeed_like_' . $aMap['type_id'], $iMapId, $aMap['parent_user_id'], Phpfox::getUserId());
}
