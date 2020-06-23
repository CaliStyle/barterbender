<?php

if ($sTemplate == 'feed.block.entry') {
    $sTemplate = 'ynfeed.block.entry';
    $aFeedCallback = [];
    if (isset($this->_aVars['aFeedCallback'])) {
        $aFeedCallback = $this->_aVars['aFeedCallback'];
        $aFeedCallback['table_prefix'] = (isset($aFeedCallback['table_prefix']) ? $aFeedCallback['table_prefix'] : $aFeedCallback['module'] . '_');
        if ($aFeedCallback['table_prefix'] == 'groups_')
            $aFeedCallback['table_prefix'] = 'pages_';
    }
    Phpfox::getService('ynfeed')->callback($aFeedCallback)->getExtraInfo($this->_aVars['aFeed']);

} else if ($sTemplate == 'feed.block.share') {
    $sTemplate = 'ynfeed.block.share.share';
    if (!empty($this->_aVars['iFeedId'])) {
        $this->_aVars['aFeed'] = Phpfox::getService('ynfeed')->createPreviewFeed($this->_aVars);
    }

    $this->_aVars['aEmojis'] = Phpfox::getService('ynfeed.emoticon')->getAll();
    $this->_aVars['corePath'] = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-feed';

    $bLoadCheckIn = false;
    if (!defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getParam('feed.enable_check_in') && (Phpfox::getParam('core.ip_infodb_api_key') || Phpfox::getParam('core.google_api_key'))) {
        $bLoadCheckIn = true;
    }
    $this->_aVars['aFeedStatusLinks'] = Phpfox::getService('ynfeed')->getShareLinks();
    $this->_aVars['bLoadCheckIn'] = $bLoadCheckIn;

} else if ($sTemplate == 'notification.controller.panel') {
    // use quick match of post type that contain comment and status for now, may have to break to every case if emoticons are parsed incorrectly (item title for example)
    array_walk($this->_aVars['aNotifications'], function (&$aNotifcation) {
        if (strpos($aNotifcation['type_id'], 'comment') !== false || strpos($aNotifcation['type_id'], 'status') !== false) {
            $aNotifcation['message'] = ynfeed_parse_emojis($aNotifcation['message']);
        }
    });

} else if ($sTemplate == 'notification.controller.index') {
    array_walk($this->_aVars['aNotifications'], function (&$aNotifcationOfDay) {
        array_walk($aNotifcationOfDay, function (&$aNotifcation) {
            if (strpos($aNotifcation['type_id'], 'comment') !== false || strpos($aNotifcation['type_id'], 'status') !== false) {
                $aNotifcation['message'] = ynfeed_parse_emojis($aNotifcation['message']);
            }
        });
    });
} else if($sTemplate == 'link.block.preview') {
    $sTemplate = 'ynfeed.block.link-preview';
}
