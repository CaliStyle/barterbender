<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

function jobposting_install301() {

	$oDb = Phpfox::getLib('phpfox.database');
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_application")."` (
	  `application_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `job_id` int(10) unsigned NOT NULL,
	  `user_id` int(10) unsigned NOT NULL,
	  `name` varchar(255) DEFAULT NULL,
	  `photo_path` varchar(255) DEFAULT NULL,
	  `email` varchar(255) DEFAULT NULL,
	  `telephone` varchar(255) DEFAULT NULL,
	  `resume` varchar(255) DEFAULT NULL COMMENT 'upload: file_path, resume: id',
	  `resume_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'upload: 0, resume: 1',
      `file_name` varchar(255) DEFAULT NULL,
      `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
      `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0: pending, 1: passed, 2: rejected',
      `server_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`application_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_company")."` (
	  `company_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `user_id` int(10) unsigned NOT NULL,
	  `name` varchar(255) NOT NULL,
	  `image_path` varchar(255) DEFAULT NULL,
      `server_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  `location` varchar(255) NOT NULL,
	  `country_iso` char(2) DEFAULT NULL,
	  `country_child_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
	  `city` varchar(255) DEFAULT NULL,
	  `postal_code` varchar(20) DEFAULT NULL,
	  `gmap` varchar(255) DEFAULT NULL,
	  `website` varchar(255) DEFAULT NULL,
	  `size_from` int(10) unsigned NOT NULL,
	  `size_to` int(10) unsigned NOT NULL,
	  `contact_info` text NOT NULL COMMENT 'serialize: name, phone, email, fax',
	  `time_stamp` int(10) unsigned NOT NULL,
	  `time_update` int(10) unsigned NOT NULL DEFAULT '0',
	  `post_status` tinyint(1) NOT NULL,
	  `is_approved` tinyint(1) NOT NULL,
	  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
	  `is_sponsor` tinyint(1) NOT NULL DEFAULT '0',
	  `privacy` tinyint(1) NOT NULL DEFAULT '0',
	  `privacy_comment` tinyint(1) NOT NULL DEFAULT '0',
	  `total_comment` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_view` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_like` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_job` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_follow` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_dislike` int(10) unsigned NOT NULL DEFAULT '0',
      `total_favorite` int(10) unsigned NOT NULL DEFAULT '0',
	  `module_id` varchar(75) NOT NULL DEFAULT 'jobposting',
	  `item_id` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`company_id`),
	  KEY `public_view` (`is_approved`,`privacy`),
	  KEY `user_id_2` (`user_id`,`is_approved`,`privacy`),
	  KEY `time_stamp` (`time_stamp`,`is_approved`,`privacy`),
	  KEY `user_id` (`user_id`,`time_stamp`,`is_approved`,`privacy`),
	  KEY `title` (`name`,`is_approved`,`privacy`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_company_admin")."` (
	  `company_id` int(10) unsigned NOT NULL,
	  `user_id` int(10) unsigned NOT NULL
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_company_form")."` (
	  `form_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `company_id` int(10) unsigned NOT NULL,
	  `title` varchar(255) NOT NULL,
	  `description` mediumtext,
	  `logo_path` varchar(255) DEFAULT NULL,
      `server_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  `job_title_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `candidate_name_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `candidate_name_require` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `candidate_photo_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `candidate_photo_require` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `candidate_email_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `candidate_email_require` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `candidate_telephone_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `candidate_telephone_require` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `resume_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      PRIMARY KEY (`form_id`),
	  KEY `company_id` (`company_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_company_image")."` (
	  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `company_id` int(10) unsigned NOT NULL,
	  `image_path` varchar(255) NOT NULL,
	  `server_id` tinyint(1) NOT NULL,
	  `ordering` tinyint(3) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`image_id`),
	  KEY `company_id` (`company_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_company_text")."` (
	  `company_id` int(10) unsigned NOT NULL,
	  `description` mediumtext,
	  `description_parsed` mediumtext,
	  KEY `company_id` (`company_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_custom_field")."` (
	  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `company_id` int(10) unsigned NOT NULL,
	  `field_name` varchar(255) NOT NULL,
	  `phrase_var_name` varchar(255) NOT NULL,
	  `type_name` varchar(50) NOT NULL,
	  `var_type` varchar(20) NOT NULL,
	  `is_active` tinyint(3) unsigned NOT NULL DEFAULT '1',
	  `is_required` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `ordering` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`field_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_custom_option")."` (
	  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `field_id` int(10) unsigned NOT NULL,
	  `phrase_var_name` varchar(255) NOT NULL,
	  PRIMARY KEY (`option_id`),
	  KEY `field_id` (`field_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_custom_value")."` (
	  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `application_id` int(10) unsigned NOT NULL,
	  `field_id` int(10) unsigned NOT NULL,
      `option_id` int(10) unsigned,
	  `value` text,
	  PRIMARY KEY (`value_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_favorite")."` (
	  `favorite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `item_type` varchar(10) NOT NULL,
	  `item_id` int(10) unsigned NOT NULL,
	  `user_id` int(10) unsigned NOT NULL,
	  `time_stamp` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`favorite_id`),
	  KEY `user_id` (`user_id`),
	  KEY `company_id` (`item_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_feed")."` (
	  `feed_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `privacy` tinyint(1) NOT NULL DEFAULT '0',
	  `privacy_comment` tinyint(1) NOT NULL DEFAULT '0',
	  `type_id` varchar(75) NOT NULL,
	  `user_id` int(10) unsigned NOT NULL,
	  `parent_user_id` int(10) unsigned NOT NULL DEFAULT '0',
	  `item_id` int(10) unsigned NOT NULL,
	  `time_stamp` int(10) unsigned NOT NULL,
	  `parent_feed_id` int(10) unsigned NOT NULL DEFAULT '0',
	  `parent_module_id` varchar(75) DEFAULT NULL,
	  `time_update` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`feed_id`),
	  KEY `parent_user_id` (`parent_user_id`),
	  KEY `time_update` (`time_update`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_feed_comment")."` (
	  `feed_comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `user_id` int(10) unsigned NOT NULL,
	  `parent_user_id` int(10) unsigned NOT NULL DEFAULT '0',
	  `privacy` tinyint(3) NOT NULL DEFAULT '0',
	  `privacy_comment` tinyint(3) NOT NULL DEFAULT '0',
	  `content` mediumtext,
	  `time_stamp` int(10) unsigned NOT NULL,
	  `total_comment` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_like` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`feed_comment_id`),
	  KEY `parent_user_id` (`parent_user_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_follow")."` (
	  `follow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `item_type` varchar(10) NOT NULL,
	  `item_id` int(10) unsigned NOT NULL,
	  `user_id` int(10) unsigned NOT NULL,
	  `time_stamp` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`follow_id`),
	  KEY `item_id` (`item_id`,`user_id`),
	  KEY `item_id_2` (`item_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('jobposting_category') ."` (
      `category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
      `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
      `is_active` tinyint(1) NOT NULL DEFAULT '0',
      `name` varchar(255) NOT NULL,
      `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
      `used` int(10) unsigned NOT NULL DEFAULT '0',
      `ordering` int(11) unsigned NOT NULL DEFAULT '0',
      `name_url` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`category_id`),
      KEY `parent_id` (`parent_id`,`is_active`),
      KEY `is_active` (`is_active`)
	);");

    $oDb->query("
		INSERT IGNORE INTO `".Phpfox::getT('jobposting_category')."`(`category_id`, `name`, `parent_id`, `time_stamp`, `used`, `is_active`) VALUES
		    (1, 'Accounting', 0, 1484189658, 0, 1),
			(2, 'Building Materials', 0, 1484189658, 0, 1),
			(3, 'Clothing', 0, 1484189658, 0, 1),
			(4, 'Cosmetics', 0, 1484189658, 0, 1),
			(5, 'Education', 0, 1484189658, 0, 1),
			(6, 'Energy', 0, 1484189658, 0, 1),
			(7, 'Entertainment', 0, 1484189658, 0, 1),
			(8, 'Financial', 0, 1484189658, 0, 1),
			(9, 'Food and drink', 0, 1484189658, 0, 1),
			(10, 'Health care', 0, 1484189658, 0, 1),
			(11, 'Manufacturing', 0, 1484189658, 0, 1),
			(12, 'Outsourcing', 0, 1484189658, 0, 1),
			(13, 'Publishing', 0, 1484189658, 0, 1),
			(14, 'Service', 0, 1484189658, 0, 1),
			(15, 'Sports', 0, 1484189658, 0, 1),
			(16, 'Storage', 0, 1484189658, 0, 1),
			(17, 'Tenology', 0, 1484189658, 0, 1),
			(18, 'Tourism', 0, 1484189658, 0, 1);
		");


    $oDb->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('jobposting_category_data') ."` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `company_id` int(10) unsigned NOT NULL,
      `no` tinyint(1) unsigned NOT NULL,
      `category_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`id`),
      KEY `company_category` (`category_id`,`company_id`),
      KEY `category_id` (`category_id`)
    );");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_invite")."` (
	  `invite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `type_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  `item_id` int(10) unsigned NOT NULL,
	  `item_type` varchar(10) NOT NULL,
	  `user_id` int(10) unsigned NOT NULL,
	  `invited_user_id` int(10) unsigned DEFAULT NULL,
	  `invited_email` varchar(255) DEFAULT NULL,
	  `time_stamp` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`invite_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_job")."` (
	  `job_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `user_id` int(10) unsigned NOT NULL,
	  `company_id` int(10) unsigned NOT NULL,
	  `title` varchar(255) NOT NULL,
	  `education_prefer` varchar(255) DEFAULT NULL,
	  `language_prefer` varchar(255) DEFAULT NULL,
	  `working_place` varchar(255) DEFAULT NULL,
	  `working_time` varchar(255) DEFAULT NULL,
	  `time_expire` int(10) unsigned NOT NULL,
	  `time_stamp` int(10) unsigned NOT NULL,
	  `time_update` int(10) unsigned NOT NULL DEFAULT '0',
	  `privacy` tinyint(1) NOT NULL DEFAULT '0',
	  `privacy_comment` tinyint(1) NOT NULL DEFAULT '0',
	  `post_status` tinyint(1) NOT NULL,
	  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
	  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
	  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
      `total_attachment` int(10) unsigned NOT NULL DEFAULT '0',
      `total_comment` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_view` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_like` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_application` int(10) unsigned NOT NULL DEFAULT '0',
	  `total_dislike` int(10) unsigned NOT NULL DEFAULT '0',
      `total_favorite` int(10) unsigned NOT NULL DEFAULT '0',
	  `is_featured` tinyint(1) unsigned NOT NULL DEFAULT '0',
      `is_hide` tinyint(1) unsigned NOT NULL DEFAULT '0',
      `is_notified` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`job_id`),
	  KEY `public_view` (`privacy`),
	  KEY `user_id_2` (`company_id`,`privacy`),
	  KEY `time_stamp` (`time_stamp`,`privacy`),
	  KEY `user_id` (`company_id`,`time_stamp`,`privacy`),
	  KEY `title` (`title`,`privacy`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_subscribe")."` (
      `subscribe_id` int(10) NOT NULL AUTO_INCREMENT,
      `user_id` int(10) NOT NULL,
      `keywords` varchar(255) NOT NULL,
      `company` varchar(255) NOT NULL,
      `location` varchar(255) NOT NULL,
      `industry` int(10) NOT NULL DEFAULT '0',
      `industry_child` int(10) NOT NULL DEFAULT '0',
      `education_prefer` varchar(255) NOT NULL,
      `language_prefer` varchar(255) NOT NULL,
      `working_place` varchar(255) NOT NULL,
      `time_expire` int(10) NOT NULL DEFAULT '0',
      `time_stamp` int(10) NOT NULL,
      PRIMARY KEY (`subscribe_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_job_text")."` (
	  `job_id` int(10) unsigned NOT NULL,
	  `description` mediumtext,
	  `description_parsed` mediumtext,
	  `skills` mediumtext,
	  `skills_parsed` mediumtext
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_package")."` (
	  `package_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) NOT NULL,
	  `post_number` int(10) unsigned NOT NULL DEFAULT '0',
	  `expire_number` int(10) unsigned DEFAULT NULL,
	  `expire_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0: never expire, 1: day, 2: week, 3: month',
	  `fee` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
	  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`package_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_package_data")."` (
	  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `company_id` int(10) unsigned NOT NULL,
	  `package_id` int(10) unsigned NOT NULL,
	  `remaining_post` int(10) unsigned NOT NULL,
	  `valid_time` int(10) unsigned NOT NULL,
	  `expire_time` int(10) unsigned NOT NULL DEFAULT '0',
	  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
      PRIMARY KEY (`data_id`)
	);");
	
	$oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_transaction")."` (
	  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `transaction_log` text,
	  `invoice` mediumtext,
	  `user_id` int(10) unsigned NOT NULL,
	  `item_id` int(10) unsigned NOT NULL,
	  `time_stamp` int(10) unsigned NOT NULL,
	  `amount` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
	  `currency` varchar(4) NOT NULL DEFAULT 'USD',
	  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
	  `paypal_account` varchar(255) DEFAULT NULL,
	  `paypal_transaction_id` varchar(50) DEFAULT NULL,
	  `payment_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`transaction_id`),
	  KEY `time_stamp` (`time_stamp`),
	  KEY `coupon_id` (`item_id`),
	  KEY `user_id` (`user_id`),
	  KEY `status` (`status`)
	);");
	
	if (!$oDb->isField(Phpfox::getT('user_field'), 'company_id'))
	{
	   $oDb->query("ALTER TABLE `".Phpfox::getT('user_field')."` ADD COLUMN `company_id` int(10) unsigned DEFAULT 0;");
	}
	
    if (!$oDb->isField(Phpfox::getT('user_space'), 'space_jobposting'))
	{
	   $oDb->query("ALTER TABLE `".Phpfox::getT('user_space')."` ADD COLUMN `space_jobposting` int(10) unsigned DEFAULT '0';");
	}
}

jobposting_install301();