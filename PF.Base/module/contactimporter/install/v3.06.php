<?php
function alter_table_contactimporter_queue()
{
	$sTable = Phpfox::getT('contactimporter_queue');
	$oDb = Phpfox::getLib('phpfox.database');

	if (!$oDb -> isField($sTable, 'jsons'))
	{
		$sql = "ALTER TABLE `" . $sTable . "` ADD  `jsons` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER  `friend_ids` ";
		$oDb -> query($sql);
	}
}

function alter_table_contactimporter_queue_list()
{
	$sTable = Phpfox::getT('contactimporter_invitation_queue_list');
	$oDb = Phpfox::getLib('phpfox.database');

	if (!$oDb -> isField($sTable, 'json'))
	{
		$sql = "ALTER TABLE `" . $sTable . "` ADD  `json` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER  `friend_id` ";
		$oDb -> query($sql);
	}
}

function contactimporter_cron_notify()
{
	$sTable = Phpfox::getT('contactimporter_cron_notify');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`item_id` int(11) NOT NULL auto_increment,
  		`user_id` int(11) NOT NULL,
  		`provider` varchar(64) NOT NULL,
  		`time_stamp` int(10) DEFAULT NULL,
  		`numbers` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,  		
		PRIMARY KEY (`item_id`)
	) ENGINE=MyISAM AUTO_INCREMENT=1";
	Phpfox::getLib('phpfox.database') -> query($sql);
}

contactimporter_cron_notify();
alter_table_contactimporter_queue();
alter_table_contactimporter_queue_list();

