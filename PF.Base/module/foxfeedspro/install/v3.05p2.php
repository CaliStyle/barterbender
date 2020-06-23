<?php

defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

$oDb->delete(Phpfox::getT('user_group_setting'), 'product_id="FoxFeedsPro" AND module_id="foxfeedspro" AND name="can_add_rss_provider_in_profile"');

$oDb->delete(Phpfox::getT('user_group_setting'), 'product_id="FoxFeedsPro" AND module_id="foxfeedspro" AND name="can_add_rss_provider_in_pages"');

?>