<?php
if (\Phpfox::isApps('YNC_Member') && $sConnection == 'main') {
    foreach ($aMenus as $key => $menu) {
        if ($menu['url'] == 'user.browse') {
            $aMenus[$key]['url'] = 'members';
        }
    }
}
