<?php

function jobposting_install301p11()
{
    $oDb = Phpfox::getLib('phpfox.database');
    
	$oDb->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('jobposting_job_category') ."` (
      `category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
      `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
      `is_active` tinyint(1) NOT NULL DEFAULT '0',
      `name` varchar(255) NOT NULL,
      `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
      `used` int(10) unsigned NOT NULL DEFAULT '0',
      `ordering` int(11) unsigned NOT NULL DEFAULT '0',
      `name_url` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`category_id`),
      KEY `parent_id` (`parent_id`,`is_active`),
      KEY `is_active` (`is_active`)
	);");

    $oDb->query("
		INSERT IGNORE INTO `".Phpfox::getT('jobposting_job_category')."`(`category_id`, `name`, `parent_id`, `time_stamp`, `used`, `is_active`) VALUES
			(1, 'Accounting/Finance', 0, 1484189658, 0, 1),
			(2, 'Admin/Human Resources', 0, 1484189658, 0, 1),
			(3, 'Arts/Media/Communications', 0, 1484189658, 0, 1),
			(4, 'Building/Construction', 0, 1484189658, 0, 1),
			(5, 'Computer/Information Technology', 0, 1484189658, 0, 1),
			(6, 'Education/Training', 0, 1484189658, 0, 1),
			(7, 'Engineering', 0, 1484189658, 0, 1),
			(8, 'Heathcare', 0, 1484189658, 0, 1),
			(9, 'Hotel/Restaurant', 0, 1484189658, 0, 1),
			(10, 'Manufacturing', 0, 1484189658, 0, 1),
			(11, 'Sales/Marketing', 0, 1484189658, 0, 1),
			(12, 'Sciences', 0, 1484189658, 0, 1),
			(13, 'Services', 0, 1484189658, 0, 1),
			(14, 'Other', 0, 1484189658, 0, 1),
			(15, 'Audit & Taxation', 1, 1484189658, 0, 1),
			(16, 'Banking/Financial', 1, 1484189658, 0, 1),
			(17, 'Corporate Finance/Investment', 1, 1484189658, 0, 1),
			(18, 'General/Cost Accounting', 1, 1484189658, 0, 1),
			(19, 'Clerical/Administrative', 2, 1484189658, 0, 1),
			(20, 'Human Resources', 2, 1484189658, 0, 1),
			(21, 'Secretarial', 2, 1484189658, 0, 1),
			(22, 'Top Management', 2, 1484189658, 0, 1),
			(23, 'Advertising', 3, 1484189658, 0, 1),
			(24, 'Arts/Creative Design', 3, 1484189658, 0, 1),
			(25, 'Entertainment', 3, 1484189658, 0, 1),
			(26, 'Public Relations', 3, 1484189658, 0, 1),
			(27, 'Architect/Interior Design', 4, 1484189658, 0, 1),
			(28, 'Civil Engineering/Construction', 4, 1484189658, 0, 1),
			(29, 'Property/Real Estate', 4, 1484189658, 0, 1),
			(30, 'Quantity Surveying', 4, 1484189658, 0, 1),
			(31, 'IT - Hardware', 5, 1484189658, 0, 1),
			(31, 'IT - Network/Sys/DBA', 5, 1484189658, 0, 1),
			(33, 'IT - Software', 5, 1484189658, 0, 1),
			(34, 'Education', 6, 1484189658, 0, 1),
			(35, 'Training&Dev', 6, 1484189658, 0, 1),
			(36, 'Chemical Engineering', 7, 1484189658, 0, 1),
			(37, 'Electronics Engineering', 7, 1484189658, 0, 1),
			(38, 'Electrial ', 7, 1484189658, 0, 1),
			(39, 'Environmental', 7, 1484189658, 0, 1),
			(40, 'Industrial', 7, 1484189658, 0, 1),
			(41, 'Mechanical/Automotive', 7, 1484189658, 0, 1),
			(42, 'Oil/Gas', 7, 1484189658, 0, 1),
			(43, 'Other', 7, 1484189658, 0, 1),
			(44, 'Doctor/Diagosis', 8, 1484189658, 0, 1),
			(45, 'Phamacy', 8, 1484189658, 0, 1),
			(46, 'Nurse/Medical Support', 8, 1484189658, 0, 1),
			(47, 'Food/Beverage/Restaurant', 9, 1484189658, 0, 1),
			(48, 'Hotel/Tourism', 9, 1484189658, 0, 1),
			(49, 'Maintenance', 10, 1484189658, 0, 1),
			(50, 'Manufacturing', 10, 1484189658, 0, 1),
			(51, 'Process Design & Control', 10, 1484189658, 0, 1),
			(52, 'Purchasing/Material Mgmt', 10, 1484189658, 0, 1),
			(53, 'Quality Asurance', 10, 1484189658, 0, 1),
			(54, 'Sales/Corporate', 11, 1484189658, 0, 1),
			(55, 'Marketing/Business Dev', 11, 1484189658, 0, 1),
			(56, 'Merchandising', 11, 1484189658, 0, 1),
			(57, 'Retail Sales', 11, 1484189658, 0, 1),
			(58, 'Sales - Eng/Tech/IT', 11, 1484189658, 0, 1),
			(59, 'Sales - Financial Services', 11, 1484189658, 0, 1),
			(60, 'Sales - Telesales/Telemarketing', 11, 1484189658, 0, 1),
			(61, 'Actuarial/Statistics', 12, 1484189658, 0, 1),
			(62, 'Agriculture', 12, 1484189658, 0, 1),
			(63, 'Aviation', 12, 1484189658, 0, 1),
			(64, 'Biotechnology', 12, 1484189658, 0, 1),
			(65, 'Chemistry', 12, 1484189658, 0, 1),
			(66, 'Food Tech/Nutritionist', 12, 1484189658, 0, 1),
			(67, 'Geology/Geophysics', 12, 1484189658, 0, 1),
			(68, 'Science & Technology', 12, 1484189658, 0, 1),
			(69, 'Security/Armed Forces', 13, 1484189658, 0, 1),
			(70, 'Customer Service', 13, 1484189658, 0, 1),
			(71, 'Logistics/Supply Chain', 13, 1484189658, 0, 1),
			(72, 'Law/Legal Services', 13, 1484189658, 0, 1),
			(73, 'Personal Care', 13, 1484189658, 0, 1),
			(74, 'Social Services', 13, 1484189658, 0, 1),
			(75, 'Tech& Helpdesk Support', 13, 1484189658, 0, 1),
			(76, 'General Work', 13, 1484189658, 0, 1),
			(77, 'Journalist/Editors', 14, 1484189658, 0, 1),
			(78, 'Publishing', 14, 1484189658, 0, 1),
			(79, 'Other', 14, 1484189658, 0, 1);
		");


    $oDb->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('jobposting_job_category_data') ."` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `job_id` int(10) unsigned NOT NULL,
      `no` tinyint(1) unsigned NOT NULL,
      `category_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`id`),
      KEY `job_category` (`category_id`,`job_id`),
      KEY `category_id` (`category_id`)
    );");

      $oDb->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('jobposting_company_working_request') ."` (
      `working_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int(10) unsigned NOT NULL,
      `company_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`working_id`) 
    );");

  $oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_applyjobpackage")."` (
    `package_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `apply_number` int(10) unsigned NOT NULL DEFAULT '0',
    `expire_number` int(10) unsigned DEFAULT NULL,
    `expire_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0: never expire, 1: day, 2: week, 3: month',
    `fee` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
    `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
    PRIMARY KEY (`package_id`)
  );");
  
  $oDb->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("jobposting_applyjobpackage_data")."` (
    `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `package_id` int(10) unsigned NOT NULL,
    `remaining_apply` int(10) unsigned NOT NULL,
    `valid_time` int(10) unsigned NOT NULL,
    `expire_time` int(10) unsigned NOT NULL DEFAULT '0',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
      PRIMARY KEY (`data_id`)
  );");


}

jobposting_install301p11();

?>