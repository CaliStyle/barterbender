<?php
if (defined('PHPFOX_IS_FEVENT_VIEW')) {
    $sFeedTable = 'fevent_feed';
} else {
    if ($iFeedId && isset($aPhotoIte['module_id']) && $aPhotoIte['module_id'] == 'fevent') {
        $sFeedTable = 'fevent_feed';
    }
}