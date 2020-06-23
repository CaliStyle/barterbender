<?php

function videochannel_install302p15()
{
    $oDb = Phpfox::getLib('phpfox.database');
    
	$oDb->query("DELETE FROM `". Phpfox::getT('setting') ."`
		WHERE var_name = 'video_enable_mass_uploader' and product_id = 'younet_videochannel';");

	 
}

videochannel_install302p15();

?>