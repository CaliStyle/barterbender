<?php

defined('PHPFOX') or exit('NO DICE!');

function ynfr_install401()
{
    $oDatabase = Phpfox::getLib('database');
    $oDatabase->query("CREATE TABLE IF NOT EXISTS ".Phpfox::getT('fundraising_gateway_log'). "(
	  `log_id` int(1) NOT NULL AUTO_INCREMENT,
	  `gateway_id` varchar(75) DEFAULT NULL,
	  `log_data` mediumtext NOT NULL,
	  `ip_address` varchar(15) DEFAULT NULL,
	  `time_stamp` int(10) NOT NULL,
	  PRIMARY KEY (`log_id`)
	) ENGINE=InnoDB;");
	
	$oDatabase->query("CREATE TABLE IF NOT EXISTS ".Phpfox::getT('fundraising_gatewayapi'). " (
		  `gateway_id` varchar(45) NOT NULL DEFAULT '',
		  `title` varchar(150) NOT NULL,
		  `description` mediumtext,
		  `is_active` tinyint(1) DEFAULT NULL,
		  `is_test` tinyint(2) DEFAULT NULL,
		  `setting` mediumtext,
		  PRIMARY KEY (`gateway_id`)
		) ENGINE=InnoDB ;");
		
	$oDatabase->query("INSERT IGNORE INTO ".Phpfox::getT('fundraising_gatewayapi'). " (`gateway_id`, `title`, `description`, `is_active`, `is_test`, `setting`) VALUES
		('paypal', 'Paypal', 'Add some information about Paypal gateway', 1, 1, NULL);");
}

ynfr_install401();

?>