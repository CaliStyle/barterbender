<?php

if ($iStatusId && !empty($aVals['status_background_id'])) {
    Phpfox::getService('yncstatusbg.process')->addBackgroundForStatus('user_status', $iStatusId,
        $aVals['status_background_id'], Phpfox::getUserId(), 'user');
} elseif (Phpfox::isModule('ynfeed') && !empty($aVals['feed_id'])) {
    if (!empty($aStatusFeed) && isset($aVals['disabled_status_background'])) {
        Phpfox::getService('yncstatusbg.process')->editUserStatusCheck($aStatusFeed['item_id'], 'user_status',
            $aStatusFeed['user_id'], !$aVals['disabled_status_background']);
    }
}