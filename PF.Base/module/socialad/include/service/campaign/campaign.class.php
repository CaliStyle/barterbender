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

class Socialad_Service_Campaign_Campaign extends Phpfox_Service
{
	public function __construct() {
		$this->_sCampaignTable = Phpfox::getT('socialad_campaign');
		$this->_aCampaignStatus = array( 
			'active' => array( 
				'id' => 1, 
				'phrase' => _p('active')
			),
			'deleted' => array( 
				'id' => 2, 
				'phrase' => _p('deleted')
			),

		);
	}

	public function getAllCampaignStatus() {
		return $this->_aCampaignStatus;
	}

	public function getTable() {
		return $this->_sCampaignTable;
	}

	public function getAllCampaignsOfUser($iUserId, $campaignStatus = null) {
		$aConds = array( 
			"sac.campaign_user_id = " . $iUserId 
		);

		if($campaignStatus != null){
			$aConds[] = 'sac.campaign_status = ' . $campaignStatus;
		}

		return $this->getCampaign($aConds);
	}

	public function getCampaignById($iCampaignId) {
		$aConds = array( 
			"sac.campaign_id = " . $iCampaignId 
		);

		$aRows = $this->getCampaign($aConds);
		return $aRows[0];
	}

	public function count($aConds) {
		$sCond = implode(' AND ' , $aConds);

		$iCnt = $this->database()->select('COUNT(sac.campaign_id)') 
			->from($this->_sCampaignTable, 'sac') 
			->where($sCond)
			->execute('getSlaveField');

		return $iCnt;
	}

	public function getCampaign($aConds, $aExtra = array()) {
		$sCond = implode(' AND ' , $aConds);

		if($aExtra && isset($aExtra['limit'])) {
			$this->database()->limit($aExtra['page'], $aExtra['limit']);
		}

		$aRows = $this->database()->select('*') 
			->from($this->_sCampaignTable, 'sac') 
			->where($sCond)
			->order('sac.campaign_timestamp DESC')
			->execute('getRows');

		foreach($aRows as &$aRow) {
			$aRow['campaign_total_impression'] = Phpfox::getService('socialad.ad')->getTotalImpressionsOfCampaign($aRow['campaign_id']);
			$aRow['campaign_total_click'] = Phpfox::getService('socialad.ad')->getTotalClicksOfCampaign($aRow['campaign_id']);
			$aRow['total_ad'] = Phpfox::getService('socialad.ad')->getTotalAdOfCampaign($aRow['campaign_id']);
			$aRow['status_phrase'] = Phpfox::getService('socialad.helper')->getPhraseById('campaign.status', $aRow['campaign_status']);
		}
		return $aRows;
	}

	public function retrievePermission($aCampaign) {
		$aCampaign['can_delete_campaign'] = Phpfox::getService('socialad.permission')->canDeleteCampaign($aCampaign);
		$aCampaign['can_edit_campaign'] = Phpfox::getService('socialad.permission')->canEditCampaign($aCampaign);

		return $aCampaign;
	}



	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Service_Campaign_Campaign__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}



