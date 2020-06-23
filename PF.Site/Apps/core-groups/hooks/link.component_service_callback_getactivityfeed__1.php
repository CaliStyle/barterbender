<?php
if ($aRow['module_id'] == 'groups' && Phpfox::isAppActive('PHPfox_Groups') && $aRow['page_id'] && $aItem['profile_page_id'] == 0) {
    $aReturn['parent_user_name'] = Phpfox::getService('groups')->getUrl($aRow['page_id'], $aRow['title'],
        $aRow['vanity_url']);
    $aReturn['feed_table_prefix'] = 'pages_';
    if (!defined('PHPFOX_IS_PAGES_VIEW') && empty($_POST)) {
        $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
    }
    unset($aReturn['parent_user_id']);
}
