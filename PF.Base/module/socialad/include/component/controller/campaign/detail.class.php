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


class Socialad_Component_Controller_Campaign_Detail extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iCampaignId = $this->request()->get('id');

		Phpfox::getService('socialad.permission')->canViewCampaignDetail($iCampaignId, $bRedirect = true);
		$aAds = Phpfox::getService('socialad.ad')->getAllAdsOfCampaign($iCampaignId);

		$aCampaign = Phpfox::getService('socialad.campaign')->getCampaignById($iCampaignId);
		$aCampaign['can_edit_campaign'] = false;
		$aCampaign['can_delete_campaign'] = false;
		$this->template()->assign(array(
			'aSaAds' => $aAds,
			'aSaCampaign' => $aCampaign,
			'aTempCampaigns' => array($aCampaign)
		));

		$this->setParam('aQueryParam', array(
			'ad_campaign_id' => $iCampaignId
		));

		Phpfox::getService('socialad.helper')->loadSocialAdJsCss();

		$this->template()->setTitle($aCampaign['campaign_name'])	
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb( $aCampaign['campaign_name'], $this->url()->makeUrl('socialad.campaign.detail', array('id'=> $aCampaign['campaign_id'])), true);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
		(($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Component_Controller_Index_clean')) ? eval($sPlugin) : false);
	}

}

