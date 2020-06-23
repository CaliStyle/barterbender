<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01
 * @by trilm
 *
 */

function ynd_install301()
{
    $oDatabase = Phpfox::getLib('database') ;

    /*----------category-------------------*/

    if (!$oDatabase->tableExists(Phpfox::getT('directory_category')))
    {
	    //ynbusiness_category
	    $oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_category') ."` (
				 `category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT ,
				 `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
				 `is_active` tinyint(1) NOT NULL DEFAULT '0',
				 `title` varchar(255) NOT NULL,
				 `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
				 `used` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of items relates this category',
				 `ordering` int(11) unsigned NOT NULL DEFAULT '0',
				 PRIMARY KEY (`category_id`),
				 KEY `parent_id` (`parent_id`,`is_active`),
				 KEY `is_active` (`is_active`)
			)  AUTO_INCREMENT=1 ;
		");

		$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('directory_category')."`(`category_id`, `title`, `parent_id`, `time_stamp`, `used`, `is_active`) VALUES
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
    }

    //ynbusiness_category_data
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_category_data') ."` (
			`data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`category_id` int(10) unsigned NOT NULL ,
			`business_id` int(10) unsigned NOT NULL ,
			`is_main` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`data_id`)
		)  AUTO_INCREMENT=1 ;
	");

/*----------customfield-------------------*/

    //ynbusiness_category_customgroup_data
	$oDatabase->query(
	        "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("directory_category_customgroup_data") . "` (
						  `category_id` int(10) NOT NULL,
						  `group_id` int(10) NOT NULL,
						  PRIMARY KEY  (`category_id`,`group_id`)
						);"
	 );

	//ynbusiness_custom_group
	$oDatabase->query(
        "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("directory_custom_group") . "` (
					  `group_id` int(11) NOT NULL auto_increment,
					  `phrase_var_name` varchar(250) default NULL,
					  `is_active` tinyint(1) default '1',
					  `ordering` tinyint(3) default '0',
					  PRIMARY KEY  (`group_id`)
					);"
    );

	//ynbusiness_custom_field
    $oDatabase->query(
        "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("directory_custom_field") . "` (
					  `field_id` int(10) unsigned NOT NULL auto_increment,
					  `var_type` varchar(250) default NULL,
					  `is_required` tinyint(1) NOT NULL,
					  `field_name` varchar(75) NOT NULL,
					  `type_name` varchar(75) default NULL,
					  `ordering` tinyint(3) default NULL,
					  `phrase_var_name` varchar(75) default NULL,
					  `is_active` tinyint(1) default NULL,
					  `group_id` int(11) NOT NULL,
					  `field_info` text,
					  PRIMARY KEY  (`field_id`)
					);"
    );

	//ynbusiness_custom_option
    $oDatabase->query(
        "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("directory_custom_option") . "` (
					  `option_id` int(10) unsigned NOT NULL auto_increment,
					  `field_id` int(10) unsigned NOT NULL,
					  `phrase_var_name` varchar(250) NOT NULL,
					  PRIMARY KEY  (`option_id`)
					);"
    );

	//ynbusiness_custom_value
    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_custom_value')."` (
      `value_id` int(10) NOT NULL AUTO_INCREMENT,
      `business_id` int(10) NOT NULL,
      `field_id` int(10) NOT NULL,
      `option_id` int(10) DEFAULT NULL,
      `value` text,
      PRIMARY KEY (`value_id`)
    )
 ");

/*----------package-------------------*/
	
	//ynbusiness_package
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_package")."` (
	  `package_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) NOT NULL,
	  `expire_number` int(10) unsigned DEFAULT NULL,
	  `expire_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0: never expire, 1: day, 2: week, 3: month',
	  `fee` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
	  `currency` varchar(3) NOT NULL,
	  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
	  `max_cover_photo` int(10) unsigned DEFAULT '10' COMMENT 'maximum cover photo can be uploaded/displayed',
	  PRIMARY KEY (`package_id`)
	);");

	//ynbusiness_package_data
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_package_data")."` (
	  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `business_id` int(10) unsigned NOT NULL,
	  `package_id` int(10) unsigned NOT NULL,
	  `valid_time` int(10) unsigned NOT NULL,
	  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0: never expire',
	  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
      PRIMARY KEY (`data_id`)
	);");

	if (!$oDatabase->tableExists(Phpfox::getT('directory_package_setting'))){
		$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_package_setting")."` (
	      `setting_id` int(10) unsigned NOT NULL,
		  `setting_phrase` varchar(255) NOT NULL,
		  `setting_name` varchar(255) NOT NULL,
		  `setting_description` varchar(255) NOT NULL,
	      PRIMARY KEY (`setting_id`)
		);");

		$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('directory_package_setting')."`(`setting_id`, `setting_phrase`, `setting_name`, `setting_description`) VALUES
			(1, '{phrase var=&#039;directory.allow_business_owner_to_add_new_content_pages&#039;}', 'allow_business_owner_to_add_new_content_pages', ''),
			(2, '{phrase var=&#039;directory.allow_business_owner_to_change_orders_of_pages&#039;}', 'allow_business_owner_to_change_orders_of_pages', ''),
			(3, '{phrase var=&#039;directory.allow_users_to_confirm_working_at_the_business&#039;}', 'allow_users_to_confirm_working_at_the_business', ''),
			(4, '{phrase var=&#039;directory.allow_users_to_share_business&#039;}', 'allow_users_to_share_business', ''),
			(5, '{phrase var=&#039;directory.allow_users_to_invite_friends_to_business&#039;}', 'allow_users_to_invite_friends_to_business', ''),
			(6, '{phrase var=&#039;directory.allow_business_owner_to_edit_contact_form&#039;}', 'allow_business_owner_to_edit_contact_form', ''),
			(7, '{phrase var=&#039;directory.allow_business_owner_to_add_more_custom_fields_to_his_business&#039;}', 'allow_business_owner_to_add_more_custom_fields_to_his_business', ''),
			(8, '{phrase var=&#039;directory.allow_business_to_have_multiple_owners&#039;}', 'allow_business_to_have_multiple_owners', '');
		");
	}

	//ynbusiness_package_setting
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_package_setting_mapping")."` (
	  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `package_id` int(10) unsigned NOT NULL,
      `setting_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`data_id`)
	);");

	if (!$oDatabase->tableExists(Phpfox::getT('directory_module'))){
		$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_module")."` (
	      `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `module_phrase` varchar(255) NOT NULL,
		  `module_name` varchar(255) NOT NULL,
		  `module_type` enum('page', 'module') DEFAULT 'page',
		  `module_description` varchar(255) NOT NULL,
		  `module_landing` tinyint(1) unsigned NOT NULL DEFAULT '1',
	      PRIMARY KEY (`module_id`)
		);");
		$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('directory_module')."`(`module_id`, `module_phrase`, `module_name`, `module_type`, `module_description`, `module_landing`) VALUES
			(1, '{phrase var=&#039;overview&#039;}', 'overview', 'page', '', 1),
			(2, '{phrase var=&#039;about_us&#039;}', 'aboutus', 'page', '', 0),
			(3, '{phrase var=&#039;activities&#039;}', 'activities', 'page', '', 0),
			(4, '{phrase var=&#039;members&#039;}', 'members', 'page', '', 0),
			(5, '{phrase var=&#039;followers&#039;}', 'followers', 'page', '', 0),
			(6, '{phrase var=&#039;reviews&#039;}', 'reviews', 'page', '', 0),
			(7, '{phrase var=&#039;photos&#039;}', 'photos', 'module', '', 0),
			(8, '{phrase var=&#039;videos&#039;}', 'videos', 'module', '', 0),
			(9, '{phrase var=&#039;musics&#039;}', 'musics', 'module', '', 0),
			(10, '{phrase var=&#039;blogs&#039;}', 'blogs', 'module', '', 0),
			(12, '{phrase var=&#039;polls&#039;}', 'polls', 'module', '', 0),
			(13, '{phrase var=&#039;coupons&#039;}', 'coupons', 'module', '', 0),
			(14, '{phrase var=&#039;events&#039;}', 'events', 'module', '', 0),
			(15, '{phrase var=&#039;jobs&#039;}', 'jobs', 'module', '', 0),
			(16, '{phrase var=&#039;marketplace&#039;}', 'marketplace', 'module', '', 0),
			(17, '{phrase var=&#039;faq&#039;}', 'faq', 'page', '', 0),
			(18, '{phrase var=&#039;contact_us&#039;}', 'contactus', 'page', '', 0);
		");
	}
	//ynbusiness_package_module
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_package_module")."` (
	  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `package_id` int(10) unsigned NOT NULL,
      `module_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`data_id`)
	);");

	if (!$oDatabase->tableExists(Phpfox::getT('directory_theme'))){
		$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_theme")."` (
	      `theme_id` int(10) unsigned NOT NULL,
		  `theme_phrase` varchar(255) NOT NULL,
		  `theme_name` varchar(255) NOT NULL,
		  `theme_description` varchar(255) NOT NULL,
	      PRIMARY KEY (`theme_id`)
		);");
		$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('directory_theme')."`(`theme_id`, `theme_phrase`, `theme_name`, `theme_description`) VALUES
			(1, 'theme1', 'theme1', ''),
			(2, 'theme2', 'theme2', '');
		");
	}
	//ynbusiness_package_theme
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_package_theme")."` (
	  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `package_id` int(10) unsigned NOT NULL,
      `theme_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`data_id`)
	);");

/*----------ynbusiness_creator-------------------*/
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_creator")."` (
      `creator_id` int(10) unsigned NOT NULL,
      `user_id` int(10) unsigned NOT NULL COMMENT 'Group of user cannot create, but this user can',
      PRIMARY KEY (`creator_id`)
	);");

/*----------ynbusiness_comparison-------------------*/
	if (!$oDatabase->tableExists(Phpfox::getT('directory_comparison'))){
		$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_comparison")."` (
	      `comparison_id` int(10) unsigned NOT NULL,
	      `comparison_name` varchar(255) NOT NULL COMMENT 'can use language phrase',
		  `is_active` tinyint(1) NOT NULL DEFAULT '1',      
	      PRIMARY KEY (`comparison_id`)
		);");

		$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('directory_comparison')."`(`comparison_id`, `comparison_name`, `is_active`) VALUES
			(1, '{phrase var=&#039;directory.ratings&#039;}', 1),
			(2, '{phrase var=&#039;directory.members&#039;}', 1),
			(3, '{phrase var=&#039;directory.follower&#039;}', 1),
			(4, '{phrase var=&#039;directory.reviews&#039;}', 1),
			(5, '{phrase var=&#039;directory.address&#039;}', 1),
			(6, '{phrase var=&#039;directory.website&#039;}', 1),
			(7, '{phrase var=&#039;directory.phone&#039;}', 1),
			(8, '{phrase var=&#039;directory.operating_hours&#039;}', 1),
			(9, '{phrase var=&#039;directory.custom_field&#039;}', 1),
			(10, '{phrase var=&#039;directory.short_description&#039;}', 1),
			(11, '{phrase var=&#039;directory.latest_reviews&#039;}', 1);
		");
	}

/*----------ynbusiness_transaction-------------------*/
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_transaction') ."` (
			`transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`transaction_log` text DEFAULT NULL,
			`invoice` mediumtext DEFAULT NULL,
			`user_id` int(10) unsigned NOT NULL,
			`business_id` int(10) unsigned NOT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			`amount` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'this is fee',
			`currency` varchar(4) NOT NULL DEFAULT 'USD',
			`status` tinyint(2) NOT NULL DEFAULT '0',
			`paypal_account` varchar(255),
			`paypal_transaction_id` varchar(50),
			`payment_type` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`transaction_id`),
			KEY `time_stamp` (`time_stamp`),
			KEY `business_id` (`business_id`),
			KEY `user_id` (`user_id`),
			KEY `status` (`status`)
		)  AUTO_INCREMENT=1 ;
	");

    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_invoice') ."` (
		  `invoice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `item_id` int(10) unsigned NOT NULL,
		  `type` enum('business') DEFAULT 'business',
		  `user_id` int(10) unsigned NOT NULL,
		  `currency_id` char(3) NOT NULL,
		  `price` decimal(14,2) NOT NULL,
		  `status` varchar(20) DEFAULT NULL,
		  `time_stamp` int(10) unsigned NOT NULL,
		  `time_stamp_paid` int(10) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`invoice_id`),
		  KEY `item_id` (`item_id`),
		  KEY `user_id` (`user_id`),
		  KEY `listing_id_2` (`item_id`,`status`),
		  KEY `listing_id_3` (`item_id`,`user_id`,`status`)
		)  AUTO_INCREMENT=1 ;
	");

/*----------ynbusiness_claim-------------------*/
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_claim') ."` (
			`claim_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL ,
			`user_id` int(10) unsigned NOT NULL ,
			`time_stamp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'it is claimed date',
			`status` tinyint(2) NOT NULL DEFAULT '0',			
			PRIMARY KEY (`claim_id`),
			KEY `business_id` (`business_id`),
			KEY `user_id` (`user_id`)
		)  AUTO_INCREMENT=1 ;
	");

/*----------email template-------------------*/
    if (!$oDatabase->tableExists(Phpfox::getT('directory_email_template'))){
	    //ynbusiness_email_template
	    $oDatabase->query("
			CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_email_template') ."` (
			  `email_template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `type` enum('admin','owner','user') NOT NULL DEFAULT 'admin',
			  PRIMARY KEY (`email_template_id`)
			)  AUTO_INCREMENT=1 ;
		");

	    $oDatabase->query("
			INSERT IGNORE INTO `". Phpfox::getT('directory_email_template') ."` (`email_template_id`, `type`) VALUES
			(1, 'admin'),
			(2, 'owner'),
			(3, 'owner'),
			(4, 'user');
		");
    }


    if (!$oDatabase->tableExists(Phpfox::getT('directory_email_template_data'))){
	    $oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_email_template_data') ."` (
				 `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				   `language_id` varchar(12) NOT NULL DEFAULT 'en' ,
				`email_template_id` int(10) unsigned NOT NULL,
				 `email_subject` mediumtext DEFAULT NULL,
				 `email_template` mediumtext DEFAULT NULL,
				 `email_template_parsed` mediumtext DEFAULT NULL,
				 PRIMARY KEY (`data_id`)
			)  AUTO_INCREMENT=1 ;
		");

	    $oDatabase->query("
			INSERT IGNORE INTO `". Phpfox::getT('directory_email_template_data') ."` (`data_id`, `language_id`, `email_template_id`, `email_subject`, `email_template`, `email_template_parsed`) VALUES
			(1, 'en', 1, 'Claim Business Successfully', 'Hello [owner_name],\r\n\r\nYou have just claimed business \'[business_name]\' successfully. Please click the following link to view it on \'My Businesses\' page [my_business_link]\r\n\r\nRegards,\r\n[site_name]', 'Hello [owner_name],\r<br />\r<br />You have just claimed business \'[business_name]\' successfully. Please click the following link to view it on \'My Businesses\' page [my_business_link]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(2, 'en', 2, 'Business Approved', 'Hello [owner_name],\r\n\r\nYour business \'[business_name]\' has just been approved. Please click the following link to view it [business_link]\r\n\r\nRegards,\r\n[site_name]', 'Hello [owner_name],\r<br />\r<br />Your business \'[business_name]\' has just been approved. Please click the following link to view it [business_link]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(3, 'en', 3, 'Claim Request Approved', 'Hello [owner_name]\r\n\r\nYour claim request for business \'[business_name]\' has just been approved. Please click the following link the view this business on \'My Businesses\' page [my_business_link]\r\n\r\nRegards,\r\n[site_name]', 'Hello [owner_name]\r<br />\r<br />Your claim request for business \'[business_name]\' has just been approved. Please click the following link the view this business on \'My Businesses\' page [my_business_link]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(4, 'en', 4, 'Create Business Successfully', 'Hello [owner_name],\r\n\r\nYou have just created business \'[business_name]\' successfully. Please click the following link to view it [business_link]\r\n\r\nRegards,\r\n[site_name]', 'Hello [owner_name],\r<br />\r<br />You have just created business \'[business_name]\' successfully. Please click the following link to view it [business_link]\r<br />\r<br />Regards,\r<br />[site_name]');
		");
    }

    //ynbusiness_email_queue
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_email_queue') ."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `business_id` int(10) unsigned NOT NULL,
			 `receivers` mediumtext DEFAULT NULL,
			 `from` varchar(255) DEFAULT NULL ,
			 `email_subject` mediumtext DEFAULT NULL,
			 `time_stamp` int(10) unsigned NOT NULL,
			 `email_message` mediumtext DEFAULT NULL,
			 `is_sent`  tinyint(1) NOT NULL DEFAULT '0',
			 `is_site_user`  tinyint(1) NOT NULL DEFAULT '1',
			 PRIMARY KEY (`id`),
			 KEY `is_sent` (`is_sent`),
			 KEY `business_id` (`business_id`)
		)  AUTO_INCREMENT=1 ;
	");

/*----------global setting-------------------*/
    if (!$oDatabase->tableExists(Phpfox::getT('directory_global_setting'))){
	    $oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_global_setting') ."` (
				 `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				 `default_theme_id` int(10) unsigned NOT NULL DEFAULT '1',
				 `default_feature_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'for 1 day',
				 PRIMARY KEY (`setting_id`)
			)  AUTO_INCREMENT=1 ;
		");
		$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('directory_global_setting')."`(`setting_id`, `default_theme_id`, `default_feature_fee`) VALUES
			(1, 1, 10); 
		");
    }

/*----------ynbusiness_invite-------------------*/
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_invite') ."` (
			`invited_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`inviting_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`invited_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`invited_email` varchar(255),
			`business_id` int(10) unsigned NOT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			`type_id` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`invited_id`)
		)  AUTO_INCREMENT=1 ;
	");


/*----------ynbusiness_rating-------------------*/
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_rating') ."` (
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

/*----------ynbusiness_follow-------------------*/
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_follow') ."` (
			 `follow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `business_id` int(10) unsigned NOT NULL,
			 `user_id` int(10) unsigned NOT NULL,
			 `time_stamp` int(10) unsigned NOT NULL,
			 PRIMARY KEY (`follow_id`),
			 KEY `item_id` (`business_id`,`user_id`),
			 KEY `item_id_2` (`business_id`)
		)  AUTO_INCREMENT=1 ;
	");

/*----------ynbusiness_favorite-------------------*/
    $oDatabase -> query("
	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_favorite')."` (
		  `favorite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `business_id` int(11) unsigned NOT NULL,
		  `user_id` int(11) unsigned NOT NULL,
		  `time_stamp` int(11) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`favorite_id`),
		  KEY `user_id` (`user_id`),
		  KEY `business_id` (`business_id`)
		);
	");	

/*----------ynbusiness_business-------------------*/
    $oDatabase -> query("
	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_business')."` (
		  `business_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `creator_id` int(10) unsigned NOT NULL COMMENT 'can NOT change',
		  `user_id` int(10) unsigned NOT NULL COMMENT 'it is onwer, can change',
		  `creating_type` enum('business', 'claiming') DEFAULT 'business' COMMENT 'type when creating, can NOT change',
		  `type` enum('business', 'claiming') DEFAULT 'business' COMMENT 'type currently, can change',

		  `package_id` INT(10) UNSIGNED NOT NULL,
		  `package_name` varchar(255) NOT NULL,
		  `package_expire_number` int(10) unsigned DEFAULT NULL,
		  `package_expire_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0: never expire, 1: day, 2: week, 3: month',
		  `package_fee` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
		  `package_currency` varchar(3) NOT NULL,
		  `package_max_cover_photo` int(10) unsigned DEFAULT '10' COMMENT 'maximum cover photo can be uploaded/displayed',
		  `package_start_time` INT(10) UNSIGNED NOT NULL,
		  `package_end_time` INT(10) UNSIGNED NOT NULL,

		  `theme_id` INT(10) UNSIGNED NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `logo_path` varchar(255) DEFAULT NULL,
	      `server_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `time_stamp` int(10) unsigned NOT NULL,
		  `time_update` int(10) unsigned NOT NULL DEFAULT '0',
		  `business_status` TINYINT(2) DEFAULT '1' COMMENT '[1 = DRAFT | 2 = UNPAID | 3 = PENDING | 4 = DENIED | 5 = RUNNING | 6 = PAUSED | 7 = COMPLETED | 8 = DELETED | 9 = APPROVED ]',
			`short_description` mediumtext NOT NULL,
			`short_description_parsed` mediumtext NOT NULL,
		  `country_iso` char(2) DEFAULT NULL,
		  `country_child_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
		  `email` varchar(255),
		  `city` varchar(255) DEFAULT NULL,
		  `province` varchar(255) DEFAULT NULL,
		  `postal_code` varchar(20) DEFAULT NULL,
		  `size` enum('1 - 50', '51 - 250', '251 - 1000', '> 1000') DEFAULT '1 - 50',
		  `time_zone` char(4) DEFAULT NULL,
		  `dst_check` tinyint(1) NOT NULL DEFAULT '0',
		  `founder` varchar(255) NOT NULL,

		  `feature_day` int(10) unsigned NOT NULL DEFAULT '0',
		  `feature_fee` int(10) unsigned NOT NULL DEFAULT '0',
		  `feature_start_time` INT(10) UNSIGNED NOT NULL,
		  `feature_end_time` INT(10) UNSIGNED NOT NULL,

			`about` mediumtext DEFAULT NULL,
			`about_parsed` mediumtext DEFAULT NULL,		  

		  `privacy` tinyint(1) NOT NULL DEFAULT '0',
		  `privacy_comment` tinyint(1) NOT NULL DEFAULT '0',
		  `total_comment` int(10) unsigned NOT NULL DEFAULT '0',
		  `total_view` int(10) unsigned NOT NULL DEFAULT '0',
		  `total_like` int(10) unsigned NOT NULL DEFAULT '0',
		  `total_follow` int(10) unsigned NOT NULL DEFAULT '0',
		  `total_dislike` int(10) unsigned NOT NULL DEFAULT '0',
	      `total_favorite` int(10) unsigned NOT NULL DEFAULT '0',
			`total_rating` int(10) unsigned NOT NULL DEFAULT '0',
			`total_score` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT 'average rating',
		  `module_id` varchar(75) NOT NULL DEFAULT 'directory',
		  `item_id` int(10) unsigned NOT NULL DEFAULT '0',

		  PRIMARY KEY (`business_id`) 
		);
	");	

	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_text') ."` (
			`text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
			`description` mediumtext DEFAULT NULL,
			`description_parsed` mediumtext DEFAULT NULL,
			PRIMARY KEY (`text_id`),
			KEY `business_id` (`business_id`)
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_location') ."` (
			`location_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
		  `location_title` varchar(255) NOT NULL,
		  `location_address` varchar(255) NOT NULL,
		  `location_longitude` varchar(64) DEFAULT '0',
		  `location_latitude` varchar(64) DEFAULT '0',
			PRIMARY KEY (`location_id`) 
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_phone') ."` (
			`phone_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
			`phone_number` varchar(255) NOT NULL,
			PRIMARY KEY (`phone_id`) 
		)  AUTO_INCREMENT=1 ;
	");
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_fax') ."` (
			`fax_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
			`fax_number` varchar(255) NOT NULL,
			PRIMARY KEY (`fax_id`) 
		)  AUTO_INCREMENT=1 ;
	");
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_website') ."` (
			`website_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
			`website_text` varchar(255) NOT NULL,
			PRIMARY KEY (`website_id`) 
		)  AUTO_INCREMENT=1 ;
	");
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_vistinghour') ."` (
			`vistinghour_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
			`vistinghour_dayofweek` tinyint(1) NOT NULL DEFAULT '1',
			`vistinghour_starttime` varchar(255) NOT NULL,
			`vistinghour_endtime` varchar(255) NOT NULL,
			PRIMARY KEY (`vistinghour_id`) 
		)  AUTO_INCREMENT=1 ;
	");
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_usercustomfield') ."` (
			`usercustomfield_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
			`usercustomfield_title` varchar(255) NOT NULL,
			`usercustomfield_content` varchar(255) NOT NULL,
			PRIMARY KEY (`usercustomfield_id`) 
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_announcement') ."` (
			`announcement_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
			`announcement_title` varchar(255) NOT NULL,
			`announcement_content` mediumtext DEFAULT NULL,
			`announcement_content_parse` mediumtext DEFAULT NULL,
			PRIMARY KEY (`announcement_id`) 
		)  AUTO_INCREMENT=1 ;
	");
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_announcement_hide') ."` (
		`data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `announcement_id` int(10) unsigned NOT NULL,
		  `user_id` int(10) unsigned NOT NULL,
			PRIMARY KEY (`data_id`) 
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_feed")."` (
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
	
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_feed_comment")."` (
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

	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_image') ."` (
			`image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`business_id` int(10) unsigned NOT NULL,
			`title` varchar(255) DEFAULT NULL,
			`image_path` varchar(255) DEFAULT NULL,
			`server_id` tinyint(3) NOT NULL DEFAULT '0',
			`ordering` int(10) unsigned NOT NULL DEFAULT '0',
			`is_profile` tinyint(1) NOT NULL DEFAULT '0',
			`file_size` int(10) unsigned NOT NULL DEFAULT '0',
			`mime_type` varchar(150),
			`extension` varchar(20) NOT NULL,
			`width` smallint(4) unsigned DEFAULT '0',
			`height` smallint(4) unsigned DEFAULT '0',
			PRIMARY KEY (`image_id`),
			KEY `business_id` (`business_id`)
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_business_module")."` (
      `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,

		`business_id` int(10) unsigned NOT NULL,
      `module_id` int(10) unsigned DEFAULT '0' COMMENT '0: content page, other: following module table',

		`contentpage` mediumtext DEFAULT NULL,
		`contentpage_parsed` mediumtext DEFAULT NULL,		  

	  `module_phrase` varchar(255) NOT NULL,
	  `module_name` varchar(255) NOT NULL,
	  `module_type` enum('page', 'module', 'contentpage') DEFAULT 'page',
	  `module_description` varchar(255) NOT NULL,
	  `is_show` tinyint(1) NOT NULL DEFAULT '1',
	  `module_landing` tinyint(1) unsigned NOT NULL DEFAULT '1',
      PRIMARY KEY (`data_id`)
	);");

	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_business_contactus")."` (
	  `contactus_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `business_id` int(10) unsigned NOT NULL,
	  `description` mediumtext,

      `email_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `email_require` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `receiver_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `receiver_require` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `title_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `title_require` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `content_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `content_require` tinyint(1) unsigned NOT NULL DEFAULT '1',

      `receiver_data` text DEFAULT NULL COMMENT 'array with json encode',
      PRIMARY KEY (`contactus_id`)
	);");
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_contactuscustomfield")."` (
	  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `contactus_id` int(10) unsigned NOT NULL,
	  `field_name` varchar(255) NOT NULL,
	  `phrase_var_name` varchar(255) NOT NULL,
	  `type_name` varchar(50) NOT NULL,
	  `var_type` varchar(20) NOT NULL,
	  `is_active` tinyint(3) unsigned NOT NULL DEFAULT '1',
	  `is_required` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `ordering` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`field_id`)
	);");
	$oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_contactuscustomoption")."` (
	  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `field_id` int(10) unsigned NOT NULL,
	  `phrase_var_name` varchar(255) NOT NULL,
	  PRIMARY KEY (`option_id`),
	  KEY `field_id` (`field_id`)
	);");
	// we do NOT store contact data 
	// $oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("directory_contactuscustomvalue")."` (
	//   `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 //      `business_id` int(10) unsigned NOT NULL,
	//   `field_id` int(10) unsigned NOT NULL,
 //      `option_id` int(10) unsigned,
	//   `value` text,
	//   PRIMARY KEY (`value_id`)
	// );");
	
    $oDatabase->query("
    	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_business_faq')."` (
			  `faq_id` int(10) NOT NULL AUTO_INCREMENT,

		      `business_id` int(10) unsigned NOT NULL,
		      
			  `parent_id` mediumint(8) NOT NULL,
			  `is_active` tinyint(1) NOT NULL,
			  `question` mediumtext NOT NULL,
			  `question_parsed` mediumtext,
			  `answer` mediumtext NOT NULL,
			  `answer_parsed` mediumtext,
			  `ordering` int(10) DEFAULT NULL,
			  `time_stamp` int(10) NOT NULL,
			  `used` int(10) NOT NULL,
			  PRIMARY KEY (`faq_id`)
			)
    ");

    $oDatabase->query("
    	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_business_memberrole')."` (
			  `role_id` int(10) NOT NULL AUTO_INCREMENT,
		      `business_id` int(10) unsigned NOT NULL,		      
	  			`role_title` varchar(255) NOT NULL COMMENT 'default: guest, admin, member',
	  			`is_default` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`role_id`)
			)
    ");

    if (!$oDatabase->tableExists(Phpfox::getT('directory_business_memberrolesetting'))){
	    $oDatabase->query("
	    	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_business_memberrolesetting')."` (
				  `setting_id` int(10) NOT NULL AUTO_INCREMENT,
				  `setting_name` varchar(255) NOT NULL,
		  		`setting_title` varchar(255) NOT NULL,
				`data` text DEFAULT NULL COMMENT 'array with json encode, store phrase options',
				  PRIMARY KEY (`setting_id`)
				)
	    ");

		$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('directory_business_memberrolesetting')."`(`setting_id`, `setting_name`, `setting_title`, `data`) VALUES
			 (1, 'invite_member',  '{phrase var=&#039;directory.invite_member&#039;}', '{\"yes\":\"{phrase var=&#039;directory.yes&#039;}\",\"no\":\"{phrase var=&#039;directory.no&#039;}\"}'),
			 (3, 'view_privacy', '{phrase var=&#039;directory.view_privacy&#039;}', '{\"yes\":\"{phrase var=&#039;directory.yes&#039;}\",\"no\":\"{phrase var=&#039;directory.no&#039;}\"}'),
			 (4, 'comment_privacy', '{phrase var=&#039;directory.comment_privacy&#039;}', '{\"yes\":\"{phrase var=&#039;directory.yes&#039;}\",\"no\":\"{phrase var=&#039;directory.no&#039;}\"}'),		
			 (5, 'share_a_photo', '{phrase var=&#039;directory.share_a_photo&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
		     (6, 'view_browse_photos', '{phrase var=&#039;directory.view_browse_photos&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (7, 'share_an_event', '{phrase var=&#039;directory.share_an_event&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (8, 'view_browse_events', '{phrase var=&#039;directory.view_browse_events&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (9, 'share_a_poll', '{phrase var=&#039;directory.share_a_poll&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (10, 'view_browse_polls', '{phrase var=&#039;directory.view_browse_polls&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (11, 'share_a_video', '{phrase var=&#039;directory.share_a_video&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (12, 'view_browse_videos', '{phrase var=&#039;directory.view_browse_videos&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (13, 'share_a_music', '{phrase var=&#039;directory.share_a_music&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (14, 'view_browse_musics', '{phrase var=&#039;directory.view_browse_musics&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (15, 'share_a_marketplace_item', '{phrase var=&#039;directory.share_a_marketplace_item&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (16, 'view_browse_marketplace_items', '{phrase var=&#039;directory.view_browse_marketplace_items&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (17, 'share_a_blog', '{phrase var=&#039;directory.share_a_blog&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (18, 'view_browse_blogs', '{phrase var=&#039;directory.view_browse_blogs&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (19, 'share_a_job', '{phrase var=&#039;directory.share_a_job&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (20, 'view_browse_jobs', '{phrase var=&#039;directory.view_browse_jobs&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (21, 'share_a_coupon', '{phrase var=&#039;directory.share_a_coupon&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (22, 'view_browse_coupons', '{phrase var=&#039;directory.view_browse_coupons&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (23, 'share_a_discussion', '{phrase var=&#039;directory.share_a_discussion&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (24, 'view_browse_discussions', '{phrase var=&#039;directory.view_browse_discussions&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (25, 'view_dashboard', '{phrase var=&#039;directory.view_dashboard&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (27, 'edit_business_information', '{phrase var=&#039;directory.edit_business_information&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (28, 'manage_cover_photos', '{phrase var=&#039;directory.manage_cover_photos&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (29, 'manage_pages', '{phrase var=&#039;directory.manage_pages&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (30, 'add_member_roles', '{phrase var=&#039;directory.add_member_roles&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (31, 'change_member_roles', '{phrase var=&#039;directory.change_member_roles&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (32, 'configure_setting_member_roles', '{phrase var=&#039;directory.configure_setting_member_roles&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (33, 'manage_announcements', '{phrase var=&#039;directory.manage_announcements&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			(34, 'manage_modules', '{phrase var=&#039;directory.manage_modules&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			(35, 'change_business_theme', '{phrase var=&#039;directory.change_business_theme&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			(36, 'update_package', '{phrase var=&#039;directory.update_package&#039;}',
				 '{
				 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
				 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
				  }'
			 ),
			 (37, 'share_a_ynblog', '{phrase var=&#039;directory.share_a_ynblog&#039;}',
                 '{
                 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
                 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
                  }'
             ),
             (38, 'view_browse_ynblogs', '{phrase var=&#039;directory.view_browse_ynblogs&#039;}',
                 '{
                 \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
                 \"no\":\"{phrase var=&#039;directory.no&#039;}\"
                  }'
             );
		");
    }

    $oDatabase->query("
    	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_business_memberrolesettingdata')."` (
			  `data_id` int(10) NOT NULL AUTO_INCREMENT,
			  `setting_id` int(10) unsigned NOT NULL,		  
		      `role_id` int(10) unsigned NOT NULL,		      
			  PRIMARY KEY (`data_id`)
			)
    ");

    $oDatabase->query("
    	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_business_userroledata')."` (
			  `data_id` int(10) NOT NULL AUTO_INCREMENT,
			  `user_id` int(10) unsigned NOT NULL,		  
		      `role_id` int(10) unsigned NOT NULL,		      
			  PRIMARY KEY (`data_id`)
			)
    ");

    $oDatabase->query("
    	CREATE TABLE IF NOT EXISTS `".Phpfox::getT('directory_business_moduledata')."` (
			  `data_id` int(10) NOT NULL AUTO_INCREMENT,
			  `module_id` int(10) unsigned NOT NULL COMMENT 'id from directory_module',
		      `business_id` int(10) unsigned NOT NULL,		      
		      `core_module_id` varchar(75) NOT NULL COMMENT 'id from core, using for default and advanced module',
		      `item_id` int(10) unsigned NOT NULL,		      
			  PRIMARY KEY (`data_id`)
			)
    ");


/*---------- searh history (WE STORE ONLY LAST HISTORY) -------------------*/
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_searchhistory') ."` (
			`searchhistory_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(10) unsigned NOT NULL,
			`data` text DEFAULT NULL,
			PRIMARY KEY (`searchhistory_id`) 
		)  AUTO_INCREMENT=1 ;
	");
/*---------- subscribe -------------------*/
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_business_subscribe') ."` (
			`subscribe_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`email` varchar(255) NOT NULL,
			`data` text DEFAULT NULL,
			PRIMARY KEY (`subscribe_id`) 
		)  AUTO_INCREMENT=1 ;
	");

/*---------- review -------------------*/
	$oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_review') ."` (
		  `review_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `business_id` int(10) unsigned NOT NULL,
		  `user_id` int(10) unsigned NOT NULL,
		  `timestamp` int(10) unsigned NOT NULL,
		  `rating` int(10) unsigned NOT NULL,
		  `title` varchar(255) NOT NULL,
		  `content` text NOT NULL,
		  PRIMARY KEY (`review_id`)
		) AUTO_INCREMENT=1;
	");

    $aRow = $oDatabase->select('block_id')
        ->from(Phpfox::getT('block'))
        ->where("m_connection ='directory.profile' AND product_id = 'younet_directory' AND module_id ='profile' AND component ='pic'")
        ->execute('getRow');

    if(!isset($aRow['block_id']))
    {
        // insert the pic block for viewing in profile
        $oDatabase->query("INSERT INTO `".Phpfox::getT('block')."` (`title`, `type_id`, `m_connection`, `module_id`, `product_id`, `component`, `location`, `is_active`, `ordering`, `disallow_access`, `can_move`, `version_id`) VALUES ('Profile Photo &amp; Menu', 0, 'directory.profile', 'profile', 'younet_directory', 'pic', '1', 1, 1, NULL, 0, NULL)");
    }


	if (!$oDatabase->isField(Phpfox::getT('user_field'), 'business_id'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('user_field')."` ADD COLUMN `business_id` int(10) unsigned DEFAULT 0;");
	}
	
    if (!$oDatabase->isField(Phpfox::getT('user_space'), 'space_business'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('user_space')."` ADD COLUMN `space_business` int(10) unsigned DEFAULT '0';");
	}
    if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'package_data'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `package_data` text DEFAULT NULL COMMENT 'array with json encode, store default theme/module/setting';");
	}
    if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'disable_visitinghourtimezone'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `disable_visitinghourtimezone` tinyint(1) NOT NULL DEFAULT '0';");
	}
    if (!$oDatabase->isField(Phpfox::getT('directory_invoice'), 'invoice_data'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_invoice')."` ADD COLUMN `invoice_data` text DEFAULT NULL COMMENT 'array with json encode';");
	}
	$oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` CHANGE  `feature_fee`  `feature_fee` DECIMAL( 14, 2 ) UNSIGNED NOT NULL DEFAULT  '0.00';");
    if (!$oDatabase->isField(Phpfox::getT('directory_business_memberrolesettingdata'), 'status'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business_memberrolesettingdata')."` ADD COLUMN `status` enum('yes', 'no') DEFAULT 'no';");
	}
    if (!$oDatabase->isField(Phpfox::getT('directory_business_memberrole'), 'type'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business_memberrole')."` ADD COLUMN `type` enum('admin', 'member', 'other') DEFAULT 'other';");
	}
	$oDatabase->query("ALTER TABLE  `".Phpfox::getT('directory_business_memberrole')."` CHANGE  `type`  `type` ENUM(  'guest',  'admin',  'member',  'other' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT  'other';");
	$oDatabase->query("ALTER TABLE  `".Phpfox::getT('directory_invoice')."` CHANGE  `type`  `type` ENUM(  'business',  'feature' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT  'business';");
    if (!$oDatabase->isField(Phpfox::getT('directory_business_moduledata'), 'status'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business_moduledata')."` ADD COLUMN `status` enum('active', 'inactive') DEFAULT 'inactive';");
	}

	if (!$oDatabase->isField(Phpfox::getT('directory_business_announcement'), 'timestamp'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business_announcement')."` ADD COLUMN `timestamp` int(10) unsigned NOT NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'last_send'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `last_send` int(10) unsigned  NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'renewal_type'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `renewal_type` int(1) unsigned  NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'time_approved'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `time_approved` int(10) unsigned  NULL;");
	}
	/*remove table rating*/
	if ($oDatabase->tableExists(Phpfox::getT('directory_rating')))
	{
	   $oDatabase->query("DROP TABLE `".Phpfox::getT('directory_rating')."`;");
	}
	//add column paytype
	if (!$oDatabase->isField(Phpfox::getT('directory_invoice'), 'pay_type'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_invoice')."` ADD COLUMN `pay_type` varchar(255)  NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'timestamp_claimrequest'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `timestamp_claimrequest` int(10) unsigned NOT NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('directory_invoice'), 'param'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_invoice')."` ADD COLUMN `param` text  DEFAULT NULL;");
	}
	
	if (!$oDatabase->isField(Phpfox::getT('directory_invoice'), 'payment_method'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_invoice')."` ADD COLUMN `payment_method` varchar(255)  NULL;");
	}


	if (!$oDatabase->isField(Phpfox::getT('directory_business_userroledata'), 'time_stamp'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business_userroledata')."` ADD COLUMN `time_stamp` int(10)  NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'total_review'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `total_review` int(10)  NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'total_checkin'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `total_checkin` int(10)  NULL;");
	}

// SHOULD BE UPDATED
// - rating
// 
	$oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_checkinhere') ."` (
		  `checkinhere_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `business_id` int(10) unsigned NOT NULL,
		  `user_id` int(10) unsigned NOT NULL,
		  `timestamp` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`checkinhere_id`)
		) AUTO_INCREMENT=1;
	");

	$oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('directory_cronlog') ."` (
		  `cronlog_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `type` enum('default') DEFAULT 'default',
		  `timestamp` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`cronlog_id`)
		) AUTO_INCREMENT=1;
	");


	if (!$oDatabase->isField(Phpfox::getT('directory_business'), 'is_send_renewal'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('directory_business')."` ADD COLUMN `is_send_renewal` tinyint(1) NOT NULL DEFAULT '0';");
	}

	// remove some features (request by BA in developement progress)
    $oDatabase->delete(Phpfox::getT('directory_package_setting'), 'setting_name = \'allow_business_owner_to_change_orders_of_pages\'');
    $oDatabase->delete(Phpfox::getT('directory_package_setting'), 'setting_name = \'allow_business_to_have_multiple_owners\'');
    $oDatabase->delete(Phpfox::getT('directory_module'), 'module_name = \'discussion\'');

    //add field for user in user table
    if(!$oDatabase->isField(Phpfox::getT('user_field'),'total_directory'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_field')."` ADD  `total_directory` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }

    if(!$oDatabase->isField(Phpfox::getT('user_activity'),'activity_directory'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_activity')."` ADD  `activity_directory` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }

    //remove memberrolesetting
    $oDatabase->delete(Phpfox::getT('directory_business_memberrolesetting'), 'setting_name = \'view_page_insight_dashboard\'');


}


ynd_install301();

?>