<?php
$coreFeedId = db()->select('feed_id')
    ->from(Phpfox::getT('feed'))
    ->where('user_id = ' . (int)$userId . ' AND type_id = "' . ($moduleId . (!empty($section) ? ('_' . $section) : '')) . '" AND item_id = ' . (int)$params['item_id'])
    ->execute('getSlaveField');
if(!empty($coreFeedId)) {
    if($isSave) {
        $params = [
            'user_id' => (int)$userId,
            'feed_id' => $coreFeedId,
            'feed_type' => ($moduleId . (!empty($section) ? ('_' . $section) : '')),
            'callback' => null
        ];
        Phpfox::getService('ynfeed.save')->add($params, true);
    }
    else {
        Phpfox::getService('ynfeed.save')->delete($userId, $coreFeedId, null, true);
    }
}