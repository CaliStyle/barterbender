<?php

function opensocialconnect_install305p2()
{
    $oDb = Phpfox::getLib('phpfox.database');

	$oDb->query("
		INSERT IGNORE INTO `".Phpfox::getT('socialconnect_fields')."` (`question`, `field`, `service`) VALUES
		('email', 'email-address', 'linkedin');
	"); 
	 
}
opensocialconnect_install305p2();
?>