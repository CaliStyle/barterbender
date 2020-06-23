<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class FoxFeedsPro_Component_Controller_Profile extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		$this->setParam('bIsProfile', true);		
		$aUser = $this->getParam('aUser');	

		$req3 = $this->request()->get('req3');	
		$aAssign = array('bNoTemplate' => true, 'sYnFfFrom' => 'profile');
		$this->setParam('sYnFfFrom', 'profile');		
		$this->setParam('aUser', $aUser);
		switch ($req3) {
			case 'profileviewrss':
				Phpfox::getComponent('foxfeedspro.profileviewrss', $aAssign, 'controller', true);
				break;

			case 'profileaddrssprovider':
				Phpfox::getComponent('foxfeedspro.addfeed', $aAssign, 'controller', true);
				break;

			case 'profilemanagerssprovider':
				Phpfox::getComponent('foxfeedspro.feeds', $aAssign, 'controller', true);
				break;
			
			default:
				$this->url()->send('', null, null);
				break;
		}
		
		return true;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}
}

?>