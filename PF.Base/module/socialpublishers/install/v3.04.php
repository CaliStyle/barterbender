<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsp_install304() {
    Phpfox::getLib('database')->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('socialpublishers_statistic_date') . "` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `statistic_date` int(10) unsigned NOT NULL,
  `total_facebook_post` int(10) unsigned NOT NULL DEFAULT '0',
  `total_twitter_post` int(10) unsigned NOT NULL DEFAULT '0',
  `total_linkedin_post` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
        
        Phpfox::getLib('database')->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('socialpublishers_statistic_user') . "` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `total_facebook_post` int(10) unsigned NOT NULL DEFAULT '0',
  `total_twitter_post` int(10) unsigned NOT NULL DEFAULT '0',
  `total_linkedin_post` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
}

ynsp_install304();
?>
