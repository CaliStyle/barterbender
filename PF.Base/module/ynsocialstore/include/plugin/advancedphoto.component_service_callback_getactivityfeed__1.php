<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/24/16
 * Time: 10:36 AM
 */
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox_Request::instance()->get('req1') == 'ynsocialstore') {
    $aReturn['feed_info'] = (count($aListPhotos) ? _p('feed.shared_a_few_photos') : _p('feed.shared_a_photo'));
}
?>