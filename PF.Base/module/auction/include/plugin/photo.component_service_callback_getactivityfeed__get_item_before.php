<?php
defined('PHPFOX') or exit('NO DICE!');
if (Phpfox_Request::instance()->get('req1') == 'auction' || (!empty($aPhotoIte['module_id']) && $aPhotoIte['module_id'] == 'ecommerce')) {
    $sFeedTable = 'ecommerce_feed';
}
