<?php
function videochannel_install401()
{
    $oDb = Phpfox::getLib('phpfox.database');
    
    $oDb->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('favorite') ."` (  
	`favorite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` varchar(200) NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `time_stamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`favorite_id`),
  KEY `type_id` (`type_id`,`item_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `favorite_id` (`favorite_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");



    $folderVideo = PHPFOX_DIR ."file". PHPFOX_DS ."pic". PHPFOX_DS ."video";
    if (!file_exists($folderVideo))
    {
       // $error_folder = "please create folder PF.Base/file/pic/video and mod 777";
        try
        {
            Phpfox::getLib('file')->mkdir($folderVideo,false,"0777");
        }catch (Exception $e)
        {
        }
    }
    $oDb->query("
        INSERT IGNORE INTO `". Phpfox::getT('channel_category') ."` (`category_id`, `parent_id`, `is_active`, `name`, `time_stamp`, `used`, `ordering`) VALUES
        (1, 0, 1, 'Animation', 1324264003, 0, 0),
        (2, 0, 1, 'Art & Design', 1324264010, 0, 0),
        (3, 0, 1, 'Cameras & Techniques', 1324264022, 0, 0),
        (4, 0, 1, 'Comedy', 1324264026, 0, 0),
        (5, 0, 1, 'Documentary', 1324264026, 0, 0),
        (6, 0, 1, 'Experimental', 1324264026, 0, 0),
        (7, 0, 1, 'Fashion', 1324264026, 0, 0),
        (8, 0, 1, 'Food', 1324264026, 0, 0),
        (9, 0, 1, 'Instructionals', 1324264026, 0, 0),
        (10, 0, 1, 'Music', 1324264026, 0, 0),
        (11, 0, 1, 'Narrative', 1324264026, 0, 0),
        (12, 0, 1, 'Personal', 1324264026, 0, 0),
        (13, 0, 1, 'Reporting & Journalism', 1324264026, 0, 0),
        (14, 0, 1, 'Sports', 1324264026, 0, 0),
        (15, 0, 1, 'Talks', 1324264026, 0, 0),
        (16, 0, 1, 'Travel', 1324264026, 0, 0);");

    $oDb->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('videochannel_feed') ."` (  
	    `feed_id` int(10) UNSIGNED NOT NULL,
        `video_id` int(10) UNSIGNED NOT NULL,
        `feed_table` varchar(255) NOT NULL DEFAULT 'feed'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

}

videochannel_install401();

?>