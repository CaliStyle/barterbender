<?php

defined('PHPFOX') or exit('NO DICE!');

function profile_completeness_install401()
{
    $oDatabase = db();

    $oDatabase->query("
CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('profilecompleteness_settings') . "` (
  `settings_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `default_value` varchar(255) NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

    $oDatabase->query("
CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('profilecompleteness_weight') . "` (
  `weight_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_iso` int(11) NOT NULL,
  `city_location` int(11) NOT NULL,
  `postal_code` int(11) NOT NULL,
  `birthday` int(11) NOT NULL,
  `gender` int(11) NOT NULL,
  `cf_relationship_status` int(11) NOT NULL,
  `signature` int(11) NOT NULL,
  `cf_about_me` int(11) NOT NULL,
  `cf_who_i_d_like_to_meet` int(11) NOT NULL,
  `cf_movies` int(11) NOT NULL,
  `cf_interests` int(11) NOT NULL,
  `cf_music` int(11) NOT NULL,
  `cf_smoker` int(11) NOT NULL,
  `cf_drinker` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`weight_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
}

profile_completeness_install401();
