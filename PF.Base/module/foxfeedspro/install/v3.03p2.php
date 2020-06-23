<?php

defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

if (!$oDb->isField(Phpfox::getT('ynnews_items'), 'server_id'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_items') . "` ADD `server_id` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `item_image`;");
}

?>