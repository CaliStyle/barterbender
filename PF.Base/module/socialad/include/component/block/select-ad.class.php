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
class Socialad_Component_Block_Select_Ad extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iCampaignId = $this->getParam('iCampaignId');
		$iDefaultAdId = $this->getParam('iDefaultAdId', 0);

		$aAds = Phpfox::getService('socialad.ad')->getAllAdsOfCampaign($iCampaignId);

		$this->template()->assign(array(
			'aSelectAds' => $aAds,
			'iDefaultAdId' => $iDefaultAdId
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

