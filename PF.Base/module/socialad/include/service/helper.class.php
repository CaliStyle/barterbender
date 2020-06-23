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

class Socialad_Service_Helper extends Phpfox_Service
{

    private $_aCampaign;
    private $_aCreditmoneyrequest;
    private $_aPackage;
    private $_aTransaction;
    private $_aTrack;
    private $_aAd;

    public function __construct() {

		// returned array should be under this format : array( 'key_string' => array('id' => id, 'phrase' => phrase))
		$this->_aAd = array( 
			'status' => Phpfox::getService('socialad.ad')->getAllStatuses(),
			'type' => Phpfox::getService('socialad.ad')->getAllAdTypes(),
			'itemtype' => Phpfox::getService('socialad.ad.item')->getAllItemTypes()
		);

		$this->_aTrack = array( 
			'type' => Phpfox::getService('socialad.ad.track')->getAllTypes(),
		);

		$this->_aTransaction = array( 
			'status' => Phpfox::getService('socialad.payment')->getAllTransactionStatus(),
			'method' => Phpfox::getService("socialad.payment")->getAllTransactionMethods(),
		);

		$this->_aCreditmoneyrequest = array( 
			'status' => Phpfox::getService('socialad.ad')->getAllCreditMoneyRequestStatuses(),
		);

		$this->_aPackage = array( 
			'benefit' => Phpfox::getService('socialad.package')->getAllPackageBenefitTypes(),
		);

		$this->_aCampaign = array( 
			'status' => Phpfox::getService('socialad.campaign')->getAllCampaignStatus(),
		);
	}

	public function getMoneyText($sAmount, $sCurrency) {
        return Phpfox::getService('core.currency')->getCurrency($sAmount,$sCurrency,2);
	}

	private $_aFriendlyModuleNameMap = array(
		'socialad' => 'YouNet Social Ad', 
		'resume' => 'YouNet Resume',
		'jobposting' => 'YouNet Job Posting' ,
		'fundraising' => 'YouNet Fundraising',
		'contest' => 'YouNet Contest',  
		'core' => 'Member Homepage', 
		'advancedmarketplace' => 'advancedmarketplace', 
		'fevent' => 'fevent', 
		'musicsharing' => 'musicsharing', 
		'videochannel' => 'videochannel', 
		'advancedphoto' => 'advancedphoto',
        'directory' => 'directory',
        'auction' => 'auction',
        'coupon' => 'coupon',
        'petition' => 'petition',
        'ultimatevideo' => 'ultimatevideo',
        'ynsocialstore' => 'ynsocialstore',
        'ynblog' => 'ynblog',
        'ynaffiliate' => 'ynaffiliate',
        'ynmember' => 'ynmember'
	);

	private $_aCorePlacementModules = array(
		'video', 'user', 'search', 'quiz', 'profile', 'photo', 'pages', 'page', 'music', 
		'marketplace', 'invite', 'friend', 'forum', 'event', 'document', 'apps', 'ad', 
		'core', 'groups',
	);
	public function convertModuleToFriendlyName($aModules) {
		$aMainMenus = Phpfox::getLib('template')->getMenu('main');
		$aRightMenus = Phpfox::getLib('template')->getMenu('main_right');
		$aAppMenus = Phpfox::getLib('template')->getMenu('explore');
		$aSubMenus = Phpfox::getLib('template')->getMenu();
		$aFooterMenu = Phpfox::getLib('template')->getMenu('footer');
		$all_menu = array_merge($aMainMenus, $aRightMenus, $aAppMenus, $aSubMenus, $aFooterMenu);
		foreach($this->_aFriendlyModuleNameMap as $key => $sModule){
			if (Phpfox::isModule($key)){
				$title = $sModule;
				foreach($all_menu as $menu){
					if($key == $menu['module']){
						$title = _p($menu['var_name']);
						$this->_aFriendlyModuleNameMap[$key] = $title;
						break;		
					}
				}

			}
		}

		foreach($aModules as &$sModule) {
			if(isset($this->_aFriendlyModuleNameMap[$sModule])) {
				$sModule = $this->_aFriendlyModuleNameMap[$sModule];
			} else {
				if(!in_array($sModule, $this->_aCorePlacementModules)) {
					unset($aModules[$sModule]); // asume key = value
				} else {
					$sModule = ucfirst($sModule);
				}
			}
		}

		return $aModules;
	}


	/**
	 * to create left sub menu for a controller 
	 * <pre>
	 * Phpfox::getService('fundraising')->buildMenu();
	 * </pre>
	 * @by minhta
	 */
	public function buildMenu() {
		$aFilterMenu = array(
			_p('my_campaigns')=> 'socialad.campaign',
			_p('my_ads')=> 'socialad.ad',
			_p('payments')=> 'socialad.payment',
			_p('reports')=> 'socialad.report',
		);



		Phpfox::getLib('template')->buildSectionMenu('socialad', $aFilterMenu);
		Phpfox::getLib('template')->setBreadCrumb(_p('ad'));
	}
	/**
	 * getPhraseById('transaction.status', 1) returns phrase of status 1 of transaction
	 * @params $sParam string
	 * @params $iID integer
	 * @return string phrase of the id
	 */

	public function getPhraseById($sParam, $iId) {

		$sType = 'phrase';
		return $this->getConstById($sParam, $iId, $sType);
	}	

	public function getNameById($sParam, $iId) {

		$sType = 'name';
		return $this->getConstById($sParam, $iId, $sType);
	}

	public function getAllById($sParam, $iId) {

		$sType = 'all';
		return $this->getConstById($sParam, $iId, $sType);
	}
	public function getConstById($sParam, $iId, $sType) {
		$aParts = explode('.', $sParam);

		if(count($aParts) > 3) { 
			return false;
		}

		$sFirst = array_shift($aParts);
		$sLast = array_pop($aParts);

		$aList = $this->getList($sFirst);
		
		if(!isset($aList[$sLast])) {
			return false;
		}

		foreach($aList[$sLast] as $aItem) {
			if($aItem['id'] == $iId) {
				if($sType == 'all') {
					return $aItem;
				}

				return isset($aItem[$sType]) ? $aItem[$sType] : false;
			}
		}
		
		return false;

	}


	public function removeEmptyElement($aArray) {
		$aReturn = array();
		foreach($aArray as $sElement ) {
			if($sElement) {
				$aReturn[] = $sElement;
			}
		}

		return $aReturn;
	}
	/**
	 * refere to https://developers.google.com/chart/interactive/docs/reference#dataparam for more detail about returned json format
	 * @param aData, for example, ([1] -> array( 'ad_id' =1, 'ad_name' = 'aaa'));
	 */
	public function convertStatisticDataIntoTableChartFormat($aDatas, $aTypes = array()) {
		$aResult = array();

		$aCols = array();
		$aCols[] = array( 
			'id' => 'date',
			'type' => 'string',
			'label' => _p('date')
		);
		if($aTypes) {
			foreach($aTypes as $sType) {
				switch($sType) {
					case 'click':
						$aCols[] = array( 
							'id' => 'date',
							'type' => 'number',
							'label' => Phpfox::getLib('parse.output')->parse(_p('click'))
						);
					break;
					case 'impression':
						$aCols[] = array( 
							'id' => 'date',
							'type' => 'number',
							'label' => Phpfox::getLib('parse.output')->parse(_p('impression'))
						);
					break;
					case 'unique_click':
						$aCols[] = array( 
							'id' => 'date',
							'type' => 'number',
							'label' => Phpfox::getLib('parse.output')->parse(_p('unique_click'))
						);
					break;
					case 'reach':
						$aCols[] = array( 
							'id' => 'date',
							'type' => 'number',
							'label' => Phpfox::getLib('parse.output')->parse(_p('reach'))
						);
					break;
				}
			}
		}

		$aRows = array();
		foreach($aDatas as $aData) {
			$aSubRow = array();
			$aSubRow[] = array('v' => $aData['start_date_text']);
			foreach($aTypes as $sType) {
				switch($sType) {
					case 'click':
						$aSubRow[] = array( 
							'v' => $aData['total_click']
						);
					break;
					case 'impression':
						$aSubRow[] = array( 
							'v' => $aData['total_impression']
						);
					break;
					case 'unique_click':
						$aSubRow[] = array( 
							'v' => $aData['total_unique_click']
						);
					break;
					case 'reach':
						$aSubRow[] = array( 
							'v' => $aData['total_reach']
						);
					break;
				}
			}

			$aRows[] = array('c' => $aSubRow);
		}

		$aResult['cols'] = $aCols;
		$aResult['rows'] = array_reverse($aRows);

		return $aResult;	
	}

	/**
	 * we only support paypal
	 * @by minhTA 
	 * @return array(currency)
	 */
	public function getCurrentCurrencies($sGateway = 'paypal', $sDefaultCurrency = '') {
		
		$aFoxCurrencies = Phpfox::getService('core.currency')->getForBrowse();

		$sDefaultCurrency = $sDefaultCurrency ? $sDefaultCurrency : Phpfox::getService('core.currency')->getDefault();
		$aDefaultCurrency = array();
		$aResults = array();
		foreach($aFoxCurrencies as $aCurrency)
		{
			if ($aCurrency['is_default'] == '1'){
                $aResults[] = $aCurrency;
			}
		}
		return $aResults;
	}

	public function getScript($sScript) {
		return '<script type="text/javascript"> ' . $sScript . ' </script>';
	}

	/**
	 * @params sParam input string, for ex, ad.pending, track.view
	 * @param get : id, phrase or get all
	 * @return id of the const
	 */
	public function getConst($sParam, $sGet = 'id') {
		$aParts = explode('.', $sParam);

		if(count($aParts) > 3) { 
			return false;
		}

		$sFirst = array_shift($aParts);
		$sLast = array_pop($aParts);

		// first is name of corresponding item
		$aList = $this->getList($sFirst);


		if(!$aParts) { // if there is not middle part
			// assume that it is to get all list ex 'transaction.status' 
			if($aList) { 
				return $aList[$sLast];
			} else {
				return false;
			}
			//$sMiddle = 'type'; // by default, all constants seem to be a type of s.thing
		} else {
			$sMiddle = $aParts[0];
		}

		if(isset($aList[$sMiddle][$sLast])) {
			switch($sGet) {
			case 'id' :
				return $aList[$sMiddle][$sLast]['id'];
				break;

			case 'phrase' :
				return $aList[$sMiddle][$sLast]['phrase'];
				break;

			case 'all' :
				return $aList[$sMiddle][$sLast];
				break;
			}

			return $aList[$sMiddle][$sLast]['id'];


		} else { 
			return false;
		}
	}

	public function getList($sName) {
		$aList = array();
		switch($sName) {
		case 'ad':
			$aList = $this->_aAd;
			break;

		case 'track':
			$aList = $this->_aTrack;
			break;
		
		case 'transaction':
			$aList = $this->_aTransaction;
			break;

		case 'package':
			$aList = $this->_aPackage;
			break;

		case 'creditmoneyrequest':
			$aList = $this->_aCreditmoneyrequest;
			break;

		case 'campaign':
			$aList = $this->_aCampaign;
			break;

		case 'default':
			break;
		}

		return $aList;
	}


	public function getJsSetupParams() {
		return array(
			'fb_small_loading_image_url' => Phpfox::getLib('template')->getStyle('image', 'ajax/add.gif'),
			'ajax_file_url' => Phpfox::getParam('core.path') . 'static/ajax.php',			
		);
	}
	public function loadSocialAdJsCss() {

		$aParams = $this->getJsSetupParams();

		Phpfox::getLib('template')->setHeader('cache', array( 
			'ynsocialad.js' => 'module_socialad',
			'ynsocialad.ajaxForm.js' => 'module_socialad',
			'jquery.validate.js' => 'module_socialad',
			'chosen.min.css' => 'module_socialad',
			'chosen.jquery.min.js' => 'module_socialad',
			'ajax-chosen.js' => 'module_socialad',
			'<script type="text/javascript">$Behavior.loadYnsocialAdSetupParam = function() { ynsocialad.setParams(\''. json_encode($aParams) .'\'); }</script>'
		)) ->setPhrase( array(  // phrase for JS
			"socialad.this_field_is_required",
			"socialad.number_character_over_limit",
			"socialad.number_character_left",
			"socialad.validator_minlength",
			"socialad.validator_maxlength",
			"socialad.validator_min",
			"socialad.validator_number",
			"socialad.the_start_time_must_be_greater_than_current_time",
			"socialad.the_end_time_must_be_greater_than_the_start_time",
			"socialad.please_enter_a_valid_url",
			"socialad.validator_accept",
		));
	}
	
	public function loadAdminSocialAdJsCss() {

		$aParams = $this->getJsSetupParams();

		Phpfox::getLib('template')->setHeader('cache', array( 
			'ynsocialad.js' => 'module_socialad',
			'ynsocialad.ajaxForm.js' => 'module_socialad',
			'jquery.validate.js' => 'module_socialad',
			'chosen.min.css' => 'module_socialad',
			'ynsocialad_admin.css' => 'module_socialad',
			'chosen.jquery.min.js' => 'module_socialad',
			'ajax-chosen.js' => 'module_socialad',
			'<script type="text/javascript">$Behavior.loadYnsocialAdSetupParam = function() { ynsocialad.setParams(\''. json_encode($aParams) .'\'); }</script>'
		)) ->setPhrase( array(  // phrase for JS
			"socialad.this_field_is_required",
			"socialad.number_character_over_limit",
			"socialad.number_character_left",
			"socialad.validator_minlength",
			"socialad.validator_maxlength",
			"socialad.validator_min",
			"socialad.validator_number",
			"socialad.the_start_time_must_be_greater_than_current_time",
			"socialad.the_end_time_must_be_greater_than_the_start_time",
			"socialad.please_enter_a_valid_url",
			"socialad.validator_accept",
		));
	}
			
	public function convertToUserTimeZone($iTime)
	{
		$iTimeZoneOffsetInSecond = Phpfox::getLib('date')->getTimeZone() * 60 * 60;
		// on the interface we have convert into gmt, now we roll back to server time
		$iTime = $iTime + $iTimeZoneOffsetInSecond;

		return $iTime;
	}

	public function convertFromUserTimeZoneToServerTime($iTime)
	{
		$iTimeZoneOffsetInSecond = Phpfox::getLib('date')->getTimeZone() * 60 * 60;
		// on the interface we have convert into gmt, now we roll back to server time
		$iTime = $iTime - $iTimeZoneOffsetInSecond;

		return $iTime;
	}

	public function isNumeric($val){
		if(empty($val)){
			return false;
		}

		if (!is_numeric($val))
		{
		    return false;
		}		

		return true;
	}

	public function getPeriodByUserTimeZone($period = 'today', $range_of_dates = array()){
		$curTimeOfUser = $this->convertToUserTimeZone(PHPFOX_TIME);

        $month = date('n', $curTimeOfUser);
        $day = date('j', $curTimeOfUser);
        $year = date('Y', $curTimeOfUser);
        $start_hour = date('H', $curTimeOfUser);
        $start_minute = date('i', $curTimeOfUser);
        $start_second = date('s', $curTimeOfUser);

        $iStartTime = 0;
        $iEndTime = 0;
		switch ($period) {
			case 'today':
				// Today
				$iStartTime = Phpfox::getService('socialad.date')->getStartOfDay($curTimeOfUser);
				$iEndTime = Phpfox::getService('socialad.date')->getEndOfDay($curTimeOfUser);
				break;			
			case 'yesterday':
				// Yesterday
				$curTimeOfUser = $curTimeOfUser - (1 * 24 * 60 * 60);
				$iStartTime = Phpfox::getService('socialad.date')->getStartOfDay($curTimeOfUser);
				$iEndTime = Phpfox::getService('socialad.date')->getEndOfDay($curTimeOfUser);
				$iLimit = 2;
				break;			
			case 'last_week':
				// Last week
	            $week = date("W", $curTimeOfUser);
	            $result = $this->getStartAndEndDateOfWeek($week - 1, $year, $curTimeOfUser);

            	// this is this_week, need to convert to last_week
	            $iStartTime = $result[0];
	            $iEndTime = $result[1];

	            $iStartTime = $iStartTime - (7 * 24 * 60 * 60);
	            $iEndTime = $iEndTime - (7 * 24 * 60 * 60);
				break;			
			case 'range_of_dates':
				// Range of dates
				break;			
			default:
				break;
		}

        $iStartTime = $this->convertFromUserTimeZoneToServerTime($iStartTime);
        $iEndTime = $this->convertFromUserTimeZoneToServerTime($iEndTime);
        return array('start' => $iStartTime, 'end' => $iEndTime);		
	}

    public function getStartAndEndDateOfWeek($week, $year, $curUser)
    {
        $time = strtotime("1 January $year", $curUser);
        $day = date('w', $time);
        $time += ((7*$week)+1-$day)*24*3600;        

        $return[0] = $time;
        
        $time += 7*24*3600 - 1;
        $return[1] = $time;
        
        return $return;
    }


}



