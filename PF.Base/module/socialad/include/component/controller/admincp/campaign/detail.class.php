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


class Socialad_Component_Controller_Admincp_Campaign_Detail extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$campaignID = $this->request()->get('id');
		if((int)$campaignID <= 0){
			Phpfox::getLib('url')->send('admincp.socialad.campaign');
			return false;
		}

		$aCampaign = Phpfox::getService('socialad.campaign')->getCampaignById((int)$campaignID);
		if(!isset($aCampaign['campaign_id'])){
			Phpfox::getLib('url')->send('admincp.socialad.campaign');
			return false;
		}

		$aQueryParam = array('ad_campaign_id' => $campaignID);
		$this->setParam('aQueryParam', $aQueryParam);
		$this->setParam('iFilterCampaignId', $campaignID);

		Phpfox::getService('socialad.helper')->loadAdminSocialAdJsCss();
		
		$this->template()->assign(array(
			'aCampaign' => $aCampaign,
		));
        $this->setParam('bIsAdminManage', true);
		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb('Campaign Details');
	}

}

