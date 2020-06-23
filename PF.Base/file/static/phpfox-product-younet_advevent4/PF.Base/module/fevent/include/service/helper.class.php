<?php

defined('PHPFOX') or exit('NO DICE!');

class Fevent_Service_Helper extends Phpfox_service
{
	/**
	 * Update start & end time for repeat events
	 */
	public function updateDurationOfRepeatEvents()
	{
        // because we generate more instances so we do not need to use this function 
        return false; 


		$curTime = PHPFOX_TIME;
		// $nextDay = $curTime + (1 * 24 * 60 * 60);  //  mean run per day
        $nextTime = $curTime + (1 * 60 * 60);   //  mean run per hour (because 'daily' type)

		if($this->shouldRunUpdateDurationOfRepeatEventsCron() == true){
			//	get all repeat events
			//	update cron
			$this->updateCron('for_repeat', $nextTime);

            //  TO DO UPDATE DURATION OF REPEAT EVENTS
            //  TO DO REMOVE ATTENEES IN PAST DURATION OF REPEAT EVENTS
            $listRepeat = Phpfox::getService('fevent')->getRepeatEventForUpdateDuration();
            foreach($listRepeat as $event){
                $iStartTime = $event['start_time'];
                $iEndTime = $event['end_time'];

                $iRepeatTime = $event['timerepeat'];
                $iDurationDays = $event['duration_days'];
                $iDurationHours = $event['duration_hours'];

                $month = date('n', $iStartTime);
                $day = date('j', $iStartTime);
                $year = date('Y', $iStartTime);
                $start_hour = date('H', $iStartTime);
                $start_minute = date('i', $iStartTime);

                //  get new start/end time
                if($event['isrepeat'] == 0 ){
                    //  daily
                    while($iEndTime < $curTime){
                        //  get next start/end time
                        $next_start_time = $iStartTime + (1 * 24 * 60 * 60);

                        $iStartTime = $next_start_time;
                        $iEndTime = $this->getEndTimeByDuration((int)$event['isrepeat'], $iStartTime, (int)$iDurationDays, (int)$iDurationHours); 
                    }
                } else if($event['isrepeat'] == 1 ){
                    //  weekly
                    while($iEndTime < $curTime){
                        //  get next start/end time
                        $next_start_time = $iStartTime + (7 * 24 * 60 * 60);
                        
                        $iStartTime = $next_start_time;
                        $iEndTime = $this->getEndTimeByDuration((int)$event['isrepeat'], $iStartTime, (int)$iDurationDays, (int)$iDurationHours); 
                    }
                } else if($event['isrepeat'] == 2 ){
                    //  monthly
                    while($iEndTime < $curTime){
                        //  get next start/end time
                        $next_start_time_obj = $this->getSameDayInNextMonth($day, $month, $year);

                        $next_start_time = Phpfox::getLib('date')->mktime($start_hour
                                , $start_minute
                                , 0
                                , $next_start_time_obj['month']
                                , $next_start_time_obj['day']
                                , $next_start_time_obj['year']
                                );

                        $iStartTime = $next_start_time;
                        $iEndTime = $this->getEndTimeByDuration((int)$event['isrepeat'], $iStartTime, (int)$iDurationDays, (int)$iDurationHours); 

                        $day = $next_start_time_obj['day'];
                        $month = $next_start_time_obj['month'];
                        $year = $next_start_time_obj['year'];
                    }
                }

                //  update new start/end time
                if($iStartTime < $iRepeatTime && $iEndTime < $iRepeatTime){
                    // ... update here
                    Phpfox::getService('fevent.process')->updateStartEndTimeByDurationOfRepeatEvent($event['event_id'], $iStartTime, $iEndTime);
                    //  remove attendees for past event
                    if((int)$event['is_delete_user_past_repeat_event'] == 1){
                        //  ... delete here
                        Phpfox::getService('fevent.process')->deleteAttendees($event['event_id'], 1, true);
                    }
                }
            }

			//	return true which mean ran cron job
			return true;
		}

		return false;
	}	

