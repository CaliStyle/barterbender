<?php

defined('PHPFOX') or exit('NO DICE!');

function ynauction_install301()
{
    $oDatabase = Phpfox::getLib('database') ;


    $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_product_auction')."` (
			`auction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`product_id` int(10) unsigned NOT NULL,
			`auction_item_reserve_price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
			`auction_item_buy_now_price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
			`auction_total_bid` int(10) unsigned NULL DEFAULT  '0',
			`auction_total_offer` int(10) unsigned NULL DEFAULT  '0',
			`auction_total_buyitnow` int(10) unsigned NULL DEFAULT  '0',
			`auction_won_bidder_user_id` int(10) unsigned NULL,
			`allow_contact_if_end` tinyint(1) default NULL,
			`is_hide_reserve_price` tinyint(1) default NULL,
			`receive_notification_someone_bid` tinyint(1) default NULL,
			PRIMARY KEY (`auction_id`)
	    )
 	");

    $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_auction_bid')."` (
			`auctionbid_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`auctionbid_user_id` int(10) unsigned NOT NULL,
			`auctionbid_product_id` int(10) unsigned NOT NULL,
			`auctionbid_ip_address` varchar(50) DEFAULT NULL,
			`auctionbid_price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
			`auctionbid_creation_datetime` INT(10) UNSIGNED NOT NULL,
			`auctionbid_modification_datetime` INT(10) UNSIGNED NOT NULL,

			`auctionbid_status` TINYINT(2) DEFAULT '0' COMMENT '',

			PRIMARY KEY (`auctionbid_id`)
	    )
 	");


    $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_auction_offer')."` (
			`auctionoffer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`auctionoffer_user_id` int(10) unsigned NOT NULL,
			`auctionoffer_product_id` int(10) unsigned NOT NULL,
			`auctionoffer_ip_address` varchar(50) DEFAULT NULL,
			`auctionoffer_price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
			`auctionoffer_creation_datetime` INT(10) UNSIGNED NOT NULL,
			`auctionoffer_modification_datetime` INT(10) UNSIGNED NOT NULL,

			`auctionoffer_status` TINYINT(2) DEFAULT '0' COMMENT '',

			PRIMARY KEY (`auctionoffer_id`)
	    )
 	");


    $oDatabase->query("
	    CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_auction_global_setting')."` (
			`setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`default_setting` text DEFAULT NULL COMMENT 'array with json encode,',
			`actual_setting` text DEFAULT NULL COMMENT 'array with json encode,',
			 PRIMARY KEY (`setting_id`)
	    )
 	");


    $oDatabase->query("
        CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_bid_increasement')."` (
         `data_id` int(11) NOT NULL AUTO_INCREMENT,
         `category_id` int(11) NOT NULL,
         `data_increasement` text NULL,
         `user_id` int(11) DEFAULT NULL,
         `type_increasement` enum('default','user') DEFAULT 'default',
         `create_timestamp` int(11) NULL,
         `update_timestamp` int(11) NULL,
         PRIMARY KEY (`data_id`)
        );
    ");
    
    if (!$oDatabase->isField(Phpfox::getT('ecommerce_product_auction'), 'auction_latest_bidder'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_product_auction')."` ADD COLUMN `auction_latest_bidder` INT NULL DEFAULT NULL;");
	}
	if (!$oDatabase->isField(Phpfox::getT('ecommerce_product_auction'), 'auction_number_transfer'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_product_auction')."` ADD COLUMN `auction_number_transfer` INT NULL DEFAULT NULL;");
	}

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_product_auction'), 'auction_latest_bid_price'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_product_auction')."` ADD COLUMN `auction_latest_bid_price` DECIMAL(14,2) NOT NULL DEFAULT '0.00';");
	}
    
    if (!$oDatabase->isField(Phpfox::getT('ecommerce_product_auction'), 'auction_won_bid_price'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_product_auction')."` ADD COLUMN `auction_won_bid_price` DECIMAL(14,2) NOT NULL DEFAULT '0.00';");
	}
    
    if (!$oDatabase->isField(Phpfox::getT('user_field'), 'total_auction'))
	{
	   $oDatabase->query("ALTER TABLE `".Phpfox::getT('user_field')."` ADD COLUMN `total_auction` INT(10) UNSIGNED NOT NULL DEFAULT '0';");
	}

    if (!$oDatabase->isField(Phpfox::getT('user_activity'), 'activity_auction'))
    {
       $oDatabase->query("ALTER TABLE `".Phpfox::getT('user_activity')."` ADD COLUMN `activity_auction` INT(10) UNSIGNED NOT NULL DEFAULT '0';");
    }
	
   //ecommerce_auction_sellersetting
    $oDatabase->query("
        CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ecommerce_auction_sellersetting')."` (
              `setting_id` int(10) NOT NULL AUTO_INCREMENT,
              `user_id` int(10) NOT NULL,
              `data_seller_setting` text,
              PRIMARY KEY (`setting_id`)
            );
   ");

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_auction_offer'), 'auctionoffer_currency'))
    {
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_auction_offer') . "`
            ADD COLUMN `auctionoffer_currency` varchar(3) NULL;");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_auction_bid'), 'auctionbid_currency'))
    {
        $oDatabase->query("ALTER TABLE `" . Phpfox::getT('ecommerce_auction_bid') . "`
            ADD COLUMN `auctionbid_currency` varchar(3) NULL;");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_auction_offer'), 'auctionoffer_approved_datetime'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_auction_offer')."` ADD COLUMN `auctionoffer_approved_datetime` INT(10) UNSIGNED  NULL;");
    }

    $aRow = $oDatabase->select('block_id')
        ->from(Phpfox::getT('block'))
        ->where("m_connection ='auction.profile' AND product_id = 'younet_auction' AND module_id ='profile' AND component ='pic'")
        ->execute('getRow');

    if(!isset($aRow['block_id']))
    {
        // insert the pic block for viewing in profile
        $oDatabase->query("INSERT INTO `".Phpfox::getT('block')."` (`title`, `type_id`, `m_connection`, `module_id`, `product_id`, `component`, `location`, `is_active`, `ordering`, `disallow_access`, `can_move`, `version_id`) VALUES ('Profile Photo &amp; Menu', 0, 'auction.profile', 'profile', 'younet_auction', 'pic', '1', 1, 1, NULL, 0, NULL)");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_auction_bid'), 'auctionbid_total_like'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_auction_bid')."` ADD COLUMN `auctionbid_total_like` INT(10) UNSIGNED  NULL;");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_auction_bid'), 'auctionbid_total_comment'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_auction_bid')."` ADD COLUMN `auctionbid_total_comment` INT(10) UNSIGNED  NULL;");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_product_auction'), 'auction_won_bid_total_like'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_product_auction')."` ADD COLUMN `auction_won_bid_total_like` INT(10) UNSIGNED  NULL;");
    }

    if (!$oDatabase->isField(Phpfox::getT('ecommerce_product_auction'), 'auction_won_bid_total_comment'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ecommerce_product_auction')."` ADD COLUMN `auction_won_bid_total_comment` INT(10) UNSIGNED  NULL;");
    }


}


ynauction_install301();

?>