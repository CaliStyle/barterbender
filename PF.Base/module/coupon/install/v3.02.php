<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.02
 *
 */

function ync_install302()
{
    $oDatabase = Phpfox::getLib('database');

    if (!$oDatabase->isField(Phpfox::getT('coupon'), 'site_url'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('coupon')."` ADD `site_url` varchar(255)");
    }

    if (!$oDatabase->isField(Phpfox::getT('coupon'), 'discount_currency'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('coupon')."` ADD `discount_currency` varchar(3)");
    }

    if (!$oDatabase->isField(Phpfox::getT('coupon'), 'print_option'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('coupon')."` ADD `print_option` varchar(255)");
    }
    
    $oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('coupon_print_template')."` (
      `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int(10) unsigned NOT NULL,
      `name` varchar(255) NOT NULL,
      `params` text NOT NULL,
      `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
      PRIMARY KEY (`template_id`)
    );");
}

ync_install302();

?>