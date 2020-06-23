<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Block_Invite extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!Phpfox::isUser())
		{
			return false;
		}
		
		$aEventInvites = Phpfox::getService('fevent')->getInviteForUser();
		
		$oHelper = Phpfox::getService('fevent.helper');

		if(count($aEventInvites)){
			foreach ($aEventInvites as $key => $aEvent) {
				$oHelper->getImageDefault($aEventInvites[$key],'block');
			}
		}
		if (!count($aEventInvites))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('invites'),
				'aEventInvites' => $aEventInvites,
                'sCustomClassName' => 'ync-block'
			)
		);
		
		return 'block';	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_invite_clean')) ? eval($sPlugin) : false);
	}
}

?>