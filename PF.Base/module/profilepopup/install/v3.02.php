<?php

//      start
defined('PHPFOX') or exit('NO DICE!');
Phpfox_Error::skip(true);

function ynpp_install302()
{
  $oDB = Phpfox::getLib('phpfox.database');
  
  if (!$oDB->tableExists(Phpfox::getT('profilepopup_module_item')))
  {
    $oDB->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('profilepopup_module_item') . "` (
            `item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `module_id` varchar(75) NOT NULL,
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

    $sql = "INSERT IGNORE INTO `" . Phpfox::getT('profilepopup_module_item') . "` (`item_id`, `module_id`, `is_custom_field`, `group_id`, `field_id`, `name`, `phrase_var_name`, `is_active`, `is_display`, `ordering`, `item_type`) VALUES
      (NULL, 'resume', '0', NULL, NULL, 'currently_work', 'pp_item_r_currently_work', '1', '1', '1', 'user'),
      (NULL, 'resume', '0', NULL, NULL, 'highest_level', 'pp_item_r_highest_level', '1', '1', '2', 'user'),
      (NULL, 'resume', '0', NULL, NULL, 'highest_education', 'pp_item_r_highest_education', '1', '1', '3', 'user'),
      (NULL, 'resume', '0', NULL, NULL, 'phone_number', 'pp_item_r_phone_number', '1', '1', '4', 'user'),
      (NULL, 'resume', '0', NULL, NULL, 'im', 'pp_item_r_im', '1', '1', '5', 'user'),
      (NULL, 'resume', '0', NULL, NULL, 'email', 'pp_item_r_email', '1', '1', '6', 'user'),
      (NULL, 'resume', '0', NULL, NULL, 'categories', 'pp_item_r_categories', '1', '1', '7', 'user');";

    $oDB->query($sql);
  }
  
  //	add cover photo field for User type
  $coverPhoto = $oDB->select("ppi.*")
                    ->from(Phpfox::getT('profilepopup_item'), 'ppi')
                    ->where(' ppi.name = \'cover_photo\' AND ppi.item_type = \'user\'')
                    ->execute('getSlaveRow');
    
  if(isset($coverPhoto) && isset($coverPhoto['item_id'])){
  	//	do nothing  	
  } else {
  	//	add new field
	$sql = "INSERT IGNORE INTO `" . Phpfox::getT('profilepopup_item') . "` (`item_id`, `is_custom_field`, `group_id`, `field_id`, `name`, `phrase_var_name`, `is_active`, `is_display`, `ordering`, `item_type`) VALUES
	  (NULL, '0', NULL, NULL, 'cover_photo', 'pp_item_cover_photo', '1', '1', '1', 'user');";
  	$oDB->query($sql);  	
  }
	
	//	add cover photo field for Pages type
  $coverPhotoPages = $oDB->select("ppi.*")
                    ->from(Phpfox::getT('profilepopup_item'), 'ppi')
                    ->where(' ppi.name = \'cover_photo\' AND ppi.item_type = \'pages\'')
                    ->execute('getSlaveRow');
					
  if(isset($coverPhotoPages) && isset($coverPhotoPages['item_id'])){
  	//	do nothing  	
  } else {
  	//	add new field
	$sql = "INSERT IGNORE INTO `" . Phpfox::getT('profilepopup_item') . "` (`item_id`, `is_custom_field`, `group_id`, `field_id`, `name`, `phrase_var_name`, `is_active`, `is_display`, `ordering`, `item_type`) VALUES
	  (NULL, '0', NULL, NULL, 'cover_photo', 'pp_item_cover_photo', '1', '1', '1', 'pages');";
  	$oDB->query($sql);  	
  }
	
}

/**
 * EXECUTE
 */
ynpp_install302();

//      end 
Phpfox_Error::skip(false);
