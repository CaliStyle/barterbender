<?php
	
defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01
 * @by MinhTA
 *  
 */
function ynfr_install301() {
	$oDatabase = Phpfox::getLib('database') ;

	//create table fundraising_campaign 
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_campaign') ."` (
		 
			`campaign_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`title` varchar(255) NOT NULL,
			`user_id` int(10) unsigned default '0' ,
			`module_id` varchar(75) NOT NULL DEFAULT 'fundraising',
			`item_id`  int(10) unsigned NOT NULL DEFAULT '0',
			`currency` varchar(4) NOT NULL,
			`total_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
			`total_like` int(10) unsigned NOT NULL DEFAULT '0',
			`total_view` int(10) unsigned NOT NULL DEFAULT '0',
			`total_comment` int(10) unsigned NOT NULL DEFAULT '0',
			`total_rating` int(10) unsigned NOT NULL DEFAULT '0',
			`total_score` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT 'average rating',
			`total_donor` int(10) unsigned NOT NULL DEFAULT '0',
			`short_description` varchar(500) NOT NULL,
			`short_description_parsed` varchar(500) NOT NULL,
			`financial_goal` decimal(14,2) DEFAULT '0.00',
			`time_stamp` int(10) unsigned NOT NULL,
			`start_time` int(10) unsigned DEFAULT NULL,
			`end_time` int(10) unsigned DEFAULT NULL,
			`sponsor_level` mediumtext DEFAULT NULL,
			`predefined_amount_list` mediumtext DEFAULT NULL ,
			`minimum_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
			`paypal_account` varchar(255) DEFAULT NULL,
			`privacy` tinyint(1) NOT NULL DEFAULT '0',
			`privacy_comment` tinyint(1) NOT NULL DEFAULT '0',
			`privacy_donate` tinyint(1) NOT NULL DEFAULT '0',
			`location_venue` varchar(255) DEFAULT NULL,
			`address` varchar(255) DEFAULT NULL,
			`city` varchar(255) DEFAULT NULL,
			`postal_code` varchar(20) DEFAULT NULL,
			`country_iso` char(3) DEFAULT NULL,
			`gmap` mediumtext DEFAULT NULL,
			`image_path` varchar(100) DEFAULT NULL,
			`is_approved` tinyint(1) NOT NULL DEFAULT '1',
			`is_featured` tinyint(1) NOT NULL DEFAULT '0',
			`status` tinyint(1) NOT NULL DEFAULT '0', 
			`server_id` tinyint(3) NOT NULL DEFAULT '0',
			`allow_anonymous` tinyint(1) NOT NULL DEFAULT '1',
			`is_draft` tinyint(1) NOT NULL DEFAULT '0',
			`is_closed` tinyint(1) NOT NULL DEFAULT '0',
			`is_highlighted` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`campaign_id`),
			KEY `module_id` (`module_id`),
			KEY `total_view` (`total_view`),
			KEY `total_donor` (`total_donor`),
			KEY `start_time` (`start_time`),
			KEY `is_featured` (`is_featured`),
			KEY `status` (`status`)
		)  AUTO_INCREMENT=1 ;
	");


	//this table map 1-1 with campaign table
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_text') ."` (
			`text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`campaign_id` int(10) unsigned NOT NULL,
			`description` mediumtext DEFAULT NULL,
			`description_parsed` mediumtext DEFAULT NULL,
			`email_subject` mediumtext DEFAULT NULL,
			`email_message` mediumtext DEFAULT NULL,
			`term_condition` mediumtext DEFAULT NULL,
			PRIMARY KEY (`text_id`),
			KEY `campaign_id` (`campaign_id`)
		)  AUTO_INCREMENT=1 ;
	");


	//this table stored all users donated 
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_donor') ."` (
			`donor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`campaign_id` int(10) unsigned NOT NULL,
			`user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`is_guest` tinyint(1) NOT NULL DEFAULT '0',
			`is_anonymous` tinyint(1) NOT NULL DEFAULT '0',
			`message` mediumtext DEFAULT NULL,
			`amount` decimal(14,2) NOT NULL DEFAULT '0.00',
			`currency` varchar(4) NOT NULL DEFAULT 'USD',
			`time_stamp` int(10) unsigned NOT NULL,
			`full_name` varchar(255) DEFAULT NULL,
			`email_address` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`donor_id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `user_id` (`user_id`)
		)  AUTO_INCREMENT=1 ;
	");


	//fundraising_supporter
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_supporter') ."` (
			`supporter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(10) unsigned NOT NULL,
			`campaign_id` int(10) unsigned NOT NULL,
			`total_comeback` int(10) unsigned DEFAULT '0',
			`support_token` varchar(255) DEFAULT NULL,
			`total_share` int(10) unsigned DEFAULT '0',
			`time_stamp` int(10) unsigned NOT NULL,
			PRIMARY KEY (`supporter_id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `user_id` (`user_id`),
			KEY `total_share` (`total_share`)
		)  AUTO_INCREMENT=1 ;
	");


	//fundraising_category
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_category') ."` (
			`category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			 `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
			 `is_active` tinyint(1) NOT NULL DEFAULT '0',
			 `title` varchar(255) NOT NULL,
			 `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
			 `used` int(10) unsigned NOT NULL DEFAULT '0',
			 `ordering` int(11) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`category_id`),
			 KEY `parent_id` (`parent_id`,`is_active`),
			 KEY `is_active` (`is_active`)
		)  AUTO_INCREMENT=1 ;
	");


	//fundraising_category
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_campaign_category') ."` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`category_id` int(10) unsigned NOT NULL ,
			`campaign_id` int(10) unsigned NOT NULL ,
			PRIMARY KEY (`id`),
			KEY `campaign_category` (`category_id`,`campaign_id`),
			KEY `category_id` (`category_id`)
		)  AUTO_INCREMENT=1 ;
	");


	//fundraising_transaction
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_transaction') ."` (
			`transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`transaction_log` text DEFAULT NULL,
			`invoice` mediumtext DEFAULT NULL,
			`donor_id` int(10) unsigned NOT NULL,
			`campaign_id` int(10) unsigned NOT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			`amount` decimal(14,2) NOT NULL DEFAULT '0.00',
			`currency` varchar(4) NOT NULL DEFAULT 'USD',
			`status` tinyint(2) NOT NULL DEFAULT '0', 
			`paypal_account` varchar(255),
			`paypal_transaction_id` varchar(50),
			PRIMARY KEY (`transaction_id`),
			KEY `time_stamp` (`time_stamp`),
			KEY `campaign_id` (`campaign_id`),
			KEY `donor_id` (`donor_id`),
			KEY `status` (`status`)
		)  AUTO_INCREMENT=1 ;
	");

	
	//fundraising_campaign_owner_profile
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_campaign_owner_profile') ."` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(10) unsigned NOT NULL,
			`avg_rating` decimal(4,2) NOT NULL DEFAULT '0.00',
			`total_rating` int(10) unsigned NOT NULL DEFAULT '0',
			`is_need_updating` tinyint(1) NOT NULL DEFAULT '0', 
			`time_stamp` int(10) unsigned NOT NULL,
			PRIMARY KEY (`id`)
		)  AUTO_INCREMENT=1 ;
	");


	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_video') ."` (
			`video_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`campaign_id` int(10) unsigned NOT NULL,
			`title` varchar(255) DEFAULT NULL,
			`video_url` varchar(255) NOT NULL ,
			`image_path` varchar(255) DEFAULT NULL,
			`server_id` tinyint(3) NOT NULL DEFAULT '0',
			`embed_code` mediumtext NOT NULL,
			PRIMARY KEY (`video_id`)
		)  AUTO_INCREMENT=1 ;
	");


	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_image') ."` (
			`image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`campaign_id` int(10) unsigned NOT NULL,
			`title` varchar(255) DEFAULT NULL,
			`image_path` varchar(255) DEFAULT NULL,
			`server_id` tinyint(3) NOT NULL DEFAULT '0',
			`ordering` int(10) unsigned NOT NULL DEFAULT '0',
			`is_profile` tinyint(1) NOT NULL DEFAULT '0',
			`file_size` int(10) unsigned NOT NULL DEFAULT '0',
			`mime_type` varchar(150),
			`extension` varchar(20) NOT NULL,
			`width` smallint(4) unsigned DEFAULT '0',
			`height` smallint(4) unsigned DEFAULT '0',
			PRIMARY KEY (`image_id`),
			KEY `campaign_id` (`campaign_id`)
		)  AUTO_INCREMENT=1 ;
	");


	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_news') ."` (
			`news_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`campaign_id` int(10) unsigned NOT NULL,
			`headline` varchar(255) NOT NULL,
			`link` varchar(255) NOT NULL,
			`content` mediumtext NOT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			PRIMARY KEY (`news_id`),
			KEY `campaign_id` (`campaign_id`)
		)  AUTO_INCREMENT=1 ;
	");


	// map 1-1 with campaign
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_contact_info') ."` (
			`contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`campaign_id` int(10) unsigned NOT NULL,
			`full_name` varchar(255),
			`phone` varchar(20),
			`email_address` varchar(255),
			`country` varchar(255),
			`state` varchar(255),
			`city` varchar(255),
			`street` varchar(255),
			`about_me` mediumtext,
			`time_stamp` int(10) unsigned NOT NULL,
			PRIMARY KEY (`contact_id`),
			KEY `campaign_id` (`campaign_id`)
		)  AUTO_INCREMENT=1 ;
	");
	
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_invited') ."` (
			`invited_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`inviting_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`invited_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			`donor_id` int(10) unsigned NOT NULL DEFAULT '0',
			`invited_email` varchar(255),
			`campaign_id` int(10) unsigned NOT NULL,
			`time_stamp` int(10) unsigned NOT NULL,
			`type_id` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`invited_id`)
		)  AUTO_INCREMENT=1 ;
	");
	    
	    
	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_rating') ."` (
			 `rate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `item_id` int(10) unsigned NOT NULL,
			 `user_id` int(10) unsigned NOT NULL,
			 `rating` decimal(4,2) NOT NULL DEFAULT '0.00',
			 `time_stamp` int(10) unsigned NOT NULL,
			 PRIMARY KEY (`rate_id`),
			 KEY `item_id` (`item_id`,`user_id`),
			 KEY `item_id_2` (`item_id`)
		)  AUTO_INCREMENT=1 ;
	");


	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_follow') ."` (
			 `follow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `campaign_id` int(10) unsigned NOT NULL,
			 `user_id` int(10) unsigned NOT NULL,
			 `time_stamp` int(10) unsigned NOT NULL,
			 PRIMARY KEY (`follow_id`),
			 KEY `item_id` (`campaign_id`,`user_id`),
			 KEY `item_id_2` (`campaign_id`)
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_email_template') ."` (
			 `email_template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `type`  tinyint(2) NOT NULL DEFAULT '0' ,
			 `email_subject` mediumtext DEFAULT NULL,
			 `email_template` mediumtext DEFAULT NULL,
			 PRIMARY KEY (`email_template_id`)
		)  AUTO_INCREMENT=1 ;
	");

	$oDatabase->query("
	CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fundraising_email_queue') ."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `campaign_id` int(10) unsigned NOT NULL,
			 `receivers` mediumtext DEFAULT NULL,
			 `from` varchar(255) DEFAULT NULL ,
			 `email_subject` mediumtext DEFAULT NULL,
			 `time_stamp` int(10) unsigned NOT NULL,
			 `email_message` mediumtext DEFAULT NULL,
			 `is_sent`  tinyint(1) NOT NULL DEFAULT '0',
			 `is_site_user`  tinyint(1) NOT NULL DEFAULT '1',
			 PRIMARY KEY (`id`),
			 KEY `is_sent` (`is_sent`),
			 KEY `campaign_id` (`campaign_id`)
		)  AUTO_INCREMENT=1 ;
	");

	if(!$oDatabase->isField(Phpfox::getT('user_field'),'total_fundraising'))
	{
		$oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_field')."` ADD  `total_fundraising` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
	}

	if(!$oDatabase->isField(Phpfox::getT('user_activity'),'activity_fundraising'))
	{
	  $oDatabase->query("ALTER TABLE  `".Phpfox::getT('user_activity')."` ADD  `activity_fundraising` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
	}


	$aRow = $oDatabase->select('block_id')
			   ->from(Phpfox::getT('block'))
			   ->where("m_connection ='fundraising.profile' AND product_id = 'younet_fundraising' AND module_id ='profile' AND component ='pic'")
			   ->execute('getRow');

	if(!isset($aRow['block_id']))
	{
		// insert the pic block for viewing in profile
		$oDatabase->query("INSERT INTO `".Phpfox::getT('block')."` (`title`, `type_id`, `m_connection`, `module_id`, `product_id`, `component`, `location`, `is_active`, `ordering`, `disallow_access`, `can_move`, `version_id`) VALUES ('Profile Photo &amp; Menu', 0, 'fundraising.profile', 'profile', 'younet_fundraising', 'pic', '1', 1, 1, NULL, 0, NULL)");
	}

	/**
     *  in set email template , totally 9 type of email template
     *  need to add some variable
     *
     * [owner_name] => owner_name : name of user who create that campaign
     * [donor_name] => donor_name : name of user who contribute or donate to that campaign
     * [inviter_name] => inviter_name : name of user that invite other user to that campaign
     * [admin_reason] => admin_reason : the reason why admin close that campaign
     * [site_name] => site_url : this site name ( social network site )
     */

    $createcampaignsuccessful_owner = array(
        'type' => 1,
        'email_subject' => "Your Campaign has been created on [site_name] ",
        'email_template' => "
 Hello [owner_name]

 Congratulations on launching your \"[title]\"

 Here's a list of this to do to get you off to a great start:
 <ul>
     <li>Make your Campaign look great - add Photo galleries, YouTube URL and descriptions </li>
     <li>Send a donation email to everyone you know with our email feature </li>
     <li>Share your Campaign on Facebook, Twitter and other social networks </li>
     <li>Use Promote feature to get more donation </li>
 </ul>

 Let the fundraising begin.

 Regards, 
 [site_name]",
    );

    $thankdonor_donor = array(
        'type' => 2,
        'email_subject' => "Thank you for contributing a campaign ",
	'email_template' => "
 Dear [donor_name]

 Thank you for choosing to contribute [title] of [owner_name]. Your contribution is very much appreciated and the money you will raise will go towards a brighter future. Every dollar you raise makes a real difference for our campaign 

 For more information on how your donations is helping to make differences, you can visit our fundraising with the following link to get updated information 

 [campaign_url]

 Thank you again, and we look forward to your continued support.

 Regards,
 
[owner_name]
[site_name]"
    );

    $updatedonor_owner = array(
        'type' => 3,
        'email_subject' => "You have a new contributor ",
	'email_template' => "
 Hello [owner_name]

 [donor_name] has been contributed to your Campaign.

 [campaign_url]
 
 Regards, 
 [site_name]",
    );

    $campaignexpired_owner = array(
        'type' => 4,
        'email_subject' => "Your Campaign Expired",
	'email_template' => "
 Hello [owner_name]

 Your campaign has been expired and hidden from listings:

 [campaign_url]

 Regards, 
 [site_name]",
    );

    $campaignexpired_donor = array(
        'type' => 5,
        'email_subject' => "The campaign which you donated has been expired",
	'email_template' => "
 Dear [donor_name]

 The Campaign \"[title]\" is expired, please go to this link to check the status of this campaign:

 [campaign_url]

 Thank you for making difference and good luck

  Regards, 
 [site_name]",
    );

    $campaigncloseduetoreach_owner = array(
        'type' => 6,
        'email_subject' => "Your Campaign Reached the Fundraising Goal",
	'email_template' => "
 Hello [owner_name],

 Your campaign has been closed due to reaching the fundraising goal. Please check it here:

 [campaign_url]

 Regards, 
 [site_name]
"
    );

    $campaigncloseduetoreach_donor = array(
        'type' => 7,
        'email_subject' => "The Campaign which you donated has been reached goal",
	'email_template' => "
 Dear all donors

 Your campaign that you donated has been closed due to reaching the fundraising goal. Please check it here:

 [campaign_url]
 
 Regards, 
 [site_name]
",
    );

    $campaignclose_owner = array(
        'type' => 8,
        'email_subject' => "Your Campaign has been closed ",
	'email_template' => "
 Hello [owner_name]

 Your campaign has been closed due to [admin_reason]. Please check it here:

 [campaign_url]

 Regards, 
 [site_name]
",
    );

    $invitefriendletter_template = array(
        'type' => 9,
        'email_subject' => "[inviter_name] invited you to the fundraising campaign [title]",
        'email_template' => "
 Hello,

 [inviter_name] invited you to \"[title]\" 

 To check out this fundraising campaign, follow the link below:
[campaign_url]

 In addition, [inviter_name] added the following personal message

 Friends,

 I have just created a fundraising campaign: \"[title]\", since I care deeply about this crucial issue

 I'm trying to collect money for this issue, and I could really use your help

 To read more about what I am trying to do and to donate my fundraising , click here:
 [campaign_url]

 It will just take a minute

 Once you are done, please ask your friends to donate the fundraising campaign as well.

 Thank you for making a difference and good luck.

 Regards, 
 [owner_name]
		",
    );

	//make sure this table empty before inserting
	$oDatabase->query(" TRUNCATE " . Phpfox::getT('fundraising_email_template'). "	");	

    $aInsertEmails = array($createcampaignsuccessful_owner,$thankdonor_donor,$updatedonor_owner,$campaignexpired_owner,$campaignexpired_donor,$campaigncloseduetoreach_owner,$campaigncloseduetoreach_donor,$campaignclose_owner,$invitefriendletter_template);

    foreach($aInsertEmails as $aInsertEmail) {
        $oDatabase->insert(Phpfox::getT('fundraising_email_template') , $aInsertEmail);
    }

	 $oDatabase->query("INSERT IGNORE INTO `". Phpfox::getT('fundraising_category') ."` (`category_id`, `title`, `parent_id`, `time_stamp`, `used`, `is_active`) VALUES
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
	

}

ynfr_install301();

?>
