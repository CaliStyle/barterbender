<?php

defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

if ($oDb->isField(Phpfox::getT('ynnews_categories'), 'category_description'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_categories') . "` Drop `category_description`");
}

if ($oDb->isField(Phpfox::getT('ynnews_categories'), 'url_link'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_categories') . "` Drop `url_link`");
}

if (!$oDb->isField(Phpfox::getT('ynnews_categories'), 'time_stamp'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_categories') . "` ADD `time_stamp` TINYINT( 11 ) UNSIGNED NOT NULL DEFAULT '0'");
}

?>