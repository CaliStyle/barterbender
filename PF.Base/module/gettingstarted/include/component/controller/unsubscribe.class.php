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

class Gettingstarted_component_controller_unsubscribe extends Phpfox_Component
{
	public function process()
	{
		$sStrParse = $this->request()->get('id');
		$aUnsubscribe = explode('type', $sStrParse);
		$user_name = Phpfox::getUserBy('user_name');
		$full_name = '';
		if(isset($aUnsubscribe) && !empty($aUnsubscribe) && count($aUnsubscribe) > 0)
		{
	
			$full_name =Phpfox::getLib('database')->select('u.full_name')
					  ->from(Phpfox::getT('user'), 'u')
					  ->where('u.user_id = '.$aUnsubscribe[0])
					  ->execute('getSlaveField');
			Phpfox::getService('gettingstarted')->updateUnsubscribeLetter($aUnsubscribe[0], $aUnsubscribe[1]);
			
		}
		$sNotice = 'Hello '.$full_name.' <br/> You will not receive this email in the future.';
		$this->template()->assign(array('sNotice'=>$sNotice));
	}
}
?>
