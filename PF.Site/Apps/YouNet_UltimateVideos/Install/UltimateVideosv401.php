<?php
/**
 * Created by PhpStorm.
 * User: davidnguyen
 * Date: 7/25/16
 * Time: 6:08 PM
 */

namespace Apps\YouNet_UltimateVideos\Install;


use Phpfox;

class UltimateVideosv401
{
    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }

    public function process()
    {
        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_videos') . "`(
            `video_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(255) DEFAULT NULL,
            `description` text DEFAULT NULL,
            `category_id` int(10) unsigned NOT NULL default '0',
            `module_id` varchar(75) DEFAULT NULL,
            `item_id` int(10) unsigned NOT NULL DEFAULT '0',
            `privacy` tinyint(1) NOT NULL DEFAULT '0',
            `type` tinyint(1) NOT NULL,
            `code` varchar(150) NOT NULL,
            `duration` varchar(8) DEFAULT NULL,
            `status` tinyint(1) NOT NULL,
            `user_id` int(10) unsigned NOT NULL DEFAULT '0',
            `parent_user_id` int(10) unsigned NOT NULL DEFAULT '0',
            `video_path` varchar(75) DEFAULT NULL,
            `video_server_id` tinyint(1) NOT NULL DEFAULT '0',
            `image_path` varchar(75) DEFAULT NULL,
            `image_server_id` tinyint(1) NOT NULL DEFAULT '0',
            `is_featured` tinyint(1) NOT NULL DEFAULT '0',
            `is_sponsor` tinyint(1) NOT NULL DEFAULT '0',
            `is_approved` tinyint(1) NOT NULL DEFAULT '0',
            `total_comment` int(10) unsigned NOT NULL DEFAULT '0',
            `total_like` int(10) unsigned NOT NULL DEFAULT '0',
            `total_favorite` int(10) unsigned NOT NULL DEFAULT '0',
            `rating` decimal(4,2) NOT NULL DEFAULT '0.00',
            `total_rating` int(10) unsigned NOT NULL DEFAULT '0',
            `total_view` int(10) unsigned NOT NULL DEFAULT '0',
            `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
            `user_token` VARCHAR( 256 ) NULL DEFAULT NULL,
            `allow_upload_channel` tinyint(1) NOT NULL default '0',
            PRIMARY KEY (`video_id`),
            KEY `user_id` (`user_id`),
            KEY `total_view` (`total_view`)
            ) ENGINE=InnoDB ;");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_category') . "`(
			 `category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			 `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
			 `is_active` tinyint(1) NOT NULL DEFAULT '0',
			 `title` varchar(255) NOT NULL,
			 `time_stamp` int(11) unsigned NOT NULL DEFAULT '0',
			 `used` int(10) unsigned NOT NULL DEFAULT '0',
			 `ordering` int(11) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`category_id`),
			 KEY `parent_id` (`parent_id`,`is_active`),
			 KEY `is_active` (`is_active`)
		     ) ENGINE=InnoDB ;");

        $this->database()->query("INSERT IGNORE INTO `" . Phpfox::getT('ynultimatevideo_category') . "`(`category_id`, `title`, `parent_id`, `time_stamp`, `used`, `is_active`) VALUES
			(1, 'Animation', 0, 1328241203, 0, 1),
			(2, 'Arts & Design', 0, 1328241200, 0, 1),
			(3, 'Cameras & Techniques', 0, 1328241197, 0, 1),
			(4, 'Comedy', 0, 1328241194, 0, 1),
			(5, 'Documentary', 0, 1328241191, 0, 1),
			(6, 'Experimental', 0, 1328241187, 0, 1),
			(7, 'Fashion', 0, 1328241185, 0, 1),
			(8, 'Food', 0, 1328241180, 0, 1),
			(9, 'Instructionals', 0, 1328241176, 0, 1),
			(10, 'Music', 0, 1328241173, 0, 1),
			(11, 'Narrative', 0, 1328241173, 0, 1),
			(12, 'Personal', 0, 1328241173, 0, 1),
			(13, 'Reporting & Journalism', 0, 1328241173, 0, 1),
			(14, 'Sports', 0, 1328241173, 0, 1),
			(15, 'Talks', 0, 1328241173, 0, 1),
			(16, 'Travel', 0, 1328241173, 0, 1);");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ynultimatevideo_category_customgroup_data") . "` (
			`category_id` int(10) NOT NULL,
			`group_id` int(10) NOT NULL,
			PRIMARY KEY  (`category_id`,`group_id`)
		    );");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ynultimatevideo_custom_group") . "` (
            `group_id` int(11) NOT NULL auto_increment,
            `phrase_var_name` varchar(250) default NULL,
            `is_active` tinyint(1) default '1',
            `ordering` tinyint(3) default '0',
            PRIMARY KEY  (`group_id`)
			);");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ynultimatevideo_custom_field") . "` (
            `field_id` int(10) unsigned NOT NULL auto_increment,
            `var_type` varchar(250) default NULL,
            `is_required` tinyint(1) NOT NULL,
            `field_name` varchar(75) NOT NULL,
            `type_name` varchar(75) default NULL,
            `ordering` tinyint(3) default NULL,
            `phrase_var_name` varchar(75) default NULL,
            `is_active` tinyint(1) default NULL,
            `group_id` int(11) NOT NULL,
            `field_info` text,
            PRIMARY KEY  (`field_id`)
		    );");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT("ynultimatevideo_custom_option") . "` (
			`option_id` int(10) unsigned NOT NULL auto_increment,
			`field_id` int(10) unsigned NOT NULL,
			`phrase_var_name` varchar(250) NOT NULL,
			PRIMARY KEY  (`option_id`)
			);");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_custom_value') . "` (
            `value_id` int(10) NOT NULL AUTO_INCREMENT,
            `video_id` int(10) NOT NULL,
            `field_id` int(10) NOT NULL,
            `option_id` int(10) DEFAULT NULL,
            `value` text,
            PRIMARY KEY (`value_id`)
            );");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_ratings') . "` (
            `video_id` int(10) unsigned NOT NULL,
            `user_id` int(9) unsigned NOT NULL,
            `rating` tinyint(1) unsigned default NULL,
            `time_stamp` int(10) unsigned NOT NULL,
            PRIMARY KEY  (`video_id`,`user_id`),
            KEY `INDEX` (`video_id`)
            );");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_favorites') . "` (
            `favorite_id` int(10) NOT NULL AUTO_INCREMENT,
            `video_id` int(10) DEFAULT NULL,
            `user_id` int(10) DEFAULT NULL,
            `time_stamp` int(10) unsigned NOT NULL,
            PRIMARY KEY (`favorite_id`),
            UNIQUE KEY `video_id_user_id` (`video_id`,`user_id`),
            KEY `video_id` (`video_id`),
            KEY `user_id` (`user_id`)
            );");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_playlists') . "` (
            `playlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int(11) unsigned NOT NULL DEFAULT '0',
            `category_id` int(11) unsigned NOT NULL default '0',
            `time_stamp` int(10) unsigned NOT NULL,
            `ordering` smallint(8) unsigned NOT NULL DEFAULT '999',
            `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            `image_path` varchar(75) DEFAULT NULL,
            `image_server_id` tinyint(1) NOT NULL DEFAULT '0',
            `is_featured` tinyint(1) NOT NULL DEFAULT '0',
            `is_approved` tinyint(1) NOT NULL DEFAULT '0',
            `total_view` int(11) unsigned NOT NULL default '0',
            `total_comment` int(11) unsigned NOT NULL default '0',
            `total_like` int(11) unsigned NOT NULL default '0',
            `total_video` int(10) NOT NULL DEFAULT '0',
            `view_mode` int(10) NOT NULL DEFAULT '0',
            `privacy` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`playlist_id`),
            KEY `user_id` (`user_id`),
            KEY `ordering` (`ordering`)
            ) ENGINE=InnoDB ;");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_playlist_data') . "` (
            `playlist_id` int(10) unsigned NOT NULL DEFAULT '0',
            `video_id` int(10) unsigned NOT NULL DEFAULT '0',
            `time_stamp` int(10) unsigned NOT NULL,
            `ordering` tinyint(3) default '0',
            UNIQUE KEY `playlist_id_video_id` (`playlist_id`,`video_id`),
            KEY `time_stamp` (`time_stamp`)
            );");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_watchlaters') . "` (
            `watchlater_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `video_id` int(10) unsigned NOT NULL DEFAULT '0',
            `user_id` int(10) unsigned NOT NULL DEFAULT '0',
            `watched` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `watched_time` int(10) unsigned NOT NULL DEFAULT '0',
            `time_stamp` int(10) unsigned NOT NULL,
            PRIMARY KEY (`watchlater_id`),
            UNIQUE KEY `video_id_user_id` (`video_id`,`user_id`),
            KEY `video_id` (`video_id`),
            KEY `user_id` (`user_id`),
            KEY `watched` (`watched`)
            );");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynultimatevideo_history') . "` (
            `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int(10) unsigned NOT NULL,
            `item_type` varchar(128) NOT NULL,
            `item_id` int(10) unsigned NOT NULL,
            `time_stamp` int(10) unsigned NOT NULL,
            PRIMARY KEY (`history_id`),
            UNIQUE KEY `item_id_item_type_user_id` (`item_id`,`item_type`,`user_id`)
            );");
        if (!$this->database()->isField(Phpfox::getT('user_activity'), 'activity_ultimatevideo_video')) {
            $this->database()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD  `activity_ultimatevideo_video` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
        }
        if (!$this->database()->isField(Phpfox::getT('user_activity'), 'activity_ultimatevideo_playlist')) {
            $this->database()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD  `activity_ultimatevideo_playlist` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
        }
        if (!$this->database()->isField(Phpfox::getT('user_field'), 'total_ultimatevideo')) {
            $this->database()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD  `total_ultimatevideo` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
        }
    }
}