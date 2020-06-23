<?php
if ($this->_bIsNewLoop)
    return;
$aLastMap = db()->select('*')
    ->from(Phpfox::getT('ynfeed_notification_map'))
    ->order('map_id DESC')
    ->limit(1)
    ->execute("getSlaveRow");
if ($aLastMap && $aLastMap['item_id'] == $this->_iLastId) {
    return;
}
$sCallback = json_encode($this->_aCallback);
if (!$this->_aCallback) {
    if (isset($_SESSION['ynfeed_process_add_callback'])) {
        $sCallback = $_SESSION['ynfeed_process_add_callback'];
        unset($_SESSION['ynfeed_process_add_callback']);
    }
}
$aOldMap = db()->select('*')
    ->from(Phpfox::getT('ynfeed_notification_map'))
    ->where("item_id = " . $this->_iLastId . " AND callback = '" . $sCallback . "'")
    ->execute("getSlaveRow");

if (isset($aOldMap['map_id'])) {
    $iMapId = $aOldMap['map_id'];
} else {
    // Add notification map
    $iMapId = db()->insert(Phpfox::getT('ynfeed_notification_map'), array(
        'item_id' => $this->_iLastId, //feed id
        'callback' => $sCallback
    ));
}

// Get tags, mentions
$aMaps = db()->select('*')
    ->from(Phpfox::getT('ynfeed_feed_map'), 'fm')
    ->where("fm.item_id = " . $iItemId . " AND fm.item_type = '" . $sType . "'")// item id in feed
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
            ], $aReducedMaps) !== false)
            continue;
    }
    /* get car owner instead of using car id to notify */
    if ($aMap['parent_user_type'] == 'car') {
        $aCar = Phpfox::getService('ynclistingcar')->getQuickBusinessById($aMap['parent_user_id']);
        if (empty($aCar)) {
            continue;
        }
        $iOwnerUserId = $aCar['user_id'];
    } else {
        $iOwnerUserId = $aMap['parent_user_id'];
    }
    $aOldNoti = db()->select('*')
        ->from(Phpfox::getT('notification'))
        ->where("type_id = '" . 'ynfeed_' . $aMap['type_id'] . "' AND item_id = " . $iMapId . " AND user_id = " . $iOwnerUserId . " AND owner_user_id = " . $aMap['user_id'])
        ->execute('getSlaveRow');
    if (!$aOldNoti)
        Phpfox::getService('notification.process')->add('ynfeed_' . $aMap['type_id'], $iMapId, $iOwnerUserId, $aMap['user_id'], true);
}

