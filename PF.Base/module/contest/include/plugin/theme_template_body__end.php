<?php

defined('PHPFOX') or exit('NO DICE!');
if ( 	Phpfox::isModule('contest') && 		
		Phpfox::getLib('module')->getFullControllerName() == 'contest.add') 
{
	if(Phpfox::getLib('request')->get('tab') == 'email_conditions')
	{
		echo '<script type="text/javascript">
		$Behavior.yncontest_setEditor = function() {
			Editor.setId("message");
		} </script>';
		 
	}
	else{
		echo '<script type="text/javascript">
		$Behavior.yncontest_setEditor = function() {
			Editor.setId("yn_contest_add_description");
		} 
		</script>';
	}
}

?>



