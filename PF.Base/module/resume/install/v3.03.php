<?php
defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

if(!$oDb->isField(Phpfox::getT('resume_basicinfo'),'privacy'))
{
	$oDb->query("ALTER TABLE `" . Phpfox::getT('resume_basicinfo') . "`
		ADD COLUMN `privacy` tinyint(1) NOT NULL DEFAULT '1';");
}
if(!$oDb->isField(Phpfox::getT('resume_basicinfo'), 'is_synchronize'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('resume_basicinfo') . "`
		ADD COLUMN `is_synchronize` tinyint(1) NOT NULL DEFAULT '1';");    
}
if(!$oDb->isField(Phpfox::getT('resume_basicinfo'), 'display_date_of_birth'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('resume_basicinfo') . "`
		ADD COLUMN `display_date_of_birth` tinyint(1) NOT NULL DEFAULT '1';");    
}
if(!$oDb->isField(Phpfox::getT('resume_basicinfo'), 'display_gender'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('resume_basicinfo') . "`
		ADD COLUMN `display_gender` tinyint(1) NOT NULL DEFAULT '1';");    
}
if(!$oDb->isField(Phpfox::getT('resume_basicinfo'), 'display_marital_status'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('resume_basicinfo') . "`
		ADD COLUMN `display_marital_status` tinyint(1) NOT NULL DEFAULT '1';");    
}
if(!$oDb->isField(Phpfox::getT('resume_basicinfo'), 'authorized'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('resume_basicinfo') . "`
		ADD COLUMN `authorized` text NULL AFTER `headline`;");
}
if(!$oDb->isField(Phpfox::getT('resume_basicinfo'), 'is_show_in_profile'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('resume_basicinfo') . "`
		ADD COLUMN `is_show_in_profile` tinyint(1) NOT NULL DEFAULT '0' AFTER `display_marital_status`;");
}
if(!$oDb->isField(Phpfox::getT('resume_level'), 'ordering'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('resume_level') . "`
		ADD COLUMN `ordering` int(11) NOT NULL DEFAULT '0' AFTER `used`;");
}
?>