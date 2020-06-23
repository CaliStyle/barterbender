<?php
defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

// check create a new resume by phpfox or import linkedin
if(!$oDb->isField(Phpfox::getT('resume_basicinfo'),'linkedin'))
{
	$oDb->query("ALTER TABLE `" . Phpfox::getT('resume_basicinfo') . "`
		ADD COLUMN `linkedin` tinyint(2) NOT NULL DEFAULT '0';");
}

//drop table setting and create a new table with name is resume_settings
$oDb->query('drop table IF EXISTS '.Phpfox::getT('resume_setting'));

//remove setting view resume of old version 
$oDb->delete(Phpfox::getT('user_group_setting'),'module_id = "resume" and product_id = "younet_resume" and name="can_view_resumes"');

$oDb->delete(Phpfox::getT('language_phrase'),'module_id = "resume" and product_id = "younet_resume" and var_name="user_setting_can_view_resumes"');
//end

//table for custom fields
$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_custom_field')."` (
  `field_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(150) NOT NULL,
  `phrase_var_name` varchar(250) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `var_type` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` tinyint(1) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`field_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_custom_option')."` (
  `option_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` smallint(4) unsigned NOT NULL,
  `phrase_var_name` varchar(250) NOT NULL,
  PRIMARY KEY (`option_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_custom_value')."` (
  `resume_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`resume_id`,`field_id`)
);");

//end custom fields

//table for setting and permission for each groups
$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_settings')."` (
  `settings_id` int(11) NOT NULL AUTO_INCREMENT,
  `begin_group` int(11) NOT NULL DEFAULT '0',
  `end_group` int(11) NOT NULL DEFAULT '0',
  `type_id` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1: who''s view me 2: view all resume',
  PRIMARY KEY (`settings_id`)
);");
	
$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_setting_permission')."` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`permission_id`)
);");

//end permission
if(Phpfox::isModule('resume'))
{
	Phpfox::getService('resume.basic.process')->synchronise();
}
?>