<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

// Add and edit request both go here 
class Socialad_Component_Block_Campaign_Campaign_Filter extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		$iCampaignDefaultStatus = Phpfox::getService('socialad.helper')->getConst('campaign.status.active');

		$this->setParam('aQueryParam', array(
			'campaign_status' => $iCampaignDefaultStatus
		));
		$this->template()->assign(array(
			'aCampaignStatus' => Phpfox::getService('socialad.campaign')->getAllCampaignStatus(),
			'iCampaignDefaultStatus' => Phpfox::getService('socialad.helper')->getConst('campaign.status.active'),
            'bIsAdminManage' => $this->getParam('bIsAdminManage', false)
		));

		
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

