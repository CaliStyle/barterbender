<?php
Phpfox_Error::skip(true);

defined('PHPFOX') or exit('NO DICE!');

$sTable = Phpfox::getT('socialbridge_token');

$oDb = Phpfox::getLib('phpfox.database');
if (!$oDb -> tableExists($sTable))
{

	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`token_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`user_id` INT(11) UNSIGNED NULL DEFAULT '0',
		`session_id` VARCHAR(64) NULL,
		`service` VARCHAR(32) CHARACTER SET utf8 NOT NULL,
		`identity` VARCHAR(64) NOT NULL DEFAULT '',
		`token` LONGTEXT NOT NULL,
		`profile` LONGTEXT,
		`timestamp` INT(11) NOT NULL,
		PRIMARY KEY (`token_id`),
		INDEX `timestamp` (`timestamp`),
		INDEX `user_id` (`user_id`),
		INDEX `session_id` (`session_id`),
		INDEX `identity` (`identity`),
		INDEX `service` (`service`)
	)
	ENGINE=MyISAM;
	";
	$oDb -> query($sql);
}
else
if (!$oDb -> isField($sTable, 'identity'))
{
	$sql = "ALTER TABLE `" . $sTable . "` 
	ADD COLUMN `identity` VARCHAR(64) NOT NULL DEFAULT '' AFTER `service`,
	ADD INDEX `identity` (`identity`);";
	$oDb -> query($sql);
}
