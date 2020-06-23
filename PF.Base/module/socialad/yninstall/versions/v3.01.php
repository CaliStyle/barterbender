<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsocialad301install () {
	$oDb = Phpfox::getLib('phpfox.database');

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_package')."` (
		`package_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`package_name` VARCHAR(255) NOT NULL,
		`package_description` TEXT NOT NULL, 
		`package_price` DECIMAL(10,2)  DEFAULT '0.00',
		`package_benefit_number` INT(10) UNSIGNED DEFAULT 0,
		`package_benefit_type_id` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '0 means having UNLIMITED benefit, 1: click, 2: impression, 3: day',
		`package_last_edited_time` INT(11) UNSIGNED NOT NULL,
		`package_user_id` INT(10) UNSIGNED NOT NULL,
		`package_currency` VARCHAR(4) DEFAULT 'USD',
		`package_is_active` TINYINT(1) DEFAULT '1',
		`package_is_deleted` TINYINT(1) DEFAULT '0',
		`package_allow_item_type` MEDIUMTEXT DEFAULT NULL COMMENT 'null means all item types are allowed',
		`package_allow_block` MEDIUMTEXT DEFAULT NULL COMMENT 'null means all blocks are allowed',
		`package_allow_module` MEDIUMTEXT DEFAULT NULL COMMENT 'null means all modules are allowed',
		`package_allow_ad_type` MEDIUMTEXT DEFAULT NULL COMMENT 'null means all ad types are allowed',
		`package_order` INT(10) UNSIGNED DEFAULT '10000',
	   PRIMARY KEY (`package_id`)	
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_ad')."` (
		`ad_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ad_user_id` INT(10) UNSIGNED NOT NULL,
		`ad_status` TINYINT(2) DEFAULT '1' COMMENT '[1 = DRAFT | 2 = UNPAID | 3 = PENDING | 4 = DENIED | 5 = RUNNING | 6 = PAUSED | 7 = COMPLETED | 8 = DELETED ]',
		`ad_title` VARCHAR(255) NOT NULL,
		`ad_text` TEXT NOT NULL, 
		`ad_item_id` INT(10) UNSIGNED,
		`ad_item_type` TINYINT(2) DEFAULT '1' COMMENT '1 = external URL | 2 = blog | 3 = event | 4 = page' ,
		`ad_last_edited_time` INT(11) UNSIGNED NOT NULL,
		`ad_start_time` INT(11) UNSIGNED ,
		`ad_end_time` INT(11) UNSIGNED,
		`ad_most_recent_computed_time` INT(11) UNSIGNED,
		`ad_last_viewed_time` INT(11) UNSIGNED,
		`ad_expect_start_time` INT(11) UNSIGNED DEFAULT 0,
		`ad_expect_end_time` INT(11) UNSIGNED DEFAULT 0,
		`ad_total_click` INT(10) UNSIGNED DEFAULT 0 ,
		`ad_total_unique_click` INT(10) UNSIGNED DEFAULT 0 ,
		`ad_total_reach` INT(10) UNSIGNED DEFAULT 0 ,
		`ad_total_impression` INT(10) UNSIGNED DEFAULT 0  ,
		`ad_total_running_day` INT(10) UNSIGNED DEFAULT 0 ,
		`ad_package_id` INT(10) UNSIGNED NOT NULL,
		`ad_external_url` VARCHAR(255) NOT NULL,
		`ad_type` TINYINT(1) DEFAULT '1' COMMENT '1: HTML | 2 : banner | 3 : feed',
		`ad_campaign_id` INT(10) UNSIGNED ,
		`audience_age_min` TINYINT(2) UNSIGNED DEFAULT 0,
		`audience_age_max` TINYINT(2) UNSIGNED DEFAULT 20,
		`audience_gender` TINYINT(1) UNSIGNED DEFAULT 0,
		`placement_block_id` INT(10) UNSIGNED DEFAULT 3,
		`ad_number_of_package` INT(10) UNSIGNED DEFAULT 1,
		`ad_benefit_type_id` TINYINT(1) UNSIGNED DEFAULT 0,
		`ad_benefit_limit_number` INT(10) UNSIGNED DEFAULT 0,
		 PRIMARY KEY (`ad_id`),
		 KEY `ad_status` (`ad_status`),
		 KEY `ad_type` (`ad_type`),
		 KEY `ad_user_id` (`ad_user_id`),
		 KEY `ad_campaign_id` (`ad_campaign_id`),
		 KEY `ad_status_type_block` (`ad_status`, `ad_type`, `placement_block_id`),
		 KEY `ad_last_viewed_time` (`ad_last_viewed_time`),
		 KEY `ad_total_click` (`ad_total_click`),
		 KEY `ad_total_unique_click` (`ad_total_unique_click`),
		 KEY `ad_total_reach` (`ad_total_reach`),
		 KEY `ad_total_impression` (`ad_total_impression`),
		 KEY `ad_total_running_day` (`ad_total_running_day`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_campaign')."` (
		`campaign_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`campaign_name` VARCHAR(255) NOT NULL,
		`campaign_user_id` INT(10) UNSIGNED NOT NULL,
		`campaign_timestamp` INT(10) UNSIGNED,
		`campaign_status` TINYINT(2) DEFAULT '1' COMMENT '1 = active | 2 = deleted',
		PRIMARY KEY (`campaign_id`),
		 KEY `campaign_status_user_id` (`campaign_status`, `campaign_user_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_image')."` (
		`image_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`image_ad_id` INT(10) UNSIGNED NOT NULL,
		`image_user_id` INT(10) UNSIGNED NOT NULL,
		`image_server_id` INT(10) UNSIGNED,
		`image_path` VARCHAR(255) NOT NULL,
	   	PRIMARY KEY (`image_id`),
		KEY `image_ad_id` (`image_ad_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_ad_track')."` (
		`track_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ad_id` INT(10) UNSIGNED NOT NULL,
		`user_id` INT(10) UNSIGNED,
		`ip_address` VARCHAR(50),
		`time_stamp` INT(10) UNSIGNED,
		`type` TINYINT(1) NOT NULL COMMENT '1 : impression, 2: click',
		`number` MEDIUMINT UNSIGNED DEFAULT '0',
		PRIMARY KEY (`track_id`)	,
		KEY `ad_id` (`ad_id`),
		KEY `time_stamp` (`time_stamp`),
		KEY `ad_user_id` (`ad_id`, `user_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_ad_statistic')."` (
		`statistic_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ad_id` INT(10) UNSIGNED NOT NULL,
		`user_id` INT(10) UNSIGNED NOT NULL,
		`total_impression` INT(10) UNSIGNED,
		`total_click` INT(10) UNSIGNED,
		`total_unique_click` INT(10) UNSIGNED,
		`total_reach` INT(10) UNSIGNED,
		`time_stamp` INT(10) UNSIGNED,
	    PRIMARY KEY (`statistic_id`),
		KEY `user_ad_id` (`user_id`, `ad_id`),
		KEY `ad_id` (`ad_id`),
		KEY `time_stamp` (`time_stamp`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_ad_audience_user_group')."` (
		`ad_audience_user_group_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ad_id` INT(10) UNSIGNED NOT NULL,
		`user_group_id` TINYINT(3) UNSIGNED,
	    PRIMARY KEY (`ad_audience_user_group_id`),
		KEY `ad_id` (`ad_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_ad_audience_location')."` (
		`ad_audience_location_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ad_id` INT(10) UNSIGNED NOT NULL,
		`child_id` INT(10) UNSIGNED NOT NULL,
		`location_id` CHAR(2),
		PRIMARY KEY (`ad_audience_location_id`),
		KEY `ad_id` (`ad_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_ad_audience_language')."` (
		`ad_audience_language_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ad_id` INT(10) UNSIGNED NOT NULL,
		`language_id` VARCHAR(12),
	    PRIMARY KEY (`ad_audience_language_id`)	,
		KEY `ad_id` (`ad_id`)
	);");


	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_ad_placement_module')."` (
		`ad_placement_module_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ad_id` INT(10) UNSIGNED NOT NULL,
		`module_id` VARCHAR(75),
	    PRIMARY KEY (`ad_placement_module_id`),
		KEY `ad_id` (`ad_id`)
	);");
	
	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_transaction')."` (
		`transaction_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`transaction_method_id` TINYINT(1) UNSIGNED,
		`transaction_status_id` TINYINT(1) UNSIGNED,
		`extra` TEXT,
		`transaction_description` MEDIUMTEXT,
		`transaction_amount` DECIMAL(10,2)  DEFAULT '0.00',
		`transaction_currency` VARCHAR(4) DEFAULT 'USD',
		`gateway_transaction_id` VARCHAR(255),
		`transaction_start_date` INT(10) UNSIGNED,
		`transaction_pay_date` INT(10) UNSIGNED,
		`transaction_ad_id` INT(10) UNSIGNED,
		`transaction_user_id` INT(10) UNSIGNED,
		PRIMARY KEY (`transaction_id`),
		KEY `transaction_user_method_status_id` (`transaction_user_id`, `transaction_method_id`, `transaction_status_id`),
		KEY `transaction_user_status_id` (`transaction_user_id`, `transaction_status_id`),
		KEY `transaction_method_id` (`transaction_method_id`),
		KEY `transaction_status_id` (`transaction_status_id`)
		
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_custominfor')."` (
		`custominfor_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`custominfor_type_id` VARCHAR(127),
		`content` TEXT,
		`content_parsed` TEXT COMMENT 'used when allow HTML in this content',
		`last_edited_time` INT(10) UNSIGNED,
	   PRIMARY KEY (`custominfor_id`)	
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_ad_ban')."` (
		`ban_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ad_id` INT(10) UNSIGNED NOT NULL,
		`user_id` INT(10) UNSIGNED,
		`time_stamp` INT(10) UNSIGNED,
		PRIMARY KEY (`ban_id`)	,
		KEY `user_id` (`user_id`)
	);");

	$sDir = phpfox::getParam('core.dir_pic') . "socialad/";
	$mode = 0775;
    if(Phpfox::getLib('file')->getFiles($sDir) === false)
    {
        Phpfox::getLib('file')->mkdir($sDir, $mode);
    } else {
    	chmod($sDir, $mode);	
    }	

    // ---------------------------------------------------------

    if (!$oDb->isField(Phpfox::getT('socialad_ad'), 'is_show_guest'))
    {
        $oDb->query("ALTER TABLE `".Phpfox::getT('socialad_ad')."` ADD `is_show_guest` TINYINT(1) DEFAULT '0' ");
    }
    if (!$oDb->isField(Phpfox::getT('socialad_ad'), 'credit_amount'))
    {
        $oDb->query("ALTER TABLE `".Phpfox::getT('socialad_ad')."` ADD `credit_amount` DECIMAL(10,2)  DEFAULT '0.00' ");
    }

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_credit_money')."` (
		`creditmoney_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`creditmoney_user_id` INT(10) UNSIGNED NOT NULL,
		`creditmoney_total_amount` DECIMAL(10,2)  DEFAULT '0.00',
		`creditmoney_remain_amount` DECIMAL(10,2)  DEFAULT '0.00',
		`creditmoney_time_stamp` INT(11) UNSIGNED NOT NULL,
		`creditmoney_description` TEXT NOT NULL, 
	   PRIMARY KEY (`creditmoney_id`)	
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_credit_money_request')."` (
		`creditmoneyrequest_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`creditmoneyrequest_creditmoney_id` INT(10) UNSIGNED NOT NULL,
		`creditmoneyrequest_amount` DECIMAL(10,2)  DEFAULT '0.00',
		`creditmoneyrequest_reason` TEXT NOT NULL,
		`creditmoneyrequest_request_time_stamp` INT(11) UNSIGNED NOT NULL, 
		`creditmoneyrequest_status` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '1:pending, 2: approved, 3:rejected',
		`creditmoneyrequest_update_time_stamp` INT(11) UNSIGNED NOT NULL, 
	   PRIMARY KEY (`creditmoneyrequest_id`)	
	);");

    if (!$oDb->isField(Phpfox::getT('socialad_credit_money_request'), 'creditmoneyrequest_ad_id'))
    {
        $oDb->query("ALTER TABLE `".Phpfox::getT('socialad_credit_money_request')."` ADD `creditmoneyrequest_ad_id` INT(10)  DEFAULT '0' ");
    }

    if (!$oDb->isField(Phpfox::getT('socialad_ad'), 'completion_rate'))
    {
        $oDb->query("ALTER TABLE `".Phpfox::getT('socialad_ad')."` ADD `completion_rate` DECIMAL(10,5)  DEFAULT '0.00000' ");
    }

    $oDb->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('socialad_faq')."`(
		`faq_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`user_id` INT(10) UNSIGNED NOT NULL,
		`parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
		`is_active` tinyint(1) NOT NULL DEFAULT '1',
		`question` mediumtext NOT NULL,
		`question_parsed` mediumtext NULL DEFAULT NULL,
		`answer` mediumtext NOT NULL,
		`answer_parsed` mediumtext NULL DEFAULT NULL,
		`ordering` int(10) DEFAULT '0',
		`time_stamp` int(10) unsigned NOT NULL,
		`used` int(10) unsigned NOT NULL DEFAULT '0',
		PRIMARY KEY (`faq_id`)
	);
	");

}

if (!defined("YOUNET_IN_UNITTEST")) { 
	ynsocialad301install();
}
?>
