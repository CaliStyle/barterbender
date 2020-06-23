<?php

defined('PHPFOX') or exit('NO DICE!');


class Ecommerce_Service_Helper extends Phpfox_Service
{
	private $_aTransaction;
	private $_aDate;
	private $_aProduct;
	public function __construct()
	{
		
		$this->_aTransaction = array( 
			'status' => array( 
				"initialized" => array(
					"id" => 1, 
					"phrase" => _p('initialized'),
					'name' => 'initialized',
					'description' => '', 
				),
				"expired" => array(
					"id" => 2, 
					"phrase" => _p('expired'),
					'name' => 'expired',
					'description' => '', 
				),
				"pending" => array(
					"id" => 3, 
					"phrase" => _p('pending'),
					'name' => 'pending',
					'description' => '', 
				),
				"completed" => array(
					"id" => 4, 
					"phrase" => _p('completed'),
					'name' => 'completed',
					'description' => '', 
				),
				"canceled" => array(
					"id" => 5, 
					"phrase" => _p('canceled'),
					'name' => 'canceled',
					'description' => '', 
				),
			),
			'method' => array( 
				"paypal" => array(
					"id" => 1, 
					"phrase" => _p('paypal'),
					'name' => 'paypal',
					'description' => '', 
				),
				"2checkout" => array(
					"id" => 2, 
					"phrase" => _p('2checkout'),
					'name' => '2checkout',
					'description' => '', 
				),
				"paylater" => array(
					"id" => 3, 
					"phrase" => _p('pay_later'),
					'name' => 'paylater',
					'description' => '', 
				),
				"paybycredit" => array(
					"id" => 4, 
					"phrase" => _p('pay_credit'),
					'name' => 'paybycredit',
					'description' => '', 
				),
			),
		);

		$this->_aDate = array(
			'dayofweek' => array( 
				"monday" => array(
					"id" => 1, 
					"phrase" => _p('monday'),
					'name' => 'monday',
					'description' => '', 
				),
				"tuesday" => array(
					"id" => 2, 
					"phrase" => _p('tuesday'),
					'name' => 'tuesday',
					'description' => '', 
				),
				"wednesday" => array(
					"id" => 3, 
					"phrase" => _p('wednesday'),
					'name' => 'wednesday',
					'description' => '', 
				),
				"thursday" => array(
					"id" => 4, 
					"phrase" => _p('thursday'),
					'name' => 'thursday',
					'description' => '', 
				),
				"friday" => array(
					"id" => 5, 
					"phrase" => _p('friday'),
					'name' => 'friday',
					'description' => '', 
				),
				"saturday" => array(
					"id" => 6, 
					"phrase" => _p('saturday'),
					'name' => 'saturday',
					'description' => '', 
				),
				"sunday" => array(
					"id" => 7, 
					"phrase" => _p('sunday'),
					'name' => 'sunday',
					'description' => '', 
				),
			),
		);


		$this->_aProduct = array(
			'status' => array( 
				'draft' => array ( 
					'id' => 1,
					'phrase' => _p('draft'),
					'description' => '', 
					'name' => 'draft'),
				'unpaid' => array (
					'id' => 2,
					'phrase' => _p('unpaid'),
					'description' => '', 
					'name' => 'unpaid'),
				'pending' => array (
					'id' => 3,
					'phrase' => _p('pending'),
					'description' => '', 
					'name' => 'pending'),
				'denied' => array (
					'id' => 4,
					'phrase' => _p('denied'),
					'description' => '', 
					'name' => 'denied'),
				'running' => array (
					'id' => 5,
					'phrase' => _p('running'),
					'description' => '', 
					'name' => 'running'),
				'paused' => array (
					'id' => 6,
					'phrase' => _p('paused'),
					'description' => '', 
					'name' => 'paused'),
				'completed' => array (
					'id' => 7,
					'phrase' => _p('expired'),
					'description' => '', 
					'name' => 'completed'),
				'deleted' => array (
					'id' => 8,
					'phrase' => _p('deleted'),
					'description' => '', 
					'name' => 'deleted'),
				'approved' => array (
					'id' => 9,
					'phrase' => _p('approved'),
					'description' => '', 
					'name' => 'approved'),
				'other' => array (
					'id' => 10,
					'phrase' => _p('other'),
					'description' => '', 
					'name' => 'other'),
			),							
		);


	}

    /**
     * Show datetime in interface
     */
    public function convertToUserTimeZone($iTime)
    {
        $iTimeZoneOffsetInSecond = Phpfox::getLib('date') -> getTimeZone() * 60 * 60;
        
        $iTime = $iTime + $iTimeZoneOffsetInSecond;
        
        return $iTime;
    }
    
    /**
     * Store datetime in server with GMT0
     */
    public function convertFromUserTimeZone($iTime)
    {
        $iTimeZoneOffsetInSecond = Phpfox::getLib('date') -> getTimeZone() * 60 * 60;
        
        $iTime = $iTime - $iTimeZoneOffsetInSecond;
        
        return $iTime;
    }

	public function getStartOfDay($iTimeStamp) {
		return mktime(0, 0,0, date('m', $iTimeStamp), date('d', $iTimeStamp), date('y', $iTimeStamp));
	}

	public function getEndOfDay($iTimeStamp) { 
		return mktime(23, 59, 59, date('m', $iTimeStamp), date('d', $iTimeStamp), date('y', $iTimeStamp));
	}

	public function getDateString($aData) {
		return $aData['month'] . '/' . $aData['day'] ;
		   //	'-' . $aData['year'];
	}

	public function convertTime($iTimeStamp, $format = null) {
		if(!$iTimeStamp) {
			return _p('none');
		}
		if(null == $format){
			$format = Phpfox::getParam('core.global_update_time');
		}
		return date($format, $iTimeStamp);
	}

    public function isNumeric($val){
        if(strlen(trim($val)) == 0){
            return false;
        }

        if (!is_numeric($val))
        {
            return false;
        }       

        return true;
    }

    public function price($sPrice)
    {
        if (empty($sPrice))
        {
            return '0.00';
        }
        
        $sPrice = str_replace(array(' ', ','), '', $sPrice);
        $aParts = explode('.', $sPrice);        
        if (count($aParts) > 2)
        {
            $iCnt = 0;
            $sPrice = '';
            foreach ($aParts as $sPart)
            {
                $iCnt++;
                $sPrice .= (count($aParts) == $iCnt ? '.' : '') . $sPart;
            }
        }       
        
        return $sPrice;
    }

    public function getUserParam($sParam, $iUserId) {

        $iGroupId = $this->getUserBy('user_group_id', $iUserId);

        return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sParam);

    }
    
    private function getUserBy($sVar, $iUserId ) {

        $result = $this->_getUserInfo($iUserId);
        if (isset($result[$sVar]))
        {
            return $result[$sVar];
        }

        return false;
    }
    
    private function _getUserInfo($iUserId) {
        $aRow = $this->database()->select('u.*')
            ->from(Phpfox::getT('user'), 'u')
            ->where('u.user_id = ' . $iUserId)
            ->execute('getRow');
        if(!$aRow) {
            return false;
        }

        $aRow['age'] = Phpfox::getService('user')->age(isset($aRow['birthday']) ? $aRow['birthday'] : '');
        $aRow['location'] = $aRow['country_iso']; // we will improve it later to deal with cities 
        $aRow['language'] = $aRow['language_id']; 
        return $aRow;
        // $this->_aUser = $aRow;
    }	


    public function getHour(){

    }

	public function getPeriodByUserTimeZone($period = 'today', $range_of_dates = array()){
		$curTimeOfUser = $this->convertToUserTimeZone(PHPFOX_TIME);
		// $curTime = PHPFOX_TIME;

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
				$iStartTime = $this->getStartOfDay($curTimeOfUser);
				$iEndTime = $this->getEndOfDay($curTimeOfUser);
				break;			
			case 'yesterday':
				// Yesterday
				$curTimeOfUser = $curTimeOfUser - (1 * 24 * 60 * 60);
				$iStartTime = $this->getStartOfDay($curTimeOfUser);
				$iEndTime = $this->getEndOfDay($curTimeOfUser);
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

	public function convertFromUserTimeZoneToServerTime($iTime)
	{
		$iTimeZoneOffsetInSecond = Phpfox::getLib('date')->getTimeZone() * 60 * 60;
		// on the interface we have convert into gmt, now we roll back to server time
		$iTime = $iTime - $iTimeZoneOffsetInSecond;

		return $iTime;
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

    public function getDefaultLanguage(){
				
		$aCond = array('l.is_default = 1');
  		$aLanguageDefault = Phpfox::getService('language')->get($aCond);
  		if(isset($aLanguageDefault[0])){
  			return $aLanguageDefault[0]['language_code'];
  		}
  		else{
  			return 'en';
  		}
    }

    public function isEmail($email){
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		}

		return true;
    }

	public function array_divide($array, $segmentCount) {
	    $dataCount = count($array);
	    if ($dataCount == 0) return false;
	    $segmentLimit = ceil($dataCount / $segmentCount);
	    $outputArray = array_chunk($array, $segmentLimit);
	 
	    return $outputArray;
	}    


	public function isHavingFeed($type_id, $item_id){
		$aFeed = $this -> database()
					-> select('feed.*')
					->from(Phpfox::getT("feed"), 'feed')
					->where('type_id = \'' . $type_id . '\' AND item_id = ' . (int)$item_id)
					-> execute("getSlaveRow");

		if(isset($aFeed['feed_id'])){
			return true;
		}

		return false;
	}

	public function getFeed($type_id, $item_id){
		return $this -> database()
					-> select('feed.*')
					->from(Phpfox::getT("feed"), 'feed')
					->where('type_id = \'' . $type_id . '\' AND item_id = ' . (int)$item_id)
					-> execute("getSlaveRow");
	}

	public function getCurrencyById($currency_id){
		return $this -> database()
					-> select('currency.*')
					->from(Phpfox::getT("currency"), 'currency')
					->where('currency_id = \'' . $currency_id . '\' ')
					-> execute("getSlaveRow");
	}

	public function getLanguageIdByUserId($iUserId){
		$language_id = $this -> database()
					->select("u.language_id")
					->from(Phpfox::getT("user"),'u')
					->where('u.user_id = ' . (int)$iUserId)
					->execute("getSlaveField");

		if($language_id == null){
			$language_id = 'en';
		}

		return $language_id;
	}

	public function getThemeFolder(){
		return Phpfox::getLib('template')->getThemeFolder();
	}


    public function displayTimeByFormat($format = 'M j, Y g:i a', $time)
    {
		return Phpfox::getTime($format, $this->convertToUserTimeZone($time), false);
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

	public function getDescriptionById($sParam, $iId) {
		$sType = 'description';
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
	public function getList($sName) {
		$aList = array();
		switch($sName) {

			case 'product':
				$aList = $this->_aProduct;
				break;

			case 'default':
				break;
			}

		return $aList;
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
			case 'name' :
				return $aList[$sMiddle][$sLast]['name'];
				break;
			case 'description' :
				return $aList[$sMiddle][$sLast]['description'];
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

	public function removeEmptyElement($aArray) {
		$aReturn = array();
		foreach($aArray as $sElement ) {
			if($sElement) {
				$aReturn[] = $sElement;
			}
		}

		return $aReturn;
	}

	public function checkEmptyInputArrayField($aArray){
		foreach ($aArray as $key => $value) {
			if(strlen(trim($value)) > 0){
				return false;
			}
		}

		return true;
	}

	public function getCurrentCurrencies($sGateway = 'paypal', $sDefaultCurrency = '') {
		
		$aFoxCurrencies = Phpfox::getService('core.currency')->getForBrowse();
		$aResults = array();
		foreach($aFoxCurrencies as $aCurrency)
		{
			if ($aCurrency['is_default'] == '1'){
				$aResults[] = $aCurrency;
			}
		}

		return $aResults;
	}

	public function getMoneyText($sAmount, $sCurrency) {
		return $sCurrency . $sAmount;
	}

    public function getMonthsBetweenTwoDays($iFromTimestamp, $iToTimestamp)
    {
		$iFromTimestamp = (int) $iFromTimestamp;
		if ($iFromTimestamp == 0)
		{
			return array();
		}
		
		$iToTimestamp = (int) $iToTimestamp;
		if ($iToTimestamp == 0)
		{
			return array();
		}
	
        $i = date("Ym", $iFromTimestamp);
        
        $aMonths = array();
        
        while ($i <= date("Ym", $iToTimestamp))
        {
            $iCurrentTimestamp = strtotime($i . "01");
            
            $iYear = (int) substr($i, 0, -2);
            $iMonth = (int) substr($i, 4);
            
            $sMonth = date("M", $iCurrentTimestamp);
            
            $aMonths[] = array(
                'sMonth' => $sMonth,
                'iYear' => $iYear,
                'iMonth' => $iMonth
                    );
            
            if (substr($i, 4, 2) == "12")
            {
                $i = (date("Y", strtotime($i . "01")) + 1) . "01";
            }
            else
            {
                $i++;
            }
        }
        
        return $aMonths;
    }
           

	public function isPhoto(){
		if(Phpfox::isModule('photo') || Phpfox::isModule('advancedphoto')){
			return true;
		}
		return false;
	}
	public function isVideo(){
		if(Phpfox::isModule('video') || Phpfox::isModule('videochannel')){
			return true;
		}
		return false;
	}

	public function isAdvPhoto(){
		return Phpfox::isModule('advancedphoto');
	}

    public function getSelectCountriesForSearch($sSelected = null)
    {
        $sContries = '<select name="search[country_iso]" id="country_iso">' . "\n";
		$sContries .= "\t\t" . '<option value="-1"'.((isset($sSelected) && $sSelected == '-1') ? ' selected="selected"' : '').'>' . _p('core.select') . ':</option>' . "\n";
		
        foreach (Phpfox::getService('core.country')->get() as $sIso => $sCountry)
		{
            $sContries .= "\t\t\t" . '<option class="js_country_option" id="js_country_iso_option_' . $sIso . '" value="' . $sIso . '"' . ((isset($sSelected) && $sSelected == $sIso) ? ' selected="selected"' : '') . ' >' . (Phpfox::getLib('locale')->isPhrase('core.translate_country_iso_' . strtolower($sIso)) ? _p('core.translate_country_iso_' . strtolower($sIso)) : '') . str_replace("'", "\'", $sCountry) . '</option>' . "\n";
		}
		
        $sContries .= "\t\t" . '</select>';
		return $sContries;
    }

    public function getGatewaySetting($sGateway){

    	$aGatewayPaypalSetting = Phpfox::getLib('database')->select('*')
            ->from(Phpfox::getT('api_gateway'))
            ->where('gateway_id = \''.$sGateway.'\'')
            ->execute('getSlaveRow');
        $aSetting =  unserialize($aGatewayPaypalSetting['setting']);
        $aGatewayPaypalSetting = array_merge($aGatewayPaypalSetting,$aSetting);

        return $aGatewayPaypalSetting;
    }

    public function buildMenu() 
    {
        if (Phpfox::isUser())
        {
	    	 $iNumberCartItem = Phpfox::getService('ecommerce')->getCountNumberCartItem();
			 if($iNumberCartItem)
			 {
			    $sTextMyCart = _p('my_cart').'<span>'.$iNumberCartItem.'</span>';
			 }
			 else{
			 	$sTextMyCart = _p('my_cart');
			 }

            $aFilterMenu[_p('my_orders')] = 'ecommerce.my-orders';
            $aFilterMenu[_p('manage_orders')] = 'ecommerce.manage-orders';
            $aFilterMenu[_p('my_requests')] = 'ecommerce.my-requests';
        }
        
		Phpfox::getLib('template')->buildSectionMenu('ecommerce', $aFilterMenu);
	}

}
