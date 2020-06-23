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

class Socialad_Service_Ad_Statistic extends Phpfox_Service
{
	public function __construct() {
		$this->_sStatisticTable = Phpfox::getT('socialad_ad_statistic');
	}

	public function getTotalOfAd($sField, $iAdId) {
		$iCnt = $this->database()->select("SUM({$sField})")
			->from($this->_sStatisticTable)
			->where('ad_id = ' . $iAdId)
			->execute('getSlaveField');

		return $iCnt;
	}

	public function report($aQuery) {
		$iItemPerPage = $aQuery['item_per_page'];
		$iPage = 1;

		$aConds = array(
				
		);
		$sStatisticAlias = 'sas';

		// the problem of alias and table is awkward
		if(isset($aQuery['campaign_id']) && $aQuery['campaign_id'] != 0) {
			$aConds[] = 'sac.campaign_id = ' . $aQuery['campaign_id'];
		}

		if(isset($aQuery['ad_id']) && $aQuery['ad_id'] != 0) {
			$aConds[] = "{$sStatisticAlias}.ad_id = " . $aQuery['ad_id'];
		}

		if(isset($aQuery['start_year']) && $aQuery['start_year']){
			$iStartTS = Phpfox::getLib('date')->mktime($iHour = 1, $iMinute = 1, $iSecond = 59, $aQuery['start_month'], $aQuery['start_day'], $aQuery['start_year']);
			$aConds[] = "{$sStatisticAlias}.time_stamp >= " . Phpfox::getService('socialad.date')->getStartOfDay($iStartTS);
		}

		if(isset($aQuery['page']) && $aQuery['page']) {
			$iPage = $aQuery['page'];
		}

		$iSummary = 1;
		if(isset($aQuery['summary']) ){
			$iSummary = intval( $aQuery['summary']);
		}
		$aExtra['limit'] = $iItemPerPage ;
		$aExtra['page'] = ($iPage - 1) * $iItemPerPage; // without count, page is offset

		if(isset($aQuery['end_year']) && $aQuery['end_year']){
			$iEndTS = Phpfox::getLib('date')->mktime($iHour = 1, $iMinute = 1, $iSecond = 59, $aQuery['end_month'], $aQuery['end_day'], $aQuery['end_year']);
			$aConds[] = "{$sStatisticAlias}.time_stamp <= " . Phpfox::getService('socialad.date')->getStartOfDay($iEndTS);
		}

		$aExtra['order'] = "{$sStatisticAlias}.time_stamp DESC";

		$aConds[] = "{$sStatisticAlias}.user_id = " . Phpfox::getUserId();

		$aRows = Phpfox::getService('socialad.ad.statistic')->get($aConds, $aExtra);

		if(isset($aQuery['summary']) && $aQuery['summary'] != 1){
			$iSummary = intval( $aQuery['summary']);
			$aNewRows = array();
			$aTracking = array();
			$iCounter = 0;

			if($aQuery['summary'] == 0) { // group all in 1 row
				$iSummary = 10000; //
			}

			foreach($aRows as $aRow) {
				$iAdId = $aRow['ad_id'];
				if(!isset($aTracking[$iAdId])) { // each ad has a slot in tracking
					$aTracking[$aRow['ad_id']] = array(
						'count' => 0,
						'row_index' => -1
					);
				}

				if($aTracking[$iAdId]['count'] >= $iSummary) {
					$aTracking[$iAdId]['count'] = 0; // reset tracking
					
					//release the row
				} 

				if($aTracking[$iAdId]['count'] == 0) { 
					$aNewRows[] = $aRow;
					$aTracking[$iAdId]['row_index'] = count($aNewRows) - 1;
				
				} else {
					// we need to update a row because we have not accumulated enough days
					$iCurrenIndex = $aTracking[$iAdId]['row_index'];
					$aNewRows[$iCurrenIndex]['total_reach'] += $aRow['total_reach'];
					$aNewRows[$iCurrenIndex]['total_impression'] += $aRow['total_impression'];
					$aNewRows[$iCurrenIndex]['total_click'] += $aRow['total_click'];
					$aNewRows[$iCurrenIndex]['total_unique_click'] += $aRow['total_unique_click'];
					$aNewRows[$iCurrenIndex]['start_date_text'] = $aRow['start_date_text'];
				}
				
				$aTracking[$iAdId]['count']++;
			}

			$aRows = $aNewRows;
		}

		$iTotalResult = count($aRows);


		return array(
			'aRows' => $aRows,
			'total_all_result' => Phpfox::getService('socialad.ad.statistic')->countStatistic($aConds),
			'total_result' => $iTotalResult,
			'page' => $iPage,
			'limit' => $iItemPerPage,
		);
		$this->setParam('aPagingParams', array(
			'total_all_result' => Phpfox::getService('socialad.ad.statistic')->countStatistic($aConds),
			'total_result' => $iTotalResult,
			'page' => $iPage,
			'limit' => $iItemPerPage,
			'bNoResultText' => true
		));

		$this->template()->assign(array(
			'aSaRows' => $aRows
		));



	}




	public function compute($ad) {
		if(is_array($ad)) {
			$aAd = $ad;
		} else {
			$aAd =Phpfox::getService('socialad.ad')->getAdBasicInfoById($ad);
		}

		if(!$aAd) {
			return false;
		}

		$iAdId = $aAd['ad_id'];


		if(!in_array($aAd['ad_status'], array(
			Phpfox::getService('socialad.helper')->getConst('ad.status.running'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.paused'),
		))) { 
			return false;
		}


		$iCurrentTS = PHPFOX_TIME;
		$iRecentComputedTS = $aAd['ad_most_recent_computed_time'];

		$iStartOfComputedDate = Phpfox::getService('socialad.date')->getStartOfDay($iRecentComputedTS);

		$iEndOfCurrentDate = Phpfox::getService('socialad.date')->getEndOfDay($iCurrentTS);

		$iTimeCounter = $iStartOfComputedDate;

		// for each beginning of day since the beginning of computed date

		while($iTimeCounter < $iCurrentTS) { 
			$iStartOfDate = $iTimeCounter;

			$iEndOfDate = Phpfox::getService('socialad.date')->getEndOfDay($iStartOfDate);

			$iImpressionCount = Phpfox::getService('socialad.ad.track')->getImpressionCountIn($iAdId, $iStartOfDate, $iEndOfDate);
			$iUniqueClickCount = Phpfox::getService('socialad.ad.track')->getUniqueClickCountIn($iAdId, $iStartOfDate, $iEndOfDate);

			$iClickCount = Phpfox::getService('socialad.ad.track')->getClickCountIn($iAdId, $iStartOfDate, $iEndOfDate);
			$iReachCount = Phpfox::getService('socialad.ad.track')->getReachCountIn($iAdId, $iStartOfDate, $iEndOfDate);

			$aInsert = array( 
				'ad_id' => $iAdId, 
				'user_id' => $aAd['ad_user_id'],
				'time_stamp' => $iStartOfDate,
				'total_click' => $iClickCount ? $iClickCount : 0,
				'total_unique_click' => $iUniqueClickCount ? $iUniqueClickCount : 0,
				'total_reach' => $iReachCount ? $iReachCount : 0,
				'total_impression' => $iImpressionCount ? $iImpressionCount : 0,
			);

			$iId = $this->add($aInsert);
			$iTimeCounter += 24 * 60 * 60;
		}

		Phpfox::getService('socialad.ad.process')->updateComputedTime($iAdId, $iCurrentTS);
		Phpfox::getService('socialad.ad.process')->updateStatisticIntoAdData($iAdId);
	}

	public function checkStatisticExist($iAdId, $iTimestamp) {
		$aConds = array( 
			'sas.ad_id = ' . $iAdId, 
			'sas.time_stamp = ' . $iTimestamp
		);


		$aRows = $this->get($aConds);
		if(count($aRows) > 0) {
			return $aRows[0]['statistic_id'];
		} else {
			return false;
		}
	}


	public function countStatistic($aConds = array(), $aExtra = array()) { 
		$sCond = implode(' AND ' , $aConds);

		$iCnt = $this->database()->select('COUNT(sas.statistic_id)') 
			->from($this->_sStatisticTable, 'sas')
			->leftJoin(Phpfox::getT('socialad_ad'), 'saa', 'sas.ad_id = saa.ad_id')
			->leftJoin(Phpfox::getT('socialad_campaign'), 'sac', 'saa.ad_campaign_id = sac.campaign_id')
			->where($sCond)
			->execute('getSlaveField');

		return $iCnt;
	}

	public function get($aConds = array(), $aExtra = array()) { 
		$sCond = implode(' AND ' , $aConds);

		if($aExtra && isset($aExtra['limit'])) {
			$this->database()->limit($aExtra['page'], $aExtra['limit']);
		}

		if($aExtra && isset($aExtra['order'])) {
			$this->database()->order($aExtra['order']);
		} else {
			$this->database()->order('sas.time_stamp DESC');
		}
		$aRows = $this->database()->select('sas.*, saa.ad_title, sac.campaign_name') 
			->from($this->_sStatisticTable, 'sas')
			->leftJoin(Phpfox::getT('socialad_ad'), 'saa', 'sas.ad_id = saa.ad_id')
			->leftJoin(Phpfox::getT('socialad_campaign'), 'sac', 'saa.ad_campaign_id = sac.campaign_id')
			->where($sCond)
			->execute('getRows');

		foreach($aRows as &$aRow) {
			$aRow['start_date_text'] = Phpfox::getService('socialad.date')->convertTime($aRow['time_stamp']);
			$aRow['end_date_text'] = Phpfox::getService('socialad.date')->convertTime($aRow['time_stamp']);
		}

		return $aRows;
	}


	public function add($aVals) {
		if(($iStatisticId = $this->checkStatisticExist($aVals['ad_id'],  $aVals['time_stamp']))) {
			$this->remove($iStatisticId);
		}

		$this->database()->insert($this->_sStatisticTable, $aVals);
	}

	public function remove($iStatisticId) {
		$this->database()->delete($this->_sStatisticTable, 'statistic_id = ' . $iStatisticId);
	}


	public function getTotalImpressionsOfAd($iAdId) {
		$aConds = array( 
			'sas.ad_id = ' . $iAdId
		);

		$aRow = $this->count($aConds);
		return $aRow['total_impression'];
	}

	public function getTotalClicksOfAd($iAdId) {
		$aConds = array( 
			'sas.ad_id = ' . $iAdId
		);

		$aRow = $this->count($aConds);
		return $aRow['total_click'];
	}

	public function count($aConds = array()) {
		static $aRow;
		if($aRow) {
			return $aRow;
		}

		$sCond = implode(' AND ', $aConds);

		$aRow = $this->database()->select('SUM(total_impression) as total_impression, SUM(total_click) AS total_click')
			->from($this->_sStatisticTable)
			->where($sCond)
			->execute('getRow');

		return $aRow;


	}

	public function getStatisticByDayOfAd($iAdId, $iLimit = 0, $period = array()) {
		$aConds = array( 
			'sas.ad_id = ' . $iAdId
		);
		if(isset($period['start'])){
			$aConds[] = 'sas.time_stamp >= ' . (int)$period['start'];
		}
		if(isset($period['end'])){
			$aConds[] = 'sas.time_stamp <= ' . (int)$period['end'];
		}

		$aExtra = array();
		if($iLimit) {
			$aExtra['page'] = 0;
			$aExtra['limit'] = $iLimit;
		}

		return $this->get($aConds, $aExtra);
	}


}



