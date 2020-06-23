<?php

function contactimporter_306p1()
{
	$sTable = Phpfox::getT('contactimporter_queue');
	$oDatabase = Phpfox::getLib('phpfox.database');
    if(!$oDatabase->isField(Phpfox::getT('user_activity'),'activity_contactimporter'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_activity')."` ADD  `activity_contactimporter` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }
    $oDatabase->query("ALTER TABLE  `".Phpfox::getT('contactimporter_queue')."` CHANGE  `message`  `message` TEXT");
    
}

contactimporter_306p1();

