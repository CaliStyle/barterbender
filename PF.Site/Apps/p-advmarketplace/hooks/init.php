<?php
defined('PHPFOX') or exit('NO DICE!');

if (\Phpfox::isModule('advancedmarketplace')) {
    Phpfox::getLib('setting')->setParam('advancedmarketplace.dir_pic',
        Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS);
    \Phpfox::getLib('setting')->setParam('advancedmarketplace.url_pic',
        Phpfox::getParam('core.url_pic') . 'advancedmarketplace/');
}
