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

class Socialad_Service_Permission extends Phpfox_Service
{
	public function canConfirmPayLaterTransaction($iTransactionId) {
		$aTransaction = Phpfox::getService('socialad.payment')->getTransactionById($iTransactionId);
		$bIsAdmin = Phpfox::isAdmin();

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest'))
		{
		    eval($sPlugin);
		}

		if($aTransaction['transaction_method_id'] != Phpfox::getService('socialad.helper')->getConst('transaction.method.paylater')) {
			return false;
		}

		if(!in_array($aTransaction['transaction_status_id'], array(
			Phpfox::getService('socialad.helper')->getConst('transaction.status.initialized'),
		))) {
			return false;
		}
		return $bIsAdmin;
	}

	public function canCancelPayLaterRequest($iTransactionId) {
		$aTransaction = Phpfox::getService('socialad.payment')->getTransactionById($iTransactionId);
		$iUserId = Phpfox::getUserId();
		$bIsAdmin = Phpfox::isAdmin();

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest'))
		{
		    eval($sPlugin);
		}

		if($aTransaction['transaction_method_id'] != Phpfox::getService('socialad.helper')->getConst('transaction.method.paylater')) {
			return false;
		}

		if(!in_array($aTransaction['transaction_status_id'], array(
			Phpfox::getService('socialad.helper')->getConst('transaction.status.initialized'),
		))) {
			return false;
		}


		if($iUserId == $aTransaction['transaction_user_id']) {
			return true;
		} else {
			return $bIsAdmin;
		}
	}


	public function canEditAd($iAdId, $bRedirect = false) {
		
		$iUserId = Phpfox::getUserId();
        $bIsAdmin = Phpfox::isAdmin();
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest'))
		{
		    eval($sPlugin);
		}
	
		if(in_array($aAd['ad_status'], array(
			Phpfox::getService('socialad.helper')->getConst('ad.status.pending'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.denied'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.running'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.paused'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.completed'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.deleted'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.approved'),
		))) {
			return false;
		}
		
        if($aAd['ad_user_id'] != $iUserId) {
			return $bIsAdmin;
		}

        return true;				
	}

	public function canEditCampaign($campaign) {
		
		if(is_array($campaign)) {
			$aCampaign = $campaign;
		} else {
			$aCampaign = Phpfox::getService('socialad.campaign')->getCampaignById($campaign);
		}

		if(!$aCampaign) {
			return false;
		}
		
		$iUserId = Phpfox::getUserId();
		$bIsAdmin = Phpfox::isAdmin();
		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest'))
		{
		    eval($sPlugin);
		}

		if($aCampaign['campaign_status'] == Phpfox::getService('socialad.helper')->getConst('campaign.status.deleted')) {
			return false;
		}

		if($aCampaign['campaign_user_id'] == $iUserId){
			return true;
		}

		return $bIsAdmin;
				
	}

	public function canDeleteCampaign($campaign) {
		
		if(is_array($campaign)) {
			$aCampaign = $campaign;
		} else {
			$aCampaign = Phpfox::getService('socialad.campaign')->getCampaignById($campaign);
		}

		if(!$aCampaign) {
			return false;
		}

		$iUserId = Phpfox::getUserId();
		$bIsAdmin = Phpfox::isAdmin();

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest'))
		{
		    eval($sPlugin);
		}

		if($aCampaign['campaign_status'] == Phpfox::getService('socialad.helper')->getConst('campaign.status.deleted')) {
			return false;
		}
		
		if($aCampaign['campaign_user_id'] == $iUserId) {
			return true;
		}

		return  $bIsAdmin;
				
	}

	public function canDeleteAd($iAdId, $bRedirect = false) {
		$iUserId = Phpfox::getUserId();
		$bIsAdmin = Phpfox::isAdmin();
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest'))
		{
		    eval($sPlugin);
		}

		if($aAd['ad_status'] == Phpfox::getService('socialad.helper')->getConst('ad.status.deleted')) {
			return false;
		}

		if($aAd['ad_user_id'] == $iUserId || $bIsAdmin) {
			return true;
		}

		return false;

	}

	public function canPlaceOrderAd($iAdId) {
		$iUserId = Phpfox::getUserId();
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest'))
		{
		    eval($sPlugin);
		}

		if($aAd['ad_user_id'] != $iUserId) {
			return false;
		}
	
		if(in_array($aAd['ad_status'], array(
			Phpfox::getService('socialad.helper')->getConst('ad.status.draft'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.unpaid'),
		))) {
			return true;
		}
		return false;
	}

	
	public function canPauseAd($iAdId) {
		$iUserId = Phpfox::getUserId();
        $bIsAdmin = Phpfox::isAdmin();
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest')) {
		    eval($sPlugin);
		}

        if(in_array($aAd['ad_status'], array(
			Phpfox::getService('socialad.helper')->getConst('ad.status.running'),
		))) {
          if($aAd['ad_user_id'] == $iUserId) {
              return true;
          }
          return $bIsAdmin;
		}
		
        return false;
	}

	public function canViewDetailAd($iAdId, $bRedirect = false) {
		$iUserId = Phpfox::getUserId();
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);

		if(!$aAd) {
			$this->_toMainPage($bRedirect);
			return false;
		}

		if($aAd['ad_user_id'] == $iUserId){
			return true;
		}

		return Phpfox::getUserParam('socialad.can_view_ad_detail_of_others', $bRedirect);
	}

	public function canResumeAd($iAdId) {
		$iUserId = Phpfox::getUserId();
        $bIsAdmin = Phpfox::isAdmin();
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest')) {
		    eval($sPlugin);
		}


		if(in_array($aAd['ad_status'], array(
			Phpfox::getService('socialad.helper')->getConst('ad.status.paused'),
		))) {
          if($aAd['ad_user_id'] == $iUserId) {
              return true;
          }
			return $bIsAdmin;
		}
		return false;
	}

	public function canDenyApproveAd($iAdId) {
		$iUserId = Phpfox::getUserId();
		$bCanDenyApproveAd = Phpfox::getUserParam('socialad.can_approve_deny_ad');

		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest')) {
		    eval($sPlugin);
		}

		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		if(!in_array($aAd['ad_status'], array(
			Phpfox::getService('socialad.helper')->getConst('ad.status.pending'),
		))) {
			return false;
		}

		return $bCanDenyApproveAd;
	}


	public function canCreateAd($bRedirect = false) {

		if(Phpfox::getUserParam('socialad.can_create_ad', $bRedirect)) {
			if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			}
		}
			
		return false;

	}

	public function canViewCampaignDetail($campaign, $bRedirect = false) {
		if(is_array($campaign)) {
			$aCampaign = $campaign;
		} else {
			$aCampaign = Phpfox::getService('socialad.campaign')->getCampaignById($campaign);
		}

		if(!$aCampaign) {
			return false;
		}
		
		$iUserId = Phpfox::getUserId();
		if ($sPlugin = Phpfox_Plugin::get('socialad.service_permission_check_start_general_unittest'))
		{
		    eval($sPlugin);
		}

		if($aCampaign['campaign_user_id'] == $iUserId){
			return true;
		}

		return $this->_toMainPage($bRedirect);
	}

	private function _toMainPage($bRedirect = true) {
		if($bRedirect) {
			Phpfox::getLib('url')->send('socialad.ad');
		}

		return false;
	}

	public function isUsingAdvEvent(){
		$result = false;
		if($result){
			if(Phpfox::isModule('fevent')){
				return true;
			}
		}
		
		return false;
	}
}



