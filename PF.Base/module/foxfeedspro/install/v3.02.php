<?php
defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

// Category Tables
$oDb -> query ("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ynnews_categories')."` (
	 `category_id` 	int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	 `parent_id` 	int(11)  NOT NULL DEFAULT 0,
	 `is_active` 	tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
	 `name` 	    varchar(255) NOT NULL DEFAULT '',
	 `name_url`		varchar(255) DEFAULT NULL,
	 `time_stamp`	int(11)	UNSIGNED NOT NULL DEFAULT 0,
	 `used` 		int(11) UNSIGNED NOT NULL DEFAULT 0,
	 `ordering` 	int(11) NOT NULL DEFAULT 0, 
	 PRIMARY KEY (`category_id`)
);");

$iCatsCount = $oDb->select('COUNT(*)')->from(Phpfox::getT('ynnews_categories'))->execute('getSlaveField');

if($oDb->isField(PHPFOX::getT("ynnews_categories"),'category_parent_id'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_categories") . "` CHANGE `category_parent_id` `parent_id` int(11)  NOT NULL DEFAULT 0;");
}

if(!$oDb->isField(PHPFOX::getT("ynnews_categories"),'user_id'))
{
	$oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_categories") . "` ADD COLUMN `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0;");
}

if($oDb->isField(PHPFOX::getT("ynnews_categories"),'category_name'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_categories") . "` CHANGE `category_name` `name` varchar(255) NOT NULL DEFAULT '';");
}

if($oDb->isField(PHPFOX::getT("ynnews_categories"),'category_alias'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_categories") . "` CHANGE `category_alias` `name_url` varchar(255) NOT NULL DEFAULT '';");
}

if($oDb->isField(PHPFOX::getT("ynnews_categories"),'category_order'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_categories") . "` CHANGE `category_order` `ordering` int(11) NOT NULL DEFAULT 0;");
}

if(!$oDb->isField(PHPFOX::getT("ynnews_categories"),'used'))
{
	$oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_categories") . "` ADD COLUMN `used` int(11) UNSIGNED NOT NULL DEFAULT 0;");
}

if($iCatsCount == 0)
{
	$oDb->query("
		INSERT IGNORE INTO `".Phpfox::getT('ynnews_categories')."` (`category_id`, `parent_id`, `is_active`, `name`, `name_url`, `time_stamp`, `used`, `ordering`) VALUES
		(1, 0, 1, 'Technology', 'technology', 1359176602, 0, 6),
		(2, 0, 1, 'Health', 'health', 1359176612, 0, 3),
		(3, 0, 1, 'Politics', 'politics', 1359176622, 0, 4),
		(4, 0, 1, 'Business', 'business', 1359712140, 0, 1),
		(5, 0, 1, 'Society', 'society', 1359712155, 0, 5),
		(7, 0, 1, 'Travelling', 'travelling', 1362630451, 0, 7),
		(8, 0, 1, 'Economy', 'economy', 1362630474, 0, 2);
	");	
}

// Category Data Table
$oDb ->query ("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ynnews_category_data')."`(
	`feed_id`		int(11) DEFAULT 0,
	`category_id`	int(11) DEFAULT 0,
	`user_id`		int(11)	DEFAULT 0,
	KEY (`feed_id`,`category_id`)
);");

// News Feed Table
$oDb -> query("CREATE TABLE IF NOT EXISTS `".PHPFOX::getT("ynnews_feeds")."`(
	`feed_id` 				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`feed_name` 			varchar(200) DEFAULT NULL,
	`category_id` 			int(10) DEFAULT NULL,
	`feed_url` 				varchar(500) DEFAULT NULL,
	`time_stamp` 			int(11) DEFAULT 0,
	`time_update` 			int(11) DEFAULT 0,
	`feed_logo`				text,
	`logo_mini_logo` 		text,
	`is_active_logo`		tinyint(1) DEFAULT 1,
	`is_active_mini_logo`	tinyint(1) DEFAULT 1,
	`order_display`			int(11) DEFAULT 1,
	`is_active`				smallint(1) DEFAULT '1',
	`feed_item_display`		int(10) DEFAULT '10',
	`feed_item_display_full`int(10) DEFAULT '3',
	`feed_item_import`		int(11) DEFAULT '10',
	`feed_language`			varchar(12) DEFAULT 'any',
	`feed_alias`			varchar(200) DEFAULT NULL,
	`user_id`				int(11) DEFAULT '0',
	`is_approved`			smallint(1) DEFAULT '0',
	`owner_type`			varchar(50) DEFAULT 'admin',
	`total_item`			int(11) DEFAULT '0',
	PRIMARY KEY (`feed_id`));"
);

if(!$oDb->isField(PHPFOX::getT("ynnews_feeds"),'time_stamp'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_feeds") . "` ADD COLUMN `time_stamp` int(11) DEFAULT ".PHPFOX_TIME.";");
}

if(!$oDb->isField(PHPFOX::getT("ynnews_feeds"),'time_update'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_feeds") . "` ADD COLUMN `time_update` int(11) DEFAULT ".PHPFOX_TIME.";");
}

if(!$oDb->isField(PHPFOX::getT("ynnews_feeds"),'total_item'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_feeds") . "` ADD COLUMN  `total_item` int(11) DEFAULT '0';");
  
  $aFeeds = $oDb->select('feed_id')->from(Phpfox::getT('ynnews_feeds'))->execute('getRows');
  foreach($aFeeds as $aFeed)
  {
  	$iNewsCount = $oDb->select('COUNT(*)')->from(Phpfox::getT('ynnews_items'))->where("feed_id = {$aFeed['feed_id']}")->execute('getSlaveField');
  	
  	if($iNewsCount)
  	{
  		$oDb->update(Phpfox::getT('ynnews_feeds'),array('total_item' => $iNewsCount),"feed_id = {$aFeed['feed_id']}");
  	}
  }
}

$iCatDataCount = $oDb->select('COUNT(*)')->from(Phpfox::getT('ynnews_category_data'))->execute('getSlaveField');

if($iCatDataCount == 0)
{
	$aFeeds = $oDb->select('feed_id, category_id, user_id')->from(Phpfox::getT('ynnews_feeds'))->execute('getSlaveRows');
	if(count($aFeeds)>0)
	{
		foreach($aFeeds as $aFeed)
		{
			$oDb->insert(Phpfox::getT('ynnews_category_data'),$aFeed);
		}
	}
}

// News Items Table
$oDb -> query("CREATE TABLE IF NOT EXISTS `" . phpfox::getT("ynnews_items") . "`(
	`item_id` 				int(11) unsigned NOT NULL AUTO_INCREMENT,
	`feed_id`				int(11) unsigned NOT NULL,
	`owner_type`			varchar(50) DEFAULT 'user',
	`user_id`				int(11) unsigned DEFAULT 1,
	`item_title`			text DEFAULT NULL,
	`item_alias`			text DEFAULT NULL,
	`item_description`		text DEFAULT NULL,
	`item_description_parse`text,
	`item_content`			text DEFAULT NULL,
	`item_content_parse`	text,
	`item_image`			varchar(300) DEFAULT NULL,
	`item_url_detail` 		varchar(300) DEFAULT NULL,
	`item_author`			varchar(200) DEFAULT NULL,
	`item_pubDate`			int(11) DEFAULT 0,
	`item_pubDate_parse` 	varchar(255) DEFAULT NULL,
	`added_time`			int(11) DEFAULT 0,
	`is_active`				tinyint(1) unsigned DEFAULT 1,
	`is_featured`			tinyint(1) unsigned DEFAULT 0,
	`is_approved`			tinyint(1) unsigned DEFAULT 0,
	`is_edited`				tinyint(1) unsigned DEFAULT 0,
	`item_tags`				text,
	`total_view`			int(11) DEFAULT 0,
	`total_comment`			int(11) DEFAULT 0,
	`total_like`			int(11) DEFAULT 0,
	`total_favorite`		int(11) DEFAULT 0,
	PRIMARY KEY (`item_id`));"
);

if($oDb->isField(PHPFOX::getT("ynnews_items"),'count_view'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_items") . "` CHANGE `count_view` `total_view` int(11) DEFAULT 0;");
}

if($oDb->isField(PHPFOX::getT("ynnews_items"),'owner_id'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_items") . "` CHANGE `owner_id` `user_id` int(11) unsigned DEFAULT 1;");
}

if(!$oDb->isField(PHPFOX::getT("ynnews_items"),'total_favorite'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_items") . "` ADD COLUMN `total_favorite` int(11) DEFAULT 0;");
}

if(!$oDb->isField(PHPFOX::getT("ynnews_items"),'item_description_parse'))
{
  $oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_items") . "` ADD `item_description_parse` text;");
  $oDb->query("UPDATE `" . phpfox::getT("ynnews_items") . "` SET `item_description_parse` = `item_description`;");
  $oDb->query("UPDATE `" . phpfox::getT("ynnews_items") . "` SET `item_content_parse` = `item_content`;");
}

$oDb->query("ALTER TABLE `" . phpfox::getT("ynnews_items") . "` CHANGE `item_pubDate` `item_pubDate` int(11) DEFAULT 0;");

$oDb->query("CREATE TABLE IF NOT EXISTS `".phpfox::getT("ynnews_subscribes")."`(
	`subscribe_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`feed_id` int(11) unsigned NOT NULL,
	`user_id` int(11) unsigned NOT NULL,
	`time_stamp`int(11)	UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`subscribe_id`));
");

?>