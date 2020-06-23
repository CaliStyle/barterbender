<?php
defined('PHPFOX') or exit('NO DICE!');
if (Phpfox::isModule('advancedmarketplace') && !Phpfox::getUserParam('advancedmarketplace.can_create_listing')) {
    foreach ($aMenus as $key => $value) {
        if ($value['url'] == 'advancedmarketplace.add') {
            unset($aMenus[$key]);
            break;
        }
    }
}
