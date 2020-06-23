<?php

defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

if (!$oDb->isField(Phpfox::getT('ynnews_feeds'), 'time_delete_news'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_feeds') . "` ADD `time_delete_news` int( 11 ) UNSIGNED NOT NULL DEFAULT '0'");
}

if (!$oDb->isField(Phpfox::getT('ynnews_feeds'), 'time_delete_news_stamp'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_feeds') . "` ADD `time_delete_news_stamp` int( 11 ) UNSIGNED NOT NULL DEFAULT '0'");
}

if (!$oDb->isField(Phpfox::getT('ynnews_feeds'), 'page_id'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_feeds') . "` ADD `page_id` int( 11 ) UNSIGNED NOT NULL DEFAULT '0'");
}

if (!$oDb->isField(Phpfox::getT('ynnews_items'), 'page_id'))
{
    $oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_items') . "` ADD `page_id` int( 11 ) UNSIGNED NOT NULL DEFAULT '0'");
}

?>