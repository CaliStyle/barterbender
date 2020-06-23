<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox_Request::instance()->get('req1') == 'ynsocialstore' || (!empty($aPhotoIte['module_id']) && $aPhotoIte['module_id'] == 'ynsocialstore')) {
    $aReturn['feed_info'] = (count($aListPhotos) ? _p('feed.shared_a_few_photos') : _p('feed.shared_a_photo'));
}
?>