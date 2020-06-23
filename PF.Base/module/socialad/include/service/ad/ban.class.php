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

class Socialad_Service_Ad_Ban extends Phpfox_Service
{
	private $_aBannedAd = array();
	public function __construct() {
		$this->_sBanTable = Phpfox::getT('socialad_ad_ban');
	}
	public function ban($iAdId, $iUserId) {
		$aInsert = array(
			'ad_id' => $iAdId,
			'user_id' => $iUserId,
			'time_stamp' => PHPFOX_TIME
		);

		$this->database()->insert($this->_sBanTable, $aInsert);
	}	

	public function getBannedAdsOfUser($iUserId) {
		if(isset($this->_aBannedAd[$iUserId])) {
			return $this->_aBannedAd[$iUserId];
		}
		$aRows = $this->database()->select('ad_id')
			->from($this->_sBanTable)
			->where('user_id = '. $iUserId)
			->execute('getRows');

		$aResult = array();
		foreach($aRows as $aRow) {
			$aResult[] = $aRow['ad_id'];
		}

		$this->_aBannedAd[$iUserId] = $aResult;

		return $aResult;
	}
}



