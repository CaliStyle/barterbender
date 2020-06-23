<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Contest_Contest extends Phpfox_service {
	private $_aContests = array();
	private $_aCheckingPermissionContests = array();

	public function __construct()
	{
		$this->_sTable = Phpfox::getT('contest');
	}

    /**
     * Get statistics for menu
     * @param $iUserId
     * @return array
     */
	public function getCountForMenu($iUserId)
    {
        $iTotalMyContest = db()->select('COUNT(*)')
                                ->from(Phpfox::getT('contest'),'ct')
                                ->innerJoin(Phpfox::getT('user'),'userDelete','userDelete.user_id = ct.user_id')
                                ->join(Phpfox::getT('user'),'u','u.user_id = ct.user_id')
                                ->where('ct.user_id = '.(int)$iUserId.' AND ct.is_deleted = 0')
                                ->execute('getSlaveField');

        $iTotalMyFollowingContest = db()->select('COUNT(*)')
                                        ->from(Phpfox::getT('contest'),'ct')
                                        ->innerJoin(Phpfox::getT('user'),'userDelete','userDelete.user_id = ct.user_id')
                                        ->leftJoin(Phpfox::getT('contest_participant'),'pa','pa.contest_id=ct.contest_id and pa.user_id = '.(int)$iUserId)
                                        ->where('ct.privacy IN(0,1,2,3,4) AND (ct.contest_status = 5 or ct.contest_status = 4) AND pa.is_followed=1  AND ct.is_deleted = 0  AND ct.is_active = 1')
                                        ->execute('getSlaveField');
        $iTotalMyFavoriteContest = db()->select('COUNT(*)')
                                    ->from(Phpfox::getT('contest'),'ct')
                                    ->innerJoin(Phpfox::getT('user'),'userDelete','userDelete.user_id = ct.user_id')
                                    ->leftJoin(Phpfox::getT('contest_participant'),'pa','pa.contest_id=ct.contest_id and pa.user_id = '.(int)$iUserId)
                                    ->where('ct.privacy IN(0,1,2,3,4) AND (ct.contest_status = 5 or ct.contest_status = 4) AND pa.is_favorite=1  AND ct.is_deleted = 0  AND ct.is_active = 1')
                                    ->execute('getSlaveField');
        $iTotalMyEntries = db()->select('COUNT(*)')
                                        ->from(Phpfox::getT('contest_entry'),'en')
                                        ->join(Phpfox::getT('contest'),'ct','ct.contest_id = en.contest_id and ct.is_deleted = 0')
                                        ->where('en.type = 1 AND en.user_id = '.(int)$iUserId)
                                        ->execute('getSlaveField');
        return [$iTotalMyContest, $iTotalMyFollowingContest, $iTotalMyFavoriteContest, $iTotalMyEntries];
    }

	/**
	 * get contests based on the offset
	 * by default they are sorted by time 
	 * @param $iOffset, $iLimit
	 * @return array of Contest
	 */
	public function getContests ($iOffset = 0, $iLimit = 10)
	{
		$aContests = $this->_aContests;
		return $aContests;		
	}
	
	public function getInfoForAction($aItem)
	{
	
		if($aItem['item_type_id']=='contest-entry')
		{
			return $this->getInfoForActionEntry($aItem);
		}
		
		if (is_numeric($aItem))
		{
			$aItem = array('item_id' => $aItem);
		}
		else {
			$aRow = $this->database()->select('ct.contest_id, ct.contest_name, ct.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('contest'), 'ct')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ct.user_id')
			->where('ct.contest_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');	
		}
		
		if (empty($aRow))
		{
			d($aRow);
			d($aItem);
		}
		
		$aRow['link'] = Phpfox::getLib('url')->permalink('contest', $aRow['contest_id'], $aRow['contest_name']);
		
		return $aRow;
	}
	
	public function getInfoForActionEntry($aItem){
	
		if (is_numeric($aItem))
		{
			$aItem = array('item_id' => $aItem);
		}
		else {
			$aRow = $this->database()->select('en.entry_id, en.title, en.user_id, u.gender, u.full_name, ct.contest_id, ct.contest_name')	
			->from(Phpfox::getT('contest_entry'), 'en')
            ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id = en.contest_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = en.user_id')
			->where('en.entry_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');	
		}
		
		if (empty($aRow))
		{
			d($aRow);
			d($aItem);
		}
		
		$aRow['link'] = Phpfox::permalink('contest', $aRow['contest_id'], $aRow['contest_name']).'entry_'.$aRow['entry_id'].'/';
		
		return $aRow;
	}
	
	
	public function implementsContestFields($aRows)
    {
        $format_datetime_month = 'M';
        $format_datetime = 'j, Y g:i a';
        
        $type_contest = array(
            '1' => array(
                'name' => 'blog',
                'phrase' => _p('contest.blog_contest')
            ),
            '2' => array(
                'name' => 'photo',
                'phrase' => _p('contest.photo_contest')
            ),
            '3' => array(
                'name' => 'video',
                'phrase' => _p('contest.video_contest')
            ),
            '4' => array(
                'name' => 'music',
                'phrase' => _p('contest.music_contest')
            ),
        );
        
        $check_array = 0;
        
        if(!isset($aRows[0]) && isset($aRows['contest_id']))
        {
            $aRowtmp = $aRows; 
            $aRows = array();
            $aRows[] = $aRowtmp;
            $check_array = 1;
        }
        
        foreach($aRows as $key=>$aRow)
        {
            $aRow['contest_timeline'] = Phpfox::getService('contest.helper')->getTimeLineStatus($aRow['begin_time'], $aRow['end_time']);
            $aRow['submit_timeline'] = Phpfox::getService('contest.helper')->getTimeLineStatus($aRow['start_time'], $aRow['stop_time']);
            $aRow['vote_timeline'] = Phpfox::getService('contest.helper')->getTimeLineStatus($aRow['start_vote'], $aRow['stop_vote']);
    
            $aRow['contest_countdown'] = Phpfox::getService('contest.helper')->timestampToCountdownString($aRow['end_time']);
            $aRow['submit_countdown'] = Phpfox::getService('contest.helper')->timestampToCountdownString($aRow['stop_time']);
            $aRow['vote_countdown'] = Phpfox::getService('contest.helper')->timestampToCountdownString($aRow['stop_vote']);
            
            $aRow['end_time_parsed'] = Phpfox::getTime($format_datetime_month, $aRow['end_time']) . ' '.Phpfox::getTime($format_datetime, $aRow['end_time']);
            
            $aRow['total_entry'] = Phpfox::getService('contest.entry')->getTotalEntriesByContestId($aRow['contest_id']);
			  $aType = array(
                    '1' => 'blog',
                    '2' => 'photo',
                    '3' => 'video',
                    '4' => 'music',
                );
            if($aRow['type']>0)
            {
                $aRow['type_name'] = $type_contest[$aRow['type']]['name'];
                $aRow['type_contest'] = $type_contest[$aRow['type']]['phrase'];
                $aRow['link_type_contest'] = Phpfox::getLib("url")->makeUrl('contest')."view_/type_".$aType[$aRow['type']]."/";
            }	
            else
            {
                $aRow['type_name'] = $type_contest[1]['name'];
                $aRow['type_contest'] = $type_contest[1]['phrase'];
                $aRow['link_type_contest'] = Phpfox::getLib("url")->makeUrl('contest')."view_/type_all/";
            }
            
            $aRow['expired_time'] = Phpfox::getService('contest.helper')->convertTimeToCountdownString($aRow['end_time']);
            if($aRow['expired_time']=="")
            {
                unset($aRows[$key]);
            }
            
            if(isset($aRow['type_entry']))
            {
                $aRow['image_path'] = $aRow['image_path_parse'];
                $aRow['total_like'] = $aRow['total_like_entry'];
                $aRow['total_view'] = $aRow['total_view_entry'];
            }
            
            $aRow = $this->retrieveContestTextsBasedOnHtmlSetting($aRow);
            
            $aRows[$key] = $aRow;
        }
        
        if($check_array)
        {
            return $aRows[0];
        }
        
        return $aRows;
    }

	public function getContestNameById ($iContestId)
	{

		$aContest = $this->database()->select('c.contest_name')
		->from(Phpfox::getT('contest'), 'c')
		->where('c.contest_id = ' . $iContestId)
		->execute('getSlaveField');
		
		if(!$aContest)
		{
			return false;
		}

		return $aContest;
	}

	public function getContestById ($iContestId, $bIsCache = true)
	{
		if(isset($this->_aContests[$iContestId]) && $bIsCache)
		{
			return $this->_aContests[$iContestId];
		}

		if (Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'contest\' AND l.item_id = c.contest_id AND l.user_id = ' . Phpfox::getUserId());
		}
		
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = c.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}

        if (Phpfox::isModule('track')) {
            $this->database()->select("contest_track.item_id AS is_viewed, ")
                ->leftJoin(Phpfox::getT('track'), 'contest_track', 'contest_track.item_id = c.contest_id AND contest_track.user_id = ' . Phpfox::getUserBy('user_id') . ' AND contest_track.type_id=\'ync_contest\'');
        }
		
		$aContest = $this->database()->select('c.*, cec.subject, cec.message, cec.term_condition, ' . Phpfox::getUserField())
		->from($this->_sTable, 'c')
		->leftJoin(Phpfox::getT('contest_email_condition'), 'cec', 'cec.contest_id = c.contest_id')
		->join(Phpfox::getT('user'),'u','u.user_id = c.user_id')
		->where('c.contest_id = ' . $iContestId)
		->execute('getSlaveRow');
		
		if(!$aContest)
		{
			return false;
		}

		$aContest = $this->retrieveContestTextsBasedOnHtmlSetting($aContest);

		// the key start_time, end_time should be changed
		// minhta merge code here
		$format_datetime_month = 'M';
		$format_datetime = 'j, Y g:i a';
        
        $aTimeLine = Phpfox::getService('contest.constant')->getTimeLine();
        foreach ($aTimeLine as $sTimeLine)
        {
            $aContest[$sTimeLine.'_parsed'] = Phpfox::getTime($format_datetime_month, $aContest[$sTimeLine]) . ' '.Phpfox::getTime($format_datetime, $aContest[$sTimeLine]);
        }

        $aContest['contest_timeline'] = Phpfox::getService('contest.helper')->getTimeLineStatus($aContest['begin_time'], $aContest['end_time']);
        $aContest['submit_timeline'] = Phpfox::getService('contest.helper')->getTimeLineStatus($aContest['start_time'], $aContest['stop_time']);
        $aContest['vote_timeline'] = Phpfox::getService('contest.helper')->getTimeLineStatus($aContest['start_vote'], $aContest['stop_vote']);

        $aContest['contest_countdown'] = Phpfox::getService('contest.helper')->timestampToCountdownString($aContest['end_time']);
        $aContest['submit_countdown'] = Phpfox::getService('contest.helper')->timestampToCountdownString($aContest['stop_time']);
        $aContest['vote_countdown'] = Phpfox::getService('contest.helper')->timestampToCountdownString($aContest['stop_vote']);
        
		$aContest['total_entry'] = Phpfox::getService('contest.entry')->getTotalEntriesByContestId($iContestId);

		$aContest['categories'] = Phpfox::getService('contest.category')->getCategoryIds($iContestId);
		$aListCategory = explode(',', $aContest['categories']);
		$aCategory = array();
		foreach($aListCategory as $iKey => $Category)
		{
			$tmpCategory = Phpfox::getService('contest.category')->getForEdit($Category);
//			die(d($tmpCategory));
			$aCategory[] = "<a href='".Phpfox::getLib('url')->permalink(array('contest.category', 'view' => ''), $tmpCategory['category_id'], $tmpCategory['name'])."'>".Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($tmpCategory['name']))."</a>";
		}
		$aContest['sCategory'] = implode(" > ", $aCategory);

		$aContest = $this->retrieveContestPermissions($aContest);

		$aContest['is_joined'] = Phpfox::getService('contest.participant')->isJoinedContest(Phpfox::getUserId(), $aContest['contest_id']);

		$aContest['is_followed'] = Phpfox::getService('contest.participant')->isFollowedContest(Phpfox::getUserId(), $aContest['contest_id']);

		$aContest['is_favorited'] = Phpfox::getService('contest.participant')->isFavoritedContest(Phpfox::getUserId(), $aContest['contest_id']);
        
        $aContest['is_manual_closed'] = $this->isManualClosed($aContest);
        $aContest['user_close'] = $this->getUserClose($iContestId);

		$this->_aContests[$iContestId] = $aContest;
//die(d($aCategory));
		return $aContest;
	}

	public function retrieveContestTextsBasedOnHtmlSetting($aContest)
	{
		if(Phpfox::getParam('core.allow_html'))
		{
			$aContest['description_show'] = $aContest['description_parsed'];
			$aContest['short_escription_show'] = $aContest['short_description_parsed'];
			$aContest['award_description_show'] = $aContest['award_description_parsed'];
		}
		else
		{
			$aContest['description_show'] = $aContest['description'];
			$aContest['short_description_show'] = $aContest['short_description'];
			$aContest['award_description_show'] = $aContest['award_description'];
		}

		return $aContest;
	}

	/**
	 * this function will check and add permission into a Contest array 
	 * @param  array $aContest a Contest
	 * @return array $aContest with added permission 
	 */
	public function retrieveContestPermissions($aContest)
	{
		$aContest['can_edit_contest'] = Phpfox::getService('contest.permission')->canEditContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_feature_contest'] = Phpfox::getService('contest.permission')->canFeatureContest($aContest['contest_id']);

		$aContest['can_premium_contest'] = Phpfox::getService('contest.permission')->canPremiumContest($aContest['contest_id']);

		$aContest['can_ending_soon_contest'] = Phpfox::getService('contest.permission')->canEndingSoonContest($aContest['contest_id']);

		$aContest['can_close_contest'] = Phpfox::getService('contest.permission')->canCloseContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_delete_contest'] = Phpfox::getService('contest.permission')->canDeleteContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_publish_contest'] = Phpfox::getService('contest.permission')->canPublishContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_register_service'] = Phpfox::getService('contest.permission')->canRegisterService($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_submit_entry'] = Phpfox::getService('contest.permission')->canSubmitEntry($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_join_contest'] = Phpfox::getService('contest.permission')->canJoinContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_follow_contest'] = Phpfox::getService('contest.permission')->canFollowContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_favorite_contest'] = Phpfox::getService('contest.permission')->canFavoriteContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_invite_friend'] = Phpfox::getService('contest.permission')->canInviteFriend($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_approve_deny_contest'] = Phpfox::getService('contest.permission')->canApproveDenyContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['can_view_browse_contest'] = Phpfox::getService('contest.permission')->canViewBrowseContest($aContest['contest_id'], Phpfox::getUserId());

		$aContest['have_action_on_contest'] = true;

		$aContest['can_view_wining_entries_action'] = Phpfox::getService('contest.permission')->canViewWinningEntriesActionLink($aContest['contest_id'], Phpfox::getUserId());

		if(!$aContest['can_edit_contest'] && 
		   !$aContest['can_feature_contest'] &&
		   !$aContest['can_premium_contest'] && 
		   !$aContest['can_ending_soon_contest'] &&
		   !$aContest['can_close_contest'] &&
		   !$aContest['can_delete_contest'] &&
		   !$aContest['can_publish_contest'] &&
		   !$aContest['can_register_service'] &&
		   !$aContest['can_view_wining_entries_action'] &&
		   !$aContest['can_approve_deny_contest'] )
		{
			$aContest['have_action_on_contest'] = false;
		}

		return $aContest;
	}


	public function getContestForCheckingPermission($iContestId)
	{
		if(isset($this->_aCheckingPermissionContests[$iContestId]))
		{
			return $this->_aCheckingPermissionContests[$iContestId];
		}
               
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = c.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}

		$aContest = $this->database()->select('c.*, cec.subject, cec.message, cec.term_condition, ' . Phpfox::getUserField())
		->from($this->_sTable, 'c')
		->leftJoin(Phpfox::getT('contest_email_condition'), 'cec', 'cec.contest_id = c.contest_id')
		->join(Phpfox::getT('user'),'u','u.user_id = c.user_id')
		->where('c.contest_id = ' . $iContestId)
		->execute('getSlaveRow');

		if(!$aContest)
		{
			return false;
		}

		// we have some conflicts here
		$aContest['total_entry'] = Phpfox::getService('contest.entry')->getTotalEntriesByContestId($iContestId);
		$this->_aCheckingPermissionContests[$iContestId] = $aContest;
                
		return $aContest;
	}
	/**
	 * @TODO: add detai later
	 */
	public function getTotalPendings ()
	{
		$iCnt = $this->database()->select('count(*)')
			->from(PHpfox::getT('contest'),'ct')
			->where('ct.contest_status = 2')
			->execute('getField');
		return $iCnt;
	}

	/**
	 * return array of needed statistics
	 * @todo: add detail later
	 * @return array() 
	 */
	public function getStatistic ()
	{
		
		return array(
			'total_contests' => $this->getCountContestByType('all'),
			'total_blog_contests' => $this->getCountContestByType('blog'),
                        'total_music_contests' => $this->getCountContestByType('music'),
			'total_photo_contests' => $this->getCountContestByType('photo'),
			'total_video_contests' => $this->getCountContestByType('video'),
			'total_participants' => Phpfox::getService('contest.participant')->getCountParticipant()
			// , 'total_my_inactive_contests' => $this->getCountContestByType('myinactive')
		);
	}

	public function getCountContestByType($type,$user_id = 0)
    {
		$where = '(ct.contest_status = 4 OR ct.contest_status = 5)';
		if($type!='myinactive'){
                    $where .="  and ct.is_active = 1 ";
                }
                else
                {
                    $where .="  and ct.is_active = 0 ";
                }
		$type_contest = array(
            'blog' => '1',
            'music' => '4',
            'photo' => '2',
            'video' => '3'
        );
        
		switch($type)
        {
			case 'blog': case 'music': case 'photo': case 'video':
				$where.=" and ct.type = ".$type_contest[$type];
				break;
			case 'profile':
				$where.=" and ct.user_id = ".$user_id;
                        case 'myinactive':
                                $where.=" and ct.user_id = ".PHpfox::getUserId();
				break;
		}
        
		$iCount = $this->database()->select('count(*)')
		->from(Phpfox::getT('contest'),'ct')
		->where($where)
		->execute('getField');
		
		return $iCount;
	}

	public function getTopContests ($sType = 'recent' , $iLimit = 9)
	{
            
            $where = '1 = 1 AND ' . 
            		'(ct.privacy = 0) AND ' .
            		' (ct.contest_status = 4) AND is_active = 1 ';
            $order = 'ct.time_stamp desc';
           
            switch (trim($sType)){
                case 'premium':
                    $where .= 'and ct.is_premium = 1';
                    break;
                case 'popular':
                    $order = 'ct.total_view desc';
                    break;
                case 'top':
                    $order = 'ct.total_participant desc';
                    break;
                case 'ending-soon':
					$setting = PHpfox::getParam('contest.ending_soon_setting');
					$day = Phpfox::getParam('contest.ending_soon_before');
					$time = $day*24*3600;
					$where .= 'and ct.is_ending_soon = 1  and contest_status <> 5 ';
					if($setting=='End of Submission')
					{
						$where .= ' AND '.(PHPFOX_TIME+$time)." >=ct.stop_time and ".PHPFOX_TIME."<=ct.stop_time";
					}
					else {
						$where .= ' AND '.(PHPFOX_TIME+$time)." >=ct.end_time and ".PHPFOX_TIME."<=ct.end_time";
					}
                    
                    break;
				case 'featured':
					$where .= 'and ct.is_feature = 1 and contest_status = 4';
					break;
                case 'recent':
                     $order = 'ct.time_stamp desc';
                    break;
            }
            $iCnt = $this->database()->select('count(*)')
                ->from(Phpfox::getT('contest'),'ct')
                ->join(Phpfox::getT('user'),'u','u.user_id = ct.user_id')
                ->where($where)
                ->execute('getField');
         
            $aRows = $this->database()->select('*,ct.server_id as server_id')
                ->from(Phpfox::getT('contest'),'ct')
                 ->join(Phpfox::getT('user'),'u','u.user_id = ct.user_id')
                ->where($where)
                ->order($order)
                ->limit($iLimit)
                ->execute('getRows');
              
             $aRows = $this->implementsContestFields($aRows);
             return array($iCnt,$aRows);
	}


    /**
     * @param $sType
     * @param $iItemId
     * @param int $iSource
     * @return bool
     */
	public function handlerAfterAddingEntry ($sType, $iItemId, $iSource = 0)
	{
		if(!$iItemId)
		{
			return false;
		}
		$sRedirectUrl = Phpfox::getService('contest.contest')->getRedirectUrlAfterAddingEntry($sType, $iItemId, $iSource);
		if(!$sRedirectUrl)
		{
			return false;
		}
		
		if($sType == 'music')
		{
		}
		else
		if($sType == 'photo'){

			echo 'window.location.href = \'' . $sRedirectUrl . '\';';
			exit;
			
		}
		else
		{
			echo "<script type=\"text/javascript\">";		
			echo 'window.parent.location.href = "' . $sRedirectUrl. '";';
			echo '</script>';
			exit;
		}
		

	}

	public function getRedirectUrlAfterAddingEntry($sType, $iItemId, $iSource = 0)
	{
		$sUrl = false;

		if(isset($_SERVER['HTTP_REFERER']))
		{
			$sRefererUrl = $_SERVER['HTTP_REFERER'];
			$iContestId = $this->getParamValueFromUrl(Phpfox::getService('contest.constant')->getYnAddParamForNavigateBack(), $sRefererUrl);
			if($iContestId)
			{
				$sUrl = Phpfox::getLib('url')->permalink('contest', $iContestId, 'itemid_' . $iItemId);
			}
			
		}

		//for some reason we can retrieve URL so we use session instead
		if(!$sUrl)
		{
			$sUrl = $this->getRedirectUrlAfterAddingEntryBySession($sType, $iItemId);
		}

		if ($iSource) {
            return $sUrl . 'source_' . $iSource;
        } else {
            return $sUrl;
        }
	}

	public function getRedirectUrlAfterAddingEntryBySession($sType, $iItemId)
	{
	 	$iContestId = Phpfox::getService('contest.helper')->getSessionAfterUserAddNewItem(Phpfox::getService('contest.constant')->getContestTypeIdByTypeName($sType));

	 	if(!$iContestId)
		{
			return false;
		}

		$sUrl = Phpfox::getLib('url')->permalink('contest', $iContestId, 'itemid_' . $iItemId);

		return $sUrl;
	}

	public function getParamValueFromUrl($sName, $sUrl)
	{
        $sPattern = '/' . $sName . '_((\d|\w)*)' . '/';
        preg_match($sPattern, $sUrl, $aMatches );
        if(isset($aMatches[1]))
        {
        	$sValue = trim($aMatches[1], '\/');
        	return $sValue;
        }
        else
        {
            $parts = parse_url($sUrl);
            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                if (isset($query[$sName])) {
                    return $query[$sName];
                }
            }
            
            return false;
        }
    }

    public function getContestImageDir()
	{
		return Phpfox::getParam('core.dir_pic'). 'contest' . PHPFOX_DS;
	}

	public function getAllFees()
	{
		$iPublishFee = Phpfox::getUserParam('contest.contest_publish_fee');

		$iEndingSoonFee = Phpfox::getUserParam('contest.contest_ending_soon_fee');

		$iFeatureFee = Phpfox::getUserParam('contest.contest_feature_fee');

		$iPremiumFee = Phpfox::getUserParam('contest.contest_premium_fee');

		return array(
			'publish' => $iPublishFee,
			'ending_soon' => $iEndingSoonFee,
			'feature' => $iFeatureFee,
			'premium' => $iPremiumFee
			);
	}

	public function getTotalFees()
	{
		$aFees = $this->getAllFees();
		$iTotal = 0;
		foreach ($aFees as $sKey => $iFee) {
			$iTotal += $iFee;	
		}

		return $iTotal;
	}
	/**
	 * get all fees and phrases and convert money into money text
	 * @param  [type] $iContestId [description]
	 * @return [type]             [description]
	 */
	public function getAllFeesAndPhraseForAContest($iContestId)
	{
		$aFees = Phpfox::getService('contest.contest')->getAllFees();
		$aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

		$aResult = array();
		foreach($aFees as $sKey => $iValue)
		{
			$sMoneyText = Phpfox::getService('contest.helper')->getMoneyText($iAmount = $iValue);
			switch($sKey)
			{
				case 'publish' :
						if(!$aContest['is_published'] && $iValue > 0 && 
							//denied contest is published so we don't need to charge it again
							$aContest['contest_status'] != Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('denied') && 
							$aContest['contest_status'] != Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('pending'))					
						{
							$aResult[$sKey] = array(
								'name' => 'publish_fee',
								'value' => $iValue,
								'phrase' => _p('contest.publish_contest_money', array('money_text' => $sMoneyText )),
								'money_text' => $sMoneyText
								);
						}
					break;
				case 'ending_soon' :
						if(!$aContest['is_ending_soon'] && $iValue > 0)					
						{
							$aResult[$sKey] = array(
								'name' => 'ending_soon_fee',
								'value' => $iValue,
								'phrase' => _p('contest.register_service_ending_soon_contest_money', array('money_text' => $sMoneyText )),
								'money_text' => $sMoneyText
								);
						}
					break;
				case 'feature' :
						if(!$aContest['is_feature'] && $iValue > 0)					
						{
							$aResult[$sKey] = array(
								'name' => 'feature_fee',
								'value' => $iValue,
								'phrase' => _p('contest.register_service_feature_contest_money', array('money_text' => $sMoneyText )),
								'money_text' => $sMoneyText
								);
						}
					break;
				case 'premium' :
						if(!$aContest['is_premium'] && $iValue > 0)					
						{
							$aResult[$sKey] = array(
								'name' => 'premium_fee',
								'value' => $iValue,
								'phrase' => _p('contest.register_service_premium_contest_money', array('money_text' => $sMoneyText )),
								'money_text' => $sMoneyText
								);
						}
					break;
				default :
					break;
			}
		}

		return $aResult;
	}

	public function searchContests($aConds, $sSort = 'contest.contest_name ASC', $iPage = '', $iLimit = '')
	{
		if(is_array($aConds)){
                    $aConds[] = ' and contest.is_deleted=0  ';
                }
                else
                {
                    if($aConds!="")
                    {
                        $aConds = ' and contest.is_deleted=0  ';
                    }
                    else
                    {
                        $aConds = ' contest.is_deleted=0 ';
                    }
                }
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'contest')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = contest.user_id')
			->where($aConds)
			->order($sSort)
			->execute('getSlaveField');


		$aStatus = array(
			// Phpfox::getService('fundraising.campaign')->getStatusCode('closed') => _p('fundraising.closed'),
			// Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') => _p('fundraising.on_going'),
			// Phpfox::getService('fundraising.campaign')->getStatusCode('expired') => _p('fundraising.expired'),
			// Phpfox::getService('fundraising.campaign')->getStatusCode('reached') => _p('fundraising.reached'),
			// Phpfox::getService('fundraising.campaign')->getStatusCode('draft') => _p('fundraising.draft'),
			// Phpfox::getService('fundraising.campaign')->getStatusCode('pending') => _p('fundraising.pending')
		);
		$aItems = array();
		if ($iCnt)
		{		
			$aItems = $this->database()->select('contest.*, ' . Phpfox::getUserField())
				->from($this->_sTable, 'contest')
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = contest.user_id')
				->where($aConds)
				->order($sSort)
				->limit($iPage, $iLimit, $iCnt)
				->execute('getSlaveRows');
				
			foreach ($aItems as $iKey => $aItem)
			{
				$sStatus = Phpfox::getService('contest.constant')->getContestStatusNameByStatusId($aItem['contest_status']);
				// ^^ convention is we should have these phrase
				$aItems[$iKey]['contest_status_text'] = _p('contest.' . $sStatus);
			
				$aItems[$iKey]['link'] = ($aItem['user_id'] ? Phpfox::getLib('url')->permalink($aItem['user_name'] . '.contest', $aItem['contest_id'], $aItem['contest_name']) : Phpfox::getLib('url')->permalink('contest', $aItem['contest_id'], $aItem['contest_name']));
			}
		}
			
		
		$this->processRows($aItems);
		
		return array($iCnt, $aItems);
	}	

	public function processRows(&$aRows)
	{
		foreach ($aRows as $iKey => $aRow)
		{
			if($aRow['module_id'] === 'pages')
			{
				$aPage = Phpfox::getService('pages')->getPage($aRow['item_id']);
				if($aPage['vanity_url'])
				{
					$aRows[$iKey]['page_link'] = Phpfox::permalink($aPage['vanity_url'], 'contest');
				}
				else
				{
					$aRows[$iKey]['page_link'] = Phpfox::permalink('pages', $aRow['item_id'], 'contest');	
				}
				$aRows[$iKey]['page_name'] = $aPage['title'];
			}			
		}
	}	

	/**
	 * check in the list of friends what friend user has invited then make the short list
	 * @TODO: slow performance 
	 * <pre>
	 * </pre>
	 * @by minhta
	 * @param int $iContestId 
	 * @param int $aFriends list of user's friend 
	 * @return array list of uninvited friend, false if there's no one
	 */
	public function isAlreadyInvited($iContestId, $aFriends) {
		if ((int) $iContestId == 0) {
			return false;
		}

		if (is_array($aFriends)) {
			if (!count($aFriends)) {
				return false;
			}

			$sIds = [];
			foreach ($aFriends as $aFriend) {
				if (!isset($aFriend['user_id'])) {
					continue;
				}

				$sIds[] = $aFriend['user_id'];
			}

			$aInvites = $this->database()->select('invite_id, invited_user_id')
					->from(Phpfox::getT('contest_invite'))
					->where('item_id = ' . (int) $iContestId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
					->execute('getSlaveRows');

			$aCache = array();
			foreach ($aInvites as $aInvite) {
				// $aCache[$aInvite['invited_user_id']] = ($aInvite['user_id'] > 0 ? _p('contest.joined') : _p('contest.invited'));
				// we add joined later
				$aCache[$aInvite['invited_user_id']] = _p('contest.invited');
			}

			if (count($aCache)) {
				return $aCache;
			}
		}

		return false;
	}

	public function getContestUrl($iContestId)
	{
        $sContestName = $this->database()->select('contest_name')
		->from($this->_sTable)
		->where('contest_id = ' . $iContestId)
		->execute('getSlaveField');
        
		return Phpfox::getLib('url')->permalink('contest', $iContestId, $sContestName);
	}

	public function getFrameUrl($iContestId, $iStatus = 3)
	{
		if(!$iContestId)
		{
			return false;
		}
		$sCorePath = Phpfox::getParam('core.path');
		$sFrameUrl = Phpfox::getService('contest.helper')->getStaticPath() . 'module/contest/static/contest-badge.php?contest_id=' . $iContestId . '&status=' . $iStatus;

		return $sFrameUrl;
	}

	public function getBadgeCode($sFrameUrl)
	{
		return '<iframe src="'.$sFrameUrl.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:600px;" allowTransparency="true"></iframe>';
	}

	public function getNotifyingMessage($sType, $aContest, $aUser = array(), $aEntry = array())
	{
		$sLink = Phpfox::getLib('url')->permalink('contest', $aContest['contest_id'], $aContest['contest_name']);
		if($aUser)
		{
			$sUser = '<span class="drop_data_user">' . $aUser['full_name'] . '</span>';
		}
		
		switch ($sType) {
			case 'close_contest':
				return  _p('contest.contest_title_has_been_close_link', array('title' => $aContest['contest_name'], 'link' => '') );
				break;
                        case 'inactive_contest':
				return  _p('contest.contest_title_has_been_inactive_link', array('title' => $aContest['contest_name'], 'link' => '') );
				break;
			case 'join_contest':
				return  _p('contest.users_joined_your_contest_title', array('users' => $sUser,'title' => $aContest['contest_name'], 'phpfox_squote' => true ) );
				break;
			case 'leave_contest':
				return  _p('contest.users_left_your_contest_title', array('users' => $sUser,'title' => $aContest['contest_name']) );
				break;
			case 'favorite_contest':
				return  _p('contest.users_favorited_your_contest_title', array('users' => $sUser,'title' => $aContest['contest_name']) );
				break;
			case 'follow_contest':
				return  _p('contest.users_followed_your_contest_title', array('users' => $sUser,'title' => $aContest['contest_name']) );
				break;
			case 'approve_entry':
			    if($aEntry['user_id'] == $aUser['user_id']) {
			        return _p('contest.your_entry_title_in_contest_title_has_been_approved', array('title' => $aEntry['title'], 'contest_title' => $aContest['contest_name']) );
                }
				return  _p('contest.user_submitted_an_entry_title', array('users' => $sUser, 'title' => $aEntry['title']) );
				break;
            case 'deny_entry':
                return _p('contest.your_entry_title_in_contest_title_has_been_denied', array('title' => $aEntry['title'], 'contest_title' => $aContest['contest_name']) );
                break;
			case 'inform_winning_entry':
				return  _p('contest.contest_title_has_chosen_winners', array('title' => $aContest['contest_name']) );
				break;
            case 'approve_contest':
                return  _p('contest.your_contest_title_has_been_approved', array('title' => $aContest['contest_name'], 'phpfox_squote' => true ) );
                break;
            case 'deny_contest':
                return  _p('contest.your_contest_title_has_been_denied', array('title' => $aContest['contest_name'], 'phpfox_squote' => true ) );
                break;
			default:
				break;
		}
	}



	/**
	 * this function used to get email messages which are not in predefined email template
	 * @param  [type] $sType    [description]
	 * @param  [type] $aContest [description]
	 * @return array           [description]
	 */
	public function getEmailMessage($sType, $aContest)
	{
		switch ($sType) {
			case 'join_contest':

				break;
			
			default:
				break;
		}

		return array(
//			'subject' => $sSubject,
//			'content' => $sContent
			);
	}

    public function getAllEntryOwnerNotInParticipantListOfContest($iContestId){
       $aRows = $this->database()->select('e.user_id, p.user_id as is_participant')	
			->from(Phpfox::getT('entry'), 'e')
			->join(Phpfox::getT('participant'), 'p', 'p.user_id = e.user_id')
			->where('e.contest_id = ' . $iContestId . ' AND is_participant IS NULL ')
			->execute('getRows');
        return $aRows;
    }

    public function isShowContestEndingSoonLabel($iContestId)
    {
    	$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

    	if(!$aContest['is_ending_soon'])
    	{
    		return false;
    	}

    	$sSetting = PHpfox::getParam('contest.ending_soon_setting');
		$iDay = Phpfox::getParam('contest.ending_soon_before');
		$iTime = $iDay * 24 * 3600;

		$iStartEndingSoonTime = 0;
		if($sSetting =='End of Submission')
		{
			$iStartEndingSoonTime = $aContest['stop_time'] - $iTime;	
		}
		elseif ($sSetting == 'End of Contest') {
			$iStartEndingSoonTime = $aContest['end_time'] - $iTime;	
		}


		if(PHPFOX_TIME > $iStartEndingSoonTime && 
			$aContest['contest_status'] == Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'))
		{
			return true;
		}

		return false;
    }
    
    public function isManualClosed($aContest)
    {
        if ($aContest['contest_status'] == 5 && $aContest['end_time'] >= PHPFOX_TIME)
        {
            return true;
        }
        
        return false;
    }
    
    public function getUserClose($iContestId)
    {
        $aUser = $this->database()->select(Phpfox::getUserField())
        ->from($this->_sTable, 'c')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.closed_by')
        ->where('c.contest_id = '.(int)$iContestId)
        ->execute('getSlaveRow');
        
        return $aUser;
    }
}