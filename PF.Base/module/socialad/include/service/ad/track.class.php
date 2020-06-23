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

class Socialad_Service_Ad_Track extends Phpfox_Service
{
	public function __construct() {
		$this->_sTrackTable = Phpfox::getT('socialad_ad_track');
		$this->_aTrackType = array( 
			'impression' => array('id' => 1, 'phrase' => _p('view')),
			'click' => array('id' => 2, 'phrase' => _p('impression')),
		);
	}

	public function getAllTypes() {
		return $this->_aTrackType;
	}
	
	public function is($iAdId, $iTrackType) {
		$aAd =Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		if(!isset($aAd['ad_id'])){
			return false;
		}

		$iStartOfToday = Phpfox::getService('socialad.date')->getStartOfDay(PHPFOX_TIME);
		$iEndOfToday = Phpfox::getService('socialad.date')->getEndOfDay(PHPFOX_TIME);
		$sCond = 'ad_id = ' . $iAdId . ' AND type = ' . $iTrackType;
		if(Phpfox::isUser()) {
			$sCond .= ' AND user_id = ' . Phpfox::getUserId();
		} else {
			if((int)$aAd['is_show_guest'] == 0){
				return false;
			}
			$sCond .= ' AND user_id = 0 AND ip_address = \'' . $this->database()->escape(Phpfox::getIp()) . '\'';
		}
		$sCond .= ' AND time_stamp >= ' . (int)$iStartOfToday . ' AND time_stamp <= ' . (int)$iEndOfToday;

		// get record tracking still today
		$aTrack = $this->database()->select('*')
			->from($this->_sTrackTable)
			->where($sCond)
			->execute('getRow');
		if (!isset($aTrack['track_id']))
		{		
			// add new with new day or not existing record tracking 
			$this->database()->insert(Phpfox::getT('socialad_ad_track'), array(
					'ad_id' => $iAdId,
					'user_id' => Phpfox::getUserId(),
					'ip_address' => Phpfox::getIp(),
					'time_stamp' => PHPFOX_TIME,
					'type' => $iTrackType,
					'number' => 1
				)
			);

		} else {
			// count record tracking if existing 
			$aUpdate = array(
				'number' => $aTrack['number'] + 1
			);

			$this->database()->update($this->_sTrackTable, $aUpdate, 'track_id = ' . $aTrack['track_id']);
		}

		// update statistic of ad
		Phpfox::getService('socialad.ad.statistic')->compute($iAdId);
		// get ads again, update completion rate
		$aAd =Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		Phpfox::getService('socialad.ad')->updateCompletionRateMoreAd(array($aAd));

		return true;

	}
	public function isViewed($iAdId, $iUserId) {

		return $this->is($iAdId, $this->_aTrackType['impression']['id']);
	}

	public function isCLicked($iAdId, $iUserId) {

		return $this->is($iAdId, $this->_aTrackType['click']['id']);
	}

	public function getImpressionCountIn($iAdId, $iStartTime, $iEndTime) {
		// total view 
		return $this->getSumIn($iAdId, $iStartTime, $iEndTime, $this->_aTrackType['impression']['id']);
	}

	public function getUniqueClickCountIn($iAdId, $iStartTime, $iEndTime) {
		return $this->getCountIn($iAdId, $iStartTime, $iEndTime, $this->_aTrackType['click']['id']);
	}

	public function getClickCountIn($iAdId, $iStartTime, $iEndTime) {
		return $this->getSumIn($iAdId, $iStartTime, $iEndTime, $this->_aTrackType['click']['id']);
	}

	public function getReachCountIn($iAdId, $iStartTime, $iEndTime) {
		// * user 1 + IPA --> +1 (by user)
		// * guest + IPA --> +1 
		// * guest + IPA --> no count
		// * user2 + IPA --> +1
		// * user2 + IPB --> no count
		// * guest + IPB --> +1
		// * guest + IPA --> no count
		return $this->getCountIn($iAdId, $iStartTime, $iEndTime, $this->_aTrackType['impression']['id']);
	}

	public function getSumIn($iAdId, $iStartTime, $iEndTime, $iTypeId) {
		$aConds = array( 
			'time_stamp <= ' . $iEndTime, 
			'time_stamp >= ' . $iStartTime,
			'type = ' . $iTypeId,
			'ad_id = ' . $iAdId
		);

		return $this->sum($aConds);
	}

	public function sum($aConds = null) {
		$sCond = implode(' AND ', $aConds); 

		$iTotal = $this->database()->select('SUM(number) AS total')
					->from($this->_sTrackTable)
					->where($sCond)
					->execute('getSlaveField');

		return $iTotal;
	}
	public function getCountIn($iAdId, $iStartTime, $iEndTime, $iTypeId) {
		$aConds = array( 
			'time_stamp <= ' . $iEndTime, 
			'time_stamp >= ' . $iStartTime,
			'type = ' . $iTypeId,
			'ad_id = ' . $iAdId
		);

		return $this->count($aConds);
	}

	public function count($aConds = null) {
		$sCond = implode(' AND ', $aConds); 

		$aRows = $this->database()->select('COUNT( DISTINCT (`user_id`) ) AS count')
					->from($this->_sTrackTable)
					->where($sCond)
					->group('`user_id`, `ip_address`')
					->execute('getSlaveRows');

		if(is_array($aRows)){
			$iTotal = count($aRows);
		} else {
			$iTotal = 0;
		}
		
		return $iTotal;
	}
}



