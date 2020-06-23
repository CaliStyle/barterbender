<?php
$sCustomUrl = Phpfox::getService('ynblog.helper')->getCustomURL();
if (Phpfox_Request::instance()->get('req2') == $sCustomUrl) {
    foreach ($aMenus as $iKey => $aMenu) {
        if ($aMenu['url'] == $aUser['user_name'] . '.' . $sCustomUrl) {
            $aMenus[$iKey]['is_selected'] = true;
        }
    }
}
