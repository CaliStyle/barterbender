<?php
if (!empty($aStatusFeed) && isset($aVals['disabled_status_background'])) {
    Phpfox::getService('yncstatusbg.process')->editUserStatusCheck($aStatusFeed['item_id'], 'user_status',
        $aStatusFeed['user_id'], !$aVals['disabled_status_background']);
}