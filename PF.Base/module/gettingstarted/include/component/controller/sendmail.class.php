<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_component_controller_sendmail extends Phpfox_Component
{
	public function process()
	{
		$aSettings=phpfox::getService("gettingstarted.settings")->getSettings(0);
		if($aSettings['active_email_remainder'] == '1')
		{
			Phpfox::getService("gettingstarted.process")->SendMail();
		}
	}
}
?>
