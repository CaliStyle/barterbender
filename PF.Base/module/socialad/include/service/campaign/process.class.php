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

class Socialad_Service_Campaign_Process extends Phpfox_Service
{
	public function __construct() {
		$this->_sCampaignTable = Phpfox::getService('socialad.campaign')->getTable();
		$this->_oParse = Phpfox::getLib('parse.input');
	}

	public function delete($iCampaignId) {
		$iStatus = Phpfox::getService('socialad.helper')->getConst('campaign.status.deleted');
		$aUpdate = array(
			'campaign_status' => $iStatus
		);
		$this->database()->update(Phpfox::getT('socialad_campaign'), $aUpdate, 'campaign_id = ' . $iCampaignId);
		$aAds = Phpfox::getService('socialad.ad')->getAllAdsOfCampaign($iCampaignId);

		foreach($aAds as $aAd) {
			Phpfox::getService('socialad.ad.process')->deleteAd($aAd['ad_id']);
		}

		return false;
	}
	public function update($aVals) {
		$iCampaignId = $aVals['campaign_id'];
		$aUpdate = array(
			'campaign_name' => $this->_oParse->clean($aVals['campaign_name']),
			'campaign_timestamp' => PHPFOX_TIME
		);

		$iCnt = $this->database()->update($this->_sCampaignTable, $aUpdate, 'campaign_id = ' . $iCampaignId);

		return $iCnt;
	}

	public function add($aVals) {
		$aInsert = array(
			'campaign_name' => $aVals['campaign_name'],
			'campaign_user_id' => Phpfox::getUserId(),
			'campaign_timestamp' => PHPFOX_TIME
		);

		$iId = $this->database()->insert($this->_sCampaignTable, $aInsert);

		return $iId;
	}

	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Service_Campaign_Process__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}



