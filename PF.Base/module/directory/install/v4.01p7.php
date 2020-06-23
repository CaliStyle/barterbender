<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 4.01p5
 * @by minhnc
 *
 */

function ynd_install401p7()
{
	$oDatabase = Phpfox::getLib('database') ;

	$oDatabase->delete(Phpfox::getT('user_group_setting'),'module_id = "directory" AND name = "can_review_business"');
	$oDatabase->query("DELETE FROM `" . Phpfox::getT('menu') . "` WHERE `module_id`='directory' AND `var_name`='module_menu';");
	$oDatabase->query("
	INSERT IGNORE INTO `".Phpfox::getT('directory_module')."`(`module_id`, `module_phrase`, `module_name`, `module_type`, `module_description`, `module_landing`) VALUES
		(19, '{phrase var=&#039;directory.ultimate_videos&#039;}', 'ultimatevideo', 'module', '', 0);
		");

	$oDatabase->query("UPDATE `". Phpfox::getT('setting')."` SET `is_hidden` = 1 WHERE `module_id` = 'directory' AND `var_name` = 'google_api_key_location';");

}

ynd_install401p7();

?>