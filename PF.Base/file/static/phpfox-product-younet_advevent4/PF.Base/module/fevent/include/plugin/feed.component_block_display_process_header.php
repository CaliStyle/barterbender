<?php
if (!empty($this->_aVars['aFeedCallback']['disable_share']) && defined('PHPFOX_IS_FEVENT_VIEW') && Phpfox::getUserParam('fevent.can_post_comment_on_event')) {
    $this->_aVars['aFeedCallback']['disable_share'] = false;
}


