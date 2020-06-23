<?php

//      start
defined('PHPFOX') or exit('NO DICE!');
Phpfox_Error::skip(true);

//     v3.01 --------------------------------------------------------------------
/**
 * CREATE TABLEs
 */
function createProfilePopupItemTable()
{
        $sTable = Phpfox::getT('profilepopup_item');
        $oDB = Phpfox::getLib('phpfox.database');
        if (!$oDB->tableExists($sTable))
        {
                $oDB->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('profilepopup_item') . "` (
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
                      );
        ");
                $sql = "INSERT IGNORE INTO `" . $sTable . "` (`item_id`, `is_custom_field`, `group_id`, `field_id`, `name`, `phrase_var_name`, `is_active`, `is_display`, `ordering`, `item_type`) VALUES
          (NULL, '0', NULL, NULL, 'first_name', 'pp_item_first_name', '1', '1', '1', 'user'),
          (NULL, '0', NULL, NULL, 'last_name', 'pp_item_last_name', '1', '1', '2', 'user'),
          (NULL, '0', NULL, NULL, 'gender', 'pp_item_gender', '1', '1', '3', 'user'),
          (NULL, '0', NULL, NULL, 'birthday', 'pp_item_birthday', '1', '1', '4', 'user'),
          (NULL, '0', NULL, NULL, 'relationship_status', 'pp_item_relationship_status', '1', '1', '5', 'user'),
          (NULL, '0', NULL, NULL, 'status', 'pp_item_status', '1', '1', '6', 'user'),
          (NULL, '0', NULL, NULL, 'category_name', 'pp_item_category', '1', '1', '1', 'pages'),
          (NULL, '0', NULL, NULL, 'total_like', 'pp_item_total_of_likes', '1', '1', '2', 'pages'),
          (NULL, '0', NULL, NULL, 'categories', 'pp_item_category', '1', '1', '1', 'event'),
          (NULL, '0', NULL, NULL, 'event_date', 'pp_item_time', '1', '1', '2', 'event'),
          (NULL, '0', NULL, NULL, 'location', 'pp_item_location', '1', '1', '3', 'event'),
          (NULL, '0', NULL, NULL, 'total_of_members', 'pp_item_total_of_members', '1', '1', '4', 'event');";

                $oDB->query($sql);
        }
}

/**
 * UPDATE COLUMNs TABLEs (ADD/UPDATE/DELETE) WHICH ARE CORE OR OTHERs
 */
/**
 * INSERT DATA 
 */
/**
 * EXECUTE
 */
createProfilePopupItemTable();

//      end 
Phpfox_Error::skip(false);
