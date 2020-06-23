<?php
if(!empty($aVals['feed_id']) && Phpfox::getUserParam('photo.photo_must_be_approved') && !empty($aImages)) {
    storage()->set('ynfeed_pending_photo_' . $aImages['photo_id'], (int)$aVals['feed_id']);
}
