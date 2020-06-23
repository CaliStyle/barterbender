<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ync-webpush-banner" id="js_ync_webpush_request_banner">
    {$sMessage|clean}
    <div class="ync-webpush-action-group">
        <a class="btn ync-webpush-skip btn-sm" href="#" onclick="$('#js_ync_webpush_request_banner').remove();yncwebpush.skipRequestBanner($(this));return false;">{_p var='skip'}</a><a class="btn ync-webpush-accept btn-sm" href="#" onclick="$('#js_ync_webpush_request_banner').remove();yncwebpush.requestPermission(); return false;">{_p var='accept'}</a>
    </div>
</div>
