<?php
if(!empty($aVals['parent_module_id']) && !empty($aVals['parent_feed_id'])) {
    if($aVals['parent_module_id'] == 'ynsocialstore_store') {
        $shareCount = Phpfox::getService('feed')->getShareCount($aVals['parent_module_id'], $aVals['parent_feed_id']);
        db()->update(Phpfox::getT('ynstore_store'), ['total_share' => $shareCount],'store_id = '. (int)$aVals['parent_feed_id']);
    }
}