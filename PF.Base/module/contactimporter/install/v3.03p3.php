<?php

function contactimporter_add_invited_table()
{
	$sTable = Phpfox::getT('contactimporter_invited');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
        `user_id` int(11) unsigned NOT NULL,
		`is_used` tinyint(1) NOT NULL DEFAULT '0',
		PRIMARY KEY (`user_id`)      
	) ENGINE=MyISAM";

	Phpfox::getLib('phpfox.database')->query($sql);
}

contactimporter_add_invited_table();