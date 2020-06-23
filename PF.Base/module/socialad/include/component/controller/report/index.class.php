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


class Socialad_Component_Controller_Report_Index extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		// 0 means all day in a row
		$aSummaryOptions = array( 
			0, 1, 2, 7, 14, 30
		);
		$iEndTime = PHPFOX_TIME;
		$iStartTime = PHPFOX_TIME - 30 * 24 * 60 * 60;
		$aForms['start_day'] = date('j', $iStartTime);
		$aForms['start_month'] = date('n', $iStartTime);
		$aForms['start_year'] = date('Y', $iStartTime);

		$aForms['end_day'] = date('j', $iEndTime);
		$aForms['end_month'] = date('n', $iEndTime);
		$aForms['end_year'] = date('Y', $iEndTime);


		$iDefaultAdId = 0;
		$iDefaultCampaignId = 0;

		if($this->request()->get('req3') == 'ad') {
			$iDefaultAdId = $this->request()->get('id');
			$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iDefaultAdId);
			$iDefaultCampaignId = $aAd['ad_campaign_id'];
		}
		$this->template()->assign(array(
			'aSummaryOptions' => $aSummaryOptions,
			'aForms' => $aForms,
			'aSaCampaigns' => Phpfox::getService('socialad.campaign')->getAllCampaignsOfUser(Phpfox::getUserId()),
			'iDefaultAdId' => $iDefaultAdId,
			'iDefaultCampaignId' => $iDefaultCampaignId
		));


		$this->template()	
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb(_p('report'), $this->url()->makeUrl('socialad.report'), true);
		Phpfox::getService('socialad.helper')->loadSocialAdJsCss();
		
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

