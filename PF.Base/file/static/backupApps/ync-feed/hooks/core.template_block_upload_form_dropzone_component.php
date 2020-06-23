<?php
if($this->_aVars['isEditedFeedPhoto'] && !empty($this->_aVars['editedFeedPhotos'])) {
    foreach($this->_aVars['editedFeedPhotos'] as $key => $editedFeedPhoto) {
        echo '<div class="dz-preview dz-image-preview" data-order="'. $key .'">
                                <input type="hidden" value="'. $editedFeedPhoto['photo_id'] .'" name="val[edited_photos][]" class="js_edited_feed_photo">
                                <div class="dz-image">
                                    <img src="'. $editedFeedPhoto['url'].'" alt="'. $editedFeedPhoto['title'] .'"/>
                                </div>
                                <div class="dz-remove-file" onclick="$Core.Photo.processUploadImageForAdvFeed.removePhoto(this);">
                                    <i class="ico ico-close"></i>
                                    <span style="display:none">'. _p('dz_remove_file'). '</span>
                                </div>'. ( $editedFeedPhoto['view_id'] == 1 ? '<span class="yncfeed-label-pending edit-photo" style="display: inline-flex;align-items: center;justify-content: center;" title="'. _p('pending') .'"><i class="ico ico-clock"></i></span>' : '') .' 
                            </div>';
    }
    if(!empty($this->_aVars['editedFeedPhotos'])) {
        echo "<script type='text/javascript'>\$Behavior.initEditPhotoFeed = function() {
            if($('.js_edited_feed_photo').length){ $('.ynfeed_form_edit').find('.dropzone-component').addClass('dz-started')};
        }</script>";
    }
    unset($this->_aVars['isEditedFeedPhoto']);
    unset($this->_aVars['editedFeedPhotos']);
}