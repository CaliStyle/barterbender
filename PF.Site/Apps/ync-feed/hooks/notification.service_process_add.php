<?php
if(strpos($sType, '_like') != false) {
    $sItemType = str_replace('_like', '', $sType);
} else {
    $sItemType = str_replace('comment_', '', $sType);
}
if(substr($sItemType, -5) == '_feed') {
    if((strpos($sType, 'comment_') != false) || (strpos($sType, '_like') != false)) {
        $sItemType = str_replace('_feed', '', $sType);
    }
    elseif(db()->tableExists(Phpfox::getT($sItemType))) {
        $aActualItem = db()->select('*')->from(Phpfox::getT($sItemType))->where('feed_id = ' . $iItemId)->execute('getSlaveRow');
        if($aActualItem) {
            $iItemId = $aActualItem['item_id'];
            $sItemType = $aActualItem['type_id'];
        }
    }

}

$aTurnoffs = db()->select('*')->from(Phpfox::getT('ynfeed_turnoff_notification'))
    ->where("item_id = " . $iItemId . " AND user_id = " . $iOwnerUserId . " AND type_id = '" . $sItemType . "'")
    ->execute('getSlaveRows');
if(count($aTurnoffs))
    $bDoNotInsert = true;

if(in_array($sType, array('ynfeed_comment_mention', 'ynfeed_comment_tag', 'ynfeed_like_mention', 'ynfeed_like_tag'))) {
    // get notification map
    $aNotificationMap = db()->select('*')->from(Phpfox::getT('ynfeed_notification_map'))->where('map_id = ' . $iItemId)->execute('getSlaveRow');
    if($aNotificationMap && isset($aNotificationMap['item_id'])) {
        // item_id is feed id
        $aCallback = json_decode($aNotificationMap['callback'], true);
        $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->get(null, $aNotificationMap['item_id']);
        if(isset($aFeed[0])) {
            $aFeed = $aFeed[0];
            // check if it has been turned off for notifications
            $aTurnoffs = db()->select('*')->from(Phpfox::getT('ynfeed_turnoff_notification'))
                ->where("item_id = " . $aFeed['item_id'] . " AND user_id = " . $iOwnerUserId . " AND type_id = '" . $aFeed['type_id'] . "'")
                ->execute('getSlaveRows');
            if(count($aTurnoffs))
                $bDoNotInsert = true;
        }
    }
}

if($sType == 'ynfeed_tag' || $sType == 'ynfeed_mention') {
    if(db()->select('*')->from(Phpfox::getT('user_notification'))->where([
        'user_id' => $iOwnerUserId,
        'user_notification' => 'ynfeed.tagged_in_post'
    ])->execute('getSlaveRow'))
        $bDoNotInsert = true;
}

if(in_array($sType, ['feed_comment_profile', 'photo_feed_profile', 'v_newItem_wall'])) {
    if(db()->select('*')->from(Phpfox::getT('user_notification'))->where([
        'user_id' => $iOwnerUserId,
        'user_notification' => 'ynfeed.post_on_wall'
    ])->execute('getSlaveRow'))
        $bDoNotInsert = true;
}