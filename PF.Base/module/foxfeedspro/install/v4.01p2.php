<?php

defined('PHPFOX') or exit('NO DICE!');


function ync_install401p2()
{
    $oDatabase = Phpfox::getLib('database') ;
    if($oDatabase->tableExists(Phpfox::getT('ynnews_items')) && !$oDatabase->isField(Phpfox::getT('ynnews_items'), 'is_download_image'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('ynnews_items')."` ADD COLUMN is_download_image tinyint(1) NOT NULL");
    }
    $oDb = Phpfox::getLib('phpfox.database');

        //Newfeed table
    $oDb -> query ("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ynnews_newfeeds')."` (
	 `id` 	int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	 `feed_id` 	int(11)  NOT NULL DEFAULT 0,
	 `item_id` 	int(11) UNSIGNED NOT NULL DEFAULT 0,
	 PRIMARY KEY (`id`)
);");
}

ync_install401p2();

?>
