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

function ynstore_install401()
{
    $oDatabase = Phpfox::getLib('database') ;

    // create table phpfox_ynstore_store
    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_store') ."` (
        `store_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(10) unsigned NOT NULL COMMENT 'can NOT change',
        `module_id` varchar(75) NOT NULL DEFAULT 'store',
        `item_id` int(10) unsigned NOT NULL DEFAULT '0',
        `package_id` int(10) unsigned NOT NULL,
        `theme_id` int(10) unsigned NOT NULL,
        `name` varchar(255) NOT NULL,
        `logo_path` varchar(255) DEFAULT NULL,
        `cover_path` varchar(255) DEFAULT NULL,
        `position_top` int(10) NOT NULL DEFAULT '0',
        `server_id` tinyint(1) NOT NULL DEFAULT '0',
        `time_stamp` int(10) unsigned NOT NULL,
        `start_time` int(10) unsigned,
        `expire_time` int(10) unsigned,
        `time_update` int(10) unsigned NOT NULL DEFAULT '0',
        `categories` mediumtext NOT NULL COMMENT 'array categories of this store',
        `status` ENUM('draft','pending','public','denied','closed','deleted','expired') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'draft' ,
        `short_description` mediumtext NOT NULL,
        `description` text NOT NULL,
        `return_policy` text,
        `buyer_protection` text,
        `tax` int(10) unsigned DEFAULT '0',
        `business_type` int(2) unsigned DEFAULT '0',
        `established_year` int(4),
        `ship_payment_info` text NULL ,
        `country_iso` char(2) DEFAULT NULL,
        `country_child_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
        `email` varchar(255) DEFAULT NULL,
        `city` varchar(255) DEFAULT NULL,
        `province` varchar(255) DEFAULT NULL,
        `postal_code` varchar(20) DEFAULT NULL,
        `feature_day` int(10) unsigned NOT NULL DEFAULT '0',
        `is_featured` tinyint(1) NOT NULL DEFAULT '0',
        `is_reminded` tinyint(1) NOT NULL DEFAULT '0',
        `feature_fee` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
        `feature_end_time` int(10) unsigned NOT NULL,
        `about` mediumtext,
        `privacy` tinyint(1) NOT NULL DEFAULT '0',
        `total_comment` int(10) unsigned NOT NULL DEFAULT '0',
        `total_view` int(10) unsigned NOT NULL DEFAULT '0',
        `total_share` int(10) unsigned NOT NULL DEFAULT '0',
        `total_follow` int(10) unsigned NOT NULL DEFAULT '0',
        `total_favorite` int(10) unsigned NOT NULL DEFAULT '0',
        `total_products` int(10) unsigned NOT NULL DEFAULT '0',
        `total_orders` int(10) unsigned NOT NULL DEFAULT '0',
        `total_review` int(10) unsigned NOT NULL DEFAULT '0',
        `total_like` int(10) unsigned NOT NULL DEFAULT '0',
        `rating` decimal(4,1) NOT NULL DEFAULT '0.0' COMMENT 'average rating',
        `renew_before` int(2) NOT NULL DEFAULT '1',
        PRIMARY KEY (`store_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1;
    ");

    // create table phpfox_ynstore_comparison
    if (!$oDatabase->tableExists(Phpfox::getT('ynstore_comparison'))){
        $oDatabase->query("
        CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_comparison') ."` (
            `comparison_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `phrase` varchar(255) NOT NULL,
            `field` varchar(255) NOT NULL,
            `for_type` ENUM('store','product') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'store' COMMENT 'comparison for this item',
            `enable` tinyint(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (`comparison_id`, `for_type`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		");

        $oDatabase->query("
        INSERT INTO `".Phpfox::getT('ynstore_comparison')."` (`comparison_id`,`field`, `phrase`, `for_type`, `enable`) VALUES
            (1, 'name', 'ynsocialstore.comparision_product_name', 'product', 1),
            (2, 'rating','ynsocialstore.comparision_product_rating', 'product', 1),
            (3, 'price','ynsocialstore.comparision_product_price', 'product', 1),
            (4, 'total_orders','ynsocialstore.comparision_product_total_orders', 'product', 1),
            (5, 'total_reviews','ynsocialstore.comparision_product_total_reviews', 'product', 1),
            (6, 'total_views','ynsocialstore.comparision_product_total_views', 'product', 1),
            (7, 'seller','ynsocialstore.comparision_product_seller', 'product', 1),
            (8, 'custom_fields','ynsocialstore.comparision_product_custom_fields', 'product', 1),
            (9, 'description','ynsocialstore.comparision_product_description', 'product', 1),
            (1, 'name','ynsocialstore.comparision_store_name', 'store', 1),
            (2, 'rating','ynsocialstore.comparision_store_rating', 'store', 1),
            (3, 'categories','ynsocialstore.comparision_store_main_categories', 'store', 1),
            (4, 'total_products','ynsocialstore.comparision_store_total_products', 'store', 1),
            (5, 'total_orders','ynsocialstore.comparision_store_total_orders', 'store', 1),
            (6, 'total_views','ynsocialstore.comparision_store_total_views', 'store', 1),
            (7, 'total_reviews','ynsocialstore.comparision_store_total_reviews', 'store', 1),
            (8, 'payment_info','ynsocialstore.comparision_store_shiping_and_payment_info', 'store', 1),
            (9, 'policy','ynsocialstore.comparision_store_policy', 'store', 1),
            (10, 'buyer_protection','ynsocialstore.comparision_store_buyer_protection', 'store', 1);
		");
    }

    //ynstore_email_queue
    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_store_package') ."` (
        `package_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `expire_number` int(10) unsigned DEFAULT NULL,
        `fee` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
        `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
        `max_cover_photo` int(10) unsigned DEFAULT '1' COMMENT 'maximum cover photo can be uploaded/displayed',
        `themes` varchar(10) NOT NULL COMMENT 'array of selected theme',
        `max_products` int(10) unsigned DEFAULT '10' COMMENT 'maximum products can be created, 0 is unlimited',
        `max_photo_per_product` int(10) unsigned DEFAULT '10' COMMENT 'maximum photos of each product can be uploaded, 0 is unlimited',
        `feature_store_fee` decimal(14,2) unsigned NOT NULL DEFAULT '1.00',
        `feature_product_fee` decimal(14,2) unsigned NOT NULL DEFAULT '1.00',
        `theme_editable` tinyint(1) NOT NULL DEFAULT '1',
        `enable_attribute` tinyint(1) NOT NULL DEFAULT '1',
        `used` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`package_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_store_following') ."` (
        `follow_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `store_id` int(11) unsigned NOT NULL,
        `user_id` int(11) unsigned NOT NULL,
        `time_stamp` int(10) unsigned NOT NULL,
        PRIMARY KEY (`follow_id`)
    )ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_store_favorite') ."` (
        `favorite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `store_id` int(11) unsigned NOT NULL,
        `user_id` int(11) unsigned NOT NULL,
        `time_stamp` int(10) unsigned NOT NULL,
        PRIMARY KEY (`favorite_id`)
    )ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_store_location') ."` (
        `location_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `store_id` int(10) unsigned NOT NULL,
        `title` varchar(255) NOT NULL,
        `location` varchar(255) DEFAULT NULL,
        `address` varchar(255) NOT NULL,
        `longitude` varchar(64) DEFAULT '0',
        `latitude` varchar(64) DEFAULT '0',
        PRIMARY KEY (`location_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_store_infomation') ."` (
        `info_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of phone, fax, website number',
        `store_id` int(11) unsigned NOT NULL,
        `info` varchar(255) NOT NULL COMMENT 'phone, fax, website number',
        `title` varchar(255) DEFAULT NULL,
        `type` ENUM('phone','fax', 'website','addinfo') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'phone' COMMENT 'Type of this information',
        PRIMARY KEY (`info_id`)
    )ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
    ");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_store_faq') ."` (
        `faq_id` int(10) NOT NULL AUTO_INCREMENT,
        `store_id` int(10) unsigned NOT NULL,
        `question` mediumtext NOT NULL,
        `answer` mediumtext NOT NULL,
        `time_stamp` int(10) NOT NULL,
        `is_active` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`faq_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
    ");


    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ecommerce_product_ynstore') ."` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `product_id` int(10) NOT NULL,
        `product_type` ENUM('physical','digital') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'physical',
        `enable_inventory` tinyint(1) NOT NULL DEFAULT '1',
        `min_order` int(10),
        `max_order` int(10),
        `link` text,
        `discount_type` ENUM('amount','percentage') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'amount',
        `discount_price` decimal(14,2) unsigned NOT NULL default '0.00' COMMENT 'dicount price of product',
        `discount_percentage` decimal(14,2) unsigned NOT NULL default '0.00' COMMENT 'dicount percent of product',
        `discount_start_date` int(10),
        `discount_end_date` int(10),
        `discount_timeless` tinyint(1) NOT NULL DEFAULT '1',
        `total_rating` int(10) unsigned NOT NULL DEFAULT '0',
        `rating` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT 'average rating',
        `attribute_style` TINYINT( 1 ) NOT NULL DEFAULT  '0',
        `attribute_name` VARCHAR( 164 ) DEFAULT NULL,
        `auto_close` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
    ");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ecommerce_product_attribute') ."` (
        `attribute_id` int(10) NOT NULL AUTO_INCREMENT,
        `product_id` int(10) NOT NULL,
        `title` varchar(64) NOT NULL,
        `style` ENUM('text','image','both') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'text',
        `auto_hide` tinyint(1) NOT NULL DEFAULT '1',
        `image_path` VARCHAR(50),
        `color` VARCHAR( 11 ) DEFAULT NULL,
        `quantity` int(10) NOT NULL default '0' COMMENT '0: unlimited',
        `remain` INT( 10 ) NOT NULL DEFAULT  '0',
        `price` decimal(14,2) COMMENT 'price sell for this attribute',
        `description` mediumtext,
        `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`attribute_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
    ");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_feed') ."` (
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
          `content` text,
          `total_view` int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`feed_id`),
          KEY `parent_user_id` (`parent_user_id`),
          KEY `time_update` (`time_update`)
        )  AUTO_INCREMENT=1 ;
    ");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_feed_comment') ."` (
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
        )  ENGINE=InnoDB  DEFAULT CHARSET=latin1;
    ");

    $oDatabase->query("
		CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ynstore_store_review') ."` (
		  `review_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `store_id` int(10) unsigned NOT NULL,
		  `user_id` int(10) unsigned NOT NULL,
		  `time_stamp` int(10) unsigned NOT NULL,
		  `rating` int(10) unsigned NOT NULL,
		  `content` text NOT NULL,
		  PRIMARY KEY (`review_id`)
		) AUTO_INCREMENT=1;
	");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ecommerce_product_ynstore_wishlist') ."` (
        `wishlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `product_id` int(11) unsigned NOT NULL,
        `user_id` int(11) unsigned NOT NULL,
        `time_stamp` int(10) unsigned NOT NULL,
        PRIMARY KEY (`wishlist_id`)
    )ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('ecommerce_product_ynstore_subscribers') ."` (
        `subcriber_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `product_id` int(11) unsigned NOT NULL,
        `email` varchar(255) NOT NULL,
        `is_send` tinyint(1) NOT NULL DEFAULT '0',
        `time_stamp` int(10) unsigned NOT NULL,
        PRIMARY KEY (`subcriber_id`)
    )ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	");

    // Insert rewrite
    $rewrite = Phpfox_Database::instance()->select('*')
        ->from(Phpfox::getT('rewrite'))
        ->where("url = 'ynsocialstore' AND replacement = 'social-store'")
        ->execute('getRow');

    // Check if rewrite is exist we will not insert to table rewrite
    if(isset($rewrite) && empty($rewrite)) {
        $oDatabase->query("INSERT IGNORE INTO `" . Phpfox::getT('rewrite') . "`(url,replacement) VALUES ('ynsocialstore','social-store');");
    }

    if(!$oDatabase->isField(Phpfox::getT('user_activity'),'activity_ynsocialstore_store'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_activity')."` ADD  `activity_ynsocialstore_store` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }
    if(!$oDatabase->isField(Phpfox::getT('user_activity'),'activity_ynsocialstore_product'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_activity')."` ADD  `activity_ynsocialstore_product` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }
    if(!$oDatabase->isField(Phpfox::getT('user_field'),'total_ynsocialstore'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_field')."` ADD  `total_ynsocialstore` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }
    //---Delete below code when build release---//
    if(!$oDatabase->isField(Phpfox::getT('ynstore_store'),'renew_before'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ynstore_store')."` ADD  `renew_before` INT( 2 ) UNSIGNED NOT NULL DEFAULT  '1'");
    }

    if(!$oDatabase->isField(Phpfox::getT('ecommerce_product_ynstore'),'product_type'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_ynstore')."` ADD  `product_type` ENUM('physical','digital') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'physical',");
    }

    if($oDatabase->isField(Phpfox::getT('ynstore_store'),'established_year'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ynstore_store')."` CHANGE COLUMN `established_year` `established_year` int(4)");
    }

    if(!$oDatabase->isField(Phpfox::getT('ecommerce_product_attribute'),'color'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_attribute')."` ADD  `color` VARCHAR( 11 ) DEFAULT NULL");
    }

    if(!$oDatabase->isField(Phpfox::getT('ecommerce_product_attribute'),'server_id'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_attribute')."` ADD  `server_id` tinyint(1) NOT NULL DEFAULT  '0'");
    }

    if($oDatabase->isField(Phpfox::getT('ecommerce_product_attribute'),'image_path'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_attribute')."` CHANGE  `image_path` `image_path` VARCHAR( 256 ) DEFAULT NULL");
    }
    else
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_attribute')."` ADD  `image_path` VARCHAR( 256 ) DEFAULT NULL");
    }

    if(!$oDatabase->isField(Phpfox::getT('ecommerce_product_ynstore'),'attribute_style'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_ynstore')."` ADD  `attribute_style` TINYINT( 1 ) NOT NULL DEFAULT  '0'");
    }
    if(!$oDatabase->isField(Phpfox::getT('ecommerce_product_ynstore'),'attribute_name'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_ynstore')."` ADD  `attribute_name` VARCHAR( 16 ) DEFAULT NULL");
    }
    if(!$oDatabase->isField(Phpfox::getT('ynstore_store_package'),'used'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ynstore_store_package')."` ADD  `used` tinyint(1) NOT NULL DEFAULT '0'");
    }

    if(!$oDatabase->isField(Phpfox::getT('ynstore_store_package'), 'max_photo_per_product'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ynstore_store_package')."` ADD  `max_photo_per_product` int(10) unsigned DEFAULT '10' COMMENT 'maximum photos of each product can be uploaded, 0 is unlimited'");
    }

    //Dont delete below code

    if($oDatabase->isField(Phpfox::getT('ecommerce_product_ynstore'),'attribute_name'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_ynstore')."` CHANGE COLUMN `attribute_name` `attribute_name` varchar(164)");
    }
    if(!$oDatabase->isField(Phpfox::getT('ecommerce_product_attribute'),'remain'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_attribute')."` ADD  `remain` INT( 10 ) NOT NULL DEFAULT  '0'");
    }
    if(!$oDatabase->isField(Phpfox::getT('ecommerce_product_attribute'),'is_deleted'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ecommerce_product_attribute')."` ADD  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'");
    }

    if(!$oDatabase->isField(Phpfox::getT('ynstore_store_location'),'location'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ynstore_store_location')."` ADD  `location` varchar(255) DEFAULT NULL");
    }
}
ynstore_install401();

?>