    public function updateRepeatEvent($eventID){
        // because we generate more instances so we do not need to use this function 
        return false; 

        $curTime = PHPFOX_TIME;
        // $nextDay = $curTime + (1 * 24 * 60 * 60);  //  mean run per day
        $nextTime = $curTime + (1 * 60 * 60);   //  mean run per hour (because 'daily' type)
        
        $event = Phpfox::getService('fevent')->getEventWithLessInfo($eventID);
        if(isset($event['event_id'])){
            $iStartTime = $event['start_time'];
            $iEndTime = $event['end_time'];

            $iRepeatTime = $event['timerepeat'];
            $iDurationDays = $event['duration_days'];
            $iDurationHours = $event['duration_hours'];

            $month = date('n', $iStartTime);
            $day = date('j', $iStartTime);
            $year = date('Y', $iStartTime);
            $start_hour = date('H', $iStartTime);
            $start_minute = date('i', $iStartTime);

            //  get new start/end time
            if($event['isrepeat'] == 0 ){
                //  daily
                while($iEndTime < $curTime){
                    //  get next start/end time
                    $next_start_time = $iStartTime + (1 * 24 * 60 * 60);

                    $iStartTime = $next_start_time;
                    $iEndTime = $this->getEndTimeByDuration((int)$event['isrepeat'], $iStartTime, (int)$iDurationDays, (int)$iDurationHours); 
                }
            } else if($event['isrepeat'] == 1 ){
                //  weekly
                while($iEndTime < $curTime){
                    //  get next start/end time
                    $next_start_time = $iStartTime + (7 * 24 * 60 * 60);
                    
                    $iStartTime = $next_start_time;
                    $iEndTime = $this->getEndTimeByDuration((int)$event['isrepeat'], $iStartTime, (int)$iDurationDays, (int)$iDurationHours); 
                }
            } else if($event['isrepeat'] == 2 ){
                //  monthly
                while($iEndTime < $curTime){
                    //  get next start/end time
                    $next_start_time_obj = $this->getSameDayInNextMonth($day, $month, $year);

                    $next_start_time = Phpfox::getLib('date')->mktime($start_hour
                            , $start_minute
                            , 0
                            , $next_start_time_obj['month']
                            , $next_start_time_obj['day']
                            , $next_start_time_obj['year']
                            );

                    $iStartTime = $next_start_time;
                    $iEndTime = $this->getEndTimeByDuration((int)$event['isrepeat'], $iStartTime, (int)$iDurationDays, (int)$iDurationHours); 

                    $day = $next_start_time_obj['day'];
                    $month = $next_start_time_obj['month'];
                    $year = $next_start_time_obj['year'];
                }
            }

            //  update new start/end time
            if($iStartTime < $iRepeatTime && $iEndTime < $iRepeatTime){
                // ... update here
                Phpfox::getService('fevent.process')->updateStartEndTimeByDurationOfRepeatEvent($event['event_id'], $iStartTime, $iEndTime);
                //  remove attendees for past event
                if((int)$event['is_delete_user_past_repeat_event'] == 1){
                    //  ... delete here
                    Phpfox::getService('fevent.process')->deleteAttendees($event['event_id'], 1, true);
                }
            }
        } else {
            return false;
        }
    }

	public function shouldRunUpdateDurationOfRepeatEventsCron()
	{
		$curTime = PHPFOX_TIME;
        $maxTime = $this->database()->select('  MAX(`time_stamp`) ')
            ->from(Phpfox::getT('fevent_cron'), 's')
            ->where(' 1=1 AND `type_cron` = \'for_repeat\' ')
            ->execute('getSlaveField');

        if(!isset($maxTime) || null == $maxTime || $curTime > (int)$maxTime){
        	return true;
        }

        return false;		
	}

