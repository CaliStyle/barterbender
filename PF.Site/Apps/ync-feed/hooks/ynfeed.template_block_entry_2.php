<?php
if(!empty($this->_aVars['aFeed'])) {
    $aFeed = $this->_aVars['aFeed'];
    $showEdit = ['link', 'photo', 'v'];
    if(in_array($aFeed['type_id'], $showEdit)) {
        $canEdit = false;
        $module = '';
        $itemId = '';
        $feedCallback = $this->_aVars['aFeedCallback'];
        $feedType = $aFeed['type_id'];
        switch ($feedType) {
            case 'link': {
                $item = Phpfox::getService('link')->getLinkById($aFeed['item_id']);
                break;
            }
            case 'photo': {
                $item = Phpfox::getService('photo')->getPhotoItem($aFeed['item_id']);
                break;
            }
            case 'v': {
                $item = Phpfox::getService('v.video')->getForEdit($aFeed['item_id']);
                break;
            }
        }

        if(!empty($feedCallback['module']) && !empty($feedCallback['item_id'])) {
            $module = $feedCallback['module'];
            $itemId = $feedCallback['item_id'];
            if(in_array($module, ['pages', 'groups']) && !empty($item) && ($item['module_id'] == $module)) {
                $appId = $module == 'pages' ? 'Core_Pages' : 'PHPfox_Groups';
                if(Phpfox::isAppActive($appId)) {
                    $isAdmin = Phpfox::getService($module)->isAdmin($aFeed['parent_user_id']);
                    $canEdit = ($aFeed['user_id'] == Phpfox::getUserId()) || $isAdmin;
                }
            } else {
                $canEdit = true;
            }
        } else {
            if((in_array($feedType, ['link', 'photo'])  && empty($item['module_id'])) || ($feedType == 'v' && in_array($item['module_id'], ['user', 'video']))) {
                $canEdit = (Phpfox::getUserParam('feed.can_edit_own_user_status') && $aFeed['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_edit_other_user_status');
            }
        }

        if($canEdit) {
            echo "<li class=\"ynfeed_feed_option\"><a href=\"javascript:void(0);\"  title=\"" . _p('edit_feed') ."\" onclick=\"\$Core.ynfeed.detachComposeForm(); tb_show('" ._p('edit_feed')."', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id=". $aFeed['feed_id'] .(!empty($module) ? "&amp;module=". $module : '') .(!empty($itemId) ? "&amp;item_id=". $itemId : ''). (!empty($aFeed['user_id']) ? '&amp;user_id='. $aFeed['user_id'] : '') ."')); return false;\">
			<i class=\"fa fa-pencil-square-o\"></i>". _p('edit_feed') ."</a></li>";
        }
    }
}
