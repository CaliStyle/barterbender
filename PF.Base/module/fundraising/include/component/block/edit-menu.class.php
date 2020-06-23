<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Edit_Menu extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() 
	{
		if(!$id = $this->request()->get('id'))
		{
			return false;	
		}
		$id = $id = $this->request()->get('id');
		if (!$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForEdit($id)) 
		{
			return false;
		}
		$aMenus = array(
			'main' => _p('main_info'),
			'gallery' => _p('gallery'),
			'contact_information' => _p('contact_information'),
			'email_conditions' => _p('email_and_conditions'),
			'invite_friends' => _p('invite_friends'),
		);
		$sView = _p('view_this_fundraising');
		$sLink = $this->url()->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']);
		$this->template()->assign(array(
				'sView' => $sView,
				'aMenus' => $aMenus,
				'sLink' => $sLink
			)
		);
		return 'block';
	}

}

?>