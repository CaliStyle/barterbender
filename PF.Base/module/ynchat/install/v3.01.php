<?php

defined('PHPFOX') or exit('NO DICE!');


function ynchat_install301()
{
    $oDatabase = Phpfox::getLib('database') ;

    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `ynchat_message` (
          `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `from` int(10) unsigned NOT NULL,
          `to` int(10) unsigned NOT NULL,
          `message` text NOT NULL,
          `data` text DEFAULT NULL,
          `sent` int(10) unsigned NOT NULL DEFAULT '0',
          `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
          `direction` tinyint(1) unsigned NOT NULL DEFAULT '0',
          `message_type` enum('text','photo','video','emoticon','sticker','link','file') DEFAULT 'text',
          PRIMARY KEY (`message_id`),
          KEY `to` (`to`),
          KEY `from` (`from`),
          KEY `direction` (`direction`),
          KEY `read` (`read`),
          KEY `sent` (`sent`)
        ) ;
	");

    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `ynchat_status` (
          `user_id` int(10) unsigned NOT NULL,
          `message` text,
          `status` enum('available','away','busy','invisible','offline') DEFAULT NULL,
          `typingto` int(10) unsigned DEFAULT NULL,
          `typingtime` int(10) unsigned DEFAULT NULL,
          `agent` enum('web','mobile') DEFAULT 'web',
          PRIMARY KEY (`user_id`),
          KEY `typingto` (`typingto`),
          KEY `typingtime` (`typingtime`)
        ) ;
	");

    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `ynchat_block` (
          `block_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `from_id` int(10) unsigned NOT NULL,
          `to_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`block_id`),
          KEY `fromid` (`from_id`),
          KEY `toid` (`to_id`),
          KEY `fromid_toid` (`from_id`,`to_id`)
        ) ;
	");

    // we use this table to display on interface
    // blocking data is still used on `ynchat_block` table
    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `ynchat_allow` (
          `allow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `from_id` int(10) unsigned NOT NULL,
          `to_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`allow_id`),
          KEY `fromid` (`from_id`),
          KEY `toid` (`to_id`),
          KEY `fromid_toid` (`from_id`,`to_id`)
        ) ;
	");

    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `ynchat_emoticon` (
	  `emoticon_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
	  `title` char(100) NOT NULL,
	  `text` varchar(200) NOT NULL,
	  `image` char(100) NOT NULL,
	  `ordering` smallint(4) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`emoticon_id`),
	  UNIQUE KEY `text` (`text`)
	) ;
	");

    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `ynchat_sticker` (
	  `sticker_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
	  `title` char(100) NOT NULL,
	  `image` char(100) NOT NULL,
	  `ordering` smallint(4) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`sticker_id`)
	) ;
	");

    $oDatabase->query("
	CREATE TABLE IF NOT EXISTS `ynchat_usersetting` (
          `user_id` int(10) unsigned NOT NULL,
          `is_notifysound` tinyint(1) unsigned NOT NULL DEFAULT '1',
          `is_goonline` tinyint(1) unsigned NOT NULL DEFAULT '1',
          `turnonoff` enum('offsome','onsome','offall','onall') DEFAULT 'onall',
          PRIMARY KEY (`user_id`)
        ) ;
	");

    $oDatabase->query("
		CREATE TABLE IF NOT EXISTS `ynchat_ban` (
          `ban_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `type_id` varchar(100) NOT NULL,
          `find_value` mediumtext NOT NULL,
          `org_find_value` mediumtext NOT NULL,
          `replacement` mediumtext,
          `org_replacement` mediumtext,
          `user_id` int(11) unsigned NOT NULL DEFAULT '0',
          `time_stamp` int(10) unsigned NOT NULL,
          `days_banned` smallint(4) unsigned DEFAULT NULL,
          `return_user_group` tinyint(3) DEFAULT NULL,
          `reason` mediumtext DEFAULT NULL,
          `user_groups_affected` varchar(255) NOT NULL,
          PRIMARY KEY (`ban_id`),
          KEY `type_id` (`type_id`)
		) ;
	");

    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `ynchat_file` (
      `file_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
      `user_id` INT( 11 ) NOT NULL ,
      `receiver_id` INT( 11 ) NOT NULL ,
      `creation_date` int(10) unsigned NOT NULL,
      `title` TEXT NOT NULL ,
      `file_name` TEXT NOT NULL ,
      `type` TEXT NOT NULL ,
      PRIMARY KEY ( `file_id` )
    ) ;
  ");

	$oDatabase->query("
	INSERT IGNORE INTO `ynchat_emoticon` (`emoticon_id`, `title`, `text`, `image`, `ordering`) VALUES
		(1, 'angel', '(angel)', 'angel.gif', 0),
		(2, 'angry', ':@', 'angry.gif', 0),
		(3, 'bearhug', '(hug)', 'bearhug.gif', 0),
		(4, 'beer', '(beer)', 'beer.gif', 0),
		(5, 'blush', '(blush)', 'blush.gif', 0),
		(6, 'bow', '(bow)', 'bow.gif', 0),
		(7, 'boxing', '(punch)', 'boxing.gif', 0),
		(8, 'brokenheart', '(u)', 'brokenheart.gif', 0),
		(9, 'cake', '(^)', 'cake.gif', 0),
		(10, 'callme', '(call)', 'callme.gif', 0),
		(11, 'cash', '(cash)', 'cash.gif', 0),
		(12, 'cellphone', '(mp)', 'cellphone.gif', 0),
		(13, 'clapping', '(clap)', 'clapping.gif', 0),
		(14, 'coffee', '(coffee)', 'coffee.gif', 0),
		(15, 'cool', '8-)', 'cool.gif', 0),
		(16, 'crying', ';(', 'crying.gif', 0),
		(17, 'dance', '(dance)', 'dance.gif', 0),
		(18, 'devil', '(devil)', 'devil.gif', 0),
		(19, 'doh', '(doh)', 'doh.gif', 0),
		(20, 'drink', '(d)', 'drink.gif', 0),
		(21, 'dull', '|-(', 'dull.gif', 0),
		(22, 'emo', '(emo)', 'emo.gif', 0),
		(23, 'evilgrin', ']:)', 'evilgrin.gif', 0),
		(24, 'flex', '(flex)', 'flex.gif', 0),
		(25, 'flower', '(F)', 'flower.gif', 0),
		(26, 'giggle', '(chuckle)', 'giggle.gif', 0),
		(27, 'handshake', '(handshake)', 'handshake.gif', 0),
		(28, 'happy', '(happy)', 'happy.gif', 0),
		(29, 'heart', '(h)', 'heart.gif', 0),
		(30, 'hi', '(wave)', 'hi.gif', 0),
		(31, 'inlove', '(inlove)', 'inlove.gif', 0),
		(32, 'itwasntme', '(wasntme)', 'itwasntme.gif', 0),
		(33, 'jealous', '(envy)', 'jealous.gif', 0),
		(34, 'kiss', ':*', 'kiss.gif', 0),
		(35, 'laughing', ':D', 'laughing.gif', 0),
		(36, 'mail', '(e)', 'mail.gif', 0),
		(37, 'makeup', '(makeup)', 'makeup.gif', 0),
		(38, 'mmm', '(mm)', 'mmm.gif', 0),
		(39, 'music', '(music)', 'music.gif', 0),
		(40, 'nerd', '8-|', 'nerd.gif', 0),
		(41, 'no', '(n)', 'no.gif', 0),
		(42, 'nod', '(nod)', 'nod.gif', 0),
		(43, 'nospeak', ':x', 'nospeak.gif', 0),
		(44, 'party', '(party)', 'party.gif', 0),
		(45, 'puke', '(puke)', 'puke.gif', 0),
		(46, 'rofl', '(rofl)', 'rofl.gif', 0),
		(47, 'sad', ':(', 'sad.gif', 0),
		(48, 'shakeno', '(shake)', 'shakeno.gif', 0),
		(49, 'smile', ':)', 'smile.gif', 0),
		(50, 'speechless', ':-|', 'speechless.gif', 0),
		(51, 'sweating', '(sweat)', 'sweating.gif', 0),
		(52, 'thinking', '(think)', 'thinking.gif', 0),
		(53, 'tongue out', ':p', 'tongue out.gif', 0),
		(54, 'wait', '(wait)', 'wait.gif', 0),
		(55, 'whew', '(whew)', 'whew.gif', 0),
		(56, 'wink', ';)', 'wink.gif', 0),
		(57, 'worried', ':S', 'worried.gif', 0),
		(58, 'yes', '(y)', 'yes.gif', 0);
	");

    $oDatabase->query("
        INSERT IGNORE INTO `ynchat_sticker` (`sticker_id`, `title`, `image`, `ordering`) VALUES
            (1, '000', '000.gif', 0),
            (2, '001', '001.gif', 0),
            (3, '002', '002.gif', 0),
            (4, '003', '003.gif', 0),
            (5, '004', '004.gif', 0),
            (6, '005', '005.gif', 0),
            (7, '006', '006.gif', 0),
            (8, '007', '007.gif', 0),
            (9, '008', '008.gif', 0),
            (10, '009', '009.gif', 0),
            (11, '010', '010.gif', 0),
            (12, '011', '011.gif', 0),
            (13, '012', '012.gif', 0),
            (14, '013', '013.gif', 0),
            (15, '014', '014.gif', 0),
            (16, '015', '015.gif', 0),
            (17, '016', '016.gif', 0),
            (18, '017', '017.gif', 0),
            (19, '018', '018.gif', 0),
            (20, '019', '019.gif', 0),
            (21, '020', '020.gif', 0),
            (22, '021', '021.gif', 0),
            (23, '022', '022.gif', 0),
            (24, '023', '023.gif', 0),
            (25, '024', '024.gif', 0);
    ");

    if (!$oDatabase->isField(Phpfox::getT('user'), 'ynchat_lastactivity'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('user')."` ADD `ynchat_lastactivity` INT default 0");
    }
    if (!$oDatabase->isField(('ynchat_usersetting'), 'data'))
    {
        $oDatabase->query("ALTER TABLE `".('ynchat_usersetting')."` ADD `data` text DEFAULT NULL");
    }
    if (!$oDatabase->isField(('ynchat_file'), 'server_id'))
    {
        $oDatabase->query("ALTER TABLE `".('ynchat_file')."` ADD `server_id` tinyint(1) NOT NULL default 0");
    }
}

ynchat_install301();

?>