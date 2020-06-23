<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Helper extends Phpfox_service
{

	/**
	 * to create left sub menu for a controller 
	 * <pre>
	 * Phpfox::getService('fundraising')->buildMenu();
	 * </pre>
	 * @by minhta
	 */
	public function buildMenu() {
	    list($iTotalMyContest, $iTotalMyFollowingContest, $iTotalMyFavoriteContest, $iTotalMyEntries) = Phpfox::getService('contest.contest')->getCountForMenu(Phpfox::getUserId());
		$aFilterMenu = array(
			_p('contest.all_contests')=> '',
            ((int)$iTotalMyContest > 0) ? _p('contest.my_contests').'<span class="count-item">'.$iTotalMyContest.'</span>' : _p('contest.my_contests') => 'my',
		);

		if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
			$aFilterMenu[_p('contest.friends_contests')] = 'friend';
		}

		if (Phpfox::getUserParam('contest.can_approve_contest')) {
			$iPendingTotal = Phpfox::getService('contest.contest')->getTotalPendings();
			
			if ($iPendingTotal) {
				$aFilterMenu[_p('contest.pending_contests') . (Phpfox::getUserParam('contest.can_approve_contest') ? ' (' . $iPendingTotal . ')' : 0)] = 'pending';
			}
		}

		$aFilterMenu[] = true;

		$aFilterMenu[_p('contest.featured_contests')] = 'featured';
		$aFilterMenu[_p('contest.premium_contests')] = 'premium';
		$aFilterMenu[_p('contest.ending_soon_contests')] = 'ending_soon';
		$aFilterMenu[_p('contest.closed_contests')] = 'closed';
		$aFilterMenu[((int)$iTotalMyFollowingContest > 0) ? _p('contest.my_following_contests').'<span class="count-item">'.$iTotalMyFollowingContest.'</span>' : _p('contest.my_following_contests')] = 'my_following';
        $aFilterMenu[((int)$iTotalMyFavoriteContest > 0) ? _p('contest.my_favorite_contests').'<span class="count-item">'.$iTotalMyFavoriteContest.'</span>' : _p('contest.my_favorite_contests')] = 'my_favorite';

		$aFilterMenu[] = true;

		$aFilterMenu[((int)$iTotalMyEntries > 0) ? _p('contest.my_entries').'<span class="count-item">'.$iTotalMyEntries.'</span>' : _p('contest.my_entries')] = 'my_entries';
        if (Phpfox::isUser() && Phpfox::getUserParam('contest.can_approve_contest')) {
            $iPendingEntryTotal = Phpfox::getService('contest.entry')->getTotalPendings();
            if($iPendingEntryTotal > 0){
                $aFilterMenu[_p('contest.pending_entries').' ('.$iPendingEntryTotal.')'] = 'pending_entries';
            }
        }
		Phpfox::getLib('template')->buildSectionMenu('contest', $aFilterMenu);
	}

	public function getPhrasesForValidator()
	{
		return array(
			'contest.this_field_is_required',
			'contest.please_enter_an_amount_greater_or_equal',
			'contest.please_enter_a_value_with_a_valid_extension',
			'contest.please_enter_a_valid_url',
            'contest.the_start_time_of_submitting_must_be_greater_than_or_equal_to_the_start_time_of_contest',
            'contest.the_end_time_of_submitting_must_be_greater_than_the_start_time_of_it',
            'contest.the_end_time_of_submitting_must_be_greater_than_current_time',
            'contest.the_end_time_of_voting_must_be_greater_than_the_start_time_of_it',
            'contest.the_end_time_of_voting_must_be_greater_than_or_equal_to_the_end_time_of_submitting',
            'contest.the_end_time_of_contest_must_be_greater_than_or_equal_to_the_end_time_of_voting'
			);
	}

	/**
	 * description
	 * @param return
	 * @return boolean
	 */
	public function copyImageFromFoxToContest ($sFullFoxImagePath, $sFullNewImagePath)
	{
		if(file_exists($sFullFoxImagePath))
		{
            return copy($sFullFoxImagePath, $sFullNewImagePath);
		}
		else
		{
			return false;
		}

	}

	public function generateErrorHtmlFromArrayOfMessage($aMessages)
	{
		$sHtml = '<div> ';
		foreach ($aMessages as $sMessage) {
			$sHtml .= '<div class="error_message">' . $sMessage . '</div>';
		}

		$sHtml .= '</div>';

		return $sHtml;
	}

	public function getMoneyText($iAmount, $sCurrency = null)
	{	
		if(!$sCurrency)
		{
			$sCurrency = Phpfox::getService('core.currency')->getDefault();
		}
		
		$sSymbol = Phpfox::getService('core.currency')->getSymbol($sCurrency);
		// return  $sCurrency . ' ' . $iAmount . ' ' . $sSymbol;
		return   $iAmount . ' ' . $sCurrency;
	}

	public function setSearchKeyword($sKeyword)
	{
		unset($_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['search']);

		$iId = md5(uniqid());
		$_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['search'][$iId] = $sKeyword;
		return $iId;
	}

	public function getSearchKeyword($iSearchId)
	{
		return isset($_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['search'][$iSearchId]) ? $_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['search'][$iSearchId] : false;
	}
	
	public function getUserImage($iUserId)
	{
        $aRow = $this->database()->select('u.*')
                ->from(Phpfox::getT('user'),'u')
                ->where('user_id = ' . $iUserId)
                ->execute('getSlaveRow');
        
        return $aRow;
	}

	public function getCurrency()
	{
		$sFoxCurrency = Phpfox::getService('contest.helper')->getPhpfoxDefaultCurrency();

		return $sFoxCurrency;
	}

	public function getContestDefaultCurrency() {
		return 'USD';
	}

	public function getPhpfoxDefaultCurrency() {
		
		$aCurrencies = Phpfox::getLib('database')->select('*')
				->from(Phpfox::getT('currency'))
				->where('is_active = 1')
				->order('ordering ASC')
				->execute('getRows');

		$sDefaultCurrency = '';		
		foreach ((array) $aCurrencies as $sKey => $aCurrency)
		{
			if ($aCurrency['is_default'] == '1')
			{
				$sDefaultCurrency = $sKey;
				break;
			}
		}

		if ($sDefaultCurrency == '' && Phpfox::isUser())
		{
			$sCurrency = Phpfox::getService('user')->getCurrency();
			if (!empty($sCurrency))
			{
				$sDefaultCurrency = $sCurrency;	
			}			
		}

		$sDefaultCurrency = ($sDefaultCurrency != '')?$sDefaultCurrency:'USD';
		
		return $sDefaultCurrency;
		//return Phpfox::getService('core.currency')->getDefault();
	}



	public function checkCurrencyInSupportedList($sCurrency, $sGateway = 'paypal')
	{
		$oGateway = Phpfox::getService('younetpaymentgateways')->load($sGateway);
		$aSupportedCurrencies = $oGateway->getSupportedCurrencies();

		if(in_array($sCurrency, $aSupportedCurrencies) )		
		{
			return true;
		}

		return false;
	}
	
    function convertTimeToCountdownString($iEndTimestamp, $bIsIncludeSecondAndMinute = false)
	{
		$sStr = '';
		$iRemainSeconds = $iEndTimestamp - PHPFOX_TIME; 
		
		$iMinuteSeconds = 60;
		$iHourSeconds = 60 * 60;
		$iDaySeconds = $iHourSeconds * 24;
		$iWeekSeconds = $iDaySeconds * 7;
		$iMonthSeconds = $iWeekSeconds * 30;
		
		if($iRemainSeconds > $iMonthSeconds)
		{
			$iRMonth = (int) ($iRemainSeconds / $iMonthSeconds);
			$sStr .= $iRMonth . _p('contest.m') . ' ';
			$iRemainSeconds = $iRemainSeconds - $iRMonth * $iMonthSeconds;
		}

		if($iRemainSeconds > $iWeekSeconds)
		{
			$iRWeek = (int) ($iRemainSeconds / $iWeekSeconds);
			$sStr .= $iRWeek . _p('contest.w') . ' ';
			$iRemainSeconds =  $iRemainSeconds  - $iRWeek * $iWeekSeconds;
		}

		if($iRemainSeconds > $iDaySeconds)
		{
			$iRDay = (int) ($iRemainSeconds / $iDaySeconds);
			$sStr .= $iRDay . _p('contest.d') . ' ';
			$iRemainSeconds =  $iRemainSeconds  - $iRDay * $iDaySeconds;
		}

		if($iRemainSeconds > $iHourSeconds)
		{
			$iRHour = (int) ($iRemainSeconds / $iHourSeconds);
			$sStr .= $iRHour . _p('contest.h') . ' ';
			$iRemainSeconds =  $iRemainSeconds  - $iRHour * $iHourSeconds;
		}

		if($bIsIncludeSecondAndMinute)
		{
			if($iRemainSeconds > $iMinuteSeconds)
			{
				$iRMinute = (int) ($iRemainSeconds / $iMinuteSeconds);
				$sStr .= $iRMinute . _p('contest.m') . ' ';
				$iRemainSeconds =  $iRemainSeconds  - $iRMinute * $iMinuteSeconds;
			}

			$sStr .= $iRemainSeconds . _p('contest.s') . ' ';
		}


		//$sStr .=  _p('contest.left');

		
		return $sStr; 
	}

	public function setSessionBeforeAddItemFromSubmitForm($iContestId, $iType)
	{
		$iCurrentUserId = Phpfox::getUserId();
		$_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['submit_entry'][$iCurrentUserId]['contest_id'] = $iContestId;
		$_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['submit_entry'][$iCurrentUserId]['type_id'] = $iType;
	}

	public function getSessionAfterUserAddNewItem($iType)
	{
		$iCurrentUserId = Phpfox::getUserId();

		if(isset($_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['submit_entry'][$iCurrentUserId]))
		{
			if($_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['submit_entry'][$iCurrentUserId]['type_id'] == $iType)
			{
				return $_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['submit_entry'][$iCurrentUserId]['contest_id'];
			}
		}

		return false;
	}

	public function removeSessionAddNewItemOfUser()
	{
		$iCurrentUserId = Phpfox::getUserId();
		unset($_SESSION[Phpfox::getParam('core.session_prefix')]['yncontest']['submit_entry'][$iCurrentUserId]);

		return true;
	}

	public function getUserGroupSettingBySettingNameAndUserId ($sSettingName, $iUserId)
	{
		
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

	public function getMaxImageFileSize()
	{
		return (Phpfox::getUserParam('contest.max_upload_size_contest') === 0 ? null : Phpfox::getLib('phpfox.file')->filesize((Phpfox::getUserParam('contest.max_upload_size_contest') / 1024) * 1048576));
	}

	public function checkFeedExist($iItemId, $sTypeId)
	{
		$aRow = $this->database()->select('feed_id')
                ->from(Phpfox::getT('feed'))
                ->where(' item_id = ' . $iItemId . 
                		' AND type_id = \'' . $sTypeId . '\'')
                ->execute('getRow');
        if(isset($aRow['feed_id']))
        {
        	return $aRow['feed_id'];
        }
        else
        {
        	return false;
        }
	}
    
    public function timestampToCountdownString($iTimeStamp)
    {
        $result = '';
        
        $iLeft = $iTimeStamp - PHPFOX_TIME;
        
        if ($iLeft >= 60)
        {
            $sLeft = $this->secondsToString($iLeft);
            $result = $sLeft.' '._p('contest.left');
        }
        elseif ($iLeft > 0)
        {
            $result = '1'._p('contest.m').' '._p('contest.left');
        }
        
        return $result;
    }

    /**
     * Convert seconds to string
     * @param int $timeInSeconds
     * @return string
     */
    public function secondsToString($timeInSeconds)
    {
        static $phrases = null;

        $seeks = array(
            31536000,
            2592000,
            86400,
            3600,
            60
        );

        if (null == $phrases)
        {
            $phrases = array(
                array(
                    ' '._p('contest.year'),
                    ' '._p('contest.month'),
                    ' '._p('contest.day'),
                    _p('contest.h'),
                    _p('contest.m')
                ),
                array(
                    ' '._p('contest.years'),
                    ' '._p('contest.months'),
                    ' '._p('contest.days'),
                    _p('contest.h'),
                    _p('contest.m')
                )
            );
        }

        $result = array();

        $remain = $timeInSeconds;

        foreach ($seeks as $index => $seek)
        {
            $check = intval($remain / $seek);
            $remain = $remain % $seek;

            if ($check > 0)
            {
                $result[] = $check . $phrases[($check > 1) ? 1 : 0][$index];
            }

            if ($timeInSeconds < 86400)
            {
                if (count($result) > 1)
                {
                    break;
                }
            }
            else
            {
                if (count($result) > 0)
                {
                    break;
                }
            }
        }

        return implode(' ', $result);
    }
    
    public function getTimeLineStatus($iStart, $iEnd)
    {
        if ($iStart > PHPFOX_TIME)
        {
            return 'opening';
        }
        elseif ($iEnd < PHPFOX_TIME)
        {
            return 'end';
        }
        else
        {
            return 'on_going';
        }
    }

    public function processImage($sImgUrl, $iUserId, $dir)
    {
    	
        $oFile = Phpfox::getLib('file');
        $oImage = Phpfox::getLib('image');
        
        $sFileName = md5($iUserId . PHPFOX_TIME . uniqid());
        $sFileDir = $oFile->getBuiltDir($dir);
        $ext = $this->getExt($sImgUrl);
        $sFilePath = $sFileDir . $sFileName . '%s.' . $ext;
        
        $result = $this->fetchImage($sImgUrl, sprintf($sFilePath, ''));
        if($result === false){
            return false;
        }

        $iFileSize = filesize(sprintf($sFilePath, ''));

        if($iFileSize)
        {
         	return $sFilePath; 
        }

        return false;
    }    

    public function fetchImage($photo_url, $tmpfile)
    {
    	$timeout = 60;
        // convert to byte(s)
        $iPostMaxSize = ((int) ini_get('post_max_size') * 1048576);

          $fp = fopen ($tmpfile, 'w+');
          # start curl
          $ch = curl_init();
          curl_setopt( $ch, CURLOPT_URL, $photo_url );
          # set return transfer to false
          curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
          curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
          curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
          curl_setopt($ch, CURLOPT_REFERER, (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . Phpfox::getParam('core.host') .'/');
          # increase timeout to download big file
          curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
          # write data to local file
          curl_setopt( $ch, CURLOPT_FILE, $fp );
          # execute curl
          curl_exec( $ch );
          # close curl
          curl_close( $ch );
          # close local file
          fclose( $fp );

          if (filesize($tmpfile) > 0) {
            return true;        
          } else {
            return false;
          }

        return false;
    }     

    public function getExt($file){
        $path_parts = pathinfo($file);
        
        if(isset($path_parts['extension'])){
            // to prevent some path: .jpg?c=6d03, .png?param1=abc, ...
            // we will check and return exactly extension
            $extension = strtolower($path_parts['extension']);

            if($extension == '')
            	return 'jpg';
            else
				return strtolower($extension);
            
        
		}
    }

    public function getStaticPath(){
    	return Phpfox::getParam('core.path_file');
    }

    public function buildPaging($iPage,$iSize,$iCnt,$bQueryFromFox = false,$aParamsQuery = array()){

        $iPage = ($iPage == 0 || $iPage == 1)?1:($iPage);

    	if($bQueryFromFox){
    		   $iCnt =  Phpfox::getLib('database')->select((isset($aParamsQuery['distinct']) ? 'COUNT(DISTINCT ' . $aParamsQuery['field'] . ')' : 'COUNT(*)'))
                        ->from($aParamsQuery['table'], $aParamsQuery['alias'])
                        ->where(Phpfox::getLib('search')->getConditions())
                        ->execute('getSlaveField');
    	}

		$iPageLast = round($iCnt / $iSize + 0.4);

        $iPagePrev = ($iPage == 0 || $iPage == 1)?1:($iPage - 1);
        $iPageNext = ($iPage == $iPageLast)?$iPageLast:($iPage + 1);


        $bDisablePrev = ($iPage == 0 || $iPage == 1)?true:false;
        $bDisableNext = ($iPage == $iPageLast)?true:false;

        $bHideAll = ($iPageLast == 1 || $iPageLast == 0);

        return array($iPagePrev,$iPageNext,$bDisablePrev,$bDisableNext,$bHideAll);

    }

}