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

class Socialad_Service_Ad_Process extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sAdTable = Phpfox::getService('socialad.ad')->getTable();

		// sap stands for social ad package
        $this->_sAdAlias = Phpfox::getService('socialad.ad')->getAlias();

		$this->_oParse = Phpfox::getLib('parse.input');
    }

	public function updateStatisticIntoAdData($iAdId) {

		$aUpdate = array(
			'ad_total_click' => Phpfox::getService('socialad.ad.statistic')->getTotalOfAd('total_click', $iAdId),
			'ad_total_impression' => Phpfox::getService('socialad.ad.statistic')->getTotalOfAd('total_impression', $iAdId),
			'ad_total_unique_click' => Phpfox::getService('socialad.ad.statistic')->getTotalOfAd('total_unique_click', $iAdId),
			'ad_total_reach' => Phpfox::getService('socialad.ad.statistic')->getTotalOfAd('total_reach', $iAdId),
		);

		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		$iRunningDay = floor((PHPFOX_TIME - $aAd['ad_start_time']) / (60 * 60 * 24));
		if($iRunningDay <= 0) $iRunningDay = 0;

		$aUpdate['ad_total_running_day'] = $iRunningDay;

		$this->database()->update(Phpfox::getT('socialad_ad'), $aUpdate, 'ad_id = ' . $iAdId);
	}

	public function updateAdLastViewedTime($iAdId) {
		$aUpdate = array(
			'ad_last_viewed_time' => PHPFOX_TIME
		);

		$this->database()->update(Phpfox::getT('socialad_ad'), $aUpdate, 'ad_id = ' . $iAdId);

	}

	public function updateCompletionRateByAdId($iAdId, $completion_rate = 0){
		$aUpdate = array(
			'completion_rate' => doubleval($completion_rate)
		);

		$this->database()->update(Phpfox::getT('socialad_ad'), $aUpdate, 'ad_id = ' . $iAdId);
	}

	public function view($iAdId) {

		$this->updateAdLastViewedTime($iAdId);
		
		if(Phpfox::getService('socialad.ad.track')->isViewed($iAdId, Phpfox::getUserId())) {
		}

	}

	public function click($iAdId) {

		if(Phpfox::getService('socialad.ad.track')->isClicked($iAdId, Phpfox::getUserId())) {
		}

	}

    
	/**
	 *
	 * @return integer id of ad
	 */
	public function handleSubmitForm($aVals) {
		$oFilter = Phpfox::getLib('parse.input');
		$aAd = array (
			'ad_title' => $oFilter->clean($aVals['ad_title']),
			'ad_text' => $oFilter->clean($aVals['ad_text']),
			'ad_item_id' => isset($aVals['ad_item_id']) ? $aVals['ad_item_id'] : 0,
			'ad_item_type' => $aVals['ad_item_type'],
			'ad_type' => $aVals['ad_type'],
			'ad_last_edited_time' => PHPFOX_TIME,
			'ad_package_id' => $aVals['ad_package_id'],
			'ad_number_of_package' => isset($aVals['ad_number_of_package']) ? $aVals['ad_number_of_package'] : 0,
			'ad_external_url' => $oFilter->clean($aVals['ad_external_url']),
			'audience_gender' => $aVals['audience_gender'],
			'is_show_guest' => isset($aVals['is_show_guest']) ? 1 : 0,
			'audience_age_min' => $aVals['audience_age_min'],
			'audience_age_max' => $aVals['audience_age_max'],
			'placement_block_id' => $aVals['placement_block_id'],
			'ad_campaign_id' => $aVals['campaign_id'] // if creating new campaign, it will be handle later
		);
		$aPackage = Phpfox::getService('socialad.package')->getPackageById($aVals['ad_package_id']);
		if($aPackage['package_is_free']){
			// currently, with free package user can ONLY purchase 1 item 
			$aAd['ad_number_of_package'] = 1;				
		}


		if($aAd['ad_type'] == Phpfox::getService('socialad.helper')->getConst('ad.type.html')) {
			$aAd['placement_block_id'] = 3;
		}

		if(!$aVals['is_continuous']) {
			$aTime = array();
			$aTimeType = Phpfox::getService('socialad.ad')->getTimeTypes();	
			foreach ($aTimeType as $k => $sTimeLine)
			{
				$aTime[$sTimeLine] = Phpfox::getLib('date')->mktime($aVals[$sTimeLine.'_hour'], $aVals[$sTimeLine.'_minute'], $iStartSubmitSecond = 59, $aVals[$sTimeLine.'_month'], $aVals[$sTimeLine.'_day'], $aVals[$sTimeLine.'_year']);
				
				// on the interface we have convert into gmt, now we roll back to server time
				$aAd[$sTimeLine] = Phpfox::getService('socialad.helper')->convertFromUserTimeZoneToServerTime($aTime[$sTimeLine]);
			}

		} else {
			$aTimeType = Phpfox::getService('socialad.ad')->getTimeTypes();	
			foreach ($aTimeType as $k => $sTimeLine)
			{
				$aAd[$sTimeLine] = 0;
			}

		}


		if($aVals['campaign_id'] == 0) { // add new campaign
			$iCampaignId = Phpfox::getService('socialad.campaign.process')->add($aVals);
			$aAd["ad_campaign_id"] = $iCampaignId;
		}

		if(isset($aVals['ad_id']) && $aVals['ad_id']) { // edit ad
			$iAdId = $aVals['ad_id'];
			$aUpdate = $aAd;
			$this->database()->update($this->_sAdTable, $aUpdate, 'ad_id = ' . $iAdId);
			
		} else { // add new ad 



			$aInsert = $aAd;
			// we set user_id here to avoid update it when editing 
			$aInsert['ad_user_id'] = Phpfox::getUserId();

			// @Todo fix it to get along with expected start date
			$aInsert['ad_most_recent_computed_time'] = PHPFOX_TIME;

			$aInsert['ad_status'] = Phpfox::getService('socialad.helper')->getConst('ad.status.draft', 'id'); // All news created ad are at draft stage
			$iAdId = $this->database()->insert($this->_sAdTable, $aInsert);
		}

		$aAdTmp = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		if($aVals['image_path']) {
			if($aVals['image_path'] != $aAdTmp['image_path']){
				// copy from temp dir to new dir
				$sImagePath = Phpfox::getService('socialad.ad.image')->copyItemFromTempToRealFolder($aVals['image_path']);
				$aData = array(
					'ad_id' => $iAdId,
					'image_path' => $sImagePath
				);

				Phpfox::getService('socialad.ad.image')->addImageUrl($aData);
			}
		}

		if(isset($aVals['placement_module_id'])) {
			Phpfox::getService('socialad.ad.placement')->addModule($iAdId, $aVals['placement_module_id']);
		}

		if(isset($aVals['audience_location'])) {
			Phpfox::getService('socialad.ad.audience')->addLocation($iAdId, $aVals['audience_location']);
		}

		$this->updateAdLimitationByPackage($aAd['ad_package_id'], $iAdId);

		return $iAdId;
	}

	public function updateAdLimitationByPackage($iPackageId, $iAdId) {
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);

		if(in_array($aAd['ad_status'], array(
			Phpfox::getService('socialad.helper')->getConst('ad.status.draft'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.unpaid'),
		))) {
			$aPackage = Phpfox::getService('socialad.package')->getPackageById($iPackageId);
			$iNumberOfPackage = $aAd['ad_number_of_package'];				
			$iTotalBenefit = $iNumberOfPackage * $aPackage['package_benefit_number'];
			$aUpdate = array(
				'ad_benefit_type_id' => $aPackage['package_benefit_type_id'],
				'ad_benefit_limit_number' => $iTotalBenefit
			);
			$this->database()->update($this->_sAdTable, $aUpdate, 'ad_id = ' . $iAdId);
		} 

		return true;

	}

    private function _verifyTime($aTime)
    {
		if ($aTime['ad_expect_start_time'] >= $aTime['ad_expect_end_time']) {
			return Phpfox_Error::set(_p('the_end_time_must_be_greater_than_the_start_time'));
		}
        
		if ($aTime['ad_expect_start_time'] <  PHPFOX_TIME){
			return Phpfox_Error::set(_p('the_start_time_must_be_greater_than_current_time'));
		}
        
        
        return true;
    }


	public function updateComputedTime($iAdId, $iTimeStamp) {
		$this->database()->update($this->_sAdTable, array('ad_most_recent_computed_time' => $iTimeStamp), 'ad_id = ' . $iAdId);
	}

	public function updateImpressionAndClick($iAdId) {
		$iTotalImpressions = Phpfox::getService('socialad.ad.statistic')->getTotalImpressionsOfAd($iAdId);
		$iTotalClicks = Phpfox::getService('socialad.ad.statistic')->getTotalClicksOfAd($iAdId);

		$aUpdate = array( 
			'ad_total_impression' => $iTotalImpressions,
			'ad_total_click' => $iTotalClicks
		);

		$this->database()->update($this->_sAdTable, $aUpdate, 'ad_id = ' . $iAdId);

	}

	/**
	 * by using this function, the ad is confirmed to make a transition into next status
	 * pre-status of ad should be draft, or unpaid
	 * @return int next status id 
	 */
	public function placeOrder($iAdId) {
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		$aPackage = Phpfox::getService('socialad.package')->getPackageById($aAd['ad_package_id']);
		$bNeedApprove = Phpfox::getService('socialad.user')->getUserParam('socialad.approve_ad', $aAd['ad_user_id']);

		$iNextStatus = 0;
	
		if ($sPlugin = Phpfox_Plugin::get('socialad.service_ad_process_placeorder_start'))
		{
		    eval($sPlugin);
		}

		if(!$bNeedApprove && $aPackage['package_is_free']) { // no need to approve and this package is free, we rush to running state if possible
			if($aAd['ad_expect_start_time'] > PHPFOX_TIME) {
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.approved');

			} else {
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.running');
			}
			
		} else if(!$aPackage['package_is_free']) {
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.unpaid');

		} else if($bNeedApprove) {
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.pending');

		}
		$this->updateStatus($iAdId, $iNextStatus);

		return $iNextStatus;

	}

	public function completeOrder($iAdId, $method = 'paypal') {
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		$bNeedApprove = Phpfox::getService('socialad.user')->getUserParam('socialad.approve_ad', $aAd['ad_user_id']);

		$iNextStatus = 0;
	
		if ($sPlugin = Phpfox_Plugin::get('socialad.service_ad_complete_placeorder_start'))
		{
		    eval($sPlugin);
		}

		if('pay_later' == $method){
			$bNeedApprove = false;
		}

		if($bNeedApprove) {
			$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.pending');
		} else {
			if($aAd['ad_expect_start_time'] > PHPFOX_TIME) {
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.approved');

			} else {
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.running');
			}
		}
		$this->updateStatus($iAdId, $iNextStatus);
		return $iNextStatus;
	}

	public function approveAd($iAdId) {
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		if($aAd['ad_expect_start_time'] > PHPFOX_TIME) {
			$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.approved');

		} else {
			$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.running');
		}

		$this->updateStatus($iAdId, $iNextStatus);
		return $iNextStatus;
	}

	public function denyAd($iAdId) {
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.denied');

		$this->updateStatus($iAdId, $iNextStatus);

		Phpfox::getService('socialad.mail')->sendMailAndNotificaiton($sType = 'deny_ad', $iAdId);
		return $iNextStatus;
	}

	public function pauseAd($iAdId) {
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.paused');

		$this->updateStatus($iAdId, $iNextStatus);
		return $iNextStatus;
	}

	public function resumeAd($iAdId) {
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.running');

		$this->updateStatus($iAdId, $iNextStatus);
		return $iNextStatus;
	}

	public function updateStatus($iAdId, $iStatusId) {
		$aUpdate = array(
			'ad_status' => $iStatusId,
			'ad_last_edited_time' => PHPFOX_TIME
		);
		$sStatusName = Phpfox::getService('socialad.helper')->getNameById('ad.status', $iStatusId);

		// at here, we handle post actions of state transition event which we cannot capture in by a specific function like approveAd()
		// ad moves to running implicitly depends on context, so it is a good way to capture it here, at final stage
		switch($sStatusName) {
			case 'running': 
				Phpfox::getService('socialad.mail')->sendMailAndNotificaiton($sType = 'run_ad', $iAdId);
				$aUpdate['ad_start_time'] = PHPFOX_TIME;
				break;
			case 'deleted': 
				$aUpdate['ad_end_time'] = PHPFOX_TIME;
				break;
			case 'completed': 
				$this->updateCreditAmount($iAdId);
				$aUpdate['ad_end_time'] = PHPFOX_TIME;
				break;
		}

		$this->database()->update($this->_sAdTable, $aUpdate, 'ad_id=' . $iAdId);

	}

	public function updateCreditAmount($iAdId){
		$aAd       =  Phpfox::getService('socialad.ad')->getAdById($iAdId);
		if(!isset($aAd['ad_id'])){
			return false;
		}

		$aTransaction = Phpfox::getService('socialad.payment')->getTransactionByAdId($iAdId);
		if(!isset($aTransaction['transaction_status_id'])
			|| $aTransaction['transaction_status_id'] != Phpfox::getService("socialad.helper")->getConst("transaction.status.completed")
		){
			return false;
		}
		$transaction_amount = doubleval($aTransaction['transaction_amount']);

		$aPackage  =  Phpfox::getService('socialad.package')->getPackageById($aAd['ad_package_id']);
		$sBenefitName = Phpfox::getService('socialad.helper')->getNameById('package.benefit', $aAd['ad_benefit_type_id']);

		$iCompared = 0; 
		$iLimit = $aAd['ad_benefit_limit_number'];
		switch($sBenefitName) {
			case 'click': 
				$iCompared = $aAd['ad_total_click'];
				break;
			case 'impression': 
				$iCompared = $aAd['ad_total_impression'];
				break;
			case 'day': 
				$iCompared = $aAd['ad_total_running_day'];
				break;
		}

		$iRemain = $iLimit - $iCompared;
		$credit_amount = 0.00;
		if((int)$iRemain > 0){
			$credit_amount = round( ( ($transaction_amount * $iRemain) / $iLimit ) , 2);

			$aUpdate = array(
				'credit_amount' => $credit_amount,
			);
			$this->database()->update($this->_sAdTable, $aUpdate, 'ad_id=' . $iAdId);

			// update credit money for user
			$aCreditMoney = Phpfox::getService('socialad.ad')->getCreditMoneyByUserId((int)$aAd['ad_user_id']);
			if(isset($aCreditMoney['creditmoney_id'])){
				// update 
				$aVals = array(
					'creditmoney_total_amount' => doubleval($aCreditMoney['creditmoney_total_amount']) + $credit_amount, 
					'creditmoney_remain_amount' => doubleval($aCreditMoney['creditmoney_remain_amount']) + $credit_amount, 
					'creditmoney_description' => '', 
				);

				$this->updateCreditMoneyByCreditMoneyId((int)$aCreditMoney['creditmoney_id'], $aVals);
			} else {
				// add 
				$aVals = array(
					'creditmoney_total_amount' => $credit_amount, 
					'creditmoney_user_id' => $aAd['ad_user_id'], 
					'creditmoney_remain_amount' => $credit_amount, 
					'creditmoney_description' => '', 
				);

				$this->addCreditMoney($aVals);
			}
		}

		return true;
	}

	public function addCreditMoney($aVals = array()){
		$aInsert = array(
			'creditmoney_user_id' => (int)$aVals['creditmoney_user_id'], 
			'creditmoney_total_amount' => doubleval($aVals['creditmoney_total_amount']), 
			'creditmoney_remain_amount' => doubleval($aVals['creditmoney_remain_amount']), 
			'creditmoney_time_stamp' => PHPFOX_TIME, 
			'creditmoney_description' => $aVals['creditmoney_description'], 
		);

		$id = $this->database()->insert(Phpfox::getT('socialad_credit_money'), $aInsert);

		return $id;
	}

	public function addCreditMoneyRequest($aVals = array()){
		$oFilter = Phpfox::getLib('parse.input');

		$aInsert = array(
			'creditmoneyrequest_creditmoney_id' => (int)$aVals['creditmoneyrequest_creditmoney_id'], 
			'creditmoneyrequest_amount' => doubleval($aVals['creditmoneyrequest_amount']), 
			'creditmoneyrequest_reason' => $oFilter->clean($aVals['creditmoneyrequest_reason'], 1000), 
			'creditmoneyrequest_request_time_stamp' => PHPFOX_TIME, 
			'creditmoneyrequest_status' => (int)$aVals['creditmoneyrequest_status'], 
			'creditmoneyrequest_ad_id' => isset($aVals['creditmoneyrequest_ad_id']) ? (int)$aVals['creditmoneyrequest_ad_id'] : 0, 
		);

		$id = $this->database()->insert(Phpfox::getT('socialad_credit_money_request'), $aInsert);

		return $id;		
	}

	public function updateCreditMoneyByCreditMoneyId($id = null, $aVals = array()){
		if(null == $id){
			return false;
		}

		$aUpdate = array(
			'creditmoney_total_amount' => doubleval($aVals['creditmoney_total_amount']), 
			'creditmoney_remain_amount' => doubleval($aVals['creditmoney_remain_amount']), 
			'creditmoney_time_stamp' => PHPFOX_TIME, 
			'creditmoney_description' => $aVals['creditmoney_description'], 
		);
		$this->database()->update(Phpfox::getT('socialad_credit_money'), $aUpdate, 'creditmoney_id=' . (int)$id);

	}

	public function updateRemainingAmountOfCreditMoneyById($id, $remainingAmount){
		$aUpdate = array(
			'creditmoney_remain_amount' => doubleval($remainingAmount), 
			'creditmoney_time_stamp' => PHPFOX_TIME, 
		);
		$this->database()->update(Phpfox::getT('socialad_credit_money'), $aUpdate, 'creditmoney_id=' . (int)$id);
	}

	public function updateStatusOfCreditMoneyRequestById($id, $status){
		$aUpdate = array(
			'creditmoneyrequest_status' => (int)$status, 
			'creditmoneyrequest_update_time_stamp' => PHPFOX_TIME, 
		);
		$this->database()->update(Phpfox::getT('socialad_credit_money_request'), $aUpdate, 'creditmoneyrequest_id=' . (int)$id);		
	}

	public function deleteAd($iAdId) {
		$iStatusId = Phpfox::getService('socialad.helper')->getConst('ad.status.deleted');
		$this->updateStatus($iAdId, $iStatusId);

		return true;

	}

	public function createSimilarAdFrom($iAdId,$iPackageId = null) {
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		// copy image	
		$sImageFullPath = sprintf(Phpfox::getService('socialad.ad.image')->getImageDir() . $aAd['image_path'], '');
		$sNewImagePath = Phpfox::getService('socialad.ad.image')->createAdImageFromItemImage($sImageFullPath);
		// copy database data

		$aVals = array( 
			"image_path" =>  $sNewImagePath,
			 "ad_package_id" => ((int)$iPackageId > 0) ? $iPackageId : $aAd['ad_package_id'],
			 "ad_item_type" => $aAd['ad_item_type'],
			 "ad_item_id" => $aAd['ad_item_id'],
			 "ad_external_url" => $aAd['ad_external_url'],
			 "ad_type" => $aAd['ad_type'],
			 "ad_title" => $aAd['ad_title'],
			 "ad_text" => $aAd['ad_text'],
			 "campaign_id" => $aAd['ad_campaign_id'] ,
			 "is_continuous" => (int)$aAd['ad_expect_start_time'] > 0 ? 0 : 1,
			 "ad_expect_start_time_month" => (int)$aAd['ad_expect_start_time'] > 0 ? date('n', $aAd['ad_expect_start_time']) : 0,
			 "ad_expect_start_time_day" => (int)$aAd['ad_expect_start_time'] > 0 ? date('j', $aAd['ad_expect_start_time']) : 0,
			 "ad_expect_start_time_year" => (int)$aAd['ad_expect_start_time'] > 0 ? date('Y', $aAd['ad_expect_start_time']) : 0,
			 "ad_expect_start_time_hour" => (int)$aAd['ad_expect_start_time'] > 0 ? date('H', $aAd['ad_expect_start_time']) : 0,
			 "ad_expect_start_time_minute" => (int)$aAd['ad_expect_start_time'] > 0 ? date('i', $aAd['ad_expect_start_time']) : 0,
			 "ad_expect_end_time_month" => (int)$aAd['ad_expect_end_time'] > 0 ? date('n', $aAd['ad_expect_end_time']) : 0,
			 "ad_expect_end_time_day" => (int)$aAd['ad_expect_end_time'] > 0 ? date('j', $aAd['ad_expect_end_time']) : 0,
			 "ad_expect_end_time_year" => (int)$aAd['ad_expect_end_time'] > 0 ? date('Y', $aAd['ad_expect_end_time']) : 0,
			 "ad_expect_end_time_hour" => (int)$aAd['ad_expect_end_time'] > 0 ? date('H', $aAd['ad_expect_end_time']) : 0,
			 "ad_expect_end_time_minute" => (int)$aAd['ad_expect_end_time'] > 0 ? date('i', $aAd['ad_expect_end_time']) : 0,
			 "placement_module_id" => Phpfox::getService('socialad.ad.placement')->getModulesOfAd($iAdId),
			 "placement_block_id" => $aAd['placement_block_id'] ,
			 "audience_location" => Phpfox::getService('socialad.ad.audience')->getLocationsOfAd($iAdId),
			 "audience_gender" => $aAd['audience_gender'],
			 "is_show_guest" => $aAd['is_show_guest'],
			 "audience_age_min" => $aAd['audience_age_min'],
			 "audience_age_max" => $aAd['audience_age_max'], 
			 "ad_number_of_package" => $aAd['ad_number_of_package'], 
		);

		$iNewAdId = $this->handleSubmitForm($aVals);

		return $iNewAdId;

		
	}

	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Service_Ad_Process__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}



