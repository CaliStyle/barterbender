<?php

defined('PHPFOX') or exit('NO DICE!');


function ync_install401()
{
    $oDatabase = Phpfox::getLib('phpfox.database') ;
	
	$oDatabase->query("
		DELETE FROM `".Phpfox::getT('coupon_email_template')."` WHERE 1;
	");
    
	$oDatabase->query("
	INSERT IGNORE INTO `".Phpfox::getT('coupon_email_template')."` (`email_template_id`, `type`, `email_subject`, `email_template`, `email_template_parsed`) VALUES
		(1, 1, 'Your Coupon has been created on [social_network_site]', 'Hello [owner_name],\n\nCongratulations! You have just created a coupon [coupon_name]. [coupon_link]\n\nBest Regards,\n\n[site_name]', 'Hello [owner_name],\r<br />\r<br />Congratulations! You have just created a coupon [coupon_name]. [coupon_link]\r<br />\r<br />Best Regards,\r<br />\r<br />[site_name]'),
		(2, 2, 'Your Coupon has been approved on [social_network_site]', 'Hello [owner_name], \n\nYour Coupon [coupon_name] has just been approved. For more information please visit [coupon_link].\n\nBest Regards,\n\n[site_name]', 'Hello [owner_name], \r<br />\r<br />Your Coupon [coupon_name] has just been approved. For more information please visit [coupon_link].\r<br />\r<br />Best Regards,\r<br />\r<br />[site_name]'),
		(3, 3, 'Your Coupon has been Featured', 'Hello [owner_name],\n\nYour Coupon [coupon_name] has just been set as featured.\n[coupon_link]\n\nBest Regards,\n\n[site_name]', 'Hello [owner_name],\r<br />\r<br />Your Coupon [coupon_name] at [coupon_link] has just been set as featured.\r<br />[coupon_link]\r<br />\r<br />Best Regards,\r<br />\r<br />[site_name]'),
		(4, 4, 'Your Coupon has been started to running on [social_network_site]', 'Hello [owner_name],\n\nYour Coupon [coupon_name] started running at [start_date].\n\nBest Regards, \n[site_name]', 'Hello [owner_name],\r<br />\r<br />Your Coupon [coupon_name] started running at [start_date].\r<br />\r<br />Best Regards, \r<br />[site_name]'),
		(5, 5, 'Your Coupon was closed', 'Hello [owner_name],\n\nYour coupon [coupon_name] has been closed and hidden from listings.\n[coupon_link]\n\nBest Regards, \n\n[site_name]', 'Hello [owner_name],\r<br />\r<br />Your coupon [coupon_name] has been closed and hidden from listings.\r<br />[coupon_link]\r<br />\r<br />Best Regards, \r<br />\r<br />[site_name]'),
		(6, 6, 'Your Coupon has been claimed', 'Hello [owner_name], \n\nYour coupon [coupon_name] has just been bought by customer with below information:\n\n   Name: [claimer_name]\n\n   Email Address: [claimer_email]\n\n   Link of Coupon: [coupon_link]\n\n   Name of coupon: [coupon_name]\n\n   Expired date: [expired_date]\n\n   Coupon Code:  [coupon_code]\n\nBest Regards, \n[site_name]', 'Hello [owner_name] \r<br />\r<br />Your coupon [coupon_name] has just been bought by customer with below information:\r<br />\r<br />   Name: [claimer_name]\r<br />\r<br />   Email Address: [claimer_email]\r<br />\r<br />   Link of Coupon: [coupon_link]\r<br />\r<br />   Name of coupon: [coupon_name]\r<br />\r<br />   Expired date: [expired_date]\r<br />\r<br />   Coupon Code:  [coupon_code]\r<br />\r<br />Best Regards, \r<br />[site_name]'),
		(7, 7, 'You have just claimed a coupon!', 'Hello [claimer_name],\n\nYou have just bought a deal with below information:\n\n   Link of Coupon: [coupon_link]\n\n   Name of coupon: [coupon_name]\n\n   Expired date: [expired_date]\n\n   Address: [coupon_address]\n\n   Coupon Code:  [coupon_code]\n\nYour information:\n\n   Name: [claimer_name]\n\n   Email Address: [claimer_email]\n\nBest Regards, \n[site_name]', 'Hello [claimer_name],\r<br />\r<br />You have just bought a deal with below information:\r<br />\r<br />   Link of Coupon: [coupon_link]\r<br />\r<br />   Name of coupon: [coupon_name]\r<br />\r<br />   Expired date: [expired_date]\r<br />\r<br />   Address: [coupon_address]\r<br />\r<br />   Coupon Code:  [coupon_code]\r<br />\r<br />Your information:\r<br />\r<br />   Name: [claimer_name]\r<br />\r<br />   Email Address: [claimer_email]\r<br />\r<br />Best Regards, \r<br />[site_name]'),
		(8, 8, 'The coupon has been paused', 'Dear [claimer_name], \n\nThe Coupon [coupon_name] will be expired at [expired_date]. You have only one day to use it, please let''s go\n\nThank you and good luck\n\nBest Regards,\n [site_name]', 'Dear [claimer_name] \n\nThe Coupon [coupon_name] will be expired at [expired_date]. You have only one day to use it, please let''s go\n\nThank you and good luck\n\nBest Regards, \n[site_name]'),
		(9, 9, 'Your Coupon has been resumed', 'Hello [owner_name], \n\nYour coupon has been closed and hidden from listings: \n[coupon_link] \n\nBest Regards, \n[site_name]', 'Hello [owner_name] \n\nYour coupon has been closed and hidden from listings: [coupon_link] \n\nBest Regards, [site_name]');
	");
    
}

ync_install401();

?>