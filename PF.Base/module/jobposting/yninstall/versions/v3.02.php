<?php

function jobposting_install302()
{
    $oDb = Phpfox::getLib('phpfox.database');
    

	if (!$oDb -> isField(Phpfox::getT('jobposting_job'), 'location'))
	{
		 $oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_job') ."`
		 ADD `location` varchar(255) NOT NULL AFTER  `working_place`,
		 ADD `country_iso` char(2) DEFAULT NULL AFTER  `working_place`,
		 ADD `country_child_id` mediumint(8) unsigned NOT NULL DEFAULT '0' AFTER  `working_place`,
		 ADD `city` varchar(255) DEFAULT NULL AFTER  `working_place`,
		 ADD `postal_code` varchar(20) DEFAULT NULL AFTER  `working_place`,
		 ADD `is_activated` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER  `is_deleted`,
		 ADD `gmap` varchar(255) DEFAULT NULL AFTER  `working_place` ;");
	}
	
	
	if (!$oDb -> isField(Phpfox::getT('jobposting_custom_field'), 'type'))
	{
		$oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_custom_field') ."`
	 	ADD  `type` TINYINT( 3 ) DEFAULT NULL AFTER  `ordering`;");
	}
	
	if (!$oDb -> isField(Phpfox::getT('jobposting_custom_value'), 'type'))
	{
		$oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_custom_value') ."`
	 	ADD  `type` TINYINT( 3 ) DEFAULT NULL AFTER  `value_id`;");
	}
	
	 
	if (!$oDb -> isField(Phpfox::getT('jobposting_company'), 'is_activated'))
	{
		$oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_company') ."`
	 	ADD  `is_activated` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER  `is_deleted`;");
	}
	
	if (!$oDb -> isField(Phpfox::getT('jobposting_company_admin'), 'add_photo'))
	{
		 $oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_company_admin') ."`
		 ADD  `add_photo` TINYINT( 1 ) NOT NULL DEFAULT '1',
		 ADD  `delete_photo` TINYINT( 1 ) NOT NULL DEFAULT '1',
		 ADD  `buy_packages` TINYINT( 1 ) NOT NULL DEFAULT '1',
		 ADD  `edit_submission_form` TINYINT( 1 ) NOT NULL DEFAULT '1',
		 ADD  `add_job` TINYINT( 1 ) NOT NULL DEFAULT '1',
		 ADD  `edit_job` TINYINT( 1 ) NOT NULL DEFAULT '1',
		 ADD  `delete_job` TINYINT( 1 ) NOT NULL DEFAULT '1',
		 ADD  `view_application` TINYINT( 1 ) NOT NULL DEFAULT '1',
		 ADD  `download_resumes` TINYINT( 1 ) NOT NULL DEFAULT '1'
		 ; ");
	}
		
	 	 
	 
	
	 
	 
}

jobposting_install302();

?>