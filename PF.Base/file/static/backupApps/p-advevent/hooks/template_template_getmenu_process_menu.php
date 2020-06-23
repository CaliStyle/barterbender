<?php
if ($sConnection == 'main' && $aMenu['module'] == 'fevent' && ($aMenu['url'] = 'fevent' || $aMenu['url'] = 'profile.fevent') && !Phpfox::getUserParam('fevent.can_access_event')) {
    unset($aMenus[$iKey]);
}
