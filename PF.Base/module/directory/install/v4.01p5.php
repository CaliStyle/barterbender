<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 4.01p5
 * @by minhnc
 *
 */

function ynd_install401p5()
{
	$oDatabase = Phpfox::getLib('database') ;
	$oDatabase->query("
		INSERT IGNORE INTO `".Phpfox::getT('directory_module')."`(`module_id`, `module_phrase`, `module_name`, `module_type`, `module_description`, `module_landing`) VALUES
			(8, '{phrase var=&#039;directory.videos&#039;}', 'videos', 'module', '', 0),
			(9, '{phrase var=&#039;directory.musics&#039;}', 'musics', 'module', '', 0),
			(12, '{phrase var=&#039;directory.polls&#039;}', 'polls', 'module', '', 0),
			(13, '{phrase var=&#039;directory.coupons&#039;}', 'coupons', 'module', '', 0),
			(15, '{phrase var=&#039;directory.jobs&#039;}', 'jobs', 'module', '', 0),
			(16, '{phrase var=&#039;directory.marketplace&#039;}', 'marketplace', 'module', '', 0);
			");
}

ynd_install401p5();

?>