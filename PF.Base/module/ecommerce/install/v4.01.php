<?php

/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: ajax.php 2771 2011-07-30 19:34:11Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

function ynecommerce_install301()
{
    $oDatabase = Phpfox::getLib('database') ;

    if (!$oDatabase->tableExists(Phpfox::getT('ecommerce_category')))
    {
	    $oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ecommerce_category') ."` (
				`category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT ,
				`parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
				`is_active` tinyint(1) NOT NULL DEFAULT '0',
				`title` varchar(255) NOT NULL,
				`time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
				`used` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of items relates this category',
				`ordering` int(11) unsigned NOT NULL DEFAULT '0',
				`image_path` varchar(255) DEFAULT NULL,
				`server_id` tinyint(3) NOT NULL DEFAULT '0',

				PRIMARY KEY (`category_id`),
				KEY `parent_id` (`parent_id`,`is_active`),
				KEY `is_active` (`is_active`)			
			)  AUTO_INCREMENT=1 ;
		");

		$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('ecommerce_category')."`(`category_id`, `title`, `parent_id`, `time_stamp`, `used`, `is_active`) VALUES
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
    
    if (!$oDatabase->tableExists(Phpfox::getT('ecommerce_email_template_data'))){
	    $oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ecommerce_email_template_data') ."` (
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
	    	INSERT INTO `".Phpfox::getT('ecommerce_email_template_data')."` (`data_id`, `language_id`, `email_template_id`, `email_subject`, `email_template`, `email_template_parsed`) VALUES
			(1, 'en', 1, 'Someone Start Bidding on your Auction', 'Dear [receiver_name],\r\n\r\nUsers have already started bidding on your following auction:\r\n\r\nAuction Name : [product_name]\r\nBidder : [user_name]\r\nCurrent Bid : [symbol_currency][amount]\r\nBid on : [date_time]\r\n\r\nFor more details, view your auction Bid History at [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />Users have already started bidding on your following auction:\r<br />\r<br />Auction Name : [product_name]\r<br />Bidder : [user_name]\r<br />Current Bid : [symbol_currency][amount]\r<br />Bid on : [date_time]\r<br />\r<br />For more details, view your auction Bid History at [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(2, 'en', 2, 'You have been outbid. Bid again now!', 'Dear [receiver_name],\r\n\r\nThere''s new highest bid on this item, but there''s still a chance to make it yours. Increase your bid to have a chance at winning:\r\n\r\nAuction Name : [product_name]\r\nSeller : [user_name]\r\nCurrent Bid : [symbol_currency][amount]\r\nEnd Date : [date_time]\r\n\r\n\r\nView the auction you''re bidding on: [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />There''s new highest bid on this item, but there''s still a chance to make it yours. Increase your bid to have a chance at winning:\r<br />\r<br />Auction Name : [product_name]\r<br />Seller : [user_name]\r<br />Current Bid : [symbol_currency][amount]\r<br />End Date : [date_time]\r<br />\r<br />\r<br />View the auction you''re bidding on: [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(3, 'en', 3, 'Your Auction has Ended', 'Dear [receiver_name],\r\n\r\nThe following auction has ended, please go to auction details page for more information:\r\n\r\nAuction Name : [product_name]\r\nHighest Bid : [symbol_currency][amount]\r\nEnd on : [date_time]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />The following auction has ended, please go to auction details page for more information:\r<br />\r<br />Auction Name : [product_name]\r<br />Highest Bid : [symbol_currency][amount]\r<br />End on : [date_time]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(4, 'en', 4, 'Congratulations, you Won!', 'Dear [receiver_name],\r\n\r\nCongratulations, you have won the following auction:\r\n\r\nAuction Name : [product_name]\r\nBy : [user_name]\r\nHighest Bid : [symbol_currency][amount]\r\n[number_bids] bids\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />Congratulations, you have won the following auction:\r<br />\r<br />Auction Name : [product_name]\r<br />By : [user_name]\r<br />Highest Bid : [symbol_currency][amount]\r<br />[number_bids] bids\r<br />\r<br />Regards,\r<br />[site_name]'),
			(5, 'en', 5, 'Bidding has Ended', 'Dear [receiver_name],\r\n\r\nYou have been outbid on the following auction and it has been already ended:\r\n\r\nAuction Name : [product_name]\r\nBy : [user_name]\r\nHighest Bid : [symbol_currency][amount]\r\n[number_bids] bids\r\n\r\nFor more details, please access to ''Didn''t win'' page at : [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />You have been outbid on the following auction and it has been already ended:\r<br />\r<br />Auction Name : [product_name]\r<br />By : [user_name]\r<br />Highest Bid : [symbol_currency][amount]\r<br />[number_bids] bids\r<br />\r<br />For more details, please access to ''Didn''t win'' page at : [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(6, 'en', 6, 'Auction have been Transferred', 'Dear [receiver_name],\r\n\r\nThe following auction which you won has been transferred to other bidders:\r\n\r\nAuction Name : [product_name]\r\nSeller : [user_name]\r\nHighest Bid : [symbol_currency][amount]\r\nEnd on : [date_time]\r\n\r\n\r\nPlease contact the seller or access to ''Didn''t win'' page at [url] for more details.\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />The following auction which you won has been transferred to other bidders:\r<br />\r<br />Auction Name : [product_name]\r<br />Seller : [user_name]\r<br />Highest Bid : [symbol_currency][amount]\r<br />End on : [date_time]\r<br />\r<br />\r<br />Please contact the seller or access to ''Didn''t win'' page at [url] for more details.\r<br />\r<br />Regards,\r<br />[site_name]'),
			(7, 'en', 7, 'Auction have been Transferred', 'Dear [receiver_name],\r\n\r\nThe following auction has been transferred to another winning bidder:\r\n\r\nAuction Name : [product_name]\r\nBuyer: [user_name]\r\nHighest Bid : [symbol_currency][amount]\r\nEnd on : [date_time]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />The following auction has been transferred to another winning bidder:\r<br />\r<br />Auction Name : [product_name]\r<br />Buyer: [user_name]\r<br />Highest Bid : [symbol_currency][amount]\r<br />End on : [date_time]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(8, 'en', 8, 'Offer Received', 'Dear [receiver_name],\r\n\r\nYou have received offer for the following auction:\r\n\r\nAuction Name : [product_name]\r\nOffer Price: [symbol_currency][amount]\r\nOffer By : [user_name]\r\n\r\nFor more details, view your auction Offer History at [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />You have received offer for the following auction:\r<br />\r<br />Auction Name : [product_name]\r<br />Offer Price: [symbol_currency][amount]\r<br />Offer By : [user_name]\r<br />\r<br />For more details, view your auction Offer History at [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(9, 'en', 9, 'You have made a Best Offer', 'Dear [receiver_name],\r\n\r\nYou''ve made the Best Offer and your offer have been approved on this following auction:\r\n\r\nAuction Name : [product_name]\r\nBy : [user_name]\r\nOffered Price: [symbol_currency][amount]\r\n\r\nFor more details, view your auction Offer History at [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />You''ve made the Best Offer and your offer have been approved on this following auction:\r<br />\r<br />Auction Name : [product_name]\r<br />By : [user_name]\r<br />Offered Price: [symbol_currency][amount]\r<br />\r<br />For more details, view your auction Offer History at [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(10, 'en', 10, 'Your offer have been denied', 'Dear [receiver_name],\r\n\r\nYour offer on the following auction have been denied:\r\n\r\nAuction Name : [product_name]\r\nBy : [user_name]\r\nOffered Price: [symbol_currency][amount]\r\n\r\nFor more details, view your auction Offer History at [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />Your offer on the following auction have been denied:\r<br />\r<br />Auction Name : [product_name]\r<br />By : [user_name]\r<br />Offered Price: [symbol_currency][amount]\r<br />\r<br />For more details, view your auction Offer History at [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(11, 'en', 11, 'Congratulations, your item sold!', 'Dear [receiver_name],\r\n\r\nCongratulations, the following item just sold:\r\n\r\n[lists_item]\r\n\r\nFor more details, please access to ''Manage Orders'' page at [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />Congratulations, the following item just sold:\r<br />\r<br />[lists_item]\r<br />\r<br />For more details, please access to ''Manage Orders'' page at [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(12, 'en', 12, 'You have bought the item', 'Dear [receiver_name],\r\n\r\nYou have made the order on this item:\r\n\r\n[lists_item]\r\n\r\nFor more details, please access to ''My Orders'' page at [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />You have made the order on this item:\r<br />\r<br />[lists_item]\r<br />\r<br />For more details, please access to ''My Orders'' page at [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(13, 'en', 13, 'Order Updated', 'Dear [receiver_name],\r\n\r\nThe order #[order_id] have been updated.\r\nFor more details, please access to ''My Orders'' page at [url]\r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />The order #[order_id] have been updated.\r<br />For more details, please access to ''My Orders'' page at [url]\r<br />\r<br />Regards,\r<br />[site_name]'),
			(14, 'en', 14, 'Your auction has been approved', 'Dear [receiver_name],\r\n\r\nCongratulation! Your auction [product_name] has just been approved. \r\n\r\nRegards,\r\n[site_name]', 'Dear [receiver_name],\r<br />\r<br />Congratulation! Your auction [product_name] has just been approved. \r<br />\r<br />Regards,\r<br />[site_name]');
		");
    }

    //ynecommerce_email_queue
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ecommerce_email_queue') ."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `product_id` int(10) unsigned NOT NULL,
			 `receivers` mediumtext DEFAULT NULL,
			 `from` varchar(255) DEFAULT NULL ,
			 `email_subject` mediumtext DEFAULT NULL,
			 `time_stamp` int(10) unsigned NOT NULL,
			 `email_message` mediumtext DEFAULT NULL,
			 `is_sent`  tinyint(1) NOT NULL DEFAULT '0',
			 `is_site_user`  tinyint(1) NOT NULL DEFAULT '1',
			 PRIMARY KEY (`id`),
			 KEY `is_sent` (`is_sent`),
			 KEY `product_id` (`product_id`)
		)  AUTO_INCREMENT=1 ;
	");
	
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ecommerce_category_data') ."` (
			`data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`category_id` int(10) unsigned NOT NULL ,
			`product_id` int(10) unsigned NOT NULL ,
			`is_main` tinyint(1) NOT NULL DEFAULT '0',

			PRIMARY KEY (`data_id`)
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query(
    "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ecommerce_category_customgroup_data") . "` (
			`category_id` int(10) NOT NULL,
			`group_id` int(10) NOT NULL,

			PRIMARY KEY  (`category_id`,`group_id`)
		);"
	 );

	$oDatabase->query(
        "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ecommerce_custom_group") . "` (
				`group_id` int(11) NOT NULL auto_increment,
				`phrase_var_name` varchar(250) default NULL,
				`is_active` tinyint(1) default '1',
				`ordering` tinyint(3) default '0',

				PRIMARY KEY  (`group_id`)
			);"
    );

    $oDatabase->query(
        "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ecommerce_custom_field") . "` (
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

    $oDatabase->query(
        "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ecommerce_custom_option") . "` (
			`option_id` int(10) unsigned NOT NULL auto_increment,
			`field_id` int(10) unsigned NOT NULL,
			`phrase_var_name` varchar(250) NOT NULL,
			PRIMARY KEY  (`option_id`)
			);"
    );

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_custom_value')."` (
		`value_id` int(10) NOT NULL AUTO_INCREMENT,
		`product_id` int(10) NOT NULL,
		`field_id` int(10) NOT NULL,
		`option_id` int(10) DEFAULT NULL,
		`value` text,

		PRIMARY KEY (`value_id`)    	
    )
 	");

    $oDatabase->query(
        "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ecommerce_product") . "` (
        		`product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(10) unsigned NOT NULL,
				`product_creating_type` varchar(255) NOT NULL,
				`uom_id` int(10) unsigned NOT NULL,
				`theme_id` int(10) unsigned NOT NULL,
				`name` varchar(255) NOT NULL,
				`logo_path` varchar(255) DEFAULT NULL,
				`server_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`product_creation_datetime` int(10) unsigned NOT NULL,
				`product_modification_datetime` int(10) unsigned NULL,
				`product_approved_datetime` int(10) unsigned NULL,
				`product_status` enum('draft', 'unpaid', 'pending', 'denied', 'running', 'paused', 'completed', 'deleted', 'approved', 'other') DEFAULT 'draft',
				`feature_day` int(10) unsigned NOT NULL DEFAULT '0',
				`feature_fee` DECIMAL( 14, 2 ) UNSIGNED NOT NULL DEFAULT  '0.00',
				`feature_start_time` INT(10) UNSIGNED NOT NULL,
				`feature_end_time` INT(10) UNSIGNED NOT NULL,
				`creating_item_fee` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
				`creating_item_currency` varchar(3) NULL,
				`start_time` INT(10) UNSIGNED NOT NULL,
				`end_time` INT(10) UNSIGNED NOT NULL,
				`actual_end_time` INT(10) UNSIGNED NOT NULL,
				`privacy` tinyint(1) NOT NULL DEFAULT '0',
				`privacy_comment` tinyint(1) NOT NULL DEFAULT '0',
				`privacy_photo` tinyint(1) NOT NULL DEFAULT '0',
				`privacy_video` tinyint(1) NOT NULL DEFAULT '0',
				`total_comment` int(10) unsigned NOT NULL DEFAULT '0',
				`total_view` int(10) unsigned NOT NULL DEFAULT '0',
				`total_like` int(10) unsigned NOT NULL DEFAULT '0',
				`total_dislike` int(10) unsigned NOT NULL DEFAULT '0',
				`total_watch` int(10) unsigned NOT NULL DEFAULT '0',
				`total_review` int(10) DEFAULT NULL,
				`module_id` varchar(75) NOT NULL DEFAULT 'ecommerce',
				`item_id` int(10) unsigned NOT NULL DEFAULT '0',
				`product_quantity` int(10) unsigned NULL DEFAULT  '0',
				`product_quantity_main` int(10) unsigned NULL DEFAULT  '0',
				`product_price` DECIMAL( 14, 2 ) UNSIGNED NOT NULL DEFAULT  '0.00' COMMENT 'meaning buy it now price',
				PRIMARY KEY (`product_id`)
			);"
    );

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_product_text')."` (
		`text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`product_id` int(10) unsigned NOT NULL,
		`description` mediumtext DEFAULT NULL,
		`description_parsed` mediumtext DEFAULT NULL,
		PRIMARY KEY (`text_id`),
		KEY `product_id` (`product_id`)   	
    )
 	");


 	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_product_usercustomfield')."` (
			`usercustomfield_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`product_id` int(10) unsigned NOT NULL,
			`usercustomfield_title` text NOT NULL,
			`usercustomfield_content` text NOT NULL,
			`usercustomfield_content_parsed` text NOT NULL,
			PRIMARY KEY (`usercustomfield_id`)
 	
	    )
 	");

 	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_review')."` (
			`review_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`product_id` int(10) unsigned NOT NULL,
			`user_id` int(10) unsigned NOT NULL,
			`timestamp` int(10) unsigned NOT NULL,
			`rating` int(10) unsigned NOT NULL,
			`title` varchar(255) NOT NULL,
			`content` text NOT NULL,
			`content_parsed` text NOT NULL,

			PRIMARY KEY (`review_id`)
 	
	    )
 	");

	if (!$oDatabase->tableExists(Phpfox::getT('ecommerce_uom')))
    {
	 	$oDatabase->query("
		    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_uom')."` (
				`uom_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`title` varchar(255) NOT NULL,
				`time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
				`ordering` int(11) unsigned NOT NULL DEFAULT '0',
				`is_active` tinyint(1) NOT NULL DEFAULT '1',

				 PRIMARY KEY (`uom_id`),
				 KEY `is_active` (`is_active`)
		    )
	 	");

	 	$oDatabase->query("
			INSERT INTO `".Phpfox::getT('ecommerce_uom')."` (`uom_id`, `title`, `time_stamp`, `ordering`, `is_active`) VALUES
				(1, 'Kg', 1436345075, 0, 1),
				(2, 'Meter', 1436421429, 0, 1);
			");  

	}
  	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_watch')."` (
			`watch_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`product_id` int(11) unsigned NOT NULL,
			`user_id` int(11) unsigned NOT NULL,
			`time_stamp` int(11) unsigned NOT NULL DEFAULT '0',

			PRIMARY KEY (`watch_id`),
			KEY `user_id` (`user_id`),
			KEY `product_id` (`product_id`)
	    )
 	");

 	 $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_product_image')."` (
			`image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`product_id` int(10) unsigned NOT NULL,
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
			KEY `product_id` (`product_id`)
	    )
 	");	

	
	if (!$oDatabase->tableExists(Phpfox::getT('ecommerce_global_setting'))){

	    $oDatabase->query("
			CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_global_setting')."` (
			`setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`default_setting` text DEFAULT NULL COMMENT 'array with json encode,',
			`actual_setting` text DEFAULT NULL COMMENT 'array with json encode,',
			PRIMARY KEY (`setting_id`)
			)
		"); 
	
		$oDatabase->query("
			INSERT INTO `".Phpfox::getT('ecommerce_global_setting')."` (`setting_id`, `default_setting`, `actual_setting`) VALUES
			(12, '{\"payment_settings\":0,\"payment_gateway_settings\":[\"2checkout\",\"paypal\"]}', '{\"payment_settings\":\"0\",\"publish_item_fee_again\":\"1\"}');
		"); 

	}
	

 	 $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_cronlog')."` (
			`cronlog_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`type` varchar(255) NOT NULL COMMENT 'can be ecommerce, auction, store, ...',
			`timestamp` int(10) unsigned NOT NULL,

			PRIMARY KEY (`cronlog_id`)


	    )
 	");	

 	 $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_feed')."` (
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

	    )
 	");	

 	 $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_feed_comment')."` (
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
	    )
 	");	

 	 $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_email_queue')."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `product_id` int(10) unsigned NOT NULL,
			 `receivers` mediumtext DEFAULT NULL,
			 `from` varchar(255) DEFAULT NULL ,
			 `email_subject` mediumtext DEFAULT NULL,
			 `time_stamp` int(10) unsigned NOT NULL,
			 `email_message` mediumtext DEFAULT NULL,
			 `is_sent`  tinyint(1) NOT NULL DEFAULT '0',
			 `is_site_user`  tinyint(1) NOT NULL DEFAULT '1',

			 PRIMARY KEY (`id`),
			 KEY `is_sent` (`is_sent`),
			 KEY `product_id` (`product_id`)
	    )
 	");	

 	 $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_cart_product')."` (
			`cartproduct_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`cartproduct_cart_id` int(10) unsigned NOT NULL,
			`cartproduct_product_id` int(10) unsigned NOT NULL,
			`cartproduct_quantity` int(10) unsigned NOT NULL,
			`cartproduct_data` text DEFAULT NULL COMMENT 'array with json encode',
			PRIMARY KEY (`cartproduct_id`), 
			KEY `cartproduct_cart_id` (`cartproduct_cart_id`), 
			KEY `cartproduct_product_id` (`cartproduct_product_id`)
	    )
 	");	

	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_cart')."` (
			`cart_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`cart_user_id` int(10) unsigned NOT NULL,
			`cart_creation_datetime` int(10) unsigned NOT NULL,
			`cart_modification_datetime` int(10) unsigned NULL,

			PRIMARY KEY (`cart_id`), 
			KEY `cart_user_id` (`cart_user_id`)
	    )
 	");	

	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_order_product')."` (
			`orderproduct_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`orderproduct_order_id` int(10) unsigned NOT NULL,
			`orderproduct_product_id` int(10) unsigned NOT NULL,

			`orderproduct_product_name` varchar(255) NULL,
			`orderproduct_product_price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
			`orderproduct_product_quantity` int(10) unsigned NULL DEFAULT  '0',
			`orderproduct_final_price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',

			PRIMARY KEY (`orderproduct_id`),
			KEY `orderproduct_order_id` (`orderproduct_order_id`),
			KEY `orderproduct_product_id` (`orderproduct_product_id`)
	    )
 	");	

	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_order')."` (
			`order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(10) unsigned NOT NULL,
			`order_item_count` int(10) unsigned NULL DEFAULT  '0',
			`order_creation_datetime` int(10) unsigned NULL DEFAULT  '0',
			`order_modification_datetime` int(10) unsigned NULL DEFAULT  '0',
			`order_purchase_datetime` int(10) unsigned NULL DEFAULT  '0',
			`order_finished_datetime` int(10) unsigned NULL DEFAULT  '0',
			`order_total_price` decimal(14,2) unsigned NULL DEFAULT '0.00',
			`order_commission_value` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
			`order_commission_type` varchar(50) NULL,
			`order_commission_rate` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
			`order_currency` varchar(3) NULL,

			`order_status` enum('new', 'shipping', 'cancel') DEFAULT 'new',

			`order_note` mediumtext NULL,
			`order_note_parsed` mediumtext NULL,

			`order_customer_name` varchar(255) NULL,
			`order_customer_location_address` varchar(255) NULL,
			`order_customer_location_longitude` varchar(64) DEFAULT '0',
			`order_customer_location_latitude` varchar(64) DEFAULT '0',
			`order_customer_country_iso` char(2) DEFAULT NULL,
			`order_customer_country_child_id` mediumint(8) unsigned NULL DEFAULT '0',
			`order_customer_email` varchar(255),
			`order_customer_city` varchar(255) DEFAULT NULL,
			`order_customer_province` varchar(255) DEFAULT NULL,
			`order_customer_postal_code` varchar(20) DEFAULT NULL,
			`order_customer_phone_number` varchar(255) NULL,

			`order_delivery_name` varchar(255) NULL,
			`order_delivery_location_address` varchar(255) NULL,
			`order_delivery_location_address_2` varchar(255) NULL,
			`order_delivery_location_longitude` varchar(64) DEFAULT '0',
			`order_delivery_location_latitude` varchar(64) DEFAULT '0',
			`order_delivery_country_iso` char(2) DEFAULT NULL,
			`order_delivery_country_child_id` mediumint(8) unsigned NULL DEFAULT '0',
			`order_delivery_email` varchar(255),
			`order_delivery_city` varchar(255) DEFAULT NULL,
			`order_delivery_province` varchar(255) DEFAULT NULL,
			`order_delivery_postal_code` varchar(20) DEFAULT NULL,
			`order_delivery_phone_number` varchar(255) NULL,
			`order_delivery_datetime` int(10) unsigned NULL,

			`order_payment_id` int(10) unsigned NULL,
			`order_payment_method` varchar(50),
			`order_payment_status` enum('initialized','expired','pending','completed','canceled') DEFAULT 'initialized',

			`order_ip_address` varchar(50),

			`order_shipping_price` decimal(14,2) unsigned NULL DEFAULT '0.00',
			`order_shipping_method` varchar(50),

			`order_invoice_id` int(10) unsigned NULL,

			`order_description` mediumtext DEFAULT NULL,
			`order_description_parsed` mediumtext DEFAULT NULL,

			`order_comment_data` varchar(255) DEFAULT NULL,

			PRIMARY KEY (`order_id`), 
			KEY `user_id` (`user_id`)

	    )
 	");	

	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_address')."` (
			`address_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`address_user_id` int(10) unsigned NOT NULL,
			`address_type` enum('buyer', 'seller', 'other') DEFAULT 'other',

			`address_user_name` varchar(255) NULL,
			`address_customer_location_address` varchar(255) NULL,
			`address_customer_location_longitude` varchar(64) DEFAULT '0',
			`address_customer_location_latitude` varchar(64) DEFAULT '0',
			`address_customer_country_iso` char(2) DEFAULT NULL,
			`address_customer_country_child_id` mediumint(8) unsigned NULL DEFAULT '0',

			`address_customer_street` varchar(255) NULL,
			`address_customer_street_2` varchar(255) NULL,
			`address_customer_city` varchar(255) DEFAULT NULL,

			`address_customer_postal_code` varchar(20) DEFAULT NULL,

			`address_customer_country_code` varchar(20) DEFAULT NULL,
			`address_customer_city_code` varchar(20) DEFAULT NULL,
			`address_customer_phone_number` varchar(255) NULL,
			`address_customer_mobile_number` varchar(255) NULL,
			
			`address_customer_email` varchar(255),

			PRIMARY KEY (`address_id`),
			KEY `address_user_id` (`address_user_id`)
	    )
 	");	

	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_payment')."` (
			`payment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`payment_type` enum('one_person', 'more_persons', 'other') DEFAULT 'other',
			`payment_title` varchar(255) NULL,
			`payment_description` mediumtext DEFAULT NULL,
			`payment_description_parsed` mediumtext DEFAULT NULL,
			`payment_data` text DEFAULT NULL COMMENT 'array with json encode',

			PRIMARY KEY (`payment_id`)
	    )
 	");	

 	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_product_searchhistory')."` (
			`searchhistory_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(10) unsigned NOT NULL,
			`product_creating_type` VARCHAR(255) NOT NULL COMMENT 'type is module id of e-commerce extensions',
			`data` text DEFAULT NULL,

			PRIMARY KEY (`searchhistory_id`) 
	    )
 	");	

 	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_creditmoney')."` (
			`creditmoney_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`creditmoney_user_id` INT(10) UNSIGNED NOT NULL,
			`creditmoney_total_amount` DECIMAL(10,2)  DEFAULT '0.00',
			`creditmoney_remain_amount` DECIMAL(10,2)  DEFAULT '0.00',
			`creditmoney_creation_datetime` INT(11) UNSIGNED NOT NULL,
			`creditmoney_modification_datetime` INT(11) UNSIGNED NOT NULL,
			`creditmoney_description` TEXT NOT NULL, 

			PRIMARY KEY (`creditmoney_id`)	
	    )
 	");	
 	
 	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_creditmoneyrequest')."` (

			`creditmoneyrequest_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`creditmoneyrequest_creditmoney_id` INT(10) UNSIGNED NOT NULL,
			`creditmoneyrequest_amount` DECIMAL(10,2)  DEFAULT '0.00',
			`creditmoneyrequest_reason` TEXT NOT NULL,
			`creditmoneyrequest_creation_datetime` INT(11) UNSIGNED NOT NULL, 
			`creditmoneyrequest_status` enum('pending', 'approved', 'rejected') DEFAULT 'pending',
			`creditmoneyrequest_modification_datetime` INT(11) UNSIGNED NOT NULL, 

			PRIMARY KEY (`creditmoneyrequest_id`)	
	    )
 	");	

 	$oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_invoice')."` (
			`invoice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`item_id` int(10) unsigned NOT NULL,
			`item_type` varchar(64) NOT NULL DEFAULT 'auction',
			`type` enum('product','feature', 'store', 'product_feature') DEFAULT 'product',
			`user_id` int(10) unsigned NOT NULL,
			`currency_id` char(3) NOT NULL,
			`price` decimal(14,2) NOT NULL,
			`status` varchar(20) DEFAULT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			`time_stamp_paid` int(10) unsigned NOT NULL DEFAULT '0',
			`invoice_data` text COMMENT 'array with json encode',
			`pay_type` varchar(255) DEFAULT NULL,
			`param` text,
			`payment_method` varchar(255) DEFAULT NULL,

			PRIMARY KEY (`invoice_id`),
			KEY `item_id` (`item_id`),
			KEY `user_id` (`user_id`),
			KEY `listing_id_2` (`item_id`,`status`),
			KEY `listing_id_3` (`item_id`,`user_id`,`status`)
	    )
 	");	

    Phpfox::getLib('database')->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_order_credit')."` (
			`ordercredit_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`ordercredit_order_id` int(10) unsigned NOT NULL,
            `ordercredit_user_id` int(10) unsigned NOT NULL,
            `ordercredit_price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
			`ordercredit_creation_datetime` INT(10) UNSIGNED NOT NULL,
			`ordercredit_modification_datetime` INT(10) UNSIGNED NOT NULL,

			`ordercredit_status` TINYINT(2) DEFAULT '0' COMMENT '',
            
			PRIMARY KEY (`ordercredit_id`) 
	    )
 	");
    
    if (!$oDatabase->isField(Phpfox::getT('ecommerce_creditmoneyrequest'), 'creditmoneyrequest_response'))
	{
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_creditmoneyrequest') . "`
            ADD COLUMN `creditmoneyrequest_response` TEXT NOT NULL;");
    }
    if (!$oDatabase->isField(Phpfox::getT('ecommerce_creditmoneyrequest'), 'user_id'))
	{
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_creditmoneyrequest') . "`
            ADD COLUMN `user_id` INT(10) UNSIGNED NOT NULL;");
    }




	if (!$oDatabase->isField(Phpfox::getT('ecommerce_product_text'), 'shipping'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_product_text')."` ADD COLUMN `shipping` mediumtext NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('ecommerce_product_text'), 'shipping_parsed'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_product_text')."` ADD COLUMN `shipping_parsed` mediumtext  NULL;");
	}

	if (!$oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_type'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_cart_product')."` ADD COLUMN `cartproduct_type`  VARCHAR(10) NOT NULL;" );
	}

	if (!$oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_module'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_cart_product')."` ADD COLUMN `cartproduct_module`  VARCHAR(10) NOT NULL;" );
	}

	if (!$oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_price'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_cart_product')."` ADD COLUMN `cartproduct_price`  decimal(14,2) NOT NULL;" );
	}

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_order'), 'seller_id'))
	{
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_order') . "` ADD COLUMN `seller_id` INT(10) UNSIGNED NOT NULL;");
	}
    
    if (!$oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_currency'))
	{
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_cart_product') . "` ADD COLUMN `cartproduct_currency` VARCHAR(3) NOT NULL;");
	}
    
    if (!$oDatabase->isField(Phpfox::getT('ecommerce_order'), 'order_delivery_mobile_number'))
	{
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_order') . "` ADD COLUMN `order_delivery_mobile_number` VARCHAR(255) NULL DEFAULT NULL AFTER `order_delivery_phone_number`;");
	}
    
    if ($oDatabase->isField(Phpfox::getT('ecommerce_order'), 'order_status'))
	{
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_order') . "` CHANGE COLUMN `order_status` `order_status` ENUM('new','shipped','cancel') NULL DEFAULT 'new' AFTER `order_currency`;;");
	}

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_type'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_cart_product')."` ADD COLUMN `cartproduct_type` ENUM( 'bid', 'buy', 'offer' ) NOT NULL;");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_module'))
    {
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_cart_product') . "` ADD COLUMN `cartproduct_module` VARCHAR(50) NOT NULL;");
    }
    // Update field: add "Bidden" status.
    if ($oDatabase->isField(Phpfox::getT('ecommerce_product'), 'product_status'))
    {
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_product') . "` CHANGE COLUMN `product_status` `product_status` ENUM('draft','unpaid','pending','denied','running','bidden','paused','completed','deleted','approved','other') NULL DEFAULT 'draft';");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_price'))
    {
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_cart_product') . "` ADD COLUMN `cartproduct_price` DECIMAL(10,2) NOT NULL DEFAULT '0.0';");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_product'), 'total_orders'))
    {
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_product') . "` ADD COLUMN `total_orders` INT(10) NOT NULL DEFAULT '0';");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_payment_status'))
    {
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_cart_product') . "` ADD COLUMN `cartproduct_payment_status` VARCHAR( 255 ) NOT NULL DEFAULT 'init';");
    }

    if ($oDatabase->isField(Phpfox::getT('ecommerce_cart_product'), 'product_quantity_remain'))
    {
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_product') . "` CHANGE  `product_quantity_remain`  `product_quantity_main` INT( 10 ) UNSIGNED NULL DEFAULT  '0';");
    }



}
ynecommerce_install301();

?>