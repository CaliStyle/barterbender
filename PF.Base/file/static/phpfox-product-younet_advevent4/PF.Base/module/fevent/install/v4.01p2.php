<?php
function ynfe_install401p2()
{
    $oDb = Phpfox::getLib('phpfox.database');
    if(!$oDb->isField(Phpfox::getT('fevent_feed'),'parent_module_id'))
	{
    	$oDb->query("ALTER TABLE `". Phpfox::getT('fevent_feed') ."`
        	ADD  `parent_module_id` INT( 10 ) NOT NULL DEFAULT  '0' AFTER  `parent_user_id`;");
	}
	$oDb->query("UPDATE `". Phpfox::getT('setting')."` SET `is_hidden` = 1 WHERE `module_id` = 'fevent' AND `var_name` = 'google_api_keys_location';");

}
ynfe_install401p2();
?>