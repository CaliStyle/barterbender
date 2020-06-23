<?php
function jobposting_install401p6()
{
	$collation = Phpfox::getLib('database')->getRow("select COLLATION_NAME from information_schema.columns where TABLE_NAME = '".Phpfox::getT('user')."' and COLUMN_NAME = 'full_name' LIMIT 1;");
	$character = Phpfox::getLib('database')->getRow("select CHARACTER_SET_NAME from information_schema.columns where TABLE_NAME = '".Phpfox::getT('user')."' and COLUMN_NAME = 'full_name' LIMIT 1;");
    $oDb = Phpfox::getLib('phpfox.database');
	$oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_job') ."`
		 DEFAULT CHARACTER SET utf8;");
	$oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_company') ."`
	 	DEFAULT CHARACTER SET utf8;");

	$oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_job') ."`
		 CHANGE `title` `title` VARCHAR(255) CHARACTER SET ".$character['CHARACTER_SET_NAME']." COLLATE ".$collation['COLLATION_NAME']." NOT NULL;");
	$oDb->query("ALTER TABLE `". Phpfox::getT('jobposting_company') ."`
		CHANGE `name` `name` VARCHAR(255) CHARACTER SET ".$character['CHARACTER_SET_NAME']." COLLATE ".$collation['COLLATION_NAME']." NOT NULL;");
}

jobposting_install401p6();

?>