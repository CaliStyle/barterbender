<?php

// New set of items from v4
function profilepopup_new_items()
{
    $oDb = Phpfox::getLib('database');

    $oDb->query("DROP TABLE IF EXISTS `" . Phpfox::getT('profilepopup_item') . "`;");

    $oDb->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('profilepopup_item') . "` (
  `item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_custom_field` tinyint(1) NOT NULL DEFAULT '0',
  `group_id` int(10) unsigned DEFAULT NULL,
  `field_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `phrase_var_name` varchar(250) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_display` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` tinyint(1) NOT NULL DEFAULT '0',
  `item_type` enum('user','pages','event') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`item_id`)
);");

    $oDb->query("INSERT INTO `" . Phpfox::getT('profilepopup_item') . "` (`item_id`, `is_custom_field`, `group_id`, `field_id`, `name`, `phrase_var_name`, `is_active`, `is_display`, `ordering`, `item_type`) VALUES
(NULL, 0, NULL, NULL, 'cover_photo', 'pp_item_cover_photo', 1, 1, 1, 'user'),
(NULL, 0, NULL, NULL, 'gender', 'pp_item_gender', 1, 1, 2, 'user'),
(NULL, 0, NULL, NULL, 'birthday', 'pp_item_birthday', 1, 1, 3, 'user'),
(NULL, 0, NULL, NULL, 'relationship_status', 'pp_item_relationship_status', 1, 1, 4, 'user'),
(NULL, 0, NULL, NULL, 'status', 'pp_item_status', 1, 1, 5, 'user'),
(NULL, 0, NULL, NULL, 'cover_photo', 'pp_item_cover_photo', 1, 1, 1, 'pages'),
(NULL, 0, NULL, NULL, 'category_name', 'pp_item_category', 1, 1, 2, 'pages'),
(NULL, 0, NULL, NULL, 'total_like', 'pp_item_total_of_likes', 1, 1, 3, 'pages'),
(NULL, 0, NULL, NULL, 'cover_photo', 'pp_item_cover_photo', 1, 1, 1, 'event'),
(NULL, 0, NULL, NULL, 'categories', 'pp_item_category', 1, 1, 2, 'event'),
(NULL, 0, NULL, NULL, 'event_date', 'pp_item_time', 1, 1, 3, 'event'),
(NULL, 0, NULL, NULL, 'location', 'pp_item_location', 1, 1, 4, 'event'),
(NULL, 0, NULL, NULL, 'total_of_members', 'pp_item_total_of_members', 1, 1, 5, 'event');");
}

profilepopup_new_items();
