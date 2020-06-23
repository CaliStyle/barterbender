<?php

function contactimporter_social_joined()
{
    $sTable = Phpfox::getT('contactimporter_social_joined');

    $sql = "CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('contactimporter_social_joined') . "` (  `joined_id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `user_id` int(10) unsigned NOT NULL,  `social_user_id` varchar(255) NOT NULL,  `inviter` int(10) unsigned NOT NULL,  PRIMARY KEY (`joined_id`)) ENGINE=InnoDB AUTO_INCREMENT=1";
    Phpfox::getLib('phpfox.database')->query($sql);
}

function contactimporter_contact()
{
    $sTable = Phpfox::getT('contactimporter_contact');
    $sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
        `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(10) NOT NULL,
        `provider` char(50) NOT NULL,
        `total` int(10) NOT NULL,
        PRIMARY KEY (`contact_id`)      
	) ENGINE=MyISAM AUTO_INCREMENT=1;";
    Phpfox::getLib('phpfox.database')->query($sql);
}

function contactimporter_queue()
{
    $sTable = Phpfox::getT('contactimporter_queue');
    $sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
        `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `session` text,
        `user_id` int(10) NOT NULL,
        `provider` char(50) NOT NULL,
        `total` int(10) NOT NULL DEFAULT '0',
        `success` int(10) DEFAULT '0',
        `fail` int(10) DEFAULT '0',
        `status` varchar(20) DEFAULT 'pending',
        `message` varchar(255) DEFAULT NULL,
        `fail_ids` longtext,
        `friend_ids` longtext,
        `next` varchar(255) DEFAULT NULL,
        `time_stamp_sendmail` int(10) DEFAULT NULL,
        `time_stamp` int(10) DEFAULT NULL,
        `server_id` varchar(20) DEFAULT NULL,
        PRIMARY KEY (`queue_id`)        
	) ENGINE=MyISAM AUTO_INCREMENT=1;";
    Phpfox::getLib('phpfox.database')->query($sql);
}

function alter_table_invite()
{    
    $sTable = Phpfox::getT('invite');
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField($sTable, 'is_resend'))
    {
        $sql = "ALTER TABLE `" . phpfox::getT('invite') . "` ADD `is_resend` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'";
        $oDb->query($sql);
    }
}

contactimporter_contact();
contactimporter_queue();
contactimporter_social_joined();
alter_table_invite();
Phpfox::getLib('phpfox.database')->query("UPDATE `" . Phpfox::getT('menu') . "` SET is_active = 0 WHERE module_id = 'invite' AND url_value='invite'");
Phpfox::getLib('phpfox.database')->query("UPDATE `" . Phpfox::getT('menu') . "` SET is_active = 0 WHERE module_id = 'invite' AND url_value='invite.invitations'");
?>