<?php


defined('PHPFOX') or exit('NO DICE!');

class Coupon_Service_Coupon extends Phpfox_Service
{
    /**
     * define the status of coupon
     * @by : datlv
     * @var array
     */
    private $_status = array(
        'running' 	 => 1,
        'upcoming' 	 => 2,
        'pending' 	 => 3,
        'pause' 	 => 4,
        'endingsoon' => 5,
        'closed' 	 => 6,
        'draft'	 	 => 7,
        'denied'     => 8,
    );

    /**
     * Hold the information about what controller call this function
     *
     * @var array
     */
    private $_aCallback = null;

    public function callback($aCallback) {
        $this->_aCallback = $aCallback;

        return $this;
    }

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('coupon');
	}
	
    /**
     * get status code to query
     * @by : datlv
     * @param $sStatus
     * @return bool
     */
    public function getStatusCode($sStatus) 
    {
        if (isset($this->_status[$sStatus])) {
            return $this->_status[$sStatus];
        } else {
            return false;
        }
    }

    /**
     * get all status code
     * @by : datlv
     * @return array
     */
    public function getAllStatus() {
        return $this->_status;
    }
	
	/**
	 *	Get all countries code
	 * @author TienNPL
	 * @return array of countries data 
	 */
	public function getCountries()
	{
		$aCountries = $this -> database()
							-> select("country_iso, name")
							-> from(Phpfox::getT("country"))
							-> execute("getRows");
		
		return $aCountries;
	}
	
	/**
	 * Get Coupon items according to the data input (this only use for back-end browsing)
	 * @author TienNPL
	 * @param array $aConditions is the array of filter conditions 
	 * @param string $sOrder is the listing order 
	 * @param int $iLimit is the limit of row's number output
	 * @return array of resume items data
	 */
	public function getCouponsForManage($aConds, $sOrder, $iPage = 0, $iLimit = NULL, $iCount = NULL)
	{
		// Generate query object						
		$oSelect = $this -> database() 
						 -> select('c.*, u.user_name, u.full_name, cc.title as category_name')
						 -> from($this->_sTable, 'c')
						 -> join(Phpfox::getT('user'),'u','u.user_id = c.user_id')
						 -> leftjoin(Phpfox::getT('coupon_category'),'cc','c.category_id = cc.category_id');
		
		// Filter select condition
		if($aConds)
		{
			$oSelect->where($aConds);
		}
		
		// Setup select ordering		
		if($sOrder)
		{
			$oSelect->order($sOrder);
		}
		
		// Setup limit items getting
		$oSelect->limit($iPage, $iLimit, $iCount);

		//print_r($oSelect->execute());die;
		$aCoupons = $oSelect->execute('getRows');
		
	 	return $aCoupons;
	}
	
	/**
	 * Get total item count from query
	 * @author TienNPL
	 * @param array $aConds is input filter conditions
	 * @return number of item gotten
	 */
	public function getItemCountForManage($aConds)
	{		
		// Generate query object	
		$oQuery = $this -> database()
						-> select('count(*)')
						-> from($this->_sTable,'c')
						-> join(Phpfox::getT('user'),'u','u.user_id = c.user_id')
						-> leftjoin(Phpfox::getT('coupon_category'),'cc','c.category_id = cc.category_id')
						-> leftjoin(Phpfox::getT('country'),'co', 'c.location_venue = co.country_iso');
						
		// Filfer conditions
		if($aConds)
		{
			$oQuery-> where($aConds);
		}
								
		return $oQuery->execute('getSlaveField');
	}
	
	/**
	 * Refresh status when admin edit running/upcoming/endingsoon/closed coupon
	 * 
	 * 'running' 	 => 1, ==> running, upcomming, endingsoon
	 * 'upcoming' 	 => 2, ==> running, upcomming, endingsoon
	 * 'pending' 	 => 3, ==> pending
	 * 'pause' 	 => 4, ==> pause
	 * 'endingsoon' => 5, ==> running, upcomming, endingsoon
	 * 'closed' 	 => 6, ==> running, upcomming, endingsoon
	 * 'draft'	 	 => 7, ==> draft
	 * 'denied'     => 8, ==> denied
	 */
	public function getRefreshStatus($iCouponId, $bRefreshPaused = FALSE)
	{
		if(!$iCouponId)
		{
			return FALSE;
		}
		// Get related coupon
		$aCoupon = $this->database()->select('*')->from($this->_sTable,'c')->where("coupon_id = {$iCouponId}")->execute('getRow');
		
		if(!$aCoupon 
			|| $aCoupon['is_removed'] 
			|| $aCoupon['status'] == $this->getStatusCode('pending')
			|| $aCoupon['status'] == $this->getStatusCode('draft')
			|| $aCoupon['status'] == $this->getStatusCode('denied')
			)
		{
			return FALSE;
		}
		
		if($aCoupon['status'] == $this->getStatusCode('pause') && !$bRefreshPaused)
		{
			return FALSE;
		}
		
		$iCurrentTime = PHPFOX_TIME;
		$iStatus = $aCoupon['status'];
		
		// Get Ending Soon Setting and calculate ending time period
		$iEndingSoon = Phpfox::getParam("coupon.ending_soon_settings");
		
		if(!$iEndingSoon)
		{
			$iEndingSoon = 0;
		}
		
		$iEndingSoonTime = $iEndingSoon*24*60*60;
		
		// Checking status
		if($iCurrentTime < $aCoupon['start_time'])
		{
			//$iStatus = 2;
			$iStatus = $this->getStatusCode('upcoming');
		}
		elseif($iCurrentTime < $aCoupon['end_time'])
		{
			$iRunningTime = $aCoupon['end_time']-$iEndingSoonTime;
			if($iRunningTime > $iCurrentTime)
			{
				//$iStatus = 1;
				$iStatus = $this->getStatusCode('running');
			}	
			else 
			{
				//$iStatus = 5;
				$iStatus = $this->getStatusCode('endingsoon');
			}
		}
        elseif($iCurrentTime > $aCoupon['end_time'])
        {
            //$iStatus = 6;
			$iStatus = $this->getStatusCode('closed');
        }

		return $iStatus;		
	}
	
	public function checkCurrentStatus($iCouponId, $bRefreshPaused = FALSE)
	{
		if(!$iCouponId)
		{
			return FALSE;
		}
		// Get related coupon
		$aCoupon = $this->database()->select('*')->from($this->_sTable,'c')->where("coupon_id = {$iCouponId}")->execute('getRow');
		
		if(!$aCoupon || $aCoupon['is_removed'] ||$aCoupon['is_draft'] || $aCoupon['is_closed'])
		{
			return FALSE;
		}
		
		if($aCoupon['status'] == 4 && !$bRefreshPaused)
		{
			return FALSE;
		}
		
		$iCurrentTime = PHPFOX_TIME;
		$iStatus = $aCoupon['status'];
		
		// Get Ending Soon Setting and calculate ending time period
		$iEndingSoon = Phpfox::getParam("coupon.ending_soon_settings");
		
		if(!$iEndingSoon)
		{
			$iEndingSoon = 0;
		}
		
		$iEndingSoonTime = $iEndingSoon*24*60*60;
		
		// Checking status
		if($iCurrentTime < $aCoupon['start_time'])
		{
			$iStatus = 2;
		}
		elseif($iCurrentTime < $aCoupon['end_time'])
		{
			$iRunningTime = $aCoupon['end_time']-$iEndingSoonTime;
			if($iRunningTime > $iCurrentTime)
			{
				$iStatus = 1;
			}	
			else 
			{
				$iStatus = 5;
			}
		}
        elseif($iCurrentTime > $aCoupon['end_time'])
        {
            $iStatus = 6;
        }

		return $iStatus;
	}
 	/**
     * @TODO : complete later
     * process more information for coupon
     * @author datlv
     * @param $aCampaign
     * @param bool $bRetrievePermission
     * @return mixed
     */
    public function retrieveMoreInfoFromCoupon($aCoupon, $bRetrievePermission = false)
    {
        $aCoupon['expire'] = $aCoupon['expire_time'] ? Phpfox::getTime(Phpfox::getParam('coupon.coupon_view_time_stamp'), $aCoupon['expire_time'],false) : 'Never';

        if (!empty($aCoupon['category'])) {
            $aCoupon['category'] = (Core\Lib::phrase()->isPhrase($aCoupon['category']) ? _p($aCoupon['category']) : Phpfox_Locale::instance()->convert($aCoupon['category']));
        } else {
            $aCoupon['category'] = _p('Uncategory');
		}
        if(isset($aCoupon['discount_value']) && isset($aCoupon['discount_type']))
        {
        	if($aCoupon['discount_type'] == 'percentage')
        	{
				$aCoupon['discount_symbol'] = '%';
                $aCoupon['discount'] = $aCoupon['discount_value'] . $aCoupon['discount_symbol'];
        	}
        	else
        	if($aCoupon['discount_type'] == 'price')
        	{
				$aCoupon['discount_symbol'] = Phpfox::getService('coupon.helper')->getCurrencySymbol($aCoupon['discount_currency']);
                $aCoupon['discount'] = $aCoupon['discount_symbol'] . $aCoupon['discount_value'];
        	}

        }
        
        if(isset($aCoupon['special_price_value'])){

        	$aCoupon['special_price_symbol'] = Phpfox::getService('coupon.helper')->getCurrencySymbol($aCoupon['special_price_currency']);
        	$aCoupon['special_price'] = $aCoupon['special_price_symbol'] . $aCoupon['special_price_value'];
        }

        $aCoupon['total_fee'] = (int)Phpfox::getUserParam('coupon.how_much_user_publish_coupon');
        if($aCoupon['is_featured'])
            $aCoupon['total_fee'] += (int)Phpfox::getUserParam('coupon.how_much_user_feature_coupon');

        $default_unit_currency_fee =  Phpfox::getService('coupon.helper')->getDefaultCurrency();
        $aCoupon['symbol_currency_fee'] = Phpfox::getService('coupon.helper')->getCurrencySymbol($default_unit_currency_fee);


        $aCoupon['have_menu'] = ($aCoupon['user_id'] ==Phpfox::getUserId()) || ($aCoupon['status'] == $this->getStatusCode('pending') && Phpfox::getUserParam('coupon.can_approve_coupon')) || ($aCoupon['status'] == $this->getStatusCode('pending') && $aCoupon['user_id'] == Phpfox::getUserId() && $aCoupon['is_featured'] == 0) || Phpfox::isAdmin();
        $aCoupon['can_edit'] = (Phpfox::getUserParam('coupon.can_edit_own_coupon') && Phpfox::getUserId() == $aCoupon['user_id'] && ($aCoupon['status'] == $this->getStatusCode('draft') || $aCoupon['status'] == $this->getStatusCode('denied')));
        $aCoupon['can_delete'] = (Phpfox::getUserParam('coupon.can_delete_own_coupon') && Phpfox::getUserId() == $aCoupon['user_id'] && ($aCoupon['status'] == $this->getStatusCode('draft') || $aCoupon['status'] == $this->getStatusCode('denied')));
   	
        $aCoupon['can_pause'] =  ( ( Phpfox::getUserParam('coupon.can_pause_own_coupon')  && Phpfox::getUserId() == $aCoupon['user_id'] ) || Phpfox::isAdmin() ) &&
		        				 (in_array($aCoupon['status'], array(	$this->getStatusCode('running'),
														        		$this->getStatusCode('upcoming'),
														        		$this->getStatusCode('endingsoon')
		        																					 ) ) ) ;
		$aCoupon['can_resume'] =( ( Phpfox::getUserParam('coupon.can_resume_own_coupon')  && Phpfox::getUserId() == $aCoupon['user_id'] ) || Phpfox::isAdmin() ) &&
		        				( in_array($aCoupon['status'], array(	$this->getStatusCode('pause')
		        																					 ) ) ) ;        																					  
        $aCoupon['can_close'] = ( ( Phpfox::getUserParam('coupon.can_close_own_coupon')  && Phpfox::getUserId() == $aCoupon['user_id'] ) || Phpfox::isAdmin() ) &&
        						( in_array($aCoupon['status'], array(
						        									$this->getStatusCode('running'),
						        									$this->getStatusCode('upcoming'),
						        									$this->getStatusCode('pause'),
						        									$this->getStatusCode('endingsoon'),
						        									$this->getStatusCode('pending')
																	))) ;		

        if (Phpfox::getLib('parse.format')->isSerialized($aCoupon['print_option']))
        {
            $aCoupon['print_option'] = unserialize($aCoupon['print_option']);
        }
        
        return $aCoupon;
    }

	

    /**
     * @TODO : complete later
     * get coupon to show up by type : liked , rated , view ...
     * @by : datlv
     * @param string $sType
     * @param null $iLimit
     * @return array
     */
    public function getCoupon($sType = 'most-liked', $iLimit = null)
    {
        if($iLimit == null)
            $iLimit = 9;

        $aCoupons = array();

        $sConditions = 'c.is_removed = 0 AND c.privacy = 0 AND ';
		
		if($sType != 'featured')
		{
			$sConditions .= "c.module_id = 'coupon' AND ";
		}
		
        $iPage = 1;
        $iPageSize = $iLimit;

        $sRun = "(" . $this->getStatusCode('running') . "," . $this->getStatusCode('upcoming') . "," . $this->getStatusCode('endingsoon') . ")";

        switch($sType)
        {
            case 'most-claimed':
                $sConditions .= 'c.status IN ' . $sRun;
                $sOrder = 'c.total_claim DESC';
                break;
            case 'most-popular':
                $sConditions .= 'c.status IN ' . $sRun;
                $sOrder = 'c.total_view DESC';
                break;
            case 'latest':
                $sConditions .= 'c.status IN ' . $sRun;
                $sOrder = 'c.time_stamp DESC';
                break;
            case 'featured':
                $sConditions .= 'c.status IN ' . $sRun . ' AND c.is_featured = 1';
                $sOrder = 'c.start_time DESC';
                break;
            case 'most-liked':
                $sKey = 'most-liked' . $iLimit ;
                $sType = 'c.most-liked';
                $sConditions .= 'c.status IN ' . $sRun;
                $sOrder = 'c.total_like DESC';
                break;
            case 'most-comment':
                $sConditions .= 'c.status IN ' . $sRun;
                $sOrder = 'c.total_comment DESC';
                break;
            default:
                $sConditions = '';
                $sOrder = 'c.coupon_id DESC';
                break;
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'c')
            ->where($sConditions)
            ->execute('getSlaveField');

        if ($iCnt) {
            $aCoupons = $this->database()->select(Phpfox::getUserField() . ', c.*')
                ->from($this->_sTable, 'c')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
                ->where($sConditions)
                ->order($sOrder)
                ->limit($iPage, $iPageSize, $iCnt)
                ->execute('getSlaveRows');

            foreach($aCoupons as &$aCoupon)
            {
                $aCoupon = $this->retrieveMoreInfoFromCoupon($aCoupon);
            }
        }

        if(!$aCoupons)
        {
            return false;
        }

        return $aCoupons;
    }
	
	/**
	 * Get coupon information through the ID
	 * @author DatLV
	 * @param <int> $iCouponId is the ID of the related coupon
	 * @return <mix> array list of coupon information
	 */
    public function getCouponById($iCouponId) {
        if(!$iCouponId)
        {
            return false;
        }

        if (Phpfox::isModule('like'))
        {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'coupon\' AND lik.item_id = c.coupon_id AND lik.user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('friend'))
        {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = c.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('c.*, ct.description_parsed as description, ct.term_condition_parsed as tern_condition, cca.title as category,cr.rate_id as has_rated,cf.follow_id as has_followed,cc.claim_id as has_claimed,cc.code,' . Phpfox::getUserField())
            ->from($this->_sTable, 'c')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->leftJoin(Phpfox::getT('coupon_category'), 'cca', 'cca.category_id = c.category_id AND cca.is_active = 1')
            ->leftJoin(Phpfox::getT('coupon_text'), 'ct', 'ct.coupon_id = c.coupon_id')
            ->leftJoin(Phpfox::getT('coupon_rating'), 'cr', 'cr.item_id = c.coupon_id AND cr.user_id = ' . (Phpfox::isUser() ? Phpfox::getUserId() : 0))
            ->leftJoin(Phpfox::getT('coupon_claim'), 'cc', 'cc.coupon_id = c.coupon_id AND cc.user_id = ' . (Phpfox::isUser() ? Phpfox::getUserId() : 0))
            ->leftJoin(Phpfox::getT('coupon_follow'), 'cf', 'cf.coupon_id = c.coupon_id AND cf.user_id = ' . (Phpfox::isUser() ? Phpfox::getUserId() : 0))
            ->where('c.coupon_id = ' . $iCouponId)
            ->execute('getSlaveRow');

        if ($aRow) {
            $aRow = $this->retrieveMoreInfoFromCoupon($aRow, true);
        }

        return $aRow;
    }

	/**
	 * Quick get coupon information through the ID
	 * @author TienNPL
	 * @param <int> $iCouponId is the ID of the related coupon
	 * @return <mix> array list of coupon information
	 */
	public function quickGetCouponById($iCouponId = 0)
	{
		if(!$iCouponId)
        {
            return FALSE;
        }
		
		 $aRow = $this->database()->select('*')
		 			->from($this->_sTable)
					->where("coupon_id = {$iCouponId}")
					->execute('getSlaveRow');
		
		return $aRow;
	}
	/**
	 * Coupon favorite checking
	 * @author TienNPL
	 * @param  <int> $iItemId is the Id of the coupon
	 * @return  
	 */
	public function isFavorited($iItemId)
	{
		$iFavoriteId = $this->database()->select('favorite_id')
						->from(phpfox::getT('coupon_favorite'))
						->where("coupon_id = {$iItemId} and user_id =".phpfox::getUserId())
						->execute('getSlaveRow');
		if($iFavoriteId)
		{
			return true;
		}
		return false;
	}
	/**
	 * 
	 */
	public function getFollowerIds($iCouponId)
	{
		$aFollowers = $this->database()->select('user_id')
						->from(Phpfox::getT('coupon_follow'))
						->where("coupon_id = {$iCouponId}")
						->execute('getSlaveRows');
						
		return $aFollowers;
	}

	public function getClaimerIds($iCouponId)
	{
		$aClaimers = $this->database()->select('user_id')
						->from(Phpfox::getT('coupon_claim'))
						->where("coupon_id = {$iCouponId}")
						->execute('getSlaveRows');
						
		return $aClaimers;
	}
	/**
	 * Randomize a coupon code when a user get code
	 * @author TienNPL
	 * @return <string> a string of code with 8 characters length (contain letter and number only)
	 */
	public function generateCode()
	{
	  // Init
	  $sCharset = "bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZaeiouAEIOU1234567890";
	  $sPassword = "";
	  
	  // Process
	  for ($i = 0; $i < 8; $i++)
	  {
	  	$sPassword .= $sCharset[rand(0, 61)];
	  }
	  // End
	  return $sPassword;
	}
	
	public function checkCode($sCode = "")
	{
		$iUserId = Phpfox::getUserId();
		$aClaim = $this -> database() 
						-> select("*")
						-> from(Phpfox::getT('coupon_claim'))
						-> where("user_id = {$iUserId} AND code = '{$sCode}'")
						-> execute('getSlaveRow');
		
		if($aClaim)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	function convertTimeToCountdownString($iEndTimestamp)
	{
		$sStr = '';
		$iRemainSeconds = $iEndTimestamp - PHPFOX_TIME; 

		$iHourSeconds = 60 * 60;
		$iDaySeconds = $iHourSeconds * 24;
		$iWeekSeconds = $iDaySeconds * 7;
		$iMonthSeconds = $iDaySeconds * 30;

		if($iRemainSeconds > $iMonthSeconds)
		{
			$iRMonth = (int) ($iRemainSeconds / $iMonthSeconds);
			$sStr .= $iRMonth . _p('m') . ' ';
			$iRemainSeconds = $iRemainSeconds - $iRMonth * $iMonthSeconds;
		}

		if($iRemainSeconds > $iWeekSeconds)
		{
			$iRWeek = (int) ($iRemainSeconds / $iWeekSeconds);
			$sStr .= $iRWeek . _p('w') . ' ';
			$iRemainSeconds =  $iRemainSeconds  - $iRWeek * $iWeekSeconds;
		}

		if($iRemainSeconds > $iDaySeconds)
		{
			$iRDay = (int) ($iRemainSeconds / $iDaySeconds);
			$sStr .= $iRDay . _p('d') . ' ';
			$iRemainSeconds =  $iRemainSeconds  - $iRDay * $iDaySeconds;
		}

		if($iRemainSeconds > $iHourSeconds)
		{
			$iRHour = (int) ($iRemainSeconds / $iHourSeconds);
			$sStr .= $iRHour . _p('h') . ' ';
			$iRemainSeconds =  $iRemainSeconds  - $iRHour * $iHourSeconds;
		}

		$sStr .=  _p('left');
		
		return $sStr; 
	}
    /**
     * get coupon for edit
     * @by : datlv
     * @param $iCouponId
     * @return mixed
     */
    public function getCouponForEdit($iCouponId) {
        $aCoupon = $this->database()->select('c.* , ct.description, ct.term_condition')
            ->from($this->_sTable, 'c')
            ->join(Phpfox::getT('coupon_text'), 'ct', 'ct.coupon_id = c.coupon_id')
            ->where('c.coupon_id = ' . $iCouponId )
            ->execute('getRow');
        $aCoupon['categories'] = Phpfox::getService('coupon.category')->getCategoryIds($iCouponId);
        if (Phpfox::getLib('parse.format')->isSerialized($aCoupon['print_option']))
        {
            $aCoupon['print_option'] = unserialize($aCoupon['print_option']);
        }
        return $aCoupon;
    }
	
    /**
     * @by : datlv
     * @param $iCampaignId
     * @return bool
     */
    public function checkIsCouponInPage($iCouponId)
    {
        $aCampaign = $this->database()->select('module_id')
            ->from(Phpfox::getT('coupon'))
            ->where('coupon_id = ' . $iCouponId)
            ->execute('getSlaveRow');

        if(isset($aCampaign['module_id']) && $aCampaign['module_id'] == 'pages')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @by : datlv
     * @param $iCampaignId
     * @return array
     */
    public function getCouponAddCallback($iId,$moduleID)
    {
        Phpfox::getService('pages')->setIsInPage();
		if($moduleID == 'groups')
			$moduleID = 'pages';
        return array(
            'module' => $moduleID,
            'item_id' => $iId,
            'table_prefix' => $moduleID.'_'
        );
    }

    public function getOwnerEmail($iUserId)
    {
        return $this->database()->select('email')->from(Phpfox::getT('user'))->where('user_id = ' . $iUserId)->execute('getField');
    }

	public function getCouponOwnerId($iCouponId)
	{
		return $this->database()->select('user_id')->from($this->_sTable)->where("coupon_id = {$iCouponId}")->execute('getSlaveField');
	}
        
    public function getClaimByCouponId($sCondition = '', $iPage = 1, $iLimit)
    {
            $iTotal = $this->database()->select('COUNT(cpc.claim_id)')
                    ->from(Phpfox::getT('coupon_claim'), 'cpc')
                    -> join(Phpfox::getT('user'),'u','u.user_id = cpc.user_id')
                    ->where($sCondition)
                    ->execute('getSlaveField');

            $aTransactions = $this->database()->select('cpc.*, u.user_name, u.full_name')
                    ->from(Phpfox::getT('coupon_claim'), 'cpc')
                    -> join(Phpfox::getT('user'),'u','u.user_id = cpc.user_id')
                    ->where($sCondition)
                    ->order('cpc.time_stamp DESC')
                    ->limit($iPage, $iLimit, $iTotal)
                    ->execute('getSlaveRows');

            if ($aTransactions)
            {
                    return array($iTotal, $aTransactions);
            } else
            {
                    return array($iTotal, null);
            }
    }
    
	public function getLatestClaimers($iCouponId, $iLimit = 8)
	{
		$aClaimers = $this->database()->select('cc.coupon_id,'.Phpfox::getUserField())
						->from(Phpfox::getT('coupon_claim'), 'cc')
						->join(Phpfox::getT('user'),'u',"u.user_id = cc.user_id and cc.coupon_id = {$iCouponId}")
						->limit($iLimit)
						->order("cc.time_stamp DESC")
						->execute('getSlaveRows');
		return $aClaimers;
	}
	public function getNumberCouponByUser($iUserId)
    {
        return $this->database()->select('COUNT(*)')->from($this->_sTable)->where('user_id = ' . $iUserId . " AND is_removed = 0 AND module_id = 'coupon'")->execute('getField');
    }


	public function canClaimACoupon($aCoupon)
	{
		$bCanClaim = TRUE;
		
		if($aCoupon['status'] != 1 && $aCoupon['status'] != 5)
		{
			return FALSE;
		}
		
		if (Phpfox::isModule('privacy') && $aCoupon['user_id'] != Phpfox::getUserId())
		{
			$bCanClaim = Phpfox::getService('privacy')->check('coupon', $aCoupon['coupon_id'], $aCoupon['user_id'], $aCoupon['privacy_claim'], $aCoupon['is_friend'],TRUE);
		}
		return $bCanClaim;
	}
	
	public function getPrintOption($iId)
	{
		$sOption = $this->database()->select('print_option')
			->from($this->_sTable)
			->where('coupon_id = '.(int)$iId)
			->execute('getSlaveField');
		
		if (!empty($sOption))
		{
			return unserialize($sOption);
		}
		
		return null;
	}
	
	/**
	 * check in the list of friends what friend user has invited then make the short list
	 * @author TienNPL
	 * @param int $iCouponId 
	 * @param int $aFriends list of user's friend 
	 * @return short list of uninvited friend, false if there's no one
	 */
	public function isAlreadyInvited($iCouponId, $aFriends) {
		if ((int) $iCouponId === 0) {
			return false;
		}

		if (is_array($aFriends)) {
			if (!count($aFriends)) {
				return false;
			}

			$sIds = '';
			foreach ($aFriends as $aFriend) {
				if (!isset($aFriend['user_id'])) {
					continue;
				}

				$sIds[] = $aFriend['user_id'];
			}

			$aInvites = $this->database()->select('invited_id, user_id, invited_user_id')
					->from(Phpfox::getT('coupon_invite'))
					->where('coupon_id = ' . (int) $iCouponId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
					->execute('getSlaveRows');
					
			$aCache = array();
			foreach ($aInvites as $aInvite) {
				$aCache[$aInvite['invited_user_id']] = ($aInvite['user_id'] > 0 ? _p('signed') : _p('invited'));
			}

			if (count($aCache)) {
				return $aCache;
			}
		}

		return false;
	}

    public function getTotalPending()
    {
        return $this->database()->select('COUNT(*)')->from($this->_sTable)->where('status = ' . $this->getStatusCode('pending'))->execute('getField');
    }

	public function getInfoForAction($aItem)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, c.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
		$aRow['link'] = Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']);
		return $aRow;
	}
    
    public function getSampleCoupon()
    {
        $aCoupon = array(
            'title' => 'Coupon Name',
            'site_url' => 'http://www.yoursite.com',
            'location_venue' => 'United States',
            'city' => '',
            'country_iso' => 'US',
            'expire_time' => '1356998400',
            'category' => 'Software',
            'code' => 'T5F57bPD',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'discount_symbol' => '%',
            'image_url' => Phpfox::getLib('template')->getStyle('image', 'logo.jpg', 'coupon'),
        );
        
        return $aCoupon;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('coupon.Service_AdvancedMarketplace__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

?>
