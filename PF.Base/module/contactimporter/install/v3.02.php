<?php
function contactimporter_providers()
{
	$sTable = Phpfox::getT('contactimporter_providers');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`name` varchar(10) NOT NULL,
		`title` varchar(20) DEFAULT NULL,
		`logo` varchar(50) DEFAULT NULL,
		`enable` int(2) NOT NULL DEFAULT '1',
		`status` int(2) NOT NULL DEFAULT '1',
		`type` varchar(20) NOT NULL,
		`description` varchar(512) DEFAULT NULL,
		`requirement` varchar(20) DEFAULT NULL,
		`check_url` varchar(100) DEFAULT NULL,
		`version` varchar(20) DEFAULT NULL,
		`base_version` varchar(20) DEFAULT NULL,
		`supported_domain` longtext,
		`order_providers` int(2) NOT NULL DEFAULT '200',
		`default_domain` varchar(20) DEFAULT NULL,
		`photo_import` int(1) NOT NULL DEFAULT '0',
		`photo_enable` int(1) NOT NULL DEFAULT '0',
		`o_type` varchar(20) NOT NULL,
		PRIMARY KEY (`name`)
	) ENGINE=MyISAM;";	
	Phpfox::getLib('phpfox.database')->query($sql);	
	$sql = "INSERT IGNORE INTO `$sTable` (`name`, `title`, `logo`, `enable`, `status`, `type`, `description`, `requirement`, `check_url`, `version`, `base_version`, `supported_domain`, `order_providers`, `default_domain`, `photo_import`, `photo_enable`, `o_type`) VALUES	
		('gmail', 'Gmail', 'gmail', 1, 1, 'email', 'Get the contacts from a Gmail account', 'email', 'https://google.com', '1.4.8', '1.6.3', 'a:2:{i:0;s:5:\"gmail\";i:1;s:10:\"googlemail\";}', 1, 'gmail.com', 0, 0, 'email'),
		('hotmail', 'Live/Hotmail', 'hotmail', 1, 1, 'email', 'Get the contacts from a Windows Live/Hotmail account', 'email', 'http://login.live.com/login.srf?id=2', '1.6.4', '1.8.0', 'a:4:{i:0;s:7:\"hotmail\";i:1;s:4:\"live\";i:2;s:3:\"msn\";i:3;s:8:\"chaishop\";}', 3, 'hotmail.com', 0, 0, 'email'),
		('linkedin', 'LinkedIn', 'linkedin', 1, 1, 'social', 'Get the contacts from a LinkedIn account', 'email', 'http://m.linkedin.com/session/new', '1.1.4', '1.8.0', 'a:0:{}', 4, '', 0, 0, 'email'),
		('facebook_', 'Facebook', 'facebook_', 1, 1, 'social', 'Get the contacts from a Facebook account', 'email', 'http://apps.facebook.com/causes/', '1.2.7', '1.8.0', 'a:0:{}', 1, '', 1, 1, 'social'),
		('twitter', 'Twitter', 'twitter', 1, 1, 'social', 'Get the contacts from a Twitter account', 'user', 'http://twitter.com', '1.1.1', '1.8.0', 'a:0:{}', 2, '', 1, 1, 'social');";
	Phpfox::getLib('phpfox.database')->query($sql);
}

function contactimporter_max_invitations()
{		
	$sTable = Phpfox::getT('contactimporter_max_invitations');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`id_max_invitation` int(11) NOT NULL AUTO_INCREMENT,
		`id_user_group` int(11) NOT NULL,
		`number_invitation` int(11) NOT NULL,
		PRIMARY KEY (`id_max_invitation`)
	) ENGINE=MyISAM AUTO_INCREMENT=1;";
	Phpfox::getLib('phpfox.database')->query($sql);
}

function contactimporter_statistics()
{
	$sTable = Phpfox::getT('contactimporter_statistics');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`statictis_id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL,
		`emails` int(11) DEFAULT '0',
		`socials` int(11) DEFAULT '0',
		PRIMARY KEY (`statictis_id`)
	) ENGINE=MyISAM AUTO_INCREMENT=1";
	Phpfox::getLib('phpfox.database')->query($sql);
}

function contactimporter_api_settings()
{
	$sTable = Phpfox::getT('contactimporter_api_settings');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`api_id` int(11) NOT NULL AUTO_INCREMENT,
		`api_name` varchar(50) NOT NULL,
		`api_params` text NOT NULL,
		PRIMARY KEY (`api_id`)
	) ENGINE=MyISAM AUTO_INCREMENT=1;";
	Phpfox::getLib('phpfox.database')->query($sql);
}

function contactimporter_settings()
{
	$sTable = Phpfox::getT('contactimporter_settings');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`settings_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`settings_type` varchar(100) NOT NULL,
		`param_values` int(10) NOT NULL DEFAULT '1',
		PRIMARY KEY (`settings_id`),
		UNIQUE KEY `settings_type` (`settings_type`)
	) ENGINE=MyISAM AUTO_INCREMENT=1;";
	Phpfox::getLib('phpfox.database')->query($sql);
}

function contactimporter()
{
	$sTable = Phpfox::getT('contactimporter');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`contactimporter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`user_id` int(10) NOT NULL,
		`provider` varchar(50) NOT NULL,
		`contactimporter_user_id` varchar(200) NOT NULL,
		`time_stamp` int(10) NOT NULL,
		PRIMARY KEY  (`contactimporter_id`),
		UNIQUE KEY `contactimporter_user_id` (`contactimporter_user_id`)
	) ENGINE=MyISAM AUTO_INCREMENT=1;";
	Phpfox::getLib('phpfox.database')->query($sql);
}

function contactimporter_unsubscribe()
{
	$sTable = Phpfox::getT('contactimporter_unsubscribe');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`unsubscribe_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`email` varchar(100) NOT NULL,
		`time_stamp` int(10) unsigned NOT NULL,
		PRIMARY KEY (`unsubscribe_id`),
		UNIQUE KEY `email` (`email`)
	) ENGINE=MyISAM AUTO_INCREMENT=1;";
	Phpfox::getLib('phpfox.database')->query($sql);
}

contactimporter_providers();
contactimporter_max_invitations();
contactimporter_statistics();
contactimporter_api_settings();
contactimporter_settings();
contactimporter_unsubscribe();
contactimporter();
?>