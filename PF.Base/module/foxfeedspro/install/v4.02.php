<?php

defined('PHPFOX') or exit('NO DICE!');


function ync_install402()
{
    $oDatabase = Phpfox::getLib('database') ;
    if($oDatabase->tableExists(Phpfox::getT('ynnews_feeds')) && !$oDatabase->isField(Phpfox::getT('ynnews_feeds'), 'server_id'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ynnews_feeds')."` ADD COLUMN server_id tinyint(1) UNSIGNED NOT NULL DEFAULT '0'");
    }
}

ync_install402();

?>
