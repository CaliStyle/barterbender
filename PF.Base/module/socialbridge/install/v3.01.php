<?php
defined('PHPFOX') or exit('NO DICE!');

function socialbridge_services()
{
	$sTable = Phpfox::getT('socialbridge_services');
	$sql = "CREATE TABLE IF NOT EXISTS `".$sTable."` (
            `service_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(32) CHARACTER SET utf8 NOT NULL,
            `title` varchar(128) NOT NULL,
            `privacy` tinyint(1) NOT NULL DEFAULT '0',
            `connect` int(11) NOT NULL DEFAULT '0',
            `protocol` varchar(32) NOT NULL DEFAULT 'openid',
            `mode` varchar(32) NOT NULL DEFAULT 'popup',
            `w` int(11) NOT NULL DEFAULT '800',
            `h` int(11) NOT NULL DEFAULT '450',
            `ordering` int(11) NOT NULL DEFAULT '0',
            `is_active` int(11) NOT NULL DEFAULT '1',
            `params` text,
            PRIMARY KEY (`service_id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1;";
          
	Phpfox::getLib('phpfox.database')->query($sql);		
}

function insert_socialbridge_services()
{
	$sTable = Phpfox::getT('socialbridge_services');

    $aRows = Phpfox::getLib('phpfox.database')->select('*')
        ->from($sTable)
        ->execute('getRow');

    if(empty($aRows) && !$aRows)
    {
        $sql = "INSERT IGNORE INTO `".$sTable."`(`service_id`, `name`, `title`, `privacy`, `connect`, `protocol`, `mode`, `w`, `h`, `ordering`, `is_active`, `params`) VALUES
          (1, 'facebook', 'Facebook', 1, 1, 'oauth', 'popup', 800, 450, 1, 0, ''),
          (2, 'twitter', 'Twitter', 1, 1, 'oauth', 'popup', 800, 450, 4, 0, ''),
          (3, 'linkedin', 'Linkedin', 1, 1, 'oauth', 'popup', 800, 450, 3, 0, '');";

        Phpfox::getLib('phpfox.database')->query($sql);
    }
}

socialbridge_services();
insert_socialbridge_services();
?>
