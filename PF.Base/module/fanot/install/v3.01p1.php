<?php
  defined('PHPFOX') or exit('NO DICE!');
 
  function alter_setting()
  {
    $oDb = Phpfox::getLib('phpfox.database');
    $sTable = Phpfox::getT('setting');
	$sql = "UPDATE `".$sTable."` SET `is_hidden` = 1 WHERE `module_id` ='fanot' AND `var_name` = 'how_many_notifications_to_show'";
    $oDb->query($sql);    
  }
 
  alter_setting();  
?>
