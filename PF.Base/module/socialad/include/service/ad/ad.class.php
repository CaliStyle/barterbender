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

class Socialad_Service_Ad_Ad extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sAdTable = Phpfox::getT('socialad_ad');

		// sap stands for social ad package
        $this->_sAdAlias = 'saa';

		$this->_aAdStatus = array( 
			'draft' => array ( 
				'id' => 1,
				'phrase' => _p('draft'),
				'name' => 'draft'),
			'unpaid' => array (
				'id' => 2,
				'phrase' => _p('unpaid'),
				'name' => 'unpaid'),
			'pending' => array (

				'id' => 3,
				'phrase' => _p('pending'),
				'name' => 'pending'),
			'denied' => array (
				'id' => 4,
				'phrase' => _p('denied'),
				'name' => 'denied'),
			'running' => array (
				'id' => 5,
				'phrase' => _p('running'),
				'name' => 'running'),
			'paused' => array (
				'id' => 6,
				'phrase' => _p('paused'),
				'name' => 'paused'),
			'completed' => array (
				'id' => 7,
				'phrase' => _p('completed'),
				'name' => 'completed'),
			'deleted' => array (
				'id' => 8,
				'phrase' => _p('deleted'),
				'name' => 'deleted'),
			'approved' => array (
				'id' => 9,
				'phrase' => _p('approved'),
				'name' => 'approved'),
		);

		$this->_aAdType = array(
			'html' => array ( 
				'id' => 1,
				'phrase' => _p('html'),
				'name' => 'html',
				'description' => _p('html_description')
			),
			'banner' => array (
				'id' => 2,
				'phrase' => _p('banner'),
				'name' => 'banner',
				'description' => _p('banner_description')
			),
			'feed' => array (
				'id' => 3,
				'phrase' => _p('feed'),
				'name' => 'feed',
				'description' => _p('feed_description')
			),
		);

		$this->_aCreditMoneyRequestStatus = array( 
			'pending' => array ( 
				'id' => 1,
				'phrase' => _p('pending'),
				'name' => 'pending'),
			'approved' => array (
				'id' => 2,
				'phrase' => _p('approved'),
				'name' => 'approved'),
			'rejected' => array (
				'id' => 3,
				'phrase' => _p('rejected'),
				'name' => 'rejected'),
		);
    }

	private $_aCachedAds = array();

    private $_aTimeType = array('ad_expect_start_time', 'ad_expect_end_time');

	private $_bIsGenerateBannerCacheAds = false;

	private $_aBannerCachedAds = array() ;

	private $_bNeverCache = false;

	public function setNeverCache($value) {
		$this->_bNeverCache = $value;
	}

	public function cronUpdate() {

		$iNumberOfAdToUpdate = Phpfox::getParam('socialad.number_of_ad_updated_by_cron');
		$aStatus = array( 
			Phpfox::getService('socialad.helper')->getConst('ad.status.running'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.paused'),
		);

		$sStatus = implode(' , ' , $aStatus);
		$aConds = array(
			"ad.ad_status IN ({$sStatus})",
		);

		$aExtra = array(
			'page' => 0,
			'limit' => $iNumberOfAdToUpdate,
			'order' => 'ad.ad_last_edited_time ASC'
		);
		$aAds = $this->getAdsBasicInfo($aConds, $aExtra);

		foreach($aAds as $aAd) {
			Phpfox::getService('socialad.ad')->checkAd($aAd);
			Phpfox::getService('socialad.ad.statistic')->compute($aAd);
		}
	}

	public function checkAd($iAdId) {
		$this->checkStartTime($iAdId);
		$this->checkEndTime($iAdId);
		$this->checkPaylaterRequestExpired($iAdId);
		$this->checkBenefitLimitation($iAdId);
	}

	public function checkStartTime($ad) {
		// if start time passed, ad will move to running
		if(is_array($ad)) {
			$aAd = $ad;
		} else {
			$aAd =Phpfox::getService('socialad.ad')->getAdBasicInfoById($ad);
		}

		if(!$aAd) {
			return false;
		}

		// ad is at approved 
		if($aAd['ad_status'] == Phpfox::getService('socialad.helper')->getConst('ad.status.approved')) {
			if($aAd['ad_expect_start_time'] <= PHPFOX_TIME ){
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.running');
				Phpfox::getService('socialad.ad.process')->updateStatus($aAd['ad_id'], $iNextStatus );
			}
		}
				
	}

	public function checkEndTime($ad) {
		if(is_array($ad)) {
			$aAd = $ad;
		} else {
			$aAd =Phpfox::getService('socialad.ad')->getAdBasicInfoById($ad);
		}

		if(!$aAd) {
			return false;
		}

		// ad is at approved 
		if($aAd['ad_status'] == Phpfox::getService('socialad.helper')->getConst('ad.status.running')) {
			if($aAd['ad_expect_end_time'] <= PHPFOX_TIME && $aAd['ad_expect_end_time']) {
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.completed');
				Phpfox::getService('socialad.ad.process')->updateStatus($aAd['ad_id'], $iNextStatus );
			}
		}
				
	}
	public function checkPaylaterRequestExpired($iAdId) {
		// ad is at unpaid and a paylter request sent
			
		
	}

	public function checkBenefitLimitation($ad) {
		//for running, paused ads only
		if(is_array($ad)) {
			$aAd = $ad;
		} else {
			$aAd =Phpfox::getService('socialad.ad')->getAdBasicInfoById($ad);
		}

		if(!$aAd) {
			return false;
		}

		// ad is at approved 
		if(in_array($aAd['ad_status'], array(
			Phpfox::getService('socialad.helper')->getConst('ad.status.running'),
			Phpfox::getService('socialad.helper')->getConst('ad.status.paused'),
		)) && $aAd['ad_benefit_type_id'] != 0 ) {

			$iRemain = $this->getRemainNumber($aAd);
			if($iRemain == 0) {
				$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.completed');
				Phpfox::getService('socialad.ad.process')->updateStatus($aAd['ad_id'], $iNextStatus );
			}
		}
	}


	public function getTimeTypes() {
		return $this->_aTimeType;
	}

	public function computeAllAdsStatistic() {
		$aAds = $this->getAllAds();
		foreach($aAds as $aAd) {
			Phpfox::getService('socialad.ad.statistic')->compute($aAd['ad_id']);
		}
	}

	public function getAllAdsOfUser($iUserId) {
		$aConds = array( 
			'ad_user_id = ' . $iUserId
		);

		$aRows = $this->getAds($aConds);

		return $aRows;
	}


	public function getAdTypesByIds($aIds) {
		$aResult = array();
		foreach($aIds as $iId) {
			$aType = Phpfox::getService('socialad.helper')->getAllById('ad.type', $iId);

			if($aType) { 
				$aResult[] = $aType;
			}
		}

		return $aResult;
	}

	public function getAllAdTypes() {
		return $this->_aAdType;
	}

	private $_aBasicAds = array();
	public function getAdBasicInfoById($iAdId, $bIsNoCache = false) {
		if(isset($this->_aBasicAds[$iAdId]) && !$this->_bNeverCache && !$bIsNoCache) {
			return $this->_aBasicAds[$iAdId];
		}
		$aConds = array(
			'ad.ad_id = ' . $iAdId
		);

		$aRows = $this->getAdsBasicInfo($aConds);
		$aResult = isset($aRows[0]) ? $aRows[0] : false;

		$this->_aBasicAds[$iAdId] = $aResult;
		return $aResult;
	}

	public function getAdsBasicInfo($aConds, $aExtra = array()) {
		$sCond = implode(' AND ', $aConds);

		if($aExtra && isset($aExtra['limit'])) {
			$this->database()->limit($aExtra['page'], $aExtra['limit']);
		}

		if($aExtra && isset($aExtra['order'])) {
			$this->database()->order($aExtra['order']);
		}

		$aRows = $this->database()->select("ad.*")
			->from($this->_sAdTable, 'ad')
			->where($sCond)
			->execute('getRows');

		
		return $aRows;
	}

	/**
	 * for html and banner type
	 * the way we made this function is for the ease of unit testing 
	 */
	public function getToDisplayOnBlock($aQuery) {
		$iUserId   = $aQuery['user_id'];
		$iBlockId  = $aQuery['block_id'];
		$sModuleId = $aQuery['module_id'];


		$aConds = array();
		$aExtra = array();

		$aConds[] =  ' ad.ad_status = ' . Phpfox::getService('socialad.helper')->getConst('ad.status.running', 'id');
		if(isset($aQuery['banner']) && $aQuery['banner']) {

			/**
			 * We only cache banner ads
			 */
			if($this->_aBannerCachedAds) {
				return isset($this->_aBannerCachedAds[$iBlockId]) ? array_slice($this->_aBannerCachedAds[$iBlockId], 0, $aQuery['limit']) : array();
			}

			$aConds[] =  ' ad.ad_type = ' . Phpfox::getService('socialad.helper')->getConst('ad.type.banner', 'id');
		} else if(isset($aQuery['html']) && $aQuery['html'] ) {
			$aConds[] =  ' ad.ad_type = ' . Phpfox::getService('socialad.helper')->getConst('ad.type.html', 'id');
		}
        $aBannedAds = Phpfox::getService('socialad.ad.ban')->getBannedAdsOfUser($iUserId);
        if($aBannedAds) {
            $aConds[] =  ' ad.ad_id NOT IN (' . implode(', ', $aBannedAds) . ') ' ;
        }

		$aConds[] = Phpfox::getService('socialad.ad.audience')->getAudienceConds($iUserId);
		$aConds[] = Phpfox::getService('socialad.ad.placement')->getPlacementConds($iBlockId, $sModuleId);
		if(Phpfox::isUser() == false){
			// it is guest 
			$aConds[] = " ad.is_show_guest = 1 ";
		}

		$aConds = Phpfox::getService('socialad.helper')->removeEmptyElement($aConds);

		if(isset($aQuery['limit']) && $aQuery['limit']) {
			$aExtra['page'] = 0;
			$aExtra['limit'] = $aQuery['limit'];
		}

		$aExtra['order'] = ' ad.completion_rate ASC, ad.ad_last_viewed_time ASC';
		$aAds = $this->getAds($aConds, $aExtra);
		return $aAds;
	}

	/**
	 * We get ads without block conditions
	 * To reduce number of query by querying all at once, 
	 * I assume a site shouldn't have more than 100 ads for each users
	 */
	public function generateCachedAdsWithBlock($aQuery) { 
		$iUserId = $aQuery['user_id'];
		$sModuleId = $aQuery['module_id'];

		// generate banned ads first to avoid querying when doing join to get ads
		if(Phpfox::getUserId()){ 
			Phpfox::getService('socialad.ad.ban')->getBannedAdsOfUser(Phpfox::getUserId());
		}
		$aConds = array();
		$aExtra = array();

		$aConds[] =  ' ad.ad_status = ' . Phpfox::getService('socialad.helper')->getConst('ad.status.running', 'id');
		$aConds[] =  ' ad.ad_type = ' . Phpfox::getService('socialad.helper')->getConst('ad.type.banner', 'id');

		$aConds[] = Phpfox::getService('socialad.ad.audience')->getAudienceConds($iUserId);
		$aConds[] = Phpfox::getService('socialad.ad.placement')->getPlacementConds($iBlockId = 0, $sModuleId);
		$aConds = Phpfox::getService('socialad.helper')->removeEmptyElement($aConds);

		$aBannedAds = Phpfox::getService('socialad.ad.ban')->getBannedAdsOfUser($iUserId);
		if($aBannedAds) {
			$aConds[] =  ' ad.ad_id NOT IN (' . implode(', ', $aBannedAds) . ') ' ;
		}

		if(isset($aQuery['limit']) && $aQuery['limit']) {
			$aExtra['page'] = 0;
			$aExtra['limit'] = 100;
		}

		$aExtra['order'] = ' ad.ad_last_viewed_time ASC';
		$aAds = $this->getAds($aConds, $aExtra);

		$aCachedAds = array();
		$aBlocks = Phpfox::getService('socialad.ad.placement')->getBlocks();
		foreach($aBlocks as $iBlock) {
			$aCachedAds[$iBlock] = array();
		}

		foreach($aAds as $aAd) {
			$aCachedAds[$aAd['placement_block_id']][] = $aAd;	
		}

		$this->_aBannerCachedAds = $aCachedAds;

		$this->_bIsGenerateBannerCacheAds = true;

		return true;

	}

	public function isGenerateBannerCacheAds() {
		return $this->_bIsGenerateBannerCacheAds;
	}


	public function displayHtmlAds($aQuery, $bIsContentOnly = false) {
		if(!Phpfox::getService('socialad.session')->shouldShowHtml()) {
			return false;
		}
		$aQuery['html'] = true;
		$iMaxHtmlAd = Phpfox::getParam('socialad.maxium_number_of_html_ads');
		$aQuery['limit'] = $iMaxHtmlAd;
		$aAds = $this->getToDisplayOnBlock($aQuery);

		Phpfox::getBlock('socialad.ad.display-list', array(
			'aDisplayAds' => $aAds,
			'bDisplayingHtml' => true,
			'iYnsaBlockId' => $aQuery['block_id'],
			'sYnsaModuleId' => $aQuery['module_id'],
			'bIsContentOnly' => $bIsContentOnly
		));
	}

	public function updateCompletionRateMoreAd($aAds = array()){
		foreach($aAds as $aAd) {
			// update $completion_rate
			$completion_rate = 0;
			$sBenefitName = Phpfox::getService('socialad.helper')->getNameById('package.benefit', $aAd['ad_benefit_type_id']);

			$iCompared = 0; 
			$iLimit = $aAd['ad_benefit_limit_number'];
			switch($sBenefitName) {
				case 'click': 
					$iCompared = $aAd['ad_total_click'];
					$completion_rate = round( doubleval($iCompared) / doubleval($iLimit), 5);
					break;
				case 'impression': 
					$iCompared = $aAd['ad_total_impression'];
					$completion_rate = round( doubleval($iCompared) / doubleval($iLimit), 5);
					break;
				case 'day': 
				 	$iCompared = $aAd['ad_total_running_day'];
				 	$completion_rate = round( doubleval($iCompared) / doubleval($iLimit), 5);
				 	Phpfox::getService('socialad.ad.process')->updateAdLastViewedTime($aAd['ad_id']);
				 	break;
			}

			if($completion_rate > 0){
				Phpfox::getService('socialad.ad.process')->updateCompletionRateByAdId($aAd['ad_id'], $completion_rate);
			}
		}
	}

	public function displayBannerAds($aQuery) {
		if(!Phpfox::getService('socialad.session')->shouldShowBanner()) {
			return false;
		}
		$iMaxBannerAd = Phpfox::getParam('socialad.maxium_number_of_banner_ads');
		$aQuery['limit'] = $iMaxBannerAd;
		$aQuery['banner'] = true;
		$aAds = $this->getToDisplayOnBlock($aQuery);

		$this->updateCompletionRateMoreAd($aAds);

		Phpfox::getBlock('socialad.ad.display-list', array(
			'aDisplayAds' => $aAds,
            'bDisplayingHtml' => false,
			'iYnsaBlockId' => $aQuery['block_id'],
			'sYnsaModuleId' => $aQuery['module_id'],
            'bIsContentOnly' => ($aQuery['block_id'] == 3) ? false : true
		));
	}

	/**
	 * this function echoes html code of this blocks
	 * Display main
	 */
	public function displayAdsOnBlock($aQuery) {
		if(!$this->isGenerateBannerCacheAds()) {
			$this->generateCachedAdsWithBlock($aQuery);
		}
		if($aQuery['block_id'] == 3) { // at block 3, we get html first
			$this->displayHtmlAds($aQuery);
		}

		$this->displayBannerAds($aQuery);
	}


	public function mergeAdAndFeedData($aAd) {
		$aResult = $this->getTemplateDataForFeed(false);

		$aResult['feed_link'] = $aAd['ad_click_url'];
		$aResult['full_ad_info'] = $aAd['ad_text'];
		$aResult['feed_title'] = $aAd['ad_title'];
		// don't display time with ads feed
	    $aResult['time_stamp'] = $aAd['ad_start_time'];
	    $aResult['time_update'] = $aAd['ad_start_time'];
		$aResult['user_id'] = $aAd['ad_user_id'];

		$aResult['user_image'] = Phpfox::getService('socialad.user')->getUserBy('user_image', $aAd['ad_user_id']);
		$aResult['user_server_id'] = Phpfox::getService('socialad.user')->getUserBy('server_id', $aAd['ad_user_id']);
		$aResult['user_name'] = Phpfox::getService('socialad.user')->getUserBy('user_name', $aAd['ad_user_id']);
		$aResult['full_name'] = Phpfox::getService('socialad.user')->getUserBy('full_name', $aAd['ad_user_id']);
		$aResult['full_ad_image'] = $aAd['image_full_url'];
		$aResult['feed_id'] = $aAd['ad_id'];

        Phpfox_Template::instance()->assign('aFeedAd', $aResult);
        Phpfox_Component::setPublicParam('custom_param_social_ad_' . $aResult['feed_id'], $aResult);

        if (Phpfox::isModule('ynfeed')) {
            Phpfox::getService('ynfeed')->getExtraInfo($aResult);
        }
		return $aResult;
	}

	public function getTemplateDataForFeed($bSetParam = true) {
		// don't display time with ads feed
		// so we remove time fields 
		$sFeedNoImage = Phpfox::getService('socialad.ad.image')->getNoImageUrlOfFeed();
		$aFeed = array( 
			'feed_id' => 28, 
			'app_id' => 0, 
			'privacy' => 0, 
			'privacy_comment' => 0, 
			'type_id' => 'socialad_ad', 
			'user_id' => Phpfox::getUserId(), 
			'parent_user_id' => 0, 
			'item_id' => 1, 
			'time_stamp' => time(),
			'feed_reference' => 0, 
			'parent_feed_id' => 0, 
			'parent_module_id' => 0, 
			'time_update' => time(),
			'is_friend' => false, 
			'app_title' => '', 
			'profile_page_id' => 1, 
			'user_server_id' => 0, 
			'user_name' => 'socialad', 
			'full_name' => 'Ad', 
			'gender' => 1, 
			'user_image' => '', 
			'is_invisible' => 0, 
			'user_group_id' => 2, 
			'language_id' => 'en', 
			'feed_time_stamp' => 1379905070,
			'can_post_comment' => null, 
			'feed_info' => '',
			'feed_link' => 'google.com', 
			'enable_like' => false,
			'full_ad_image' => $sFeedNoImage ,
			'bShowEnterCommentBlock' => false,
			'feed_month_year' => '',
			'feed_is_liked' => null, 
			'feed_like_phrase' => null,
			'feed_total_like' => 0,
            'load_block' => 'socialad.ad.display-feed-ad',
            'full_ad_info' => 'Example Ad Text',
            'feed_title' => 'Example Ad Title',
            'sponsored_feed' => true
		);
        if ($bSetParam) {
            Phpfox_Template::instance()->assign('aFeedAd', $aFeed);
            Phpfox_Component::setPublicParam('custom_param_social_ad_' . $aFeed['feed_id'], $aFeed);
            if (Phpfox::isModule('ynfeed')) {
                Phpfox::getService('ynfeed')->getExtraInfo($aFeed);
            }
        }
        return $aFeed;
	}
	/**
	 * for feed type
	 */
	public function getToDisplayOnFeed() {
		if(!Phpfox::getService('socialad.session')->shouldShowFeed()) {
			return false;
		}

		$iNumberOfAd = Phpfox::getParam('socialad.number_ads_shown_per_view_on_feed');
		$aAds = $this->getAdsOnFeed($iNumberOfAd);

		$aResults = array();
		foreach($aAds as $aAd) {
			$aResult = $this->mergeAdAndFeedData($aAd);
			$aResults[] = $aResult;
			Phpfox::getService('socialad.ad.process')->view($aAd['ad_id']);
		}

		return  $aResults;

	}

	public function getAdsOnFeed($iLimit = 0) {
		$aConds[] = " ad.ad_type = "  . Phpfox::getService('socialad.helper')->getConst('ad.type.feed', 'id');
		$aConds[] = " ad.ad_status = "  . Phpfox::getService('socialad.helper')->getConst('ad.status.running', 'id');
		if(Phpfox::isUser() == false){
			// it is guest 
			$aConds[] = " ad.is_show_guest = 1 ";
		}

		$aExtra = array();
		if($iLimit) {
			$aExtra = array(
				'page' => 0,
				'limit' => $iLimit,
				'order' => 'ad.completion_rate ASC, ad.ad_last_viewed_time ASC'
			);
		}
		return $this->getAds($aConds, $aExtra);
		
	}

	public function getTotalImpressionsOfCampaign($iCampaignId) {
		$aConds = array( 
			'ad_campaign_id = ' . $iCampaignId
		);

		$aRow = $this->getTotal($aConds);

		return $aRow['total_impression'];
	}

	public function getTotalClicksOfCampaign($iCampaignId) {
		$aConds = array( 
			'ad_campaign_id = ' . $iCampaignId
		);

		$aRow = $this->getTotal($aConds);

		return $aRow['total_click'];
	}

	public function getTotal($aConds) {
		$sCond = implode(' AND ', $aConds);

		$aRow = $this->database()->select('SUM(ad_total_impression) as total_impression, SUM(ad_total_click) AS total_click')
			->from($this->_sAdTable)
			->where($sCond)
			->execute('getRow');

		return $aRow;
	}


	public function getAllStatuses() {
		return $this->_aAdStatus;
	}

	public function getAllCreditMoneyRequestStatuses() {
		return $this->_aCreditMoneyRequestStatus;
	}

	public function getTable() {
		return $this->_sAdTable;
	}

	public function getAlias() {
		return $this->_sAdAlias;
	}

	public function getAdById($iAdId) {
		$aConds = array( 
			'ad.ad_id = ' . $iAdId
		);

		$aRows = $this->getAds($aConds);

		return $aRows[0];
	}

	public function count($aConds) {
		$sCond = implode(' AND ', $aConds);

		$iCnt = $this->database()->select("COUNT(ad_id)")
			->from($this->_sAdTable, 'ad')
			->where($sCond)
			->execute('getSlaveField');	

		return $iCnt;
	}

	public function retrievePermission($aAd) {
		$iAdId = $aAd['ad_id'];
		$aAd['can_edit_ad'] = Phpfox::getService('socialad.permission')->canEditAd($iAdId);
		$aAd['can_delete_ad'] = Phpfox::getService('socialad.permission')->canDeleteAd($iAdId);
		$aAd['can_place_order'] = Phpfox::getService('socialad.permission')->canPlaceOrderAd($iAdId);
		$aAd['can_deny_approve_ad'] = Phpfox::getService('socialad.permission')->canDenyApproveAd($iAdId);
		$aAd['can_pause_ad'] = Phpfox::getService('socialad.permission')->canPauseAd($iAdId);
		$aAd['can_resume_ad'] = Phpfox::getService('socialad.permission')->canResumeAd($iAdId);
		return $aAd;
	}

	public function getTotalAdOfCampaign($iCampaignId) {

		$iCnt = $this->database()->select("COUNT(ad_id)")
			->from($this->_sAdTable, 'ad')
			->where('ad.ad_campaign_id = ' . $iCampaignId)
			->execute('getSlaveField');	

		return $iCnt;

	}
	public function getAds($aConds = array(), $aExtra = array()) {

		if(count($aConds)){
			$sCond = implode(' AND ', $aConds);
		}
		else{
			$sCond = '1=1';
		}
		

		if($aExtra && isset($aExtra['limit'])) {
			$this->database()->limit($aExtra['page'], $aExtra['limit']);
		}

		if($aExtra && isset($aExtra['order'])) {
			$this->database()->order($aExtra['order']);
		}

		$aRows = $this->database()->select("DISTINCT ad.ad_id, ad.*, si.*, sc.campaign_name, sp.* ")
			->from($this->_sAdTable, 'ad')
			->leftJoin(Phpfox::getT('socialad_image'), 'si', 'ad.ad_id = si.image_ad_id') 
			->leftJoin(Phpfox::getT('socialad_campaign'), 'sc', 'ad.ad_campaign_id = sc.campaign_id') 
			->leftJoin(Phpfox::getT('socialad_package'), 'sp', 'ad.ad_package_id = sp.package_id')
			->where($sCond)
			->execute('getRows');
		foreach($aRows as $key => $aRow) {
		    $aRow['ad_title_encode'] = htmlentities($aRow['ad_title']);
			$aRow['ad_total_price'] = $aRow['ad_number_of_package'] * $aRow['package_price'];
			$aRow['ad_total_benefit'] = $aRow['ad_number_of_package'] * $aRow['package_benefit_number'];
			$aRow['ad_status_phrase'] = Phpfox::getService('socialad.helper')->getPhraseById('ad.status', $aRow['ad_status']);

			$aRow['ad_type_name'] = Phpfox::getService('socialad.helper')->getNameById('ad.type', $aRow['ad_type']);
			$aRow['ad_type_phrase'] = Phpfox::getService('socialad.helper')->getPhraseById('ad.type', $aRow['ad_type']);

			$aRow['ad_item_type_name'] = Phpfox::getService('socialad.helper')->getNameById('ad.itemtype', $aRow['ad_item_type']);

			$aRow['ad_start_time_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['ad_expect_start_time'] ? $aRow['ad_expect_start_time'] : $aRow['ad_start_time']);

			$aRow['ad_end_time_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['ad_expect_end_time'] ? $aRow['ad_expect_end_time'] : $aRow['ad_end_time']);

			$aRow['ad_remain_number'] =  $this->getRemainNumber($aRow);
			if($aRow['ad_benefit_type_id'] != 0){ // it is not NULL and not 0 (0 means UNLIMITED)
				$aRow['ad_remaining_phrase'] =  $aRow['ad_remain_number'] . ' ' . Phpfox::getService('socialad.helper')->getPhraseById('package.benefit', $aRow['ad_benefit_type_id']);

			} else {
				$aRow['ad_remaining_phrase'] = _p('unlimited');
			}


			$sImagePath = $aRow['image_path'];
			switch($aRow['ad_type_name']) {
			case 'html' :
				$sSuffix = '_html';
				break;
			case 'banner' :
                $sSuffix = '_block' . $aRow['placement_block_id'];
				break;
			case 'feed' :
                $sSuffix = '_feed';
				break;
			}
			$aRow['image_full_url'] = $sImagePath ?  Phpfox::getService('socialad.ad.image')->getFullUrlFromPath($sImagePath,$sSuffix,$aRow['image_server_id']) : '';

			$aRow['ad_click_url'] = Phpfox::getLib('url')->makeUrl('socialad.ad.click', array('id' => $aRow['ad_id']));

			if($aRow['ad_total_impression'] > 0 ) {
				$aRow['ad_ctr'] = sprintf("%.3f", ($aRow['ad_total_click'] / $aRow['ad_total_impression']) * 100);
			} else {
				$aRow['ad_ctr'] = 0;
			}

			$aRow['ad_ctr_phrase'] = $aRow['ad_ctr'] . '%';
			$aRow['audience_location'] = Phpfox::getService("socialad.ad.audience")->getLocationsOfAd($aRow["ad_id"]);
			$aRows[$key] = $aRow;
		}

		return $aRows;
	}

	public function getRemainNumber($aAd) {
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
		return $iRemain > 0 ? $iRemain : 0;

	}

	public function getCreditMoneyByUserId($userID = null){
		if(null == $userID){
			return array();
		}

		$aRow = $this->database()->select('scm.*')
			->from(Phpfox::getT('socialad_credit_money'), 'scm')			
			->where('scm.creditmoney_user_id = ' . (int)$userID)
			->execute('getRow');

		return $aRow;		
	}

	public function getCreditMoneyById($id = null){
		if(null == $id){
			return array();
		}

		$aRow = $this->database()->select('scm.*')
			->from(Phpfox::getT('socialad_credit_money'), 'scm')			
			->where('scm.creditmoney_id = ' . (int)$id)
			->execute('getRow');

		return $aRow;		
	}

	public function getCreditMoneyRequestById($id = null){
		if(null == $id){
			return array();
		}

		$aRow = $this->database()->select('cmr.*')
			->from(Phpfox::getT('socialad_credit_money_request'), 'cmr')			
			->where('cmr.creditmoneyrequest_id = ' . (int)$id)
			->execute('getRow');

		return $aRow;		
	}

	public function countAdByPackageId($packageId){
		return $this->database()->select('COUNT(sa.ad_id)')
			->from($this->_sAdTable, 'sa')
			->where('sa.ad_package_id = ' . (int)$packageId)
			->execute('getSlaveField');	
	}	

	public function getCreditMoneyRequestByUserId($userID = null, $aConds = array(), $aExtra = array()){
		if(null == $userID){
			return array();
		}

		if(count($aConds) > 0){
			$sConds = implode(" AND ", $aConds);
		} else {
			$sConds = '1=1';
		}
		

		$iCnt = $this->database()->select('COUNT(cmr.creditmoneyrequest_id)')
			->from(Phpfox::getT('socialad_credit_money'), 'scm')
			->join(Phpfox::getT('socialad_credit_money_request'), 'cmr', 'cmr.creditmoneyrequest_creditmoney_id = scm.creditmoney_id') 
			->where($sConds . ' AND scm.creditmoney_user_id = ' . (int)$userID)
			->execute('getSlaveField');	

		$aRows = array();
		if((int)$iCnt > 0){
			$aCurrentCurrencies = Phpfox::getService('socialad.helper')->getCurrentCurrencies($sGateway = 'paypal');

			if($aExtra && isset($aExtra['limit'])) {
				$this->database()->limit($aExtra['page'], $aExtra['limit']);
			}

			$aRows = $this->database()->select('cmr.*')
				->from(Phpfox::getT('socialad_credit_money'), 'scm')
				->join(Phpfox::getT('socialad_credit_money_request'), 'cmr', 'cmr.creditmoneyrequest_creditmoney_id = scm.creditmoney_id') 
				->where($sConds . ' AND scm.creditmoney_user_id = ' . (int)$userID)
				->execute('getRows');

			foreach($aRows as &$aRow) {
				$aRow["creditmoneyrequest_status_phrase"] = Phpfox::getService("socialad.helper")->getPhraseById("creditmoneyrequest.status", $aRow["creditmoneyrequest_status"]);
				$aRow["creditmoneyrequest_amount_text"] = Phpfox::getService("socialad.helper")->getMoneyText($aRow["creditmoneyrequest_amount"], $aCurrentCurrencies[0]["currency_id"]);
				$aRow['creditmoneyrequest_request_date_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['creditmoneyrequest_request_time_stamp']);
				$aRow['creditmoneyrequest_update_date_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['creditmoneyrequest_update_time_stamp']);
			}				
		}

		return array($iCnt, $aRows);		

	}

	public function getCreditMoney($aConds = array(), $aExtra = array()){
		if(count($aConds) > 0){
			$sConds = implode(" AND ", $aConds);
		} else {
			$sConds = '1=1';
		}

		$iCnt = $this->database()->select('COUNT(scm.creditmoney_id)')
			->from(Phpfox::getT('socialad_credit_money'), 'scm')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = scm.creditmoney_user_id')
			->where($sConds)
			->execute('getSlaveField');	

		$aRows = array();
		if((int)$iCnt > 0){
			$aCurrentCurrencies = Phpfox::getService('socialad.helper')->getCurrentCurrencies($sGateway = 'paypal');

			if($aExtra && isset($aExtra['limit'])) {
				$this->database()->limit($aExtra['page'], $aExtra['limit']);
			}

			$aRows = $this->database()->select('scm.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('socialad_credit_money'), 'scm')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = scm.creditmoney_user_id')
				->where($sConds)
				->execute('getRows');

			foreach($aRows as &$aRow) {
				$aRow["creditmoney_total_amount_text"] = Phpfox::getService("socialad.helper")->getMoneyText($aRow["creditmoney_total_amount"], $aCurrentCurrencies[0]["currency_id"]);
				$aRow["creditmoney_remain_amount_text"] = Phpfox::getService("socialad.helper")->getMoneyText($aRow["creditmoney_remain_amount"], $aCurrentCurrencies[0]["currency_id"]);
				$aRow['creditmoney_time_stamp_date_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['creditmoney_time_stamp']);
			}				
		}

		return array($iCnt, $aRows);			
	}

	public function getCreditMoneyRequestByStatus($status, $aExtra = array()){
		$iCnt = $this->database()->select('COUNT(cmr.creditmoneyrequest_id)')
			->from(Phpfox::getT('socialad_credit_money_request'), 'cmr')
			->where('cmr.creditmoneyrequest_status = ' . (int)$status)
			->execute('getSlaveField');	

		$aRows = array();
		if((int)$iCnt > 0){
			$aCurrentCurrencies = Phpfox::getService('socialad.helper')->getCurrentCurrencies($sGateway = 'paypal');

			if($aExtra && isset($aExtra['limit'])) {
				$this->database()->limit($aExtra['page'], $aExtra['limit']);
			}

			$aRows = $this->database()->select('cmr.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('socialad_credit_money_request'), 'cmr')
				->join(Phpfox::getT('socialad_credit_money'), 'scm', 'cmr.creditmoneyrequest_creditmoney_id = scm.creditmoney_id') 				
				->join(Phpfox::getT('user'), 'u', 'u.user_id = scm.creditmoney_user_id')
				->where('cmr.creditmoneyrequest_status = ' . (int)$status)
				->execute('getRows');

			foreach($aRows as &$aRow) {
				$aRow["creditmoneyrequest_status_phrase"] = Phpfox::getService("socialad.helper")->getPhraseById("creditmoneyrequest.status", $aRow["creditmoneyrequest_status"]);
				$aRow["creditmoneyrequest_amount_text"] = Phpfox::getService("socialad.helper")->getMoneyText($aRow["creditmoneyrequest_amount"], $aCurrentCurrencies[0]["currency_id"]);
				$aRow['creditmoneyrequest_request_date_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['creditmoneyrequest_request_time_stamp']);
				$aRow['creditmoneyrequest_update_date_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['creditmoneyrequest_update_time_stamp']);
			}				
		}

		return array($iCnt, $aRows);				
	}

	public function getAllAdsOfCampaign($iCampaignId) {
		
		$aConds = array();
		
		if($iCampaignId == 0){ //in case get All
			$aConds = array();
		}
		else{	
			$aConds = array( 
				'ad.ad_campaign_id = ' . $iCampaignId
			);	
		}

		$aRows = $this->getAds($aConds);
		return $aRows;
	}

	public function getAllAds() {
		return $this->getAds();
	}

	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Service_Ad_Ad__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}



