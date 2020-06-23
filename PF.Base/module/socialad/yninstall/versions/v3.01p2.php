<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsocialad301p2install () {
	$oDatabase = Phpfox::getLib('phpfox.database');
	if (!$oDatabase->tableExists(Phpfox::getT('socialad_package_placement_module')))
	{
		// insert new tables 
		$oDatabase -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialad_package_placement_module')."` (
			`package_placement_module_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`package_id` INT(10) UNSIGNED NOT NULL,
			`module_id` VARCHAR(75),
			PRIMARY KEY (`package_placement_module_id`),
			KEY `package_id` (`package_id`)
		);");
				
		// get all packages  	
		$package = $oDatabase->select("ppi.*")
						->from(Phpfox::getT('socialad_package'), 'ppi')
						->execute('getSlaveRows');
		foreach($package as $aPackage){
			if($aPackage['package_allow_module']) {
				$package_allow_module = unserialize($aPackage['package_allow_module']);
				foreach($package_allow_module as $module){
				  $oDatabase->query("
				  INSERT IGNORE INTO `" . Phpfox::getT('socialad_package_placement_module') . "` (`package_placement_module_id`, `package_id`, `module_id`) VALUES
					(NULL, " . (int)$aPackage['package_id'] . ", '" . $module . "');
				  ");				
				}
			}
		}	
	}
}

if (!defined("YOUNET_IN_UNITTEST")) {
	ynsocialad301p2install();
}
?>
