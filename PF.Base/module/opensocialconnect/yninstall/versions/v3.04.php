<?php

function opensocialconnect_install304()
{
    $oDb = Phpfox::getLib('phpfox.database');
	
    $oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialconnect_options')."` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `service` varchar(128) NOT NULL,
	  `name` varchar(128) NOT NULL,
	  `label` varchar(128) NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `service_name` (`service`,`name`)
	);");
	 
	$oDb->query("
		INSERT IGNORE INTO `".Phpfox::getT('socialconnect_options')."` (`id`, `service`, `name`, `label`) VALUES
		(1, 'yahoo', 'email', 'Email'),
		(2, 'yahoo', 'full_name', 'Full Name'),
		(3, 'yahoo', 'FirstName', 'First Name'),
		(4, 'yahoo', 'LastName', 'Last Name'),
		(5, 'yahoo', 'gender', 'Gender'),
		(6, 'google', 'full_name', 'Full Name'),
		(7, 'google', 'email', 'Email'),
		(8, 'google', 'FirstName', 'First Name'),
		(9, 'google', 'LastName', 'Last Name'),
		(10, 'google', 'gender', 'Gender'),
		(29, 'facebook', 'id', 'ID'),
		(30, 'facebook', 'name', 'Name'),
		(31, 'facebook', 'first_name', 'First Name'),
		(32, 'facebook', 'birthday', 'Birthday'),
		(33, 'facebook', 'last_name', 'Last Name'),
		(34, 'facebook', 'link', 'Link'),
		(35, 'facebook', 'gender', 'Gender'),
		(36, 'facebook', 'timezone', 'Timezone'),
		(37, 'facebook', 'locale', 'Locale'),
		(41, 'twitter', 'notifications', 'Notifications'),
		(43, 'twitter', 'description', 'Description'),
		(44, 'twitter', 'lang', 'Language'),
		(46, 'twitter', 'location', 'Location'),
		(49, 'twitter', 'time_zone', 'Timezone'),
		(51, 'twitter', 'user_name', 'User Name'),
		(52, 'twitter', 'first_name', 'First Name'),
		(53, 'twitter', 'following', 'Following'),
		(56, 'twitter', 'followers_count', 'Followers Count'),
		(58, 'twitter', 'contributors_enabled', 'Contributors'),
		(62, 'twitter', 'favourites_count', 'Favourites Count'),
		(64, 'twitter', 'screen_name', 'Screent Name'),
		(66, 'twitter', 'name', 'Name'),
		(67, 'twitter', 'friends_count', 'Friends Count'),
		(68, 'twitter', 'id', 'User ID'),
		(69, 'twitter', 'follow_request_sent', 'Follow Request Sent'),
		(70, 'twitter', 'about_me', 'About Me'),
		(71, 'twitter', 'url', 'URL'),
		(77, 'twitter', 'last_name', 'Last Name'),
		(85, 'twitter', 'website', 'Website'),
		(128, 'facebook', 'email', 'Email'),
		(129, 'linkedin', 'id', 'User Id'),
		(130, 'linkedin', 'first_name', 'First Name'),
		(131, 'linkedin', 'last_name', 'Last Name'),
		(132, 'linkedin', 'headline', 'Headline'),
		(135, 'linkedin', 'user_name', 'User Name'),
		(136, 'linkedin', 'current-status', 'Status'),
		(137, 'linkedin', 'email-address', 'Email'),
		(138, 'linkedin', 'displayname', 'Full Name'),
		(165, 'flickr', 'user_name', 'User Name'),
		(166, 'flickr', 'realname', 'Real Name'),
		(167, 'flickr', 'location', 'Location'),
		(168, 'flickr', 'photosurls', 'Photo Url'),
		(170, 'flickr', 'profileurls', 'Profile Url'),
		(251, 'clavid', 'nickname', 'Nickname'),
		(252, 'clavid', 'email', 'Email'),
		(253, 'clavid', 'fullname', 'Full Name'),
		(254, 'clavid', 'dob', 'Date of Birth'),
		(255, 'clavid', 'gender', 'Gender'),
		(256, 'clavid', 'postcode', 'Postcode'),
		(257, 'clavid', 'country', 'Country'),
		(258, 'clavid', 'language', 'Language'),
		(259, 'clavid', 'timezone', 'Timezone'),
		(311, 'liquidid', 'nickname', 'Nickname'),
		(312, 'liquidid', 'email', 'Email'),
		(313, 'liquidid', 'fullname', 'Full Name'),
		(314, 'liquidid', 'dob', 'Date of Birth'),
		(315, 'liquidid', 'gender', 'Gender'),
		(316, 'liquidid', 'postcode', 'Postcode'),
		(317, 'liquidid', 'country', 'Country'),
		(318, 'liquidid', 'language', 'Language'),
		(319, 'liquidid', 'timezone', 'Timezone'),
		(371, 'verisign', 'nickname', 'Nickname'),
		(372, 'verisign', 'email', 'Email'),
		(373, 'verisign', 'fullname', 'Full Name'),
		(374, 'verisign', 'dob', 'Date of Birth'),
		(375, 'verisign', 'gender', 'Gender'),
		(376, 'verisign', 'postcode', 'Postcode'),
		(377, 'verisign', 'country', 'Country'),
		(378, 'verisign', 'language', 'Language'),
		(379, 'verisign', 'timezone', 'Timezone'),
		(381, 'wordpress', 'nickname', 'nickname'),
		(382, 'wordpress', 'email', 'Email'),
		(383, 'wordpress', 'fullname', 'Full Name'),
		(384, 'wordpress', 'dob', 'Date of Birth'),
		(385, 'wordpress', 'gender', 'Gender'),
		(386, 'wordpress', 'postcode', 'Postcode'),
		(387, 'wordpress', 'country', 'Country'),
		(389, 'wordpress', 'timezone', 'Timezone'),
		(390, 'facebook', 'website', 'Website'),
		(391, 'facebook', 'user_name', 'User Name'),
		(392, 'live', 'email', 'Email'),
		(393, 'live', 'full_name', 'Full Name'),
		(394, 'live', 'first_name', 'First Name'),
		(395, 'live', 'last_name', 'Last Name');
	");
	 
	 
	 $oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('socialconnect_fields')."` (
		`id` int(11) NOT NULL auto_increment,
		`question` varchar(32) collate utf8_unicode_ci default NULL,
		`field` varchar(32) collate utf8_unicode_ci default NULL,
		`service` varchar(32) collate utf8_unicode_ci default NULL,
		PRIMARY KEY  (`id`)
	);");
	
	$oDb->query("
		INSERT IGNORE INTO `".Phpfox::getT('socialconnect_fields')."` (`id`, `question`, `field`, `service`) VALUES
		(1, 'full_name', 'name', 'facebook'),
		(2, 'email', 'email', 'facebook'),
		(3, 'user_name', 'user_name', 'facebook'),
		(4, 'gender', 'gender', 'facebook'),
		(5, 'birthday', 'birthday', 'facebook'),
		(6, 'full_name', 'full_name', 'yahoo'),
		(7, 'gender', 'gender', 'yahoo'),
		(8, 'email', 'email', 'yahoo'),
		(9, 'user_name', 'full_name', 'yahoo'),
		(10, 'full_name', 'full_name', 'google'),
		(11, 'gender', 'gender', 'google'),
		(12, 'email', 'email', 'google'),
		(13, 'user_name', 'full_name', 'google'),
		(14, 'full_name', 'name', 'twitter'),
		(15, 'full_name', 'displayname', 'linkedin'),
		(16, 'user_name', 'user_name', 'linkedin'),
		(17, 'user_name', 'user_name', 'twitter'),
		(18, 'full_name', 'full_name', 'live'),
		(19, 'email', 'email', 'live'),
		(20, 'user_name', 'full_name', 'live'),
		(21, 'full_name', 'fullname', 'verisign'),
		(22, 'gender', 'gender', 'verisign'),
		(23, 'email', 'email', 'verisign'),
		(24, 'birthday', 'dob', 'verisign'),
		(25, 'user_name', 'nickname', 'verisign'),
		(26, 'full_name', 'fullname', 'clavid'),
		(27, 'gender', 'gender', 'clavid'),
		(28, 'email', 'email', 'clavid'),
		(29, 'birthday', 'dob', 'clavid'),
		(30, 'user_name', 'nickname', 'clavid'),
		(31, 'full_name', 'full_name', 'flickr'),
		(32, 'user_name', 'user_name', 'flickr'),
		(33, 'full_name', 'fullname', 'wordpress'),
		(34, 'gender', 'gender', 'wordpress'),
		(35, 'email', 'email', 'wordpress'),
		(36, 'birthday', 'dob', 'wordpress'),
		(37, 'user_name', 'nickname', 'wordpress'),
		(38, 'full_name', 'fullname', 'liquidid'),
		(39, 'gender', 'gender', 'liquidid'),
		(40, 'email', 'email', 'liquidid'),
		(41, 'birthday', 'dob', 'liquidid'),
		(42, 'user_name', 'nickname', 'liquidid');
	");
	
	$oDb->query("
		INSERT IGNORE INTO `".Phpfox::getT('socialconnect_fields')."` (`question`, `field`, `service`) VALUES
		('email', 'email-address', 'linkedin');
	");

	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'identity'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'xlogon'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'blogses'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'netlog'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'livejournal'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'hyves'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'picasa'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'myopenid'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'meinguter'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'verisign'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'clavid'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'flickr'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'wordpress'");
	$oDb->query("DELETE FROM `".Phpfox::getT('socialconnect_services')."` WHERE `name` = 'liquidid'");
	 
	 
}
opensocialconnect_install304();
?>