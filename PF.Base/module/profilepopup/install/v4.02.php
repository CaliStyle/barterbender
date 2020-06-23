<?php

function ynpp_install402()
{
    $oDb = Phpfox::getLib('database');
    $oDb->query("ALTER TABLE `" . Phpfox::getT('profilepopup_item') . "` CHANGE COLUMN `item_type` `item_type` ENUM('user', 'pages', 'event', 'groups');");
    $oDb->query("INSERT INTO `" . Phpfox::getT('profilepopup_item') . "` (`item_id`, `is_custom_field`, `group_id`, `field_id`, `name`, `phrase_var_name`, `is_active`, `is_display`, `ordering`, `item_type`) VALUES
(NULL, 0, NULL, NULL, 'cover_photo', 'pp_item_cover_photo', 1, 1, 1, 'groups'),
(NULL, 0, NULL, NULL, 'category_name', 'pp_item_category', 1, 1, 2, 'groups'),
(NULL, 0, NULL, NULL, 'total_like', 'pp_item_total_members', 1, 1, 3, 'groups');");
    $oDb->update(':module',['menu' => 'a:5:{s:39:"profilepopup.admin_menu_global_settings";a:1:{s:3:"url";a:2:{i:0;s:12:"profilepopup";i:1;s:4:"user";}}s:45:"profilepopup.admin_menu_pages_global_settings";a:1:{s:3:"url";a:2:{i:0;s:12:"profilepopup";i:1;s:5:"pages";}}s:45:"profilepopup.admin_menu_event_global_settings";a:1:{s:3:"url";a:2:{i:0;s:12:"profilepopup";i:1;s:5:"event";}}s:46:"profilepopup.admin_menu_groups_global_settings";a:1:{s:3:"url";a:2:{i:0;s:12:"profilepopup";i:1;s:6:"groups";}}s:43:"profilepopup.admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:12:"profilepopup";i:1;s:19:"user-group-settings";}}}'],'module_id = \'profilepopup\'');
}

ynpp_install402();

