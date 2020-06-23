<?php
defined('PHPFOX') or exit('NO DICE!');
Phpfox::getLib('phpfox.database')->query("INSERT  IGNORE INTO ".phpfox::getT('socialpublishers_modules')." (`id`, `product_id`, `module_id`, `title`, `is_active`, `facebook`, `twitter`, `linkedin`) VALUES (12, 'phpfox', 'feed_comment', 'socialpublishers.comment_on_friends_wall', '1', '1', '1', '1');");
Phpfox::getLib('phpfox.database')->query("INSERT IGNORE INTO `".phpfox::getT('socialpublishers_modules')."` (`id`, `product_id`, `module_id`, `title`, `is_active`, `facebook`, `twitter`, `linkedin`) VALUES (13, 'phpfox', 'pages_comment', 'socialpublishers.post_on_pages_wall', '1', '1', '1', '1');");
?>
