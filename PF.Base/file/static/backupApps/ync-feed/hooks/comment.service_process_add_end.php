<?php

if ($aVals['is_via_feed'] > 0) {
    $aFeed = db()->select('*')
        ->from(Phpfox::getT($aVals['table_prefix'] . 'feed'), 'feed')
        ->where(array(
            'feed_id' => $aVals['is_via_feed'],
        ))
        ->execute('getSlaveRow');
    if(empty($aFeed))
        return;


    $aFeedCallback = [];
    if($aVals['table_prefix'] != '') {
        $aFeedCallback['module'] = substr($aVals['table_prefix'], 0, strlen($aVals['table_prefix']) - 1);
        $aFeedCallback['table_prefix'] = $aVals['table_prefix'];
        $aFeedCallback['item_id'] = $aVals['item_id'];
    }
    $aOldMap = db()->select('*')
        ->from(Phpfox::getT('ynfeed_notification_map'))
        ->where("item_id = " . $aVals['is_via_feed'] . " AND callback = '" . json_encode($aFeedCallback) . "'")
        ->execute("getSlaveRow");

    if (isset($aOldMap['map_id'])) {
        $iMapId = $aOldMap['map_id'];
    } else {
        // Add notification map
        $iMapId = db()->insert(Phpfox::getT('ynfeed_notification_map'), array(
            'item_id' => $aVals['is_via_feed'],
            'callback' => json_encode($aFeedCallback)
        ));
    }

// Get tags, mentions
    $aMaps = db()->select('*')
        ->from(Phpfox::getT('ynfeed_feed_map'), 'fm')
        ->where("fm.item_id = " . $aVals['item_id'] . " AND fm.item_type = '" . $aFeed['type_id'] . "'")
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
        Phpfox::getService('notification.process')->add('ynfeed_comment_' . $aMap['type_id'], $iMapId, $aMap['parent_user_id'], Phpfox::getUserId());
    }
}