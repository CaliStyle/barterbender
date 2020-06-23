<?php
defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_account')."` (
  `account_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0',
  `start_employer_time` int(11) unsigned NOT NULL DEFAULT '0',
  `view_resume` tinyint(1) unsigned DEFAULT '0',
  `is_employee` tinyint(1) NOT NULL DEFAULT '0',
  `is_employer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`account_id`),
  KEY `user_id` (`user_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_addition')."` (
  `resume_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `website` text,
  `sport` text,
  `sport_parsed` text,
  `movies` text,
  `movies_parsed` text,
  `interests` text,
  `interestes_parsed` text,
  `music` text,
  `music_parsed` text,
  PRIMARY KEY (`resume_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_basicinfo')."` (
  `resume_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `status` enum('none','approving','denied','approved') DEFAULT 'none',
  `level_id` int(11) DEFAULT '0',
  `total_favorite` int(11) unsigned NOT NULL DEFAULT '0',
  `total_view` int(11) unsigned NOT NULL DEFAULT '0',
  `full_name` varchar(255) NOT NULL DEFAULT 'No Name',
  `birthday` char(10) DEFAULT NULL,
  `birthday_search` bigint(20) NOT NULL DEFAULT '0',
  `gender` smallint(3) unsigned DEFAULT '0',
  `marital_status` enum('single','married','other') DEFAULT 'single',
  `phone` text,
  `is_completed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `time_stamp` int(11) unsigned NOT NULL DEFAULT '0',
  `time_update` int(11) unsigned NOT NULL DEFAULT '0',
  `time_publish` int(11) NOT NULL DEFAULT '0',
  `imessage` text,
  `email` text,
  `image_path` varchar(255) DEFAULT '',
  `headline` varchar(255) DEFAULT '',
  `authorized_location` varchar(255) DEFAULT '',
  `authorized_country_iso` char(2) DEFAULT '',
  `authorized_country_child_id` mediumint(8) DEFAULT '0',
  `authorized_level_id` int(11) DEFAULT '0',
  `authorized_other_level` varchar(64) DEFAULT '',
  `location` varchar(255) DEFAULT '',
  `country_iso` char(2) DEFAULT '',
  `country_child_id` mediumint(8) DEFAULT '0',
  `city` varchar(255) DEFAULT '',
  `zip_code` varchar(20) DEFAULT '',
  `year_exp` smallint(3) unsigned NOT NULL DEFAULT '0',
  `summary` text,
  `summary_parsed` text,
  `skills` text,
  `server_id` tinyint(4) NOT NULL DEFAULT '0',
  `position_section` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`resume_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `level_id` (`level_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_category')."` (
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

$oDb -> query(
	"INSERT IGNORE INTO `".Phpfox::getT('resume_category')."` (`category_id`, `parent_id`, `is_active`, `name`, `name_url`, `time_stamp`, `used`, `ordering`) VALUES
		(1, 0, 1, 'Sales/Business Development', 'sales-business-development', 0, 0, 10),
		(2, 0, 1, 'Executive Management', 'executive-management', 0, 0, 5),
		(3, 0, 1, 'Marketing', 'marketing', 0, 0, 7),
		(4, 0, 1, 'IT - Software', 'it-software', 0, 0, 9),
		(5, 0, 1, 'Accounting / Auditing / Tax', 'accounting-auditing-tax', 0, 0, 1),
		(6, 0, 1, 'Customer Service', 'customer-service', 0, 0, 3),
		(7, 0, 1, 'Manufacturing / Process', 'manufatoring-process', 0, 0, 6),
		(8, 0, 1, 'Electrical / Eletronics', 'electrical-eletronics', 0, 0, 4),
		(9, 0, 1, 'Mechanical / Auto / Automation', 'mechanical-auto-automation', 0, 0, 8),
		(10, 0, 1, 'Administrative / Clerical', 'administrative-clerical', 0, 0, 2);"
);

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_category_data')."` (
  `resume_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`resume_id`,`category_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_certification')."` (
  `certification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL,
  `certification_name` varchar(255) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT '',
  `training_place` varchar(255) DEFAULT '',
  `start_month` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `start_year` smallint(4) unsigned NOT NULL DEFAULT '0',
  `end_month` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `end_year` smallint(4) unsigned NOT NULL DEFAULT '0',
  `note` text,
  `note_parsed` text,
  PRIMARY KEY (`certification_id`),
  KEY `resume_id` (`resume_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_completeness_weight')."` (
  `name` varchar(50) NOT NULL,
  `score` int(11) DEFAULT '1',
  `inumber` int(11) DEFAULT '1',
  `iorder` int(11) DEFAULT '1',
  PRIMARY KEY (`name`)
);");

$oDb-> query("INSERT IGNORE INTO `".Phpfox::getT('resume_completeness_weight')."` (`name`, `score`, `inumber`, `iorder`) VALUES
	('full_name', 1, 1, 1),
	('birthday', 1, 1, 1),
	('gender', 1, 1, 1),
	('marital_status', 1, 1, 1),
	('phone', 1, 1, 1),
	('imessage', 1, 1, 1),
	('email', 1, 1, 1),
	('image_path', 1, 1, 1),
	('headline', 1, 1, 1),
	('authorized_country_iso', 1, 1, 1),
	('authorized_location', 1, 1, 1),
	('authorized_level_id', 1, 1, 1),
	('country_iso', 1, 1, 1),
	('city', 1, 1, 1),
	('zip_code', 1, 1, 1),
	('category', 1, 1, 1),
	('level_id', 1, 1, 1),
	('year_exp', 1, 1, 1),
	('summary', 1, 1, 1),
	('skills', 1, 1, 1),
	('experience', 1, 1, 1),
	('language', 1, 1, 1),
	('publication', 1, 1, 1),
	('certification', 1, 1, 1),
	('addition', 1, 1, 1),
	('education', 1, 1, 1);"
);

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_education')."` (
  `education_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL DEFAULT '0',
  `school_name` varchar(255) DEFAULT '',
  `degree` varchar(255) DEFAULT '',
  `field` varchar(255) DEFAULT '',
  `start_month` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `start_year` smallint(4) unsigned NOT NULL DEFAULT '0',
  `end_month` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `end_year` smallint(4) unsigned NOT NULL DEFAULT '0',
  `grade` varchar(10) DEFAULT '',
  `activity` text,
  `activity_parsed` text,
  `note` text,
  `note_parsed` text,
  PRIMARY KEY (`education_id`),
  KEY `resume_id` (`resume_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_experience')."` (
  `experience_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL,
  `level_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'set null on cascadue delete',
  `company_name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  `start_month` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `start_year` smallint(4) unsigned NOT NULL DEFAULT '0',
  `end_month` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `end_year` smallint(4) unsigned NOT NULL DEFAULT '0',
  `is_working_here` tinyint(1) NOT NULL DEFAULT '0',
  `show_flag` smallint(5) unsigned NOT NULL DEFAULT '0',
  `description` text,
  `description_parsed` text,
  PRIMARY KEY (`experience_id`),
  KEY `level_id` (`level_id`),
  KEY `resume_id` (`resume_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_favorite')."` (
  `favorite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `time_stamp` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`favorite_id`),
  KEY `user_id` (`user_id`),
  KEY `resume_id` (`resume_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_language')."` (
  `language_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `level` varchar(255) NOT NULL DEFAULT '',
  `note` text,
  `note_parsed` text,
  PRIMARY KEY (`language_id`),
  KEY `resume_id` (`resume_id`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_level')."` (
  `level_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `used` int(11) unsigned DEFAULT 0,
  PRIMARY KEY (`level_id`)
);");

$oDb -> query("INSERT IGNORE INTO `".Phpfox::getT('resume_level')."` (`level_id`, `name`) VALUES
(1, 'Developer'),
(2, 'Technical Architect'),
(3, 'Business Analyst'),
(4, 'Project Manager'),
(5, 'Director'),
(6, 'Designer'),
(7, 'Associate Developer'),
(8, 'Technical Writer'),
(9, 'Technical Leader'),
(10, 'Technical Director');
");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_publication')."` (
  `publication_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL,
  `type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `other_type` varchar(255) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `publisher` varchar(255) DEFAULT '',
  `publication_url` varchar(255) DEFAULT '',
  `published_day`  tinyint(2) unsigned NOT NULL DEFAULT '0',
  `published_month` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `published_year` smallint(4) unsigned NOT NULL DEFAULT '0',
  `author` text,
  `note` text,
  `note_parsed` text,
  PRIMARY KEY (`publication_id`),
  KEY `resume_id` (`resume_id`),
  KEY `published_year_magazine_published_month` (`published_day`,`published_year`,`title`(180),`published_month`)
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_skill')."` (
  `skill_name` varchar(255) NOT NULL DEFAULT '',
  `ordering` smallint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`skill_name`(180))
);");

$oDb -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_viewme')."` (
  `view_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `resume_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_id` int(11) unsigned NOT NULL DEFAULT '0',
  `time_stamp` int(11) unsigned NOT NULL DEFAULT '0',
  `total_view` int(11) unsigned NOT NULL DEFAULT '0',
  `note` text NOT NULL,
  `sent_messages` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`view_id`),
  KEY `user_id` (`user_id`),
  KEY `resume_id` (`resume_id`)
);");

$oDb ->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('resume_setting')."`(
	`setting_id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
	`name`	varchar(255)NOT NULL,
	`value` text,
	PRIMARY KEY(`setting_id`)
);");

$oDb -> query("INSERT IGNORE INTO `".Phpfox::getT('resume_setting')."` (`name`,`value`) VALUES 
('who_viewed_me_group_id',''),
('view_all_resume_group_id','');");


// Add resume counter into user field
if(!$this->database()->isField(Phpfox::getT('user_field'),'total_resume'))
{
	$this->database()->query("ALTER TABLE `" . Phpfox::getT('user_field') . "`
		ADD COLUMN `total_resume` int(10) NOT NULL DEFAULT '0';");
}

//firstly check whether the block exits
$aRow = $this->database()->select('block_id')
        ->from(Phpfox::getT('block'))
        ->where("m_connection ='resume.profile' AND product_id = 'phpfox' AND module_id ='profile' AND component ='pic'")
        ->execute('getRow');

if(!isset($aRow['block_id']))
{
    // insert the feed share link of module photo again
    $this->database()->query("INSERT INTO `".Phpfox::getT('block')."` (`title`, `type_id`, `m_connection`, `module_id`, `product_id`, `component`, `location`, `is_active`, `ordering`, `disallow_access`, `can_move`, `version_id`)
    VALUES ('Profile Photo & Menu', 0, 'resume.profile', 'profile', 'phpfox', 'pic', '1', 1, 1, NULL, 1, 3)");
}
	
?>