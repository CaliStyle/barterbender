<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox_Request::instance()->get('req1') == 'ynsocialstore' || (!empty($aPhotoIte['module_id']) && $aPhotoIte['module_id'] == 'ynsocialstore')) {
    $sFeedTable = 'ynstore_feed';
}
?>