<?php
defined('PHPFOX') or exit('NO DICE!');

function yncontest301install ()
{
	$oDb = Phpfox::getLib('phpfox.database');

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest')."` (
	  `contest_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `contest_name` varchar(255) NOT NULL,
	  `contest_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:draft; 2: pending; 3:denied; 4;on-going; 5:closed',
	  `short_description` text,
	  `short_description_parsed` text,
	  `description` text,
	  `description_parsed` text,
	  `award_description` text,
	  `award_description_parsed` text,
	  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '1:blog; 2: photo; 3:video', 
	  `time_stamp` int(11) unsigned NOT NULL DEFAULT '0',
	  `start_time` int(11) unsigned NOT NULL DEFAULT '0',
	  `stop_time` int(11) unsigned NOT NULL DEFAULT '0',
	  `end_time` int(11) unsigned NOT NULL DEFAULT '0',
	  `number_entry_max` int(11) unsigned DEFAULT '1' COMMENT 'Maximum entries a participant can submit', 
	  `number_winning_entry_max` int(11) unsigned DEFAULT '1' COMMENT 'The number of entries win by votes', 
	  `privacy` tinyint(1) unsigned DEFAULT '0', 
	  `privacy_comment` tinyint(1) unsigned DEFAULT '0', 
	  `total_comment` int(11) unsigned NOT NULL DEFAULT '0',
	  `total_attachment` int(11) unsigned NOT NULL DEFAULT '0',
	  `total_view` int(11) unsigned NOT NULL DEFAULT '0',
	  `total_like` int(11) unsigned NOT NULL DEFAULT '0',
	  `total_dislike` int(11) unsigned NOT NULL DEFAULT '0',
	  `total_favorite` int(11) unsigned NOT NULL DEFAULT '0',
	  `total_participant` int(11) unsigned NOT NULL DEFAULT '0',
	  `user_id` int(11) unsigned NOT NULL, 
	  `image_path` varchar(255) NULL, 
	  `is_feature` tinyint(1) unsigned DEFAULT '0', 
	  `is_approved` tinyint(1) unsigned DEFAULT '0', 
	  `is_published` tinyint(1) unsigned DEFAULT '0', 
	  `is_premium` tinyint(1) unsigned DEFAULT '0', 
	  `is_ending_soon` tinyint(1) unsigned DEFAULT '0', 
	  `can_submit_entry` tinyint(1) unsigned DEFAULT '0',
	  `module_id` varchar(75) NOT NULL DEFAULT 'contest',
	  `item_id`  int(10) unsigned NOT NULL DEFAULT '0',
	  `is_auto_approve` tinyint(1) unsigned DEFAULT '0', 
	  PRIMARY KEY (`contest_id`),
	  KEY `start_time` (`start_time`),
	  KEY `stop_time` (`stop_time`),
	  KEY `end_time` (`end_time`),
	  KEY `contest_status` (`contest_status`),
	  KEY `total_participant` (`total_participant`),
	  KEY `type` (`type`),
	  KEY `user_id` (`user_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_category')."` (
	  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
	  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  `name` varchar(255) NOT NULL DEFAULT '',
	  `name_url` varchar(255) DEFAULT NULL,
	  `time_stamp` int(11) unsigned NOT NULL DEFAULT '0',
	  `used` int(11) unsigned NOT NULL DEFAULT '0',
	  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`category_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_category_data')."` (
	  `contest_id` int(11) unsigned NOT NULL DEFAULT '0',
	  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`contest_id`,`category_id`),
	  KEY `contest_id` (`contest_id`),
	  KEY `category_id` (`category_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_invite')."` (
	  `invite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `item_id` int(10) unsigned NOT NULL COMMENT 'contest_id or entry_id',
	  `type_id` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: contest; 2:entry',
	  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
	  `invited_user_id` int(10) unsigned NOT NULL DEFAULT '0',
	  `invited_email` varchar(100) DEFAULT NULL,
	  `time_stamp` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`invite_id`),
	  KEY `item_id` (`item_id`,`type_id`),
	  KEY `item_id_1` (`item_id`,`type_id`,`invited_user_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_announcement')."` (
	  `announcement_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `headline` varchar(255) NOT NULL,
	  `link` varchar(255),
	  `content` text,
	  `contest_id` int(11) unsigned NOT NULL,
	  `user_id` int(11) unsigned NOT NULL,
	  `time_stamp` int(11) unsigned NOT NULL,
	  PRIMARY KEY (`announcement_id`),
	  KEY `user_id` (`user_id`),
	  KEY `contest_id` (`contest_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_email_condition')."` (
	  `email_condition_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `subject` varchar(255) NOT NULL,
	  `message` text,
	  `term_condition` text,
	  `contest_id` int(11) unsigned NOT NULL,
	  `user_id` int(11) unsigned NOT NULL,
	  PRIMARY KEY (`email_condition_id`),
	   KEY `contest_id` (`contest_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_emailtemplate')."` (
	  `emailtemplate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) NOT NULL,
	  `subject` varchar(255) NOT NULL,
	  `content` text,
	  `type_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:Create Contest Successfully; 1: Thank Participant; 2:Thank for submitting entry; 3;Contest closed; 4:Contest approved; 5:Contest Denied; 6: INvite Friend letter',
	  `time_stamp` int(11) unsigned NOT NULL,
	  PRIMARY KEY (`emailtemplate_id`)
	);");
	if($oDb->isField(Phpfox::getT('contest_emailtemplate'),'time_stamp'))
	{
	  $oDb->query("ALTER TABLE `" . Phpfox::getT('contest_emailtemplate') . "`
	    MODIFY COLUMN `time_stamp` int(11) unsigned NOT NULL DEFAULT '0'");
	}
	if($oDb->isField(Phpfox::getT('contest_emailtemplate'),'name'))
	{
	  $oDb->query("ALTER TABLE `" . Phpfox::getT('contest_emailtemplate') . "`
	    MODIFY COLUMN `name` varchar(255)  NULL");
	}	
	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_setting')."` (
	  `setting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) NOT NULL,
	  `value` text,
	  PRIMARY KEY (`setting_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_participant')."` (
	  `participant_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `contest_id` int(11) unsigned NOT NULL,
	  `user_id` int(11) unsigned NOT NULL,
	  `is_followed` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  `is_joined` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  `is_favorite` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`participant_id`),
	  KEY `user_id` (`user_id`),
	  KEY `contest_id` (`contest_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_entry_vote')."` (
	  `vote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `user_id` int(11) unsigned NOT NULL,
	  `entry_id` int(11) unsigned NOT NULL,
	  PRIMARY KEY (`vote_id`),
	  KEY `entry_user_id` (`entry_id`, `user_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_winner')."` (
	  `rank` int(11) unsigned NOT NULL,
	  `user_id` int(11) unsigned NOT NULL,
	  `entry_id` int(11) unsigned NOT NULL,
	  `award` text,
	  `time_stamp` int(11) unsigned NOT NULL,
	  PRIMARY KEY (`user_id`,`entry_id`),
	   KEY `entry_id` (`entry_id`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_entry')."` (
	  `entry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `title` varchar(255) NOT NULL,
	  `video_url` varchar(255) 	NULL,
	  `embed_code` mediumtext NULL,
	  `summary` text NOT NULL,
	  `summary_parsed` text NOT NULL,
	  `image_path` varchar(255) NULL, 
	  `blog_content` text NOT NULL,
	  `blog_content_parsed` text NOT NULL,
	  `user_id` int(11) unsigned NOT NULL,
	  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '1:blog; 2: photo; 3:video', 
	  `item_id` int(11) unsigned NOT NULL,
	  `time_stamp` int(11) unsigned NOT NULL,
	  `approve_stamp` int(11) unsigned NOT NULL,
	  `contest_id` int(11) unsigned NOT NULL,
	  `total_vote` int(11) unsigned NOT NULL,
	  `total_like` int(11) unsigned NOT NULL,
	  `total_dislike` int(11) unsigned NOT NULL,
	  `total_comment` int(11) unsigned NOT NULL,
	  `total_attachment` int(11) unsigned NOT NULL,
	  `total_view` int(11) unsigned NOT NULL,
	  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '0:pending; 1: approved; 2:denied; 3:draft;', 
	  PRIMARY KEY (`entry_id`),
	  KEY `total_entry_contest` (`contest_id`, `status`),
	  KEY `user_id` (`user_id`),
	  KEY `contest_id` (`contest_id`),
	  KEY `total_vote` (`total_vote`)
	);");

	$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('contest_transaction')."` (
	  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `transaction_log` text,
	  `invoice` mediumtext,
	  `user_id` int(10) unsigned NOT NULL,
	  `contest_id` int(10) unsigned NOT NULL,
	  `time_stamp` int(10) unsigned NOT NULL,
	  `amount` decimal(14,2) NOT NULL DEFAULT '0.00',
	  `currency` varchar(4) NOT NULL DEFAULT 'USD',
	  `status` tinyint(2) NOT NULL DEFAULT '0',
	  `paypal_account` varchar(255) DEFAULT NULL,
	  `paypal_transaction_id` varchar(50) DEFAULT NULL,
	  PRIMARY KEY (`transaction_id`),
	  KEY `time_stamp` (`time_stamp`),
	  KEY `contest_id` (`contest_id`),
	  KEY `user_id` (`user_id`),
	  KEY `status` (`status`)
	);");


	// insert email template intoDB
	// 
	$create_contest_successfully = array(
			'type_id' => 1,
			'subject' => "Contest is Successfully Created ",
			'content' => "
Hello [owner_name],

Congratulations!

Your contest [title] has been successfully created. 

[contest_url]

Regards,

[site_name]

			",
			);

	$thanks_participant = array(
		'type_id' => 2,
		'subject' => "Thanks for joining the contest",
		'content' => "
Hello [participant_name],

Thanks for joining [title] contest. Please find the contest details, awards, participants, entries and submit an entry at [contest_url].

For further information feel free to contact [owner_name].

Have a nice day!

Regards,

[owner_name]

		"
		);

	$thanks_for_submitting_entry = array(
		'type_id' => 3,
		'subject' => "Thanks For Submitting Entry ",
		'content' => "
Hello [participant_name],

Thanks for submitting the entry. Please find more entries at [contest_url].
For further information feel free to contact [owner_name]. 

Have a nice day!

Regards,

[owner_name]

		",
		);

	$contest_closed = array(
		'type_id' => 4,
		'subject' => "Contest is Closed",
		'content' => "
Hello,

The Contest [title] is closed. Please find the final results at [contest_url]

Regards,

[site_name]
		",
		);

	$contest_approved = array(
		'type_id' => 5,
		'subject' => "Contest is Approved ",
		'content' => "
Hello [owner_name],

Your contest [title] has been approved.

[contest_url]

Regards,

[site_name]

		",
		);

	$contest_denied = array(
		'type_id' => 6,
		'subject' => "Contest is Denied ",
		'content' => "
Hello [owner_name],

Your contest [title] was denied. Please contact admin for more information. To re-publish please edit it and click Publish.

Regards,

[site_name]

		"
		);

	$invite_friend_letter = array(
		'type_id' => 7,
		'subject' => "Invite Friend Letter",
		'content' => "
Hello,

You’re invited to join contest [title]. Please find further information at [contest_url]

Regards,

[site_name]

		",
		);

	$entry_denied = array(
		'type_id' => 8,
		'subject' => "Entry is denied",
		'content' => "
Hello [entry_owner_name],

Your entry was denied. Don’t worry, please submit another one before the end date!

Regards,

[owner_name]

		",
		);

	$invite_friend_view_entry_letter = array(
			'type_id' => 9,
			'subject' => "[inviter_name] invited you to view entry [entry_name] in contest [title]",
			'content' => "
Hello,

You’re invited to view entry [entry_name] in contest [title]. Please find the link below

[entry_url]

Regards,

[inviter_name]


			",
			);

	$inform_winning_entries = array(
			'type_id' => 10,
			'subject' => "Congratulations! The Contest [title] has Winners!",
			'content' => "

Hi all,

We’re so glad to inform the results of the contest [title]. Please find the link below to view as details.

[contest_url]

Congratulations! 

Regards,

[owner_name]


			",
			);

	

//make sure this table empty before inserting
	$oDb->query(" TRUNCATE " . Phpfox::getT('contest_emailtemplate'). "	");	

	$aInsertEmails = array($create_contest_successfully,
		$thanks_participant,
		$thanks_for_submitting_entry,
		$contest_closed,
		$contest_approved,
		$contest_denied,
		$invite_friend_letter,
		$entry_denied,
		$invite_friend_view_entry_letter,
		$inform_winning_entries);

	foreach($aInsertEmails as $aInsertEmail) {
		$oDb->insert(Phpfox::getT('contest_emailtemplate') , $aInsertEmail);
	}

	$oDb->query("INSERT IGNORE INTO `". Phpfox::getT('contest_category') ."` (`category_id`, `name`, `parent_id`, `time_stamp`, `used`, `is_active`) VALUES
		(11, 'Sustainable Food', 0, 1328241168, 0, 1),
		(10, 'Immigrant Rights', 0, 1328241173, 0, 1),
		(9, 'Human Trafficking', 0, 1328241176, 0, 1),
		(8, 'Human Rights', 0, 1328241180, 0, 1),
		(7, 'Health', 0, 1328241185, 0, 1),
		(6, 'Gay Rights', 0, 1328241187, 0, 1),
		(5, 'Environment', 0, 1328241191, 0, 1),
		(4, 'Education', 0, 1328241194, 0, 1),
		(3, 'Economic Justice', 0, 1328241197, 0, 1),
		(2, 'Criminal Justice', 0, 1328241200, 0, 1),
		(1, 'Animals', 0, 1328241203, 0, 1);");

	if(!$oDb->isField(Phpfox::getT('user_field'),'total_contest'))
	{
		$oDb->query("ALTER TABLE  `".Phpfox::getT('user_field')."` ADD  `total_contest` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
	}

	if(!$oDb->isField(Phpfox::getT('user_activity'),'activity_contest'))
	{
		$oDb->query("ALTER TABLE  `".Phpfox::getT('user_activity')."` ADD  `activity_contest` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
	}


	$aRow = $oDb->select('block_id')
		->from(Phpfox::getT('block'))
		->where("m_connection ='contest.profile' AND product_id = 'younet_contest4' AND module_id ='profile' AND component ='pic'")
		->execute('getRow');

	if(!isset($aRow['block_id']))
	{
	// insert the pic block for viewing in profile
		$oDb->query("INSERT INTO `".Phpfox::getT('block')."` (`title`, `type_id`, `m_connection`, `module_id`, `product_id`, `component`, `location`, `is_active`, `ordering`, `disallow_access`, `can_move`, `version_id`) VALUES ('Profile Photo &amp; Menu', 0, 'contest.profile', 'profile', 'younet_contest', 'pic', '1', 1, 1, NULL, 0, NULL)");
	}
}

yncontest301install();	
?>