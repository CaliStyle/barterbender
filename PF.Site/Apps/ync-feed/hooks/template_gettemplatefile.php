<?php
if($sTemplate == 'core.block.upload-form' && $this->_aVars['isEditedFeedPhoto']) {
    $feedId = $this->_aVars['iFeedId'];
    $callback = $this->_aVars['aFeedCallback'];

    $feed = Phpfox::getService('feed')->callback($callback)->getFeed($feedId);
    if(!empty($feed) && $feed['type_id'] == 'photo' && ($itemPhoto = Phpfox::getService('photo')->getForProcess($feed['item_id'], $feed['user_id']))) {
        $count = 1;
        $restPhotos = Phpfox::getService('ynfeed')->getPhotosForEditStatus($feedId, $callback['module']);
        if(!empty($restPhotos)) {
            $count += count($restPhotos);
        }
        $this->_aVars['aUploadCallback']['max_file'] -= $count;
        $this->_aVars['aUploadCallback']['js_events']['removedfile'] = '$Core.Photo.processUploadImageForAdvFeed.dropzoneOnRemovedFileInFeedForEditPhoto';
    }
}