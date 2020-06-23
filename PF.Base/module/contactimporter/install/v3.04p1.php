<?php

function alter_table_queue_add_access_token_queue_list_and_indexing()
{    
    $sTable = Phpfox::getT('contactimporter_invitation_queue_list');
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField($sTable, 'access_token'))
    {
        $sql = "ALTER TABLE `" . $sTable . "` ADD `access_token` varchar(200) NOT NULL DEFAULT '0'";
        $oDb->query($sql);
    }

    if(!$oDb->isIndex($sTable, 'access_token'))
    {	
    	 $sql = "ALTER TABLE `" . $sTable . "` ADD INDEX `access_token` (`access_token`) ";
        $oDb->query($sql);
    }	
}

function alter_table_queue_add_time_stamp_realsent()
{    
    $sTable = Phpfox::getT('contactimporter_queue');
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField($sTable, 'time_stamp_realsent'))
    {
        $sql = "ALTER TABLE `" . phpfox::getT('contactimporter_queue') . "` ADD `time_stamp_realsent` int(11) unsigned NOT NULL DEFAULT 0";
        $oDb->query($sql);
    }
}

function alter_table_invite_add_invited_name()
{    
    $sTable = Phpfox::getT('invite');
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField($sTable, 'invited_name'))
    {
        $sql = "ALTER TABLE `" . phpfox::getT('invite') . "` ADD `invited_name` VARCHAR(255) DEFAULT NULL";
        $oDb->query($sql);
    }
}

function alter_table_queue_add_is_xmpp()
{    
    $sTable = Phpfox::getT('contactimporter_queue');
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField($sTable, 'is_xmpp'))
    {
        $sql = "ALTER TABLE `" . phpfox::getT('contactimporter_queue') . "` ADD `is_xmpp` TINYINT(1) DEFAULT 0";
        $oDb->query($sql);
    }
}


function contactimporter_install304p1() {
    alter_table_queue_add_time_stamp_realsent();

    alter_table_invite_add_invited_name();
    
    alter_table_queue_add_is_xmpp();

	alter_table_queue_add_access_token_queue_list_and_indexing();
}



contactimporter_install304p1();


