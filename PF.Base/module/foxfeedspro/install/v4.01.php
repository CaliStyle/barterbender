<?php
defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

// Favorite Table
$oDb -> query("CREATE TABLE IF NOT EXISTS `".PHPFOX::getT("ynnews_favorite")."` (
  	`favorite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  	`type_id` varchar(75) NOT NULL,
  	`item_id` int(10) unsigned NOT NULL,
  	`user_id` int(10) unsigned NOT NULL,
	`time_stamp` int(10) unsigned NOT NULL,
  	PRIMARY KEY (`favorite_id`),
  	KEY `type_id` (`type_id`,`item_id`,`user_id`),
  	KEY `user_id` (`user_id`),
  	KEY `favorite_id` (`favorite_id`,`user_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1;"
);

$oDb -> query("DELETE FROM `".PHPFOX::getT("user_group_setting")."` WHERE `product_id` = 'foxfeedspro' AND `name` = 'can_add_rss_provider_in_pages';"
);
?>