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
class Socialad_Component_Block_Campaign_Edit extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iCampaignId = $this->getParam('campaign_id', $default = false);

		if($iCampaignId) {
			$aCampaign = Phpfox::getService('socialad.campaign')->getCampaignById($iCampaignId);
			$this->template()->assign(array(
				'aForms' => $aCampaign
			));
		}
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

