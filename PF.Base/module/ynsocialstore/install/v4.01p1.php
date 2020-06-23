<?php

/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: ajax.php 2771 2011-07-30 19:34:11Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

function ynstore_install401p1()
{
    $oDatabase = Phpfox::getLib('database') ;
    if($oDatabase->isField(Phpfox::getT('ynstore_store'),'tax'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('ynstore_store')."` CHANGE COLUMN `tax` `tax` double(10,2)");
    }
    if(!$oDatabase->isField(Phpfox::getT('ynstore_store'),'cover_server_id'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ynstore_store')."` ADD COLUMN `cover_server_id` tinyint(1) unsigned DEFAULT 0;");
        $oDatabase->query("UPDATE `".Phpfox::getT('ynstore_store')."` SET `cover_server_id` = `server_id`");
    }
}
ynstore_install401p1();

?>