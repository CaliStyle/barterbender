<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox_Request::instance()->get('req1') == 'directory' || (!empty($aPhotoIte['module_id']) && $aPhotoIte['module_id'] == 'directory')) {
    $sFeedTable = 'directory_feed';
}

