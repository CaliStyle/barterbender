<?php
defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

$oDb->query("INSERT IGNORE INTO `" . Phpfox::getT('rewrite') . "`(url,replacement) VALUES ('foxfeedspro','news');");

//update phrase for fox feeds pro include 2 sections, there are changes title to News text.
Phpfox::getService('language.phrase.process')->updateVarName('en','foxfeedspro.menu_foxfeedspro_foxfeedspro_fad58de7366495db4650cfefac2fcd61','News',true);

//end update phrase

if(!$oDb->isField(Phpfox::getT('ynnews_feeds'),'rssparse'))
{
	$oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_feeds') . "`
		ADD COLUMN `rssparse` tinyint(2) NOT NULL DEFAULT '0';");
}

if(!$oDb->isField(Phpfox::getT('ynnews_feeds'),'lengthcontent'))
{
	$oDb->query("ALTER TABLE `" . Phpfox::getT('ynnews_feeds') . "`
		ADD COLUMN `lengthcontent` int(10) NOT NULL DEFAULT '0';");
}

$oDb->delete(Phpfox::getT('setting'),'module_id = "foxfeedspro" and product_id = "FoxFeedsPro" and var_name = "parse_full_news_content"');
$oDb->delete(Phpfox::getT('setting'),'module_id = "foxfeedspro" and product_id = "FoxFeedsPro" and var_name = "display_full_news_content"');
		
		
?>