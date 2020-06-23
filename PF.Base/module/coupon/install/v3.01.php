<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01
 * @by datlv
 *
 */

function ync_install301()
{
    $oDatabase = Phpfox::getLib('database') ;

    //create table coupon
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon') ."` (
			`coupon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`title` varchar(255) NOT NULL,
			`user_id` int(10) unsigned default '0' ,
			`module_id` varchar(75) NOT NULL DEFAULT 'coupon',
			`item_id`  int(10) unsigned NOT NULL DEFAULT '0',
			`image_path` varchar(100) DEFAULT NULL,
			`start_time` int(10) unsigned NOT NULL,
			`end_time` int(10) unsigned NOT NULL,
			`expire_time` int(10) unsigned DEFAULT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			`discount_type` varchar(50) NOT NULL,
			`discount_value` varchar(50) NOT NULL,
			`location_venue` varchar(255) DEFAULT NULL,
			`address` varchar(255) DEFAULT NULL,
			`city` varchar(255) DEFAULT NULL,
			`postal_code` varchar(20) DEFAULT NULL,
			`country_iso` char(3) DEFAULT NULL,
			`gmap` mediumtext DEFAULT NULL,
			`quantity` int(10) DEFAULT NULL,
			`code_setting` varchar(50) DEFAULT NULL,
			`can_reuse` tinyint(1) NOT NULL DEFAULT '0',
			`is_draft` tinyint(1) NOT NULL DEFAULT '0',
			`is_featured` tinyint(1) NOT NULL DEFAULT '0',
			`is_approved` tinyint(1) NOT NULL DEFAULT '1',
			`status` tinyint(1) NOT NULL DEFAULT '0',
			`server_id` tinyint(3) NOT NULL DEFAULT '0',
			`is_closed` tinyint(1) NOT NULL DEFAULT '0',
			`total_claim` int(10) NOT NULL DEFAULT '0',
			`total_like` int(10) unsigned NOT NULL DEFAULT '0',
			`total_dislike` int(10) unsigned NOT NULL DEFAULT '0',
			`total_view` int(10) unsigned NOT NULL DEFAULT '0',
			`total_comment` int(10) unsigned NOT NULL DEFAULT '0',
			`total_rating` int(10) unsigned NOT NULL DEFAULT '0',
			`total_score` decimal(4,2) NOT NULL DEFAULT '0.00',
			`privacy` tinyint(1) NOT NULL DEFAULT '0',
			`privacy_comment` tinyint(1) NOT NULL DEFAULT '0',
			`privacy_claim` tinyint(1) NOT NULL DEFAULT '0',
			`category_id` int(10) NOT NULL DEFAULT '0',
			`is_removed` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`coupon_id`),
			KEY `module_id` (`module_id`),
			KEY `total_view` (`total_view`),
			KEY `total_donor` (`total_claim`),
			KEY `start_time` (`start_time`),
			KEY `is_featured` (`is_featured`),
			KEY `status` (`status`)
		)  AUTO_INCREMENT=1 ;
	");

    //this table map 1-1 with coupon table
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_text') ."` (
			`text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`coupon_id` int(10) unsigned NOT NULL,
			`description` mediumtext DEFAULT NULL,
			`description_parsed` mediumtext DEFAULT NULL,
			`term_condition` mediumtext DEFAULT NULL,
			`term_condition_parsed` mediumtext DEFAULT NULL,
			PRIMARY KEY (`text_id`),
			KEY `coupon_id` (`coupon_id`)
		)  AUTO_INCREMENT=1 ;
	");

    //coupon_category
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_category') ."` (
			`category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			 `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
			 `is_active` tinyint(1) NOT NULL DEFAULT '0',
			 `title` varchar(255) NOT NULL,
			 `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
			 `used` int(10) unsigned NOT NULL DEFAULT '0',
			 `ordering` int(11) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`category_id`),
			 KEY `parent_id` (`parent_id`,`is_active`),
			 KEY `is_active` (`is_active`)
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("
	INSERT IGNORE INTO `".Phpfox::getT('coupon_category')."`(`category_id`, `title`, `parent_id`, `time_stamp`, `used`, `is_active`) VALUES
		(1, 'Entertainment', 0, 1328241203, 0, 1),
		(2, 'Book', 0, 1328241200, 0, 1),
		(3, 'Food', 0, 1328241197, 0, 1),
		(4, 'Clothes', 0, 1328241194, 0, 1),
		(5, 'Beauty/Spa', 0, 1328241191, 0, 1),
		(6, 'Fashion', 0, 1328241187, 0, 1),
		(7, 'Games', 0, 1328241185, 0, 1),
		(8, 'Movie', 0, 1328241180, 0, 1),
		(9, 'Music', 0, 1328241176, 0, 1),
		(10, 'Sport', 0, 1328241173, 0, 1);
	");
    //coupon_category_data
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_category_data') ."` (
			`data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`category_id` int(10) unsigned NOT NULL ,
			`coupon_id` int(10) unsigned NOT NULL ,
			PRIMARY KEY (`data_id`),
			KEY `coupon_category` (`category_id`,`coupon_id`),
			KEY `category_id` (`category_id`)
		)  AUTO_INCREMENT=1 ;
	");

    //coupon_claim
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_claim') ."` (
			`claim_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`coupon_id` int(10) unsigned NOT NULL ,
			`user_id` int(10) unsigned NOT NULL ,
			`time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
			`code` varchar(50) NOT NULL DEFAULT '00000000',
			PRIMARY KEY (`claim_id`),
			KEY `coupon_id` (`coupon_id`),
			KEY `user_id` (`user_id`),
			KEY `coupon_id_code` (`coupon_id`,`code`)
		)  AUTO_INCREMENT=1 ;
	");

    //coupon_transaction
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_transaction') ."` (
			`transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`transaction_log` text DEFAULT NULL,
			`invoice` mediumtext DEFAULT NULL,
			`user_id` int(10) unsigned NOT NULL,
			`coupon_id` int(10) unsigned NOT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			`amount` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'this is fee',
			`currency` varchar(4) NOT NULL DEFAULT 'USD',
			`status` tinyint(2) NOT NULL DEFAULT '0',
			`paypal_account` varchar(255),
			`paypal_transaction_id` varchar(50),
			`payment_type` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`transaction_id`),
			KEY `time_stamp` (`time_stamp`),
			KEY `coupon_id` (`coupon_id`),
			KEY `user_id` (`user_id`),
			KEY `status` (`status`)
		)  AUTO_INCREMENT=1 ;
	");

    //coupon_invite
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_invite') ."` (
			`invited_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`inviting_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`invited_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`invited_email` varchar(255),
			`coupon_id` int(10) unsigned NOT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			`type_id` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`invited_id`)
		)  AUTO_INCREMENT=1 ;
	");

    //coupon_rating
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_rating') ."` (
			 `rate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `item_id` int(10) unsigned NOT NULL,
			 `user_id` int(10) unsigned NOT NULL,
			 `rating` decimal(4,2) NOT NULL DEFAULT '0.00',
			 `time_stamp` int(10) unsigned NOT NULL,
			 PRIMARY KEY (`rate_id`),
			 KEY `item_id` (`item_id`,`user_id`),
			 KEY `item_id_2` (`item_id`)
		)  AUTO_INCREMENT=1 ;
	");

    //coupon_follow
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_follow') ."` (
			 `follow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `coupon_id` int(10) unsigned NOT NULL,
			 `user_id` int(10) unsigned NOT NULL,
			 `time_stamp` int(10) unsigned NOT NULL,
			 PRIMARY KEY (`follow_id`),
			 KEY `item_id` (`coupon_id`,`user_id`),
			 KEY `item_id_2` (`coupon_id`)
		)  AUTO_INCREMENT=1 ;
	");

    //coupon_email_template
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_email_template') ."` (
			 `email_template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `type`  tinyint(2) NOT NULL DEFAULT '0' ,
			 `email_subject` mediumtext DEFAULT NULL,
			 `email_template` mediumtext DEFAULT NULL,
			 `email_template_parsed` mediumtext DEFAULT NULL,
			 PRIMARY KEY (`email_template_id`)
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("
	INSERT IGNORE INTO `".Phpfox::getT('coupon_email_template')."` (`email_template_id`, `type`, `email_subject`, `email_template`, `email_template_parsed`) VALUES
		(1, 1, 'Your Coupon has been created on [social_network_site]', 'Hello [owner_name],\r\n\r\nCongratulations! You have just created a coupon [coupon_name]. [coupon_link]\r\n\r\nBest Regards,\r\n\r\n[site_name]', 'Hello [owner_name],\r<br />\r<br />Congratulations! You have just created a coupon [coupon_name]. [coupon_link]\r<br />\r<br />Best Regards,\r<br />\r<br />[site_name]'),
		(2, 2, 'Your Coupon has been approved on [social_network_site]', 'Hello [owner_name], \r\n\r\nYour Coupon [coupon_name] has just been approved. For more information please visit [coupon_link].\r\n\r\nBest Regards,\r\n\r\n[site_name]', 'Hello [owner_name], \r<br />\r<br />Your Coupon [coupon_name] has just been approved. For more information please visit [coupon_link].\r<br />\r<br />Best Regards,\r<br />\r<br />[site_name]'),
		(3, 3, 'Your Coupon has been Featured', 'Hello [owner_name],\r\n\r\nYour Coupon [coupon_name] has just been set as featured.\r\n[coupon_link]\r\n\r\nBest Regards,\r\n\r\n[site_name]', 'Hello [owner_name],\r<br />\r<br />Your Coupon [coupon_name] at [coupon_link] has just been set as featured.\r<br />[coupon_link]\r<br />\r<br />Best Regards,\r<br />\r<br />[site_name]'),
		(4, 4, 'Your Coupon has been started to running on [social_network_site]', 'Hello [owner_name],\r\n\r\nYour Coupon [coupon_name] started running at [start_date].\r\n\r\nBest Regards, \r\n[site_name]', 'Hello [owner_name],\r<br />\r<br />Your Coupon [coupon_name] started running at [start_date].\r<br />\r<br />Best Regards, \r<br />[site_name]'),
		(5, 5, 'Your Coupon was closed', 'Hello [owner_name],\r\n\r\nYour coupon [coupon_name] has been closed and hidden from listings.\r\n[coupon_link]\r\n\r\nBest Regards, \r\n\r\n[site_name]', 'Hello [owner_name],\r<br />\r<br />Your coupon [coupon_name] has been closed and hidden from listings.\r<br />[coupon_link]\r<br />\r<br />Best Regards, \r<br />\r<br />[site_name]'),
		(6, 6, 'Your Coupon has been claimed', 'Hello [owner_name], \r\n\r\nYour coupon [coupon_name] has just been bought by customer with below information:\r\n\r\n   Name: [claimer_name]\r\n\r\n   Email Address: [claimer_email]\r\n\r\n   Link of Coupon: [coupon_link]\r\n\r\n   Name of coupon: [coupon_name]\r\n\r\n   Expired date: [expired_date]\r\n\r\n   Coupon Code:  [coupon_code]\r\n\r\nBest Regards, \r\n[site_name]', 'Hello [owner_name] \r<br />\r<br />Your coupon [coupon_name] has just been bought by customer with below information:\r<br />\r<br />   Name: [claimer_name]\r<br />\r<br />   Email Address: [claimer_email]\r<br />\r<br />   Link of Coupon: [coupon_link]\r<br />\r<br />   Name of coupon: [coupon_name]\r<br />\r<br />   Expired date: [expired_date]\r<br />\r<br />   Coupon Code:  [coupon_code]\r<br />\r<br />Best Regards, \r<br />[site_name]'),
		(7, 7, 'You have just claimed a coupon!', 'Hello [claimer_name],\r\n\r\nYou have just bought a deal with below information:\r\n\r\n   Link of Coupon: [coupon_link]\r\n\r\n   Name of coupon: [coupon_name]\r\n\r\n   Expired date: [expired_date]\r\n\r\n   Address: [coupon_address]\r\n\r\n   Coupon Code:  [coupon_code]\r\n\r\nYour information:\r\n\r\n   Name: [claimer_name]\r\n\r\n   Email Address: [claimer_email]\r\n\r\nBest Regards, \r\n[site_name]', 'Hello [claimer_name],\r<br />\r<br />You have just bought a deal with below information:\r<br />\r<br />   Link of Coupon: [coupon_link]\r<br />\r<br />   Name of coupon: [coupon_name]\r<br />\r<br />   Expired date: [expired_date]\r<br />\r<br />   Address: [coupon_address]\r<br />\r<br />   Coupon Code:  [coupon_code]\r<br />\r<br />Your information:\r<br />\r<br />   Name: [claimer_name]\r<br />\r<br />   Email Address: [claimer_email]\r<br />\r<br />Best Regards, \r<br />[site_name]'),
		(8, 8, 'The coupon has been paused', 'Dear [claimer_name], \r\n\r\nThe Coupon [coupon_name] will be expired at [expired_date]. You have only one day to use it, please let''s go\r\n\r\nThank you and good luck\r\n\r\nBest Regards,\r\n [site_name]', 'Dear [claimer_name] \r\n\r\nThe Coupon [coupon_name] will be expired at [expired_date]. You have only one day to use it, please let''s go\r\n\r\nThank you and good luck\r\n\r\nBest Regards, \r\n[site_name]'),
		(9, 9, 'Your Coupon has been resumed', 'Hello [owner_name], \r\n\r\nYour coupon has been closed and hidden from listings: \r\n[coupon_link] \r\n\r\nBest Regards, \r\n[site_name]', 'Hello [owner_name] \r\n\r\nYour coupon has been closed and hidden from listings: [coupon_link] \r\n\r\nBest Regards, [site_name]');
	");
    //coupon_email_queue
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_email_queue') ."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `coupon_id` int(10) unsigned NOT NULL,
			 `receivers` mediumtext DEFAULT NULL,
			 `from` varchar(255) DEFAULT NULL ,
			 `email_subject` mediumtext DEFAULT NULL,
			 `time_stamp` int(10) unsigned NOT NULL,
			 `email_message` mediumtext DEFAULT NULL,
			 `is_sent`  tinyint(1) NOT NULL DEFAULT '0',
			 `is_site_user`  tinyint(1) NOT NULL DEFAULT '1',
			 PRIMARY KEY (`id`),
			 KEY `is_sent` (`is_sent`),
			 KEY `coupon_id` (`coupon_id`)
		)  AUTO_INCREMENT=1 ;
	");

    //coupon_faq
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_faq')."`(
		`faq_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
	) AUTO_INCREMENT=1;
	");

    //coupon_theme_template
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('coupon_theme_template') ."` (
			 `theme_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `title` mediumtext NOT NULL,
			 `css_code` mediumtext NOT NULL,
			 `html_code` mediumtext NOT NULL,
			 `order` int(10) NULL DEFAULT '0',
			 `time_stamp` int(10) unsigned NOT NULL,
			 PRIMARY KEY (`theme_id`)
		)  AUTO_INCREMENT=1 ;
	");

    $oDatabase -> query("
	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('coupon_favorite')."` (
		  `favorite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `coupon_id` int(11) unsigned NOT NULL,
		  `user_id` int(11) unsigned NOT NULL,
		  `time_stamp` int(11) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`favorite_id`),
		  KEY `user_id` (`user_id`),
		  KEY `coupon_id` (`coupon_id`)
		);
	");

    //add field for user in user table
    if(!$oDatabase->isField(Phpfox::getT('user_field'),'total_coupon'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_field')."` ADD  `total_coupon` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }

    if(!$oDatabase->isField(Phpfox::getT('user_activity'),'activity_coupon'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_activity')."` ADD  `activity_coupon` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }

    $aRow = $oDatabase->select('block_id')
        ->from(Phpfox::getT('block'))
        ->where("m_connection ='coupon.profile' AND product_id = 'younet_coupon' AND module_id ='profile' AND component ='pic'")
        ->execute('getRow');

    if(!isset($aRow['block_id']))
    {
        // insert the pic block for viewing in profile
        $oDatabase->query("INSERT INTO `".Phpfox::getT('block')."` (`title`, `type_id`, `m_connection`, `module_id`, `product_id`, `component`, `location`, `is_active`, `ordering`, `disallow_access`, `can_move`, `version_id`) VALUES ('Profile Photo &amp; Menu', 0, 'coupon.profile', 'profile', 'younet_coupon', 'pic', '1', 1, 1, NULL, 0, NULL)");
    }

    $path = phpfox::getParam('core.dir_pic') . "coupon/";
    $mode = 0775;

    if(Phpfox::getLib('file')->getFiles($path) === false)
    {
        Phpfox::getLib('file')->mkdir($path, $mode);
    }
}

ync_install301();

?>