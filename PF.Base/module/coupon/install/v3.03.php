<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.03
 *
 */

function ync_install303()
{
    $oDatabase = Phpfox::getLib('database');

    if (!$oDatabase->isField(Phpfox::getT('coupon'), 'is_show_map'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('coupon')."` ADD `is_show_map` tinyint(1)");
    }

    if (!$oDatabase->isField(Phpfox::getT('coupon'), 'special_price_value'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('coupon')."` ADD `special_price_value` varchar(50)");
    }

    if (!$oDatabase->isField(Phpfox::getT('coupon'), 'special_price_currency'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('coupon')."` ADD `special_price_currency` varchar(3)");
    }

        if (!$oDatabase->isField(Phpfox::getT('coupon'), 'country_child_id'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('coupon')."` ADD `country_child_id` varchar(10)");
    }

     $oDatabase->query("
        CREATE TABLE IF NOT EXISTS `".Phpfox::getT('coupon_custom_field')."` (
          `field_id` int(10) NOT NULL AUTO_INCREMENT,
          `field_name` varchar(255) NOT NULL,
          `phrase_var_name` varchar(255) NOT NULL,
          `type_name` varchar(50) NOT NULL,
          `var_type` varchar(20) NOT NULL,
          `is_active` tinyint(3) NOT NULL,
          `is_required` tinyint(3) NOT NULL,
          `ordering` tinyint(3) NOT NULL,
          PRIMARY KEY (`field_id`)
        ) 
     ");

    $oDatabase->query("
        CREATE TABLE IF NOT EXISTS `".Phpfox::getT('coupon_custom_option')."` (
          `option_id` int(10) NOT NULL AUTO_INCREMENT,
          `field_id` int(10) NOT NULL,
          `phrase_var_name` varchar(255) NOT NULL,
          PRIMARY KEY (`option_id`)
        )
     ");

    $oDatabase->query("
        CREATE TABLE IF NOT EXISTS `".Phpfox::getT('coupon_custom_value')."` (
          `value_id` int(10) NOT NULL AUTO_INCREMENT,
          `coupon_id` int(10) NOT NULL,
          `field_id` int(10) NOT NULL,
          `option_id` int(10) DEFAULT NULL,
          `value` text,
          PRIMARY KEY (`value_id`)
        )
     ");

}

ync_install303();

?>