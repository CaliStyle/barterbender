<?php
defined('PHPFOX') or exit('NO DICE!');


Phpfox::getLib('phpfox.database')->query("CREATE TABLE IF NOT EXISTS `".phpfox::getT('socialpublishers_agents')."` (
  `agent_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `identity` varchar(128) NOT NULL,
  `service_id` int(11) unsigned NOT NULL,
  `ordering` int(11) unsigned NOT NULL,
  `token` text,
  `params` text,
  `full_name` varchar(255) DEFAULT NULL,
  `img_url` varchar(255) NOT NULL,
  PRIMARY KEY (`agent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1");

Phpfox::getLib('phpfox.database')->query("CREATE TABLE IF NOT EXISTS `".phpfox::getT('socialpublishers_modules')."` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(75) DEFAULT 'phpfox',
  `module_id` varchar(75) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `facebook` int(11) NOT NULL DEFAULT '1',
  `twitter` int(11) NOT NULL DEFAULT '1',
  `linkedin` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12") ;

Phpfox::getLib('phpfox.database')->query("INSERT IGNORE INTO `".phpfox::getT('socialpublishers_modules')."` (`id`, `product_id`, `module_id`, `title`, `is_active`, `facebook`, `twitter`, `linkedin`) VALUES
(1, 'phpfox', 'blog', 'blog.blog', 1, 1, 1, 1),
(2, 'phpfox', 'music', 'music.music', 1, 1, 1, 1),
(3, 'phpfox', 'link', 'link.link', 1, 1, 1, 1),
(4, 'phpfox', 'photo', 'photo.photo', 1, 1, 1, 1),
(5, 'phpfox', 'poll', 'poll.poll', 1, 1, 1, 1),
(6, 'phpfox', 'video', 'video.video', 1, 1, 1, 1),
(7, 'phpfox', 'event', 'socialpublishers.event', 1, 1, 1, 1),
(8, 'phpfox', 'marketplace', 'socialpublishers.marketplace', 1, 1, 1, 1),
(9, 'phpfox', 'quiz', 'socialpublishers.quiz', 1, 1, 1, 1),
(11, 'phpfox', 'status', 'socialpublishers.status', 1, 1, 1, 1);");

Phpfox::getLib('phpfox.database')->query("CREATE TABLE IF NOT EXISTS `".phpfox::getT('socialpublishers_services')."` (
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
) ENGINE=MyISAM AUTO_INCREMENT=4;");

Phpfox::getLib('phpfox.database')->query("INSERT IGNORE INTO `".phpfox::getT('socialpublishers_services')."`(`service_id`, `name`, `title`, `privacy`, `connect`, `protocol`, `mode`, `w`, `h`, `ordering`, `is_active`, `params`) VALUES
(1, 'facebook', 'Facebook', 1, 1, 'oauth', 'popup', 800, 450, 1, 1, ''),
(2, 'twitter', 'Twitter', 1, 1, 'oauth', 'popup', 800, 450, 4, 1, ''),
(3, 'linkedin', 'Linkedin', 1, 1, 'oauth', 'popup', 800, 450, 3, 1, '');");

Phpfox::getLib('phpfox.database')->query("CREATE TABLE IF NOT EXISTS `".phpfox::getT('socialpublishers_settings')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  `facebook` text NOT NULL,
  `twitter` text NOT NULL,
  `linkedin` text NOT NULL,
  `auto_publish` text NOT NULL,
  `no_ask` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;");

?>