	public function updateCron($typeCron, $time)
	{
        $iId = $this->database()->insert(Phpfox::getT('fevent_cron'), array(
                'type_cron' => $typeCron,
				'time_stamp' =>  $time
            )
        );
		return $iId;
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

    public function isIntegerNumber($val = "")
    {
    	if(strlen(trim($val)) == 0)
    	{
    		return false;
    	}

    	return ctype_digit($val);
    }

    public function getSameDayInNextMonth($day, $month, $year)
    {
        //  if date is invalid, date will be become last date in month 
        if($month == 12){
            $month = 1;
            $year = $year + 1; 
        } else {
            $month = $month + 1;     
        }

        $exist = false; 
        while($exist == false){
            if (checkdate($month, $day, $year) == true) {
              $exist = true;
              break;
            } else {
              $exist = false;
              $day = $day - 1;
            }        
        }

        return array('day' => $day, 'month' => $month, 'year' => $year);
    }

    public function daysToDate($day1, $month1, $year1, $day2, $month2, $year2)
    {
        $oneDay = 1 * 24 * 60 * 60; // hours*minutes*seconds
        $firstDate = Phpfox::getLib('date')->mktime(0, 0, 0, $month1, $day1, $year1);
        $secondDate = Phpfox::getLib('date')->mktime(0, 0, 0, $month2, $day2, $year2);

        $diffDays = round(abs(($firstDate - $secondDate)/($oneDay))); 

        return (int)$diffDays;        
    }

    /**
     * Return time and does not check timezone
     */
    public function getEndTimeByDuration($type = 0, $startTime = 0, $durDays = 0, $durHours = 0)
    {
        $endTime = 0; 

        //  daily 
        if(0 == $type){
            $endTime = $startTime + ($durHours * 60 * 60); 
        }

        //  weekly
        if(1 == $type){
            $endTime = $startTime + ($durDays * 24 * 60 * 60) + ($durHours * 60 * 60); 
        }

        //  monthly
        if(2 == $type){
            $endTime = $startTime + ($durDays * 24 * 60 * 60) + ($durHours * 60 * 60); 
        }

        return $endTime; 
    }

    public function canEditStartTimeByEventID($eventID = null)
    {
        // if(null == $eventID){
        //     return false;
        // }

        // $result = false;
        // if(Phpfox::getUserParam('fevent.can_edit_start_date') == true){
        //     $result = true;
        // }
        // if(Phpfox::getUserParam('fevent.can_edit_start_date_if_event_has_no_attendee') == true){
        //     if((int)Phpfox::getService('fevent')->getNumbersOfAttendee($eventID, 1) > 0){
        //         $result = false;
        //     }            
        // }

        // return $result;
        
        return true;
    }

    public function canEditEndTimeByEventID($eventID = null)
    {
        // return Phpfox::getUserParam('fevent.can_edit_end_date');
        return true;
    }

    public function canEditDurationByEventID($eventID = null)
    {
        // return Phpfox::getUserParam('fevent.can_edit_duration');
        return true;
    }

    public function canViewEventInPageWhichJoined()
    {
    	return false;
    }

    public function getListOfPagesWhichJoinedByUserID($userID){
        $aRows = $this->database()->select('p.page_id')
            ->from(Phpfox::getT('pages'), 'p')         
            ->join(Phpfox::getT('like'), 'l', 'l.user_id = ' . (int)$userID . ' AND l.`type_id` = \'pages\'  AND l.`item_id` = p.page_id ')
            ->where(' p.view_id = 0 ')         
            ->execute('getSlaveRows');
        
        return $aRows;        
    }

    /**
     * Get now time (gmt0)
     * Convert to user timezone 
     * Get start/end time by user timezone
     * Convert to start/end time by gmt0
     * 
     */
    public function getStartEndTimeByTypeWithGMT0($type = 'today')
    {
        $curTimeGMT0 = PHPFOX_TIME; 
        $curUser = $this->convertToUserTimeZone($curTimeGMT0);

        $month = date('n', $curUser);
        $day = date('j', $curUser);
        $year = date('Y', $curUser);
        $start_hour = date('H', $curUser);
        $start_minute = date('i', $curUser);
        $start_second = date('s', $curUser);

        $iStartTime = 0;
        $iEndTime = 0;
        if('today' == $type){
            $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $month, $day, $year);
            $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $month, $day, $year);
        } else if('this-week' == $type){
            $week = date("W", $curUser);
            $result = $this->getStartAndEndDateOfWeek($week - 1, $year, $curUser);

            $iStartTime = $result[0];
            $iEndTime = $result[1];
        } else if('this-month' == $type){
            $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $month, 1, $year);
            $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $month, date('t', $curUser), $year);
        }

        $iStartTime = $this->convertFromUserTimeZone($iStartTime);
        $iEndTime = $this->convertFromUserTimeZone($iEndTime);
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


    public function displayRepeatTime($isrepeat = -1, $timerepeat = 0)
    {
        $content_repeat = "";
        $until = "";
        if ($isrepeat == 0)
        {
            $content_repeat = _p('daily');
        }
        elseif ($isrepeat == 1)
        {
            $content_repeat = _p('weekly');
        }
        elseif ($isrepeat == 2)
        {
            $content_repeat = _p('monthly');
        }
        if ($content_repeat != "")
        {
            if ($timerepeat != 0)
            {
                $until = Phpfox::getTime("M j, Y", $this->convertToUserTimeZone($timerepeat), false);
                $content_repeat .= ", " . _p('until') . " " . $until;
            }
        }

        return $content_repeat;        
    }

    public function displayTimeByFormat($format = 'M j, Y g:i a', $time, $bIsShortType = false)
    {
		return Phpfox::getTime($format, $this->convertToUserTimeZone($time), false, $bIsShortType);
    }

    public function displayTimeByFormatForBlock($aEvents)
    {
        if(count($aEvents)){
            foreach ($aEvents as $i => $aEvent) {
                $aEvents[$i]['d_start_time'] = $this->displayTimeByFormat(Phpfox::getParam('fevent.fevent_browse_time_stamp'), (int)$aEvents[$i]['start_time']); //day
                $aEvents[$i]['M_start_time'] = $this->displayTimeByFormat('M', (int)$aEvents[$i]['start_time']); //month
                $aEvents[$i]['short_start_time'] = $this->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aEvents[$i]['start_time']); //hour
                $aEvents[$i]['d_end_time'] = $this->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time'), (int)$aEvents[$i]['end_time']);
                $aEvents[$i]['d_start_time_hour'] = $this->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aEvents[$i]['start_time']); //time hour
                $aEvents[$i]['detail_start_time'] = $this->displayTimeByFormat(Phpfox::getParam('core.global_update_time'), (int)$aEvents[$i]['start_time']); //day
                $aEvents[$i]['D_start_time'] = $this->displayTimeByFormat('D', (int)$aEvents[$i]['start_time']); //day
                $aEvents[$i]['d_end_time_past'] = $this->displayTimeByFormat(Phpfox::getParam('fevent.fevent_browse_time_stamp'), (int)$aEvents[$i]['end_time']); //day
                $aEvents[$i]['short_end_time'] = $this->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aEvents[$i]['end_time']); //hour
            }
            return $aEvents;
        }
    }
    public function getTimeLineStatus($iStart, $iEnd)
    {
        if ($iStart > PHPFOX_TIME)
        {
            return 'upcoming';
        }
        elseif ($iEnd < PHPFOX_TIME)
        {
            return 'past';
        }
        else
        {
            return 'ongoing';
        }
    }

    public function timestampToCountdownString($iTimeStamp, $type = 'upcoming')
    {
        $result = '';

        if ('past' == $type) {
            $iLeft = PHPFOX_TIME - $iTimeStamp;
        } else {
            $iLeft = $iTimeStamp - PHPFOX_TIME;
        }
        
        if ($iLeft >= 60)
        {
            $sLeft = $this->secondsToString($iLeft);
            if('upcoming' == $type){
                $result =  $sLeft;
            } else if('ongoing' == $type){
                $result = $sLeft;
            } else if('past' == $type) {
                $result =  $sLeft;
            }
        }
        elseif ($iLeft > 0)
        {
            if('upcoming' == $type){
                $result = '1'.' '._p('minute');
            } else if('ongoing' == $type){
                $result = '1'.' '._p('minute');
            } else if('past' == $type) {
                $result = '1'.' '._p('minute');
            }
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
                    ' '._p('year'),
                    ' '._p('month'),
                    ' '._p('day'),
                    ' '._p('hour'),
                    ' '._p('minute')
                ),
                array(
                    ' '._p('years'),
                    ' '._p('months'),
                    ' '._p('days'),
                    ' '._p('hours'),
                    ' '._p('minutes')
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

    public function isAdminEvent($iId, $iUserId)
    {
        $aAdmin = $this->database()->select('*')
            ->from(Phpfox::getT('fevent_admin'))
            ->where('event_id = '.(int)$iId.' AND user_id = '.(int)$iUserId)
            ->execute('getSlaveRow');
        
        if(!$aAdmin)
        {
            return false;
        }
        
        return true;
    }

    public function canEditEvent($iEventID, $iUserId = 0, $eventUserID = null)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        if(null == $eventUserID){
            $aEvent = Phpfox::getService('fevent')->getEventByID($iEventID);
            if (!$aEvent)
            {
                return false;
            }

            $eventUserID = $aEvent['user_id'];
        }


        if ((($iUserId == $eventUserID || $this->isAdminEvent($iEventID, $iUserId)) && Phpfox::getUserParam('fevent.can_edit_own_event')) 
            || Phpfox::getUserParam('fevent.can_edit_other_event')
            )
        {
            return true;
        }

        return false;
    }

    public function canDeleteEvent($iEventID, $iUserId = 0, $eventUserID = null)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        if(null == $eventUserID){
            $aEvent = Phpfox::getService('fevent')->getEventByID($iEventID);
            if (!$aEvent)
            {
                return false;
            }

            $eventUserID = $aEvent['user_id'];
        }


        if (($iUserId == $eventUserID && Phpfox::getUserParam('fevent.can_delete_own_event')) 
            || Phpfox::getUserParam('fevent.can_delete_other_event')
            )
        {
            return true;
        }
        //owner of page/group
        $aEvent = Phpfox::getService('fevent')->getEventByID($iEventID);
        if (Phpfox::isModule($aEvent['module_id'])) {
            if ($aEvent['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aEvent['item_id'])) {
                return true; // is owner of page
            } elseif ($aEvent['module_id'] == 'groups' && Phpfox::getService('groups')->isAdmin($aEvent['item_id'])) {
                return true; // is owner of group
            }
        }
        return false;
    }

    public function canApproveEvent($bRedirect = false)
    {
        if(Phpfox::getUserParam('fevent.can_approve_events', $bRedirect)) {
            if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
                return true;
            }
        }
            
        return false;       
    }    

    public function canFeatureEvent($bRedirect = false)
    {
        if(Phpfox::getUserParam('fevent.can_feature_events', $bRedirect)) {
            if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
                return true;
            }
        }
            
        return false;       
    }    

    public function canSponsorEvent($bRedirect = false)
    {
        if(Phpfox::isModule('ad') && Phpfox::getUserParam('fevent.can_sponsor_fevent', $bRedirect)) {
            if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
                return true;
            }
        }
            
        return false;       
    }    

    public function retrieveEventPermissions($aEvent)
    {
        $userID = Phpfox::getUserId();
        
        $aEvent['can_edit_event'] = $this->canEditEvent($aEvent['event_id'], $userID, $aEvent['user_id']);
        $aEvent['can_delete_event'] = $this->canDeleteEvent($aEvent['event_id'], $userID, $aEvent['user_id']);
        $aEvent['can_approve_event'] = $this->canApproveEvent();
        $aEvent['can_feature_event'] = $this->canFeatureEvent();
        $aEvent['can_sponsor_event'] = $this->canSponsorEvent();

        return $aEvent;
    }

    public function getAdvSearchFields()
    {
        $aVals = array();
        
        if(strtolower($this->search()->get('submit')) == strtolower(_p('advs_reset')))
        {
            $aVals = array(
                'gender' => '',
                'level_id' => 0,
                'year_exp_from' => 0,
                'year_exp_to' => 0
            );
        }
        return $aVals;
    }

    public function getImageDefault(&$aEvent,$type){

            if($aEvent['image_path'] == ''){
                $aEvent['image_path'] = Phpfox::getService('fevent')->getDefaultPhoto($type == 'block' ? false : true);
            }
            else{
                $aEvent['image_path'] = Phpfox::getLib('image.helper')->display(array(
                                    'server_id' => isset($aEvent['event_server_id'])?$aEvent['event_server_id']:$aEvent['server_id'],
                                    'path' => 'event.url_image',
                                    'file' => $aEvent['image_path'],
                                    'suffix' => '',
                                    'return_url' => true
                                ));
            }

    }

    public function buildSectionMenu()
    {
        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            $iMyTotal = Phpfox::getService('fevent')->getMyTotal();
            $aFilterMenu = array(
                _p('all_events') => '',
                _p('my_events') . (($iMyTotal > 0) ? '<span class="my count-item">' . ($iMyTotal > 99 ? '99+' : $iMyTotal) . '</span>' : '') => 'my'
            );
            $aFilterMenu[_p('calendar')] = 'pagecalendar';

            if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community')) {
                $aFilterMenu[_p('friends_events')] = 'friend';
            }

            $iTotalFeatured = Phpfox::getService('fevent')->getFeaturedTotal();
            if ($iTotalFeatured) {
                $aFilterMenu[_p('featured_events') . (($iTotalFeatured > 0) ? '<span class="count-item">' . ($iTotalFeatured > 99 ? '99+' : $iTotalFeatured ). '</span>' : '')] = 'featured';
            }

            if (Phpfox::getUserParam('fevent.can_approve_events')) {
                $iPendingTotal = Phpfox::getService('fevent')->getPendingTotal();

                if ($iPendingTotal) {
                    $aFilterMenu[_p('pending_events') . (($iPendingTotal > 0) ? '<span id="pending-fevent" class="count-item">' . ($iPendingTotal > 99 ? '99+' : $iPendingTotal ) . '</span>' : '')] = 'pending';
                }
            }
            $iInviteTotal = Phpfox::getService('fevent')->getAttendingTotal(0);
            $iAttendingTotal = Phpfox::getService('fevent')->getAttendingTotal(1);
            $iMayAttendingTotal = Phpfox::getService('fevent')->getAttendingTotal(2);
            $iNotAttendingTotal = Phpfox::getService('fevent')->getAttendingTotal(3);
            $aFilterMenu[_p('events_i_m_attending') . (($iAttendingTotal > 0) ? '<span class="count-item">' . ($iAttendingTotal > 99 ? '99+' : $iAttendingTotal ). '</span>' : '')] = 'attending';
            $aFilterMenu[_p('events_i_may_attend') . (($iMayAttendingTotal > 0) ? '<span class="count-item">' . ($iMayAttendingTotal > 99 ? '99+' : $iMayAttendingTotal ). '</span>' : '')] = 'may-attend';
            $aFilterMenu[_p('events_i_m_not_attending') . (($iNotAttendingTotal > 0) ? '<span class="count-item">' . ($iNotAttendingTotal > 99 ? '99+' : $iNotAttendingTotal ). '</span>' : '')] = 'not-attending';
            $aFilterMenu[_p('event_invites') . (($iInviteTotal > 0) ? '<span class="count-item">' . ($iInviteTotal > 99 ? '99+' : $iInviteTotal ). '</span>' : '')] = 'invites';
            $aFilterMenu[_p('google_map')] = 'gmap';
            Phpfox::getLib('template')->buildSectionMenu('fevent', $aFilterMenu);
        }
    }

}