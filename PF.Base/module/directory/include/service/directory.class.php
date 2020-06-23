<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Service_Directory extends Phpfox_Service
{
    private $_aCallback = null;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('directory_business');
    }

    public function countTotalFollowingBusinessByUserId($iUserId)
    {
        $iCount = $this->database()
            ->select("COUNT(DISTINCT(dbus.business_id)) ")
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->join(Phpfox::getT('directory_category'), 'dc', 'dc.category_id = dcd.category_id')
            ->join(Phpfox::getT("directory_business_location"), 'dbl', 'dbl.business_id = dbus.business_id')
            ->join(Phpfox::getT("directory_follow"), 'dfo', 'dfo.business_id = dbus.business_id')
            ->join(Phpfox::getT("user"), 'u', 'dbus.user_id =  u.user_id')
            ->where('dfo.user_id = ' . (int)$iUserId . ' AND dbus.business_status IN ' . "("
                . Phpfox::getService('directory.helper')->getConst('business.status.approved')
                . "," . Phpfox::getService('directory.helper')->getConst('business.status.running')
                . "," . Phpfox::getService('directory.helper')->getConst('business.status.completed')
                . ")")
            ->execute("getSlaveField");

        return $iCount;
    }

    public function countTotalFavoriteBusinessByUserId($iUserId)
    {
        $iCount = $this->database()
            ->select("COUNT(DISTINCT(dbus.business_id)) ")
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->join(Phpfox::getT('directory_category'), 'dc', 'dc.category_id = dcd.category_id')
            ->join(Phpfox::getT("directory_business_location"), 'dbl', 'dbl.business_id = dbus.business_id')
            ->join(Phpfox::getT("directory_favorite"), 'dfv', 'dfv.business_id = dbus.business_id')
            ->join(Phpfox::getT("user"), 'u', 'dbus.user_id =  u.user_id')
            ->where('dfv.user_id = ' . (int)$iUserId . ' AND dbus.business_status IN ' . "("
                . Phpfox::getService('directory.helper')->getConst('business.status.approved')
                . "," . Phpfox::getService('directory.helper')->getConst('business.status.running')
                . "," . Phpfox::getService('directory.helper')->getConst('business.status.completed')
                . ")")
            ->execute("getSlaveField");

        return $iCount;
    }


    public function doComparisonField($aFields)
    {
        $aFieldStatus = array();
        foreach ($aFields as $keyaFields => $valueaFields) {
            switch ($valueaFields['comparison_id']) {
                case '1':
                    $aFieldStatus['ratings'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['ratings'] = true;
                    }
                    break;
                case '2':
                    $aFieldStatus['members'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['members'] = true;
                    }
                    break;
                case '3':
                    $aFieldStatus['follower'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['follower'] = true;
                    }
                    break;
                case '4':
                    $aFieldStatus['reviews'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['reviews'] = true;
                    }
                    break;
                case '5':
                    $aFieldStatus['address'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['address'] = true;
                    }
                    break;
                case '6':
                    $aFieldStatus['website'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['website'] = true;
                    }
                    break;
                case '7':
                    $aFieldStatus['phone'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['phone'] = true;
                    }
                    break;
                case '8':
                    $aFieldStatus['operating_hours'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['operating_hours'] = true;
                    }
                    break;
                case '9':
                    $aFieldStatus['custom_field'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['custom_field'] = true;
                    }
                    break;
                case '10':
                    $aFieldStatus['short_description'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['short_description'] = true;
                    }
                    break;
                case '11':
                    $aFieldStatus['latest_reviews'] = false;
                    if ($valueaFields['is_active']) {
                        $aFieldStatus['latest_reviews'] = true;
                    }
                    break;
            }
        }

        return $aFieldStatus;
    }

    public function getSettingByIds($ids = array())
    {
        $sIds = implode(',', $ids);
        $sIds = trim($sIds, ',');

        if (empty($sIds)) {
            return array();
        }

        return $this->database()
            ->select("pks.*")
            ->from(Phpfox::getT("directory_package_setting"), 'pks')
            ->where('pks.setting_id IN (' . $sIds . ')')
            ->execute("getSlaveRows");
    }

    public function getModuleViewInBusiness($iBusinessId, $aBusiness = null)
    {
        if (null == $aBusiness) {
            $aBusiness = Phpfox::getService('directory')->getBusinessById($iBusinessId);
        }
        // echo '<pre>';
        // print_r($aBusiness['business_id']);die;
        $sView = '';
        $sViewPage = 0;
        // get data for menu (which is as same as dashboard)
        $aModules = Phpfox::getService('directory')->getPageModuleForManage($aBusiness['business_id']);
        $aModuleView = array();
        $IsModuleActive = false;

        foreach ($aModules[0] as $iModuleId => $aModule) {
            $aItem = Phpfox::getService('directory')->getPageByBusinessModuleId($aBusiness['business_id'], $iModuleId);
            if (isset($aItem['module_name'])) {
                $aModuleView[$aItem['module_name']] = $aItem;

                $sTitle = $aBusiness['name'];
                if (!empty($sTitle)) {
                    if (preg_match('/\{phrase var\=(.*)\}/i', $sTitle, $aMatches) && isset($aMatches[1])) {
                        $sTitle = str_replace(array("'", '"', '&#039;'), '', $aMatches[1]);
                        $sTitle = _p($sTitle);
                    }

                    $sTitle = Phpfox::getLib('url')->cleanTitle($sTitle);
                }

                $aModuleView[$aItem['module_name']]['link'] = Phpfox::getLib('url')->makeUrl('directory.detail' . '.' . $aBusiness['business_id'] . '.' . $sTitle . '.' . $aItem['module_name']);

                $aModuleView[$aItem['module_name']]['active'] = false;
                if ($sView == '' && $sViewPage == 0 && $aModuleView[$aItem['module_name']]['module_landing']) {
                    $aModuleView[$aItem['module_name']]['active'] = true;
                    $IsModuleActive = true;
                } else
                    if ($sView == $aItem['module_name']) {
                        $aModuleView[$aItem['module_name']]['active'] = true;
                        $IsModuleActive = true;

                    }
            }
        }

        return $aModuleView;
    }

    public function executeCron()
    {
        $aCronLogDefault = $this->database()
            ->select("cronlog.*")
            ->from(Phpfox::getT("directory_cronlog"), 'cronlog')
            ->where('cronlog.type = \'default\'')
            ->order('cronlog.cronlog_id DESC')
            ->limit(1)
            ->execute("getSlaveRow");
        $oldRunTimestamp = 0;
        $newRunTimestamp = PHPFOX_TIME;
        if (isset($aCronLogDefault['cronlog_id'])) {
            $oldRunTimestamp = (int)$aCronLogDefault['timestamp'];
        }
        $this->database()->insert(Phpfox::getT('directory_cronlog'), array(
            'type' => 'default',
            'timestamp' => (int)$newRunTimestamp,
        ));

        $this->cronUpdateBusinessStatus($oldRunTimestamp, $newRunTimestamp);
        $this->cronSendSubscribeMail($oldRunTimestamp, $newRunTimestamp);
        $this->cronSendMailInQueue($oldRunTimestamp, $newRunTimestamp);
        $this->cronSendNotification($oldRunTimestamp, $newRunTimestamp);
    }

    public function cronUpdateBusinessStatus($oldRunTimestamp, $newRunTimestamp)
    {
        // get business with {approve, running} and update status with below logic:
        // 		approve --> running
        // 		running --> expire
        $sConditions = ' dbus.business_status IN (' . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running') . ") ";

        $aBusinesses = $this->database()->select('dbus.*')
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->where($sConditions)
            ->execute('getSlaveRows');

        foreach ($aBusinesses as $keyaBusinesses => $valueaBusinesses) {
            $this->checkAndUpdateStatus($valueaBusinesses);
        }
    }

    public function cronSendSubscribeMail($oldRunTimestamp, $newRunTimestamp)
    {
        // get all subscribe emails
        // each emails, do:
        // 		get all businesses which are appropriated with conditions
        // 		send mail

        $aSubscribeEmail = $this->database()->select('dbus.*')
            ->from(Phpfox::getT("directory_business_subscribe"), 'dbus')
            ->execute('getSlaveRows');
        foreach ($aSubscribeEmail as $keyaSubscribeEmail => $valueaSubscribeEmail) {
            $data = (array)json_decode($valueaSubscribeEmail['data']);
            $aConds = array();
            if (!empty($data['categories'])) {
                $aConds[] = " AND cad.category_id IN (" . trim($data['categories'], ',') . ")";
            }

            if (!empty($data['location_lat']) && !empty($data['location_lng']) && !empty($data['radius'])
                && floatval($data['location_lat']) > 0 && floatval($data['location_lng']) > 0 && floatval($data['radius']) > 0
            ) {
                $rangevaluefrom = Phpfox::getLib('parse.input')->prepare($data['radius']);
                preg_match("/[0-9]*/", $rangevaluefrom, $kq);
                if ($kq == null || strlen(trim($kq[0])) < strlen(trim($rangevaluefrom))) {
                    $aConds[] = " AND (1=0)";
                } else {
                    $rangevaluefrom = floatval($rangevaluefrom);
                    // $rangetype = Phpfox::getLib('request')->get('rangetype');
                    $glat = floatval($data['location_lat']);
                    $glong = floatval($data['location_lng']);

                    // if ($rangetype == 1)
                    // {
                    //     // 1km = (1000 / 1609) miles = 0.6215 miles
                    //     $rangevaluefrom = $rangevaluefrom * 0.6215;
                    // }
                    // elseif($rangetype == 0)
                    // {
                    //     //$rangevaluefrom = $rangevaluefrom * 1609;
                    //     $rangevaluefrom = $rangevaluefrom;
                    // }

                    $aConds[] = " AND (
	                        (3959 * acos(
	                                cos( radians('{$glat}'))
	                                * cos( radians( dbl.location_latitude ) )
	                                * cos( radians( dbl.location_longitude ) - radians('{$glong}') )
	                                + sin( radians('{$glat}') ) * sin( radians( dbl.location_latitude ) )
	                            ) < {$rangevaluefrom}
	                        )
	                    )";

                }
            }

            $sWhere = '1=1';
            if (count($aConds) > 0) {
                $sCond = implode('  ', $aConds);
                $sWhere .= ' ' . $sCond;
            }
            $aBusinesses = $this->database()->select('dbus.*')
                ->from(Phpfox::getT("directory_category_data"), 'cad')
                ->join(Phpfox::getT('directory_business'), 'dbus', 'dbus.business_id = cad.business_id AND dbus.time_stamp >= ' . (int)$oldRunTimestamp . ' AND dbus.business_status IN (' . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running') . ") ")
                ->join(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = cad.business_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
                ->where($sWhere)
                ->group('dbus.business_id')
                ->execute('getSlaveRows');
            foreach ($aBusinesses as $keyaBusinesses => $valueaBusinesses) {
                $aBusinesses[$keyaBusinesses]['aCategories'] = Phpfox::getService('directory.category')->getForBrowseByBusinessId($valueaBusinesses['business_id']);

                $aBusinessLocation = $this->database()->select('dbl.location_title,dbl.location_longitude,dbl.location_latitude, dbl.location_address')
                    ->from($this->_sTable, 'dbus')
                    ->leftJoin(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = dbus.business_id')
                    ->where('dbus.business_id = ' . $valueaBusinesses['business_id'])
                    ->order('location_id ASC')
                    ->execute('getSlaveRows');
                $aBusinesses[$keyaBusinesses]['aBusinessLocation'] = $aBusinessLocation;
            }

            if (count($aBusinesses) > 0) {
                $subject = _p('directory.businesses_you_may_interested_in_site_name', array(
                    'site_name' => Phpfox::getParam('core.site_title'),
                ));
                $email = $valueaSubscribeEmail['email'];
                $data_business = '';
                foreach ($aBusinesses as $keyaBusinesses => $valueaBusinesses) {
                    $name = Phpfox::getLib('phpfox.parse.output')->clean($valueaBusinesses['name']);

                    $sTextCategories = "";
                    foreach ($valueaBusinesses['aCategories'] as $key_category => $aCategory) {
                        $sTextCategories .= ' ' . $aCategory['title'];
                        if (isset($aCategory['sub']) && count($aCategory['sub'])) {
                            foreach ($aCategory['sub'] as $key_sub_category => $aSubCategory) {
                                $sTextCategories .= ' >> ' . $aSubCategory['title'];
                            }
                        }
                        $sTextCategories .= ' |';
                    }
                    $sTextCategories = trim($sTextCategories);
                    $sTextCategories = rtrim($sTextCategories, '|');

                    $location = '';
                    foreach ($valueaBusinesses['aBusinessLocation'] as $keyaBusinessLocation => $valueaBusinessLocation) {
                        $location .= $valueaBusinessLocation['location_title'] . ' - ' . $valueaBusinessLocation['location_address'] . ' | ';
                    }
                    $location = trim($location);
                    $location = rtrim($location, '|');

                    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $valueaBusinesses['business_id'], $valueaBusinesses['name']);

                    $data = '';
                    $data .= '- <a href="' . $sLink . '">' . $name . '</a><br />';
                    $data .= _p('directory.category') . ': ' . $sTextCategories . '<br />';
                    $data .= _p('directory.location') . ': ' . $location . '<br /><br />';

                    $data_business .= $data;
                }
                $message = _p('directory.dear_sir_or_madam_here_are_businesses_you_may_interested_in_site_name_data_business_regards_site_name', array(
                    'site_name' => Phpfox::getParam('core.site_title'),
                    'data_business' => $data_business,
                ));

                Phpfox::getService('directory.mail.send')->send($subject, $message, $email);
            }
        }
    }

    public function cronSendMailInQueue($oldRunTimestamp, $newRunTimestamp)
    {
        // for now, email types are sent directly (NOT adding in queue)
        // so we do NOT implement this function
    }

    public function cronSendNotification($oldRunTimestamp, $newRunTimestamp)
    {
        // calculate:
        // 		. oneDay : get running business with {status = running} and {package_end_time <= oneDay} and {setting is one DAY}
        // 		. oneWeek : get running business with {status = running} and {package_end_time <= oneWeek} and {setting is one WEEK}
        // 		. oneMonth : get running business with {status = running} and {package_end_time <= oneMonth} and {setting is one MONTH}
        $interval = 0;
        $oneDay = $newRunTimestamp + (1 * 24 * 60 * 60);
        $aOneDayBusinesses = $this->database()->select('dbus.*')
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->where('dbus.renewal_type = 1 AND dbus.is_send_renewal = 0 AND dbus.package_start_time <= ' . (int)$newRunTimestamp . ' AND dbus.package_end_time >= ' . (int)$newRunTimestamp . ' AND dbus.package_end_time <= ' . (int)$oneDay)
            ->execute('getSlaveRows');

        $oneWeek = $newRunTimestamp + (7 * 1 * 24 * 60 * 60);
        $aOneWeekBusinesses = $this->database()->select('dbus.*')
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->where('dbus.renewal_type = 2 AND dbus.is_send_renewal = 0 AND dbus.package_start_time <= ' . (int)$newRunTimestamp . ' AND dbus.package_end_time >= ' . (int)$newRunTimestamp . ' AND dbus.package_end_time <= ' . (int)$oneWeek)
            ->execute('getSlaveRows');

        $oneMonth = $newRunTimestamp + (30 * 1 * 24 * 60 * 60);
        $aOneMonthBusinesses = $this->database()->select('dbus.*')
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->where('dbus.renewal_type = 3 AND dbus.is_send_renewal = 0 AND dbus.package_start_time <= ' . (int)$newRunTimestamp . ' AND dbus.package_end_time >= ' . (int)$newRunTimestamp . ' AND dbus.package_end_time <= ' . (int)$oneMonth)
            ->execute('getSlaveRows');

        $aBusinesses = array_merge($aOneDayBusinesses, $aOneWeekBusinesses, $aOneMonthBusinesses);
        foreach ($aBusinesses as $key => $value) {
            Phpfox::getService('notification.process')->add('directory_expirenotify', $value['business_id'], $value['user_id'], 1);
            $this->database()->update(Phpfox::getT('directory_business')
                , array(
                    'is_send_renewal' => 1,
                )
                , 'business_id = ' . $value['business_id']
            );
        }
    }

    public function getCheckinhereList($iBusinessId)
    {
        $aRows = $this->database()
            ->select("cih.*, " . Phpfox::getUserField())
            ->from(Phpfox::getT("directory_checkinhere"), 'cih')
            ->join(Phpfox::getT("user"), 'u', 'cih.user_id =  u.user_id')
            ->where('cih.business_id = ' . (int)$iBusinessId)
            ->execute("getSlaveRows");

        return $aRows;
    }

    public function countCheckinhere($iBusinessId)
    {
        $iCount = $this->database()
            ->select("COUNT(cih.checkinhere_id)")
            ->from(Phpfox::getT("directory_checkinhere"), 'cih')
            ->join(Phpfox::getT("user"), 'u', 'cih.user_id =  u.user_id')
            ->where('cih.business_id = ' . (int)$iBusinessId)
            ->execute("getSlaveField");

        return $iCount;
    }

    public function getCheckinhere($iBusinessId, $iUserId)
    {
        return $this->database()->select('cih.*')
            ->from(Phpfox::getT('directory_checkinhere'), 'cih')
            ->where('cih.business_id = ' . (int)$iBusinessId . ' AND cih.user_id = ' . (int)$iUserId)
            ->execute('getSlaveRow');
    }

    public function getBadgeCode($sFrameUrl)
    {
        return '<iframe src="' . $sFrameUrl . '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:250px;" allowTransparency="true"></iframe>';
    }

    public function getFrameUrl($iBusinessId)
    {
        $sFrameUrl = $this->getStaticPath() . 'module/directory/static/directory-badge.php?business_id=' . $iBusinessId;

        return $sFrameUrl;
    }

    public function processPollRows(&$aPolls2)
    {
        $oDatabase = Phpfox::getLib('database');
        $aPolls = $aPolls2;
        $aPolls2 = array();

        // we "implode" the poll_ids to run only one query on the DB to get the
        // answers
        $aAnswers = array();
        if (count($aPolls) > 0) {
            $sPolls = '';
            foreach ($aPolls as $aPoll) {
                $sPolls .= $aPoll['poll_id'] . ',';
            }
            $sPolls = rtrim($sPolls, ',');

            $aAnswers = $oDatabase->select('pa.*, pr.user_id as voted')
                ->from(Phpfox::getT('poll_answer'), 'pa')
                ->where('pa.poll_id IN(' . $sPolls . ')')
                ->leftjoin(Phpfox::getT('poll_result'), 'pr', 'pr.answer_id = pa.answer_id AND pr.user_id = ' . Phpfox::getUserId())
                ->order('pa.ordering ASC')
                ->execute('getSlaveRows');
        }

        // now merge both arrays by their poll_id and add the count for the total votes
        $iTotalVotes = 0;
        $aTotalVotes = array();
        foreach ($aAnswers as $aAnswer) {
            if ($aAnswer['total_votes'] > 0) {
                if (isset($aTotalVotes[$aAnswer['poll_id']])) {
                    $aTotalVotes[$aAnswer['poll_id']] += $aAnswer['total_votes'];//$aTotalVotes[$aAnswer['poll_id']]+1;
                } else {
                    $aTotalVotes[$aAnswer['poll_id']] = $aAnswer['total_votes'];
                }
            }
        }

        foreach ($aPolls as $iKey => $aPoll) {
            $aPoll['aFeed'] = array(
                'feed_display' => 'mini',
                'comment_type_id' => 'poll',
                'privacy' => $aPoll['privacy'],
                'comment_privacy' => $aPoll['privacy_comment'],
                'like_type_id' => 'poll',
                'feed_is_liked' => (isset($aPoll['is_liked']) ? $aPoll['is_liked'] : false),
                'feed_is_friend' => (isset($aPoll['is_friend']) ? $aPoll['is_friend'] : false),
                'item_id' => $aPoll['poll_id'],
                'user_id' => $aPoll['user_id'],
                'total_comment' => $aPoll['total_comment'],
                'feed_total_like' => $aPoll['total_like'],
                'total_like' => $aPoll['total_like'],
                'feed_link' => Phpfox::permalink('poll', $aPoll['poll_id'], $aPoll['question']),
                'feed_title' => $aPoll['question'],
                'type_id' => 'poll'
            );

            $aPolls2[$aPoll['poll_id']] = $aPoll;

            if (isset($aPoll['poll_id']['user_id']) && $aPoll['poll_id']['user_id'] == Phpfox::getUserId()) {
                $aPolls2[$aPoll['poll_id']]['user_voted_this_poll'] = 'true';
            } else {
                $aPolls2[$aPoll['poll_id']]['user_voted_this_poll'] = 'false'; // this could be tricky, test and see if it works everywhere
            }

            if (!isset($aPolls2[$aPoll['poll_id']]['total_votes'])) {
                $aPolls2[$aPoll['poll_id']]['total_votes'] = 0;
            }

            foreach ($aAnswers as &$aAnswer) { // we add the total votes for the poll

                if (!isset($aAnswer['vote_percentage'])) {
                    $aAnswer['vote_percentage'] = 0;
                }
                if (!isset($aAnswer['total_votes'])) {
                    $aAnswer['total_votes'] = 0;
                }
                // Normalize if user voted this answer or not
                if (isset($aAnswer['voted']) && $aAnswer['voted'] == Phpfox::getUserId()) {
                    $aAnswer['user_voted_this_answer'] = 1;
                } else {
                    $aAnswer['user_voted_this_answer'] = 2;
                }
                if ($aPoll['poll_id'] == $aAnswer['poll_id']) {
                    if ((isset($aTotalVotes[$aAnswer['poll_id']]) && $aTotalVotes[$aAnswer['poll_id']] > 0)) {
                        $aAnswer['vote_percentage'] = round(($aAnswer['total_votes'] / $aTotalVotes[$aAnswer['poll_id']]) * 100);
                    } else {
                        $aAnswer['vote_percentage'] = 0;
                    }

                    $aPolls2[$aPoll['poll_id']]['answer'][$aAnswer['answer_id']] = $aAnswer;

                    $aPolls2[$aPoll['poll_id']]['total_votes'] += $aAnswer['total_votes'];
                }
            }

            if ($aPoll['randomize'] == 1 && !empty($aPolls2[$aPoll['poll_id']]['answer'])) {
                shuffle($aPolls2[$aPoll['poll_id']]['answer']);
            }
        }

        unset($aPolls);
    }

    public function processEventRows(&$aRows)
    {
        if (Phpfox::getService('directory.helper')->isAdvEvent()) {

            $oHelper = Phpfox::getService('fevent.helper');

            $len = count($aRows);
            $formatTime = Phpfox::getParam('fevent.fevent_browse_time_stamp');
            for ($i = 0; $i < $len; $i++) {
                $aRows[$i]['d_type'] = $oHelper->getTimeLineStatus($aRows[$i]['start_time'], $aRows[$i]['end_time']);
                if ('upcoming' == $aRows[$i]['d_type']) {
                    $aRows[$i]['d_start_in'] = $oHelper->timestampToCountdownString($aRows[$i]['start_time'], 'upcoming');
                }
                if ('ongoing' == $aRows[$i]['d_type']) {
                    $aRows[$i]['d_left'] = $oHelper->timestampToCountdownString($aRows[$i]['end_time'], 'ongoing');
                }

                if ((int)$aRows[$i]['isrepeat'] >= 0) {
                    $aRows[$i]['d_repeat_time'] = $oHelper->displayRepeatTime((int)$aRows[$i]['isrepeat'], (int)$aRows[$i]['timerepeat']);
                }

                $aRows[$i]['d_start_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aRows[$i]['start_time']);
                //  any status event (upcoming, ongoing, past) has start time
                //  with: upcoming event: start time at this time is next start time
                $aRows[$i]['d_next_start_time'] = $aRows[$i]['d_start_time'];
                $aRows[$i]['d_end_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aRows[$i]['end_time']);
                $aRows[$i]['short_end_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aRows[$i]['end_time']);
                $aRows[$i]['url'] = Phpfox::getLib('url')->permalink('fevent', $aRows[$i]['event_id'], $aRows[$i]['title']);
                $aRows[$i]['start_time_micro'] = Phpfox::getTime('Y-m-d', $aRows[$i]['start_time']);
                $aRows[$i]['start_time_phrase'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_browse_time_stamp'), $aRows[$i]['start_time']);
                $aRows[$i]['start_time_phrase_stamp'] = Phpfox::getTime('g:sa', $aRows[$i]['start_time']);
                $aRows[$i]['url_photo'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aRows[$i]['server_id'],
                        'file' => $aRows[$i]['image_path'],
                        'path' => 'event.url_image',
                        'suffix' => '',
                        'max_width' => '240',
                        'max_height' => '240',
                        'return_url' => true
                    )
                );
                if (empty($aRows[$i]['image_path']) || !$aRows[$i]['url_photo'] || $aRows[$i]['url_photo'] == '<span class="no_image_item i_size_"><span></span></span>') {
                    $aRows[$i]['url_photo'] = $this->getStaticPath() . 'module/directory/static/image/default_events.png';
                }
            }
        } else {

            $oHelper = Phpfox::getService('directory.helper');

            $len = count($aRows);
            $formatTime = Phpfox::getParam('event.event_browse_time_stamp');
            for ($i = 0; $i < $len; $i++) {
                $aRows[$i]['d_type'] = $oHelper->getTimeLineStatus($aRows[$i]['start_time'], $aRows[$i]['end_time']);
                if ('upcoming' == $aRows[$i]['d_type']) {
                    $aRows[$i]['d_start_in'] = $oHelper->timestampToCountdownString($aRows[$i]['start_time'], 'upcoming');
                }
                if ('ongoing' == $aRows[$i]['d_type']) {
                    $aRows[$i]['d_left'] = $oHelper->timestampToCountdownString($aRows[$i]['end_time'], 'ongoing');
                }

                $aRows[$i]['isrepeat'] = -1;
                $aRows[$i]['d_start_time'] = $oHelper->displayTimeByFormat('M d, Y', (int)$aRows[$i]['start_time']);
                //  any status event (upcoming, ongoing, past) has start time
                //  with: upcoming event: start time at this time is next start time
                $aRows[$i]['d_next_start_time'] = $aRows[$i]['d_start_time'];
                $aRows[$i]['d_end_time'] = $oHelper->displayTimeByFormat('M d, Y', (int)$aRows[$i]['end_time']);
                $aRows[$i]['start_time_month'] = Phpfox::getTime('F', $aRows[$i]['start_time']);
                $aRows[$i]['start_time_short_month'] = Phpfox::getTime('M', $aRows[$i]['start_time'], true, true);
                $aRows[$i]['start_time_short_day'] = Phpfox::getTime('j', $aRows[$i]['start_time']);
                $aRows[$i]['start_time_phrase'] = Phpfox::getTime('l, F j', $aRows[$i]['start_time']);
                $aRows[$i]['start_time_micro'] = Phpfox::getTime('M d, Y', $aRows[$i]['start_time'], true, true);
                $aRows[$i]['start_time_phrase_stamp'] = Phpfox::getTime('g:ia', $aRows[$i]['start_time']);
                $aRows[$i]['url'] = Phpfox::getLib('url')->permalink('event', $aRows[$i]['event_id'], $aRows[$i]['title']);
                $aRows[$i]['url_photo'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aRows[$i]['server_id'],
                        'file' => $aRows[$i]['image_path'],
                        'path' => 'event.url_image',
                        'suffix' => '',
                        'max_width' => '240',
                        'max_height' => '240',
                        'return_url' => true
                    )
                );
                if ($aRows[$i]['url_photo'] == '<span class="no_image_item i_size_240"><span></span></span>') {
                    $aRows[$i]['url_photo'] = $this->getStaticPath() . 'module/directory/static/image/default_ava.png';
                }
            }
        }
    }

    public function getStaticPath()
    {
        return $core_path = Phpfox::getParam('core.path_file');
    }

    public function processJobsRows($aRows)
    {
        foreach ($aRows as $key => $aRow) {
            $aRow['post_date_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']);
            $aRows[$key] = $aRow;
        }

        return $aRows;
    }

    public function processMarketplaceRows(&$aRows)
    {
        if (Phpfox::getService('directory.helper')->isAdvMarketplace()) {
            foreach ($aRows as $iKey => $aListing) {
                $aRows[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aListing['listing_id'], $aListing['title']);
            }
        } else {
            foreach ($aRows as $iKey => $aListing) {
                $aRows[$iKey]['url'] = Phpfox::getLib('url')->permalink('marketplace', $aListing['listing_id'], $aListing['title']);
            }
        }
    }

    public function getNewestItemInBusiness($iBusinessId, $type)
    {
        $iCount = 0;
        $aItem = array();
        $aConds = array(' 1=1 ');

        $iItemPerPage = 4;
        $aExtra = array(
            'limit' => $iItemPerPage,
            'page' => (1 - 1) * $iItemPerPage,
        );

        $getData = true;

        switch ($type) {
            case 'photos':
                if (Phpfox::getService('directory.helper')->isPhoto()) {
                    $aExtra['order'] = "photo.photo_id DESC";
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdPhoto();
                    list($aItem, $iCount) = $this->getPhotoByBusinessId($iBusinessId, $aConds, $aExtra, $getData);


                    foreach ($aItem as $iKey => $item) {
                        if (isset($aItem[$iKey]['photo_id'])) {
                            $aItem[$iKey]['link'] = Phpfox::permalink($sModuleId, $aItem[$iKey]['photo_id'],
                                $aItem[$iKey]['title']);
                            $aItem[$iKey]['destination'] = Phpfox::getService($sModuleId)->getPhotoUrl($aItem[$iKey]);
                        }
                    }
                }
                break;
            case 'videos':
                if (Phpfox::getService('directory.helper')->isVideoChannel()) {
                    $aExtra['order'] = "m.video_id DESC";
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdVideo();
                    list($aItem, $iCount) = $this->getVideoByBusinessId($iBusinessId, $aConds, $aExtra, $getData);

                    foreach ($aItem as $iKey => $value) {
                        $this->convertImagePath($aItem[$iKey], 120);
                    }
                }
                break;
            case 'v':
                if (Phpfox::getService('directory.helper')->isVideo()) {
                    $aExtra['order'] = "m.video_id DESC";
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdV();
                    list($aItem, $iCount) = $this->getVByBusinessId($iBusinessId, $aConds, $aExtra, $getData);

                    foreach ($aItem as $iKey => $value) {
                        Phpfox::getService('v.video')->convertImagePath($aItem[$iKey]);
                        $aItem[$iKey]['duration'] = $this->getDuration($aItem[$iKey]['duration']);
                    }

                }
                break;
            case 'musics':
                if (Phpfox::getService('directory.helper')->isMusic()) {
                    $aExtra['order'] = "m.song_id DESC";
                    list($aItem, $iCount) = $this->getMusicByBusinessId($iBusinessId, $aConds, $aExtra, $getData);

                    foreach ($aItem as $iKey => $value) {
                        $aItem[$iKey]['song_path'] = Phpfox::getService('music')->getSongPath($aItem[$iKey]['song_path'], $aItem[$iKey]['server_id']);
                    }
                }
                break;
            case 'blogs':
                if (Phpfox::getService('directory.helper')->isBlog()) {
                    $aExtra['order'] = "blog.blog_id DESC";
                    list($aItem, $iCount) = $this->getBlogByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'ynblog':
                if (Phpfox::getService('directory.helper')->isAdvBlog()) {
                    $aExtra['order'] = "blog.blog_id DESC";
                    list($aItem, $iCount) = $this->getAdvBlogByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'polls':
                if (Phpfox::getService('directory.helper')->isPoll()) {
                    $aExtra['order'] = "poll.poll_id DESC";
                    list($aItem, $iCount) = $this->getPollByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                    $this->processPollRows($aItem);
                }
                break;
            case 'coupons':
                if (Phpfox::getService('directory.helper')->isCoupon()) {
                    $aExtra['order'] = "c.coupon_id DESC";
                    list($aItem, $iCount) = $this->getCouponByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                    if (isset($aItem['coupon_id'])) {
                        $aItem = Phpfox::getService('coupon')->retrieveMoreInfoFromCoupon($aItem);
                    }
                }
                break;
            case 'events':
                if (Phpfox::getService('directory.helper')->isEvent()) {
                    $aExtra['order'] = "m.event_id DESC";
                    list($aItem, $iCount) = $this->getEventByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                    $this->processEventRows($aItem);
                    break;
                }
                break;
            case 'jobs':
                if (Phpfox::getService('directory.helper')->isJob()) {
                    $aExtra['order'] = "job.job_id DESC";
                    list($aItem, $iCount) = $this->getJobByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                    $aItem = $this->processJobsRows($aItem);
                }
                break;
            case 'marketplace':
                if (Phpfox::getService('directory.helper')->isMarketplace()) {
                    $aExtra['order'] = "l.listing_id DESC";
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdMarketplace();
                    list($aItem, $iCount) = $this->getMarketplaceByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                    $this->processMarketplaceRows($aItem);
//                    $aItem = ((isset($aItem[0])) ? $aItem[0] : array());
                }
                break;
            case 'ultimatevideo':
                if (Phpfox::getService('directory.helper')->isUltVideo()) {
                    $aExtra['order'] = "m.video_id DESC";
                    $sModuleId = Phpfox::getService('directory.helper')->getModuleIdUltimateVideo();
                    list($aItem, $iCount) = $this->getUltimateVideoByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                    foreach ($aItem as $iKey => $value) {
                        $this->convertImagePath($aItem[$iKey], 120);
                        $aItem[$iKey]['duration'] = $this->getDuration($aItem[$iKey]['duration']);
                    }
                }
                break;                
        }

        return $aItem;
    }

    public function convertImagePath(&$aRow, $iSize)
    {
        $aRow['image_path'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aRow['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $aRow['image_path'],
                'suffix' => '_' . $iSize,
                'return_url' => true
            )
        );
    }

    public function getDuration($value)
    {
        $value = intval($value);

        if ($value <= 0) {
            return '';
        }

        $hour = floor($value / 3600);
        $min = floor(($value - $hour * 3600) / 60);
        $second = $value - $hour * 3600 - $min * 60;
        $result = [];

        if ($hour) {
            $result[] = str_pad($hour, 2, '0', STR_PAD_LEFT);
        }
        $result[] = str_pad($min, 2, '0', STR_PAD_LEFT);
        $result[] = str_pad($second, 2, '0', STR_PAD_LEFT);

        return implode(':', $result);
    }

    public function getNumberOfItemInBusiness($iBusinessId, $type)
    {
        $iCount = 0;
        $aConds = array(' 1=1 ');
        $aExtra = array();
        $getData = false;

        switch ($type) {
            case 'photos':
                if (Phpfox::getService('directory.helper')->isPhoto()) {
                    $iCount = $this->getPhotoByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'videos':
                if (Phpfox::getService('directory.helper')->isVideoChannel()) {
                    $iCount = $this->getVideoByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'musics':
                if (Phpfox::getService('directory.helper')->isMusic()) {
                    $iCount = $this->getMusicByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'blogs':
                if (Phpfox::getService('directory.helper')->isBlog()) {
                    $iCount = $this->getBlogByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'ynblog':
                if (Phpfox::getService('directory.helper')->isAdvBlog()) {
                    $iCount = $this->getAdvBlogByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'polls':
                if (Phpfox::getService('directory.helper')->isPoll()) {
                    $iCount = $this->getPollByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'coupons':
                if (Phpfox::getService('directory.helper')->isCoupon()) {
                    $iCount = $this->getCouponByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'events':
                if (Phpfox::getService('directory.helper')->isEvent()) {
                    $iCount = $this->getEventByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'jobs':
                if (Phpfox::getService('directory.helper')->isJob()) {
                    $iCount = $this->getJobByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'marketplace':
                if (Phpfox::getService('directory.helper')->isMarketplace()) {
                    $iCount = $this->getMarketplaceByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'ultimatevideo':
                if (Phpfox::getService('directory.helper')->isUltVideo()) {
                    $iCount = $this->getUltimateVideoByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
            case 'v':
                if (Phpfox::getService('directory.helper')->isVideo()) {
                    $iCount = $this->getVByBusinessId($iBusinessId, $aConds, $aExtra, $getData);
                }
                break;
        }

        return $iCount;
    }

    public function processNotificationPostItem($sType, $iItemId, $iPrivacy = 0, $iPrivacyComment = 0, $iParentUserId = 0, $iOwnerUserId = null, $bIsTag = 0, $iParentFeedId = 0, $sParentModuleName = null)
    {
        $iBusinessId = null;
        $aBusiness = null;
        switch ($sType) {
            case 'blog':
                $aBlog = $this->database()
                    ->select('*')
                    ->from(Phpfox::getT("blog"), 'blog')
                    ->where('blog.blog_id = ' . (int)$iItemId)
                    ->execute("getSlaveRow");

                if (isset($aBlog['blog_id']) && $aBlog['module_id'] == 'directory') {
                    $iBusinessId = (int)$aBlog['item_id'];
                    $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
                }
                break;

            case 'music_song':
                $aSong = $this->database()
                    ->select('*')
                    ->from(Phpfox::getT("music_song"), 'music_song')
                    ->where('music_song.song_id = ' . (int)$iItemId)
                    ->execute("getSlaveRow");
                if (isset($aSong['song_id']) && $aSong['module_id'] == 'directory') {
                    $iBusinessId = (int)$aSong['item_id'];
                    $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
                }
                break;

            case 'coupon':
                if ((int)$iParentUserId > 0) {
                    $aCoupon = $this->database()
                        ->select('*')
                        ->from(Phpfox::getT("coupon"), 'coupon')
                        ->where('coupon.coupon_id = ' . (int)$iItemId)
                        ->execute("getSlaveRow");
                    if (isset($aCoupon['coupon_id']) && $aCoupon['module_id'] == 'directory') {
                        $iBusinessId = (int)$aCoupon['item_id'];
                        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
                    }
                }
                break;
        }

        if ($iBusinessId != null && isset($aBusiness['business_id'])) {
            if ($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved')
                || $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running')
                || $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.completed')
            ) {
                // send notification to owner
                Phpfox::getService('notification.process')->add('directory_postitem', $iBusinessId, $aBusiness['user_id'], Phpfox::getUserId());
                // send notification to follower(s)
                $aFollowers = Phpfox::getService('directory')->getFollowerIds((int)$iBusinessId);
                foreach ($aFollowers as $keyaFollowers => $valueaFollowers) {
                    Phpfox::getService('notification.process')->add('directory_postitem', (int)$iBusinessId, $valueaFollowers['user_id'], Phpfox::getUserId());
                }
            }
        }
    }

    public function getCreatorByUserId($iUserId)
    {
        return $this->database()
            ->select('*')
            ->from(Phpfox::getT("directory_creator"), 'crt')
            ->where('crt.user_id = ' . (int)$iUserId)
            ->execute("getSlaveRow");
    }

    public function getTransactionBusiness($aConds = array(), $iPage = 0, $iLimit = NULL, $iCount = NULL, $sOrder = 'inv.time_stamp_paid DESC, inv.time_stamp DESC')
    {
        $sWhere = '';
        $sWhere .= '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        $iCount = $this->database()
            ->select("COUNT(inv.invoice_id)")
            ->from(Phpfox::getT("directory_invoice"), 'inv')
            ->join(Phpfox::getT("directory_business"), 'dbus', 'inv.item_id =  dbus.business_id')
            ->join(Phpfox::getT("user"), 'u', 'inv.user_id =  u.user_id')
            ->where($sWhere)
            ->execute("getSlaveField");

        $aTransaction = array();
        if ($iCount) {
            $aTransaction = $this->database()
                ->select("inv.*, dbus.business_id, dbus.name, dbus.package_name, " . Phpfox::getUserField())
                ->from(Phpfox::getT("directory_invoice"), 'inv')
                ->join(Phpfox::getT("directory_business"), 'dbus', 'inv.item_id =  dbus.business_id')
                ->join(Phpfox::getT("user"), 'u', 'inv.user_id =  u.user_id')
                ->where($sWhere)
                ->order($sOrder)
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getSlaveRows");
        }

        return array($iCount, $aTransaction);
    }

    public function getManageBusiness($aConds = array(), $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {
        $sWhere = '';
        $sWhere .= '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        $iCount = $this->database()
            ->select("COUNT(dbus.business_id)")
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT("user"), 'u', 'dbus.user_id =  u.user_id')
            ->join(Phpfox::getT("user"), 'u2', 'dbus.creator_id =  u2.user_id')
            ->join(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id AND dcd.is_main = 1')
            ->join(Phpfox::getT('directory_category'), 'dc', 'dc.category_id = dcd.category_id')
            ->where($sWhere)
            ->execute("getSlaveField");

        $aBusinesses = array();
        if ($iCount) {
            $aBusinesses = $this->database()
                ->select("dbus.*, dc.title as category_title, u2.user_id as creator_user_id, u2.profile_page_id as creator_profile_page_id, u2.server_id as creator_server_id, u2.user_name as creator_user_name, u2.full_name as creator_full_name, u2.gender as creator_gender, u2.user_image as creator_user_image, u2.is_invisible as creator_is_invisible, u2.language_id as creator_language_id, u2.language_id as creator_language_id, " . Phpfox::getUserField())
                ->from(Phpfox::getT("directory_business"), 'dbus')
                ->join(Phpfox::getT("user"), 'u', 'dbus.user_id =  u.user_id')
                ->join(Phpfox::getT("user"), 'u2', 'dbus.creator_id =  u2.user_id')
                ->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id AND dcd.is_main = 1')
                ->leftJoin(Phpfox::getT('directory_category'), 'dc', 'dc.is_active = 1 AND dc.category_id = dcd.category_id')
                ->where($sWhere)
                ->order('dbus.business_id DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getSlaveRows");

            foreach ($aBusinesses as $key => $aBusiness) {
                $aBusinesses[$key] = $this->retrieveMoreInfoFromBusiness($aBusinesses[$key], '');

                if ($aBusinesses[$key]['creator_user_id'] > 0) {
                    $aBusinesses[$key]['creator_data'] = array(
                        'user_id' => $aBusinesses[$key]['creator_user_id'],
                        'profile_page_id' => $aBusinesses[$key]['creator_profile_page_id'],
                        'user_server_id' => $aBusinesses[$key]['creator_server_id'],
                        'user_name' => $aBusinesses[$key]['creator_user_name'],
                        'full_name' => $aBusinesses[$key]['creator_full_name'],
                        'gender' => $aBusinesses[$key]['creator_gender'],
                        'user_image' => $aBusinesses[$key]['creator_user_image'],
                        'is_invisible' => $aBusinesses[$key]['creator_is_invisible'],
                        'language_id' => $aBusinesses[$key]['creator_language_id'],
                    );
                } else {
                    $aBusinesses[$key]['creator_data'] = false;
                }

                // update status in back end
                $this->checkAndUpdateStatus($aBusinesses[$key]);
            }
        }

        return array($iCount, $aBusinesses);
    }

    public function changePageWhenAccessingBusinessDetail($page)
    {
        switch ($page) {
            case 'add-comment':
                $page = 'activities';
                break;
        }

        return $page;
    }

    public function getClaimRequest($aConds = array(), $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {
        $sWhere = '';
        $sWhere .= '1=1';
        $sWhere .= ' AND dbus.type = "business"';
        $sWhere .= ' AND dbus.business_status = ' . (int)Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming');
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        $iCount = $this->database()
            ->select("COUNT(*)")
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT("user"), 'u', 'dbus.user_id =  u.user_id')
            ->where($sWhere)
            ->execute("getSlaveField");
        $aRows = array();
        if ($iCount) {
            $aRows = $this->database()
                ->select("dbus.*, " . Phpfox::getUserField())
                ->from(Phpfox::getT("directory_business"), 'dbus')
                ->join(Phpfox::getT("user"), 'u', 'dbus.user_id =  u.user_id')
                ->where($sWhere)
                ->order('dbus.timestamp_claimrequest ASC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getSlaveRows");
        }

        return array($iCount, $aRows);
    }

    public function getNumberBusinessByUser($iUserId)
    {
        if ($iUserId == Phpfox::getUserId()) {
            return $this->database()->select('COUNT(dbus.business_id)')->from(Phpfox::getT("directory_business"), 'dbus')->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id AND is_main = 1')->where('dbus.user_id = ' . $iUserId . " AND dbus.business_status != 8 AND dbus.module_id = 'directory'")->execute('getField');
        } else {
            $sStatus = Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running');
            return $this->database()->select('COUNT(*)')->from(Phpfox::getT("directory_business"), 'dbus')->join(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id AND is_main = 1')->where('dbus.user_id = ' . $iUserId . " AND dbus.business_status IN (" .$sStatus. ") AND dbus.module_id = 'directory'")->execute('getField');
        }
    }

    public function getCss()
    {
        $urlStyle = Phpfox::getParam('core.path_file') . 'theme/frontend/default/style/default/css/';

        $sCss = file_get_contents($urlStyle . 'layout.css');
        $sCss .= file_get_contents($urlStyle . 'custom.css');
        $sCss .= ' #main_content { margin-left: 0; } #right { display:none; } #content_holder { overflow:hidden; } .content3 { width: 100% !important; }';

        return $sCss;
    }

    public function getBusinessByIdInList($aBusinessId)
    {
        if (count($aBusinessId) == 0) {
            return array();
        }
        $sWhere = '';
        $inList = trim(implode(',', $aBusinessId), ',');
        $sWhere .= " AND db.business_id IN ({$inList}) ";
        $aRows = $this->database()
            ->select('db.*')
            ->from(Phpfox::getT("directory_business"), 'db')
            ->where('1=1' . $sWhere)
            ->execute("getSlaveRows");

        return $aRows;
    }

    public function getLastChildCategoryIdOfBusiness($iBusinessId)
    {

        $aCat = $this->database()->select('dct.category_id AS `parent_category_id`, dct.title AS `parent_title`')
            ->from(Phpfox::getT('directory_category_data'), 'dcd')
            ->join(Phpfox::getT('directory_category'), 'dct', 'dct.category_id = dcd.category_id')
            ->where('dcd.is_main = 1 AND dcd.business_id = ' . (int)$iBusinessId . ' AND dct.parent_id = 0')
            ->order('dct.parent_id DESC')
            ->limit(1)
            ->execute('getSlaveRows');

        if (isset($aCat[0]) && $aCat[0]['parent_category_id']) {
            /*if((int)$aCat[0]['child_category_id'] > 0){
	        	return array(
	        		'category_id' => $aCat[0]['child_category_id'],
	        		'title' => Phpfox::getLib('locale')->convert($aCat[0]['child_title']),
        		);
        	} else {*/
            return array(
                'category_id' => $aCat[0]['parent_category_id'],
                'title' => (Core\Lib::phrase()->isPhrase($aCat[0]['parent_title']) ? _p($aCat[0]['parent_title']) : Phpfox_Locale::instance()->convert($aCat[0]['parent_title'])),
            );
            //}
        } else {
            return false;
        }
    }

    public function getCouponByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('coupon');
        if (Phpfox::getService('directory.helper')->isAdvCoupon()) {
            // $table = Phpfox::getT('blog');
        }
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'c')
            ->where('c.module_id = \'directory\' AND c.privacy IN(0) AND c.is_removed = 0 AND ( (c.is_approved = 1 && c.is_draft = 0) || c.user_id = 1) AND c.item_id  = ' . (int)$iBusinessId . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    $this->database()->select('lik.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'lik', ' (lik.type_id = \'coupon\' AND lik.item_id = c.coupon_id AND lik.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("ct.description_parsed AS description, c.*, " . Phpfox::getUserField())
                    ->from($table, 'c')
                    ->join(Phpfox::getT('coupon_category_data'), 'ccd', 'ccd.coupon_id = c.coupon_id')
                    ->join(Phpfox::getT('coupon_text'), 'ct', 'ct.coupon_id = c.coupon_id')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
                    ->where('c.module_id = \'directory\' AND c.privacy IN(0) AND c.is_removed = 0 AND ( (c.is_approved = 1 && c.is_draft = 0) || c.user_id = 1) AND c.item_id  = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->group('c.coupon_id')
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getMarketplaceByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('marketplace');
        $core_module_id = 'marketplace';
        $module_id = 16;
        $table2 = Phpfox::getT('marketplace_text');
        $extraSelect = '';
        if (Phpfox::getService('directory.helper')->isAdvMarketplace()) {
            $table = Phpfox::getT('advancedmarketplace');
            $core_module_id = 'advancedmarketplace';
            $table2 = Phpfox::getT('advancedmarketplace_text');
            $extraSelect = ' , mt.short_description_parsed AS mini_description ';
        }
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'l')
            ->join(Phpfox::getT('directory_business_moduledata'), 'bmd', ' (bmd.business_id = ' . (int)$iBusinessId . ' AND bmd.core_module_id = \'' . $core_module_id . '\' AND bmd.module_id = ' . (int)$module_id . ' AND bmd.item_id = l.listing_id) ')
            ->where('l.view_id = 0 AND l.privacy IN(0) AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    $this->database()->select('lik.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'lik', ' (lik.type_id = \'' . $core_module_id . '\' AND lik.item_id = l.listing_id AND lik.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("mt.description_parsed AS description " . $extraSelect . " , l.*, bmd.*, " . Phpfox::getUserField())
                    ->from($table, 'l')
                    ->join($table2, 'mt', 'mt.listing_id = l.listing_id')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
                    ->join(Phpfox::getT('directory_business_moduledata'), 'bmd', ' (bmd.business_id = ' . (int)$iBusinessId . ' AND bmd.core_module_id = \'' . $core_module_id . '\' AND bmd.module_id = ' . (int)$module_id . ' AND bmd.item_id = l.listing_id) ')
                    ->where('l.view_id = 0 AND l.privacy IN(0) AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getJobByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('jobposting_job');
        if (Phpfox::getService('directory.helper')->isAdvJob()) {
            $table = Phpfox::getT('jobposting_job');
        }
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'job')
            ->join(Phpfox::getT('jobposting_company'), 'jc', 'jc.company_id = job.company_id and jc.is_deleted = 0')
            ->join(Phpfox::getT('directory_business_moduledata'), 'bmd', ' (bmd.business_id = ' . (int)$iBusinessId . ' AND bmd.core_module_id = \'jobposting\' AND bmd.module_id = 15 AND bmd.item_id = job.job_id) ')
            ->where('job.is_deleted = 0 AND job.post_status = 1  and job.is_approved = 1 AND job.is_hide = 0  and job.is_activated = 1  and job.time_expire > ' . PHPFOX_TIME . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                $aRows = $this->database()->select("jc.name, jc.location, jc.image_path, jc.server_id as image_server_id, bmd.*, job.*, " . Phpfox::getUserField())
                    ->from($table, 'job')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = job.user_id')
                    ->join(Phpfox::getT('jobposting_company'), 'jc', '(jc.company_id = job.company_id and jc.is_deleted = 0)')
                    ->join(Phpfox::getT('directory_business_moduledata'), 'bmd', ' (bmd.business_id = ' . (int)$iBusinessId . ' AND bmd.core_module_id = \'jobposting\' AND bmd.module_id = 15 AND bmd.item_id = job.job_id) ')
                    ->where('job.is_deleted = 0 AND job.post_status = 1  and job.is_approved = 1 AND job.is_hide = 0  and job.is_activated = 1  and job.time_expire > ' . PHPFOX_TIME . ' AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getItemOfModuleInBusiness($iItemId, $sModuleId, $sCoreModuleid)
    {
        $aRow = $this->database()->select('e.*')
            ->from(Phpfox::getT('directory_business_moduledata'), 'e')
            ->where('item_id = ' . (int)$iItemId . ' AND module_id = ' . $sModuleId . ' AND core_module_id = \'' . $sCoreModuleid . '\'')
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function getCountItemPageByBusinessModuleId($iBusinessId, $sModuleId)
    {
        $iCnt = $this->database()->select('COUNT(*) as count')
            ->from(Phpfox::getT('directory_business_moduledata'), 'e')
            ->where('business_id = ' . (int)$iBusinessId . ' AND module_id = ' . $sModuleId)
            ->execute('getSlaveRow');
        return $iCnt['count'];
    }

    public function getPollByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('poll');
        if (Phpfox::getService('directory.helper')->isAdvPolls()) {
            $table = Phpfox::getT('poll');
        }
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'poll')
            ->where('poll.view_id = 0 AND poll.privacy IN(0) AND poll.module_id = \'directory\' AND poll.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    $this->database()->select('lik.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'lik', ' (lik.type_id = \'poll\' AND lik.item_id = poll.poll_id AND lik.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("pd.background, pd.percentage, pd.border, pr.answer_id, pr.user_id as voted, friends2.friend_id AS is_friend, poll.*, " . Phpfox::getUserField())
                    ->from($table, 'poll')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = poll.user_id')
                    ->leftJoin(Phpfox::getT('poll_design'), 'pd', ' (pd.poll_id = poll.poll_id) ')
                    ->leftJoin(Phpfox::getT('poll_result'), 'pr', ' (pr.poll_id = poll.poll_id AND pr.user_id = ' . Phpfox::getUserId() . ') ')
                    ->leftJoin(Phpfox::getT('friend'), 'friends2', ' (friends2.user_id = poll.user_id AND friends2.friend_user_id = ' . Phpfox::getUserId() . ') ')
                    ->where('poll.view_id = 0 AND poll.privacy IN(0) AND poll.module_id = \'directory\' AND poll.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getEventByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true, $upcoming = false)
    {
        $table = Phpfox::getT('event');
        if (Phpfox::getService('directory.helper')->isAdvEvent()) {
            $table = Phpfox::getT('fevent');
        }
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'm')
            ->where('m.view_id = 0 AND m.privacy IN(0) AND m.module_id = \'directory\' AND m.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    if (Phpfox::getService('directory.helper')->isAdvEvent()) {
                        $this->database()->select('lik.like_id AS is_liked, ')
                            ->leftJoin(Phpfox::getT('like'), 'lik', ' (lik.type_id = \'fevent\' AND lik.item_id = m.event_id AND lik.user_id = ' . Phpfox::getUserId() . ') ');
                    } else {
                        $this->database()->select('lik.like_id AS is_liked, ')
                            ->leftJoin(Phpfox::getT('like'), 'lik', ' (lik.type_id = \'event\' AND lik.item_id = m.event_id AND lik.user_id = ' . Phpfox::getUserId() . ') ');
                    }
                }

                $aRows = $this->database()->select("ei.rsvp_id, m.*, " . Phpfox::getUserField())
                    ->from($table, 'm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                    ->leftJoin(Phpfox::getT('event_invite'), 'ei', ' (ei.event_id = m.event_id AND ei.invited_user_id = ' . Phpfox::getUserId() . ') ')
                    ->where('m.view_id = 0 AND m.privacy IN(0) AND m.module_id = \'directory\' AND m.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->group('m.event_id')
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }
        return array($aRows, $iCount);
    }

    public function getAdvBlogByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('ynblog_blogs');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'blog')
            ->where('blog.is_approved = 1 AND blog.privacy IN(0) AND blog.post_status = \'public\' AND blog.module_id = \'directory\' AND blog.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    $this->database()->select('lik.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'lik', ' (lik.type_id = \'ynblog\' AND lik.item_id = blog.blog_id AND lik.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("blog.*, bc.*, " . Phpfox::getUserField())
                    ->from($table, 'blog')
                    ->join(Phpfox::getT('ynblog_category_data'), 'bcd', 'bcd.blog_id = blog.blog_id AND bcd.is_main = 1')
                    ->join(Phpfox::getT('ynblog_category'), 'bc', 'bc.category_id = bcd.category_id AND bc.is_active = 1')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = blog.user_id')
                    ->where('blog.is_approved = 1 AND blog.privacy IN(0) AND blog.post_status = \'public\' AND blog.module_id = \'directory\' AND blog.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');

                foreach ($aRows as &$aRow) {
                    $aRow['image_url'] = Phpfox::getService('ynblog.helper')->getImagePath($aRow['image_path'], $aRow['server_id'], '_big', false);
                }
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getBlogByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('blog');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'blog')
            ->where('blog.is_approved = 1 AND blog.privacy IN(0) AND blog.post_status = 1 AND blog.module_id = \'directory\' AND blog.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    $this->database()->select('lik.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'lik', ' (lik.type_id = \'blog\' AND lik.item_id = blog.blog_id AND lik.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("blog_text.text_parsed AS text, blog.*, " . Phpfox::getUserField())
                    ->from($table, 'blog')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = blog.user_id')
                    ->leftJoin(Phpfox::getT('blog_text'), 'blog_text', ' (blog_text.blog_id = blog.blog_id) ')
                    ->where('blog.is_approved = 1 AND blog.privacy IN(0) AND blog.post_status = 1 AND blog.module_id = \'directory\' AND blog.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getFollowersByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('directory_follow');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'df')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = df.user_id')
            ->where('df.business_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                $aRows = $this->database()->select("df.*, " . Phpfox::getUserField())
                    ->from($table, 'df')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = df.user_id')
                    ->where('df.business_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getMembersByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('directory_business_userroledata');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'dm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dm.user_id')
            ->join(Phpfox::getT('directory_business_memberrole'), 'dmu', 'dmu.role_id = dm.role_id')
            ->where('dmu.business_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                $aRows = $this->database()->select("dm.*,dmu.*, " . Phpfox::getUserField())
                    ->from($table, 'dm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = dm.user_id')
                    ->join(Phpfox::getT('directory_business_memberrole'), 'dmu', 'dmu.role_id = dm.role_id')
                    ->where('dmu.business_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }


    public function isMemberOfBusiness($iBusinessId, $iUserId)
    {
        $iCount = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('directory_business_userroledata'), 'dm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dm.user_id')
            ->join(Phpfox::getT('directory_business_memberrole'), 'dmu', 'dmu.role_id = dm.role_id')
            ->where('dmu.business_id = ' . (int)$iBusinessId . ' AND dm.user_id =  ' . (int)$iUserId)
            ->execute('getSlaveField');

        return $iCount;
    }


    public function getMusicByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('music_song');
        if (Phpfox::getService('directory.helper')->isAdvMusic()) {
            // $table = Phpfox::getT('channel_video');
        }
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'm')
            ->where('m.view_id = 0 AND m.privacy IN(0) AND m.module_id = \'directory\' AND m.item_id = ' . (int)$iBusinessId . ' AND m.privacy IN(0)' . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    $this->database()->select('lik.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'lik', ' (lik.type_id = \'music_song\' AND lik.item_id = m.song_id AND lik.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("ma.name AS album_name, mp.play_id AS is_on_profile, m.*,  " . Phpfox::getUserField())
                    ->from($table, 'm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                    ->leftJoin(Phpfox::getT('music_album'), 'ma', ' (ma.album_id = m.album_id) ')
                    ->leftJoin(Phpfox::getT('music_profile'), 'mp', ' (mp.song_id = m.song_id AND mp.user_id = ' . Phpfox::getUserId() . ') ')
                    ->where('m.view_id = 0 AND m.privacy IN(0) AND m.module_id = \'directory\' AND m.item_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }


    public function getVideoByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('video');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);
        $sWhere = 'm.in_process = 0 AND m.view_id = 0 AND m.module_id = \'directory\' AND m.item_id = ' . (int)$iBusinessId . ' AND m.privacy IN(0)' . ' AND ' . $sCond;
        if (Phpfox::getService('directory.helper')->isAdvVideo()) {
            $table = Phpfox::getT('channel_video');
        }
//        if (Phpfox::getService('directory.helper')->isUltVideo()) {
//            $table = Phpfox::getT('ynultimatevideo_videos');
//            $sWhere = 'm.status = 1 AND m.module_id = \'directory\' AND m.item_id = ' . (int)$iBusinessId . ' AND m.privacy IN(0)' . ' AND ' . $sCond;
//        }
        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'm')
            ->where($sWhere)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                $aRows = $this->database()->select("m.*, " . Phpfox::getUserField())
                    ->from($table, 'm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                    ->where($sWhere)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getVByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('video');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);
        $sWhere = 'm.in_process = 0 AND m.view_id = 0 AND m.module_id = \'directory\' AND m.item_id = ' . (int)$iBusinessId . ' AND m.privacy IN(0)' . ' AND ' . $sCond;

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'm')
            ->where($sWhere)
            ->execute('getSlaveField');

        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                $aRows = $this->database()->select("m.*, " . Phpfox::getUserField())
                    ->from($table, 'm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                    ->where($sWhere)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getUltimateVideoByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        
        $aRows = array();
        $sCond = implode(' AND ', $aConds);
        if (Phpfox::getService('directory.helper')->isUltVideo()) {
            $table = Phpfox::getT('ynultimatevideo_videos');
            $sConds = 'm.status = 1 AND m.is_approved = 1 AND m.module_id = \'directory\' AND m.item_id = ' . (int)$iBusinessId . ' AND m.privacy IN(0)' . ' AND ' . $sCond;
        }
        else
        {
            return false;
        }

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'm')
            ->where($sConds)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {
                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                $aRows = $this->database()->select("m.*, " . Phpfox::getUserField())
                    ->from($table, 'm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                    ->where($sConds)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }
    public function getRoleByUserIdWithGuest($iUserId, $iBusinessId)
    {
        $roleOfUser = $this->getRoleByUserId($iUserId, $iBusinessId);

        if (count($roleOfUser) == 0) {
            $roleOfUser = $this->getRoleByBusinessId($iBusinessId, 'guest');
        }

        return $roleOfUser;
    }

    public function getPhotoInBusiness($iPhotoId)
    {
        $table = Phpfox::getT('photo');
        $aRow = $this->database()->select('e.*')
            ->from($table, 'e')
            ->where('e.photo_id = ' . (int)$iPhotoId . ' AND e.module_id = \'directory\'')
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function getAlbumByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('photo_album');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'pa')
            ->where('pa.view_id = 0 AND pa.privacy IN(0) AND pa.total_photo > 0 AND pa.profile_id = 0 AND pa.module_id = \'directory\' AND pa.group_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
            ->execute('getSlaveField');
        if ($getData) {
            if ($iCount) {

                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    $this->database()->select('l.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'l', ' (l.type_id = "photo_album" AND l.item_id = pa.album_id AND l.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("p.destination, p.server_id, pa.*, " . Phpfox::getUserField())
                    ->from($table, 'pa')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
                    ->leftJoin(Phpfox::getT('photo'), 'p', ' (p.album_id = pa.album_id AND pa.view_id = 0 AND p.is_cover = 1) ')
                    ->where('pa.view_id = 0 AND pa.privacy IN(0) AND pa.total_photo > 0 AND pa.profile_id = 0 AND pa.module_id = \'directory\' AND pa.group_id = ' . (int)$iBusinessId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getPhotoByBusinessId($iBusinessId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $table = Phpfox::getT('photo');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'photo')
            ->where('photo.view_id = 0 AND photo.module_id = \'directory\' AND photo.group_id = ' . (int)$iBusinessId . ' AND photo.privacy IN(0)' . ' AND ' . $sCond)
            ->execute('getSlaveField');

        if ($getData) {
            if ($iCount) {

                if ($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like')) {
                    $this->database()->select('l.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'l', ' (l.type_id = "photo" AND l.item_id = photo.photo_id AND l.user_id = ' . Phpfox::getUserId() . ') ');
                    $this->database()->select('adisliked.action_id as is_disliked, ')
                        ->leftJoin(Phpfox::getT('action'), 'adisliked', ' (adisliked.action_type_id = 2 AND adisliked.item_id = photo.photo_id AND adisliked.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("pa.name AS album_name, pa.profile_id AS album_profile_id, ppc.name as category_name, ppc.category_id, photo.*, " . Phpfox::getUserField())
                    ->from($table, 'photo')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = photo.user_id')
                    ->leftJoin(Phpfox::getT('photo_album'), 'pa', ' (pa.album_id = photo.album_id) ')
                    ->leftJoin(Phpfox::getT('photo_category_data'), 'ppcd', ' (ppcd.photo_id = photo.photo_id) ')
                    ->leftJoin(Phpfox::getT('photo_category'), 'ppc', ' (ppc.category_id = ppcd.category_id) ')
                    ->where('photo.view_id = 0 AND photo.module_id = \'directory\' AND photo.group_id = ' . (int)$iBusinessId . ' AND photo.privacy IN(0) AND ' . $sCond)
                    ->group('photo.photo_id')
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function handlerAfterAddingEntry($sType, $iItemId)
    {
        if (!$iItemId) {
            return false;
        }
        $sRedirectUrl = $this->getRedirectUrlAfterAddingEntry($sType, $iItemId);
        if (!$sRedirectUrl) {
            return false;
        }

        switch ($sType) {
            case 'photos':
            case 'musics':
                echo 'window.location.href = \'' . $sRedirectUrl . '\'';
                exit;
                break;

            default:
                Phpfox::getLib('url')->send($sRedirectUrl);
                break;
        }
    }

    public function getRedirectUrlAfterAddingEntry($sType, $iItemId)
    {
        $sUrl = false;

        if (isset($_SERVER['HTTP_REFERER'])) {
            $sRefererUrl = $_SERVER['HTTP_REFERER'];
            $iBusinessId = $this->getParamValueFromUrl(Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(), $sRefererUrl);
            if ($iBusinessId) {
                $aRow = Phpfox::getService('directory')->getBusinessById($iBusinessId);
                $sUrl = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);
            }
        }

        //for some reason we can retrieve URL so we use session instead
        if (!$sUrl) {
            $sUrl = $this->getRedirectUrlAfterAddingEntryBySession($sType, $iItemId);
        }
        if ($sUrl !== false) {
            $sUrl .= $sType . '/';
        }


        return $sUrl;
    }

    public function getRedirectUrlAfterAddingEntryBySession($sType, $iItemId)
    {
        $iBusinessId = Phpfox::getService('directory.helper')->getSessionAfterUserAddNewItem($sType);

        if (!$iBusinessId) {
            return false;
        }

        $aRow = Phpfox::getService('directory')->getBusinessById($iBusinessId);
        $sUrl = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);

        return $sUrl;
    }

    public function getParamValueFromUrl($sName, $sUrl)
    {
        $sPattern = '/' . $sName . '_((\d|\w)*)' . '/';
        preg_match($sPattern, $sUrl, $aMatches);
        if (isset($aMatches[1])) {
            $sValue = trim($aMatches[1], '\/');
            return $sValue;
        } else {
            return false;
        }
    }


    public function getFirstPageCanAccessInBusiness($iBusinessId)
    {
        // filter by manage pages from owner business : http://app.mockflow.com/view/7089164E822783BE33E95D4E06E968F8#/page/360F150576E71FDFC789828B4421F471
        // check landing page
        return 'overview';
    }

    public function getDefaultModulesInBusiness()
    {
        $list = array(
            '1' => 'overview',
            '2' => 'aboutus',
            '3' => 'activities',
            '4' => 'members',
            '5' => 'followers',
            '6' => 'reviews',
            '7' => 'photos',
            '8' => 'videos',
            '9' => 'musics',
            '10' => 'blogs',
            '11' => 'discussion',
            '12' => 'polls',
            '13' => 'coupons',
            '14' => 'events',
            '15' => 'jobs',
            '16' => 'marketplace',
            '17' => 'faq',
            '18' => 'contactus',
            '19' => 'ultimatevideo',
            '20' => 'advanced-blog',
            '21' => 'v',
        );
        $listIgnore = array(1, 2, 3, 4, 5, 6, 17, 18);

        return array($list, $listIgnore);
    }

    public function getMenuListCanAccessInBusinessDetail($iBusinessId, $aBusiness = null)
    {
        if (null == $aBusiness) {
            $aBusiness = $this->getBusinessForEdit($iBusinessId, true);
        }
        if (isset($aBusiness['business_id']) == false || $aBusiness['type'] == 'claiming') {
            return array();
        }

        list($list, $listIgnore) = $this->getDefaultModulesInBusiness();

        // filter by package
        $package_data = (array)json_decode($aBusiness['package_data']);

        if (isset($package_data['modules'])) {
            $modules = (array)$package_data['modules'];
            if (!empty($package_data['updated_packaged'])) {
                foreach ($list as $keyDefault => $default) {
                    $remove = true;
                    foreach ($modules as $keyModule => $module) {
                        if ($module->module_id == $keyDefault) {
                            $remove = false;
                        }
                    }
                    if (in_array($keyDefault, $listIgnore) == false && $remove) {
                        unset($list[$keyDefault]);
                    }
                }
            } else {
                $bShouldUpdate = false;
                foreach ($list as $keyDefault => $default) {
                    $remove = true;
                    foreach ($modules as $keyModule => $module) {
                        if ($module->module_id == $keyDefault) {
                            $remove = false;
                        }

                        if (!$bShouldUpdate && $module->module_name == 'blogs') {
                            $bShouldUpdate = true;
                            $remove = false;
                        }
                    }
                    if (in_array($keyDefault, $listIgnore) == false && $remove) {
                        unset($list[$keyDefault]);
                    }
                }

                if ($bShouldUpdate) {
                    $package_data['modules'][] = [
                        'data_id' => 11,
                        'package_id' => 1,
                        'module_id' => 20,
                        'module_phrase' => "{phrase var=&#039;ynblog&#039;}",
                        'module_name' => 'ynblog',
                        'module_type' => 'module',
                        'module_description' => '',
                        'module_landing' => 0,
                    ];
                }

                $package_data['updated_packaged'] = true;
                Phpfox::getService('directory.process')->updatePackageDataForBussiness($aBusiness['business_id'], $package_data);
            }
        }

        // filter by manage pages from owner business : http://app.mockflow.com/view/7089164E822783BE33E95D4E06E968F8#/page/360F150576E71FDFC789828B4421F471
        $modulesInBusiness = $this->getModuleInBusiness($iBusinessId);
        $keyLandingPage = 'first';
        foreach ($list as $keyDefault => $default) {
            $remove = true;
            foreach ($modulesInBusiness as $keyModule => $module) {
                if ($module['module_id'] == $keyDefault && $module['is_show'] == 1) {
                    $remove = false;
                    if ($module['module_landing']) {
                        $keyLandingPage = $keyDefault;
                    }
                }
            }
            if ($remove) {
                unset($list[$keyDefault]);
            }
        }

        // filter by setting of role : http://app.mockflow.com/view/7089164E822783BE33E95D4E06E968F8#/page/53AC5A4D3EE623415FD982C3A476BAC5
        $roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

        foreach ($roleOfUser as $keyRole => $valueRole) {
            $module_name = null;
            switch ($valueRole['setting_name']) {
                case 'view_browse_photos':
                    $module_name = 'photos';
                    break;
                case 'view_browse_events':
                    $module_name = 'events';
                    break;
                case 'view_browse_polls':
                    $module_name = 'polls';
                    break;
                case 'view_browse_videos':
                    $module_name = 'videos';
                    break;
                case 'view_browse_musics':
                    $module_name = 'musics';
                    break;
                case 'view_browse_marketplace_items':
                    $module_name = 'marketplace';
                    break;
                case 'view_browse_blogs':
                    $module_name = 'blogs';
                    break;
                case 'view_browse_jobs':
                    $module_name = 'jobs';
                    break;
                case 'view_browse_coupons':
                    $module_name = 'coupons';
                    break;
                case 'view_browse_discussions':
                    $module_name = 'discussion';
                    break;
                case 'view_browse_ynblogs':
                    $module_name = 'ynblog';
                    break;
            }
            $status = $valueRole['status'];
            if (null != $module_name && $status == 'no') {
                foreach ($list as $keyDefault => $default) {
                    if ($module_name == $default) {
                        unset($list[$keyDefault]);
                        break;
                    }
                }
            }
        }

        return array($list, $keyLandingPage);
    }

    public function getPageModuleForManage($iBusinessId, $aBusiness = null)
    {
        if (null == $aBusiness) {
            $aBusiness = $this->getBusinessForEdit($iBusinessId, true);
        }
        if (isset($aBusiness['business_id']) == false) {
            return array();
        }

        list($list, $listIgnore) = $this->getDefaultModulesInBusiness();

        // filter by package
        $package_data = (array)json_decode($aBusiness['package_data']);
        if (isset($package_data['modules'])) {
            $modules = (array)$package_data['modules'];
            foreach ($list as $keyDefault => $default) {
                $remove = true;
                foreach ($modules as $keyModule => $module) {
                    if ($module->module_id == $keyDefault) {
                        $remove = false;
                    }
                }
                if (in_array($keyDefault, $listIgnore) == false && $remove) {
                    unset($list[$keyDefault]);
                }
            }
        }

        return array($list, '');
    }

    public function getRoleOfUserInBusiness($iBusinessId)
    {
        $aRow = $this->database()->select('e.*, et.*')
            ->from(Phpfox::getT('directory_business_userroledata'), 'e')
            ->join(Phpfox::getT('directory_business_memberrole'), 'et', ' ( et.business_id = ' . $iBusinessId . ' AND et.role_id = e.role_id )')
            ->where('e.user_id = ' . (int)Phpfox::getUserId())
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function getModuleInBusiness($iBusinessId)
    {
        $aRows = $this->database()->select('e.*')
            ->from(Phpfox::getT('directory_business_module'), 'e')
            ->where('e.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRows');

        return $aRows;
    }

    public function callback($aCallback)
    {
        $this->_aCallback = $aCallback;

        return $this;
    }

    public function getRoleByUserId($iUserId, $iBusinessId = 0)
    {
        $aRows = $this->database()->select('e.*, et.*, mrs.*')
        ->from(Phpfox::getT('directory_business_userroledata'), 'e')
            ->join(Phpfox::getT('directory_business_memberrole'), 'bmr', ' ( bmr.role_id = e.role_id AND bmr.business_id = ' . (int)$iBusinessId . ' ) ')
            ->join(Phpfox::getT('directory_business_memberrolesettingdata'), 'et', 'et.role_id = e.role_id')
            ->join(Phpfox::getT('directory_business_memberrolesetting'), 'mrs', 'mrs.setting_id = et.setting_id')
            ->where('e.user_id = ' . (int)$iUserId)
            ->execute('getSlaveRows');

        return $aRows;
    }

    public function getCurrentRoleOfUserId($iUserId, $iBusinessId)
    {

        $aRow = $this->database()->select('e.*')
            ->from(Phpfox::getT('directory_business_userroledata'), 'e')
            ->join(Phpfox::getT('directory_business_memberrole'), 'bmr', ' ( bmr.role_id = e.role_id AND bmr.business_id = ' . (int)$iBusinessId . ' ) ')
            ->where('e.user_id = ' . (int)$iUserId)
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function getRoleByBusinessId($iBusinessId, $type = 'guest', $role_id = null)
    {
        $sWhere = '';
        if ($type != null) {
            $sWhere .= ' AND e.type = \'' . $type . '\' ';
        }
        if ($role_id != null) {
            $sWhere .= ' AND e.role_id = ' . $role_id;
        }
        $aRows = $this->database()->select('e.*, et.*, mrs.*')
            ->from(Phpfox::getT('directory_business_memberrole'), 'e')
            ->join(Phpfox::getT('directory_business_memberrolesettingdata'), 'et', 'et.role_id = e.role_id')
            ->join(Phpfox::getT('directory_business_memberrolesetting'), 'mrs', 'mrs.setting_id = et.setting_id')
            ->where('e.business_id = ' . (int)$iBusinessId . $sWhere)
            ->execute('getSlaveRows');

        return $aRows;
    }

    public function getRoleIdByBusinessId($iBusinessId, $type = 'guest')
    {
        $sWhere = '';
        if ($type != null) {
            $sWhere .= ' AND e.type = \'' . $type . '\' ';
        }
        $aRow = $this->database()->select('e.*')
            ->from(Phpfox::getT('directory_business_memberrole'), 'e')
            ->where('e.business_id = ' . (int)$iBusinessId . $sWhere)
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function getDefaultBusinessRoleMemberSetting($isGuest = false)
    {
        return array(
            '1' => 'no',
            '2' => 'no',
            '3' => 'yes',
            '4' => 'yes',
            '5' => $isGuest ? 'no' : 'yes',
            '6' => 'yes',
            '7' => $isGuest ? 'no' : 'yes',
            '8' => 'yes',
            '9' => $isGuest ? 'no' : 'yes',
            '10' => 'yes',
            '11' => 'yes',
            '12' => 'yes',
            '13' => $isGuest ? 'no' : 'yes',
            '14' => 'yes',
            '15' => $isGuest ? 'no' : 'yes',
            '16' => 'yes',
            '17' => $isGuest ? 'no' : 'yes',
            '18' => 'yes',
            '19' => 'yes',
            '20' => 'yes',
            '21' => 'yes',
            '22' => 'yes',
            //'23' => 'yes',remove dicussion
            //'24' => 'yes',
            '25' => 'no',
            '26' => 'no',
            '27' => 'no',
            '28' => 'no',
            '29' => 'no',
            '30' => 'no',
            '31' => 'no',
            '32' => 'no',
            '33' => 'no',
            '34' => 'no',
            '35' => 'no',
            '36' => 'no',
            '37' => $isGuest ? 'no' : 'yes',
            '38' => 'yes',
        );
    }

    public function getBusinessById($iBusinessId)
    {
        if (!$iBusinessId) {
            return false;
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'directory\' AND lik.item_id = e.business_id AND lik.user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = e.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        } else {
            $this->database()->select('0 as is_friend, ');
        }

        $aRow = $this->database()->select('e.*, e.short_description as short_description, ' . (Phpfox::getParam('core.allow_html') ? 'et.description_parsed' : 'et.description') . ' AS description, ' . Phpfox::getUserField() . ',er.review_id as has_rated')
            ->from(Phpfox::getT('directory_business'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->leftjoin(Phpfox::getT('directory_business_text'), 'et', 'et.business_id = e.business_id')
            ->leftjoin(Phpfox::getT('directory_review'), 'er', 'er.business_id = e.business_id')
            ->where('e.business_id = ' . (int)$iBusinessId)
            ->execute('getRow');

        if ($aRow) {
            $aRow = $this->retrieveMoreInfoFromBusiness($aRow, '');
        }

        if (isset($aRow['business_id']) == false) {
            return false;
        }

        $iId = $aRow['business_id'];
        $aRow['locations'] = Phpfox::getService('directory')->getBusinessLocation($iId);
        $aRow['phones'] = Phpfox::getService('directory')->getBusinessPhone($iId);
        $aRow['websites'] = Phpfox::getService('directory')->getBusinessWebsite($iId);
        $aRow['faxs'] = Phpfox::getService('directory')->getBusinessFax($iId);
        $aRow['vistinghours'] = Phpfox::getService('directory')->getBusinessVistingHour($iId);
        $aRow['additioninfo'] = Phpfox::getService('directory')->getBusinessAdditionInfo($iId);

        return $aRow;
    }

    public function getBusinessAddCallback($iId)
    {
        Phpfox::getService('pages')->setIsInPage();

        return array(
            'module' => 'pages',
            'item_id' => $iId,
            'table_prefix' => 'pages_'
        );
    }

    public function getBusinessForEdit($iId, $bForce = false)
    {
        $aItem = $this->database()->select('l.*, description')
            ->from(Phpfox::getT('directory_business'), 'l')
            ->join(Phpfox::getT('directory_business_text'), 'mt', 'mt.business_id = l.business_id')
            ->where('l.business_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (!empty($aItem['user_id']) && !empty($aItem['business_id'])) {
            if (Phpfox::getService('directory.permission')->canEditBusiness($aItem['user_id'], $aItem['business_id']) || ($bForce === true)) {
                $aItem['categories'] = Phpfox::getService('directory.category')->getCategoryIds($aItem['business_id']);

            }
        }

        if (!empty($aItem['logo_path'])) {
            $aItem['current_logo'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aItem['server_id'],
                'path' => 'core.url_pic',
                'file' => $aItem['logo_path'],
                'suffix' => '_200',
                'return_url' => true
            ));
        }

        $aItem['locations'] = Phpfox::getService('directory')->getBusinessLocation($iId);
        $aItem['phones'] = Phpfox::getService('directory')->getBusinessPhone($iId);
        $aItem['websites'] = Phpfox::getService('directory')->getBusinessWebsite($iId);
        $aItem['faxs'] = Phpfox::getService('directory')->getBusinessFax($iId);
        $aItem['vistinghours'] = Phpfox::getService('directory')->getBusinessVistingHour($iId);
        $aItem['additioninfo'] = Phpfox::getService('directory')->getBusinessAdditionInfo($iId);

        return $aItem;
    }

    public function getInvoice($iId)
    {
        $aPurchase = $this->database()->select('sp.*')
            ->from(Phpfox::getT('directory_invoice'), 'sp')
            ->where('sp.invoice_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aPurchase['invoice_id'])) {
            return false;
        }

        $aCurrentCurrencies = Phpfox::getService('directory.helper')->getCurrentCurrencies();
        $aPurchase['default_cost'] = $aPurchase['price'];
        $aPurchase['default_currency_id'] = $aCurrentCurrencies[0]['currency_id'];

        return $aPurchase;
    }

    public function getCustomFieldByCategoryId($iCategoryId)
    {
        $sWhere = '';
        $sWhere .= ' AND ccd.category_id = ' . (int)$iCategoryId;
        $aFields = $this->database()
            ->select('cfd.*')
            ->from(Phpfox::getT("directory_category_customgroup_data"), 'ccd')
            ->join(Phpfox::getT('directory_custom_group'), 'cgr', ' ( cgr.group_id = ccd.group_id AND cgr.is_active = 1 ) ')
            ->join(Phpfox::getT('directory_custom_field'), 'cfd', ' ( cfd.group_id = cgr.group_id ) ')
            ->where('1=1' . $sWhere)
            ->order('cgr.group_id ASC , cfd.ordering ASC, cfd.field_id ASC')
            ->execute("getSlaveRows");

        $aHasOption = Phpfox::getService('directory.custom')->getHasOption();
        if (is_array($aFields) && count($aFields)) {
            foreach ($aFields as $k => $aField) {
                if (in_array($aField['var_type'], $aHasOption)) {
                    $aOptions = $this->database()->select('*')->from(Phpfox::getT('directory_custom_option'))->where('field_id = ' . $aField['field_id'])->order('option_id ASC')->execute('getSlaveRows');
                    if (is_array($aOptions) && count($aOptions)) {
                        foreach ($aOptions as $k2 => $aOption) {
                            $aFields[$k]['option'][$aOption['option_id']] = $aOption['phrase_var_name'];
                        }
                    }
                }
            }
        }

        return $aFields;
    }

    public function getBusiness($sType, $iLimit = null, $iPage = null)
    {


        $sConditions = 'dbus.privacy = 0 ';


        if ($iPage == null) {
            $iPage = 1;
        }


        $sRun = "(" . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running') . ")";

        $sConditions .= ' AND dbus.package_start_time <= ' . PHPFOX_TIME . ' AND dbus.package_end_time >= ' . PHPFOX_TIME . ' AND dbus.business_status IN ' . $sRun;

        switch ($sType) {
            case 'most-liked':
                $sConditions .= ' AND dbus.total_like != 0';
                $sOrder = 'dbus.total_like DESC';
                break;
            case 'most-popular':
                $sConditions .= ' AND dbus.total_view != 0';
                $sOrder = 'dbus.total_view DESC';
                break;
            case 'most-comment':
                $sConditions .= ' AND dbus.total_comment != 0';
                $sOrder = 'dbus.total_comment DESC';
                break;
            case 'most-rated':
                $sConditions .= ' AND dbus.total_score != 0';
                $sOrder = 'dbus.total_score DESC';
                break;
            case 'most-reviewed':
                $sConditions .= ' AND dbus.total_review != 0';
                $sOrder = 'dbus.total_review DESC';
                break;
            case 'most-checkin':
                $sConditions .= ' AND dbus.total_checkin != 0';
                $sOrder = 'dbus.total_checkin DESC';
                break;
            case 'featured':
                $sConditions .= ' AND dbus.feature_start_time <= ' . PHPFOX_TIME . ' AND dbus.feature_end_time >= ' . PHPFOX_TIME;
                $sOrder = 'dbus.time_stamp DESC';
                break;
            case '':
                $sOrder = 'dbus.business_id DESC';
                break;
        }

        /*        	print_r( $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'dbus')
            ->where($sConditions)
            ->execute(''));
            die;*/
        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'dbus')
            ->where($sConditions)
            ->execute('getSlaveField');

        if ($iLimit == null) {
            $iLimit = $iCnt;
        }

        $iPageSize = $iLimit;

        $sConditions .= " AND dcd.is_main = 1";
        $aBusinesses = array();
        if ($iCnt) {
            $aBusinesses = $this->database()->select(Phpfox::getUserField() . ', dbus.*, dc.title as category_title, dc.category_id as category_id')
                ->from($this->_sTable, 'dbus')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
                ->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
                ->leftJoin(Phpfox::getT('directory_category'), 'dc', 'dc.is_active = 1 AND dc.category_id = dcd.category_id')
                ->where($sConditions)
                ->order($sOrder)
                ->limit($iPage, $iPageSize, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aBusinesses as $key => $aBusiness) {
                $aBusinesses[$key] = $this->retrieveMoreInfoFromBusiness($aBusinesses[$key], $sType);
                if (empty($aBusinesses[$key]['logo_path'])) {
                    $aBusinesses[$key]['default_logo_path'] = Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png';
                }
            }
        }

        if (!count($aBusinesses)) {
            return false;
        }

        if ($sType == '') {
            return array($iCnt, $aBusinesses);
        }

        return $aBusinesses;

    }

    public function retrieveMoreInfoFromBusiness($aBusiness, $sType)
    {

        /*get location*/
        $aBusinessLocation = $this->database()->select('dbl.location_title,dbl.location_longitude,dbl.location_latitude, dbl.location_address')
            ->from($this->_sTable, 'dbus')
            ->leftJoin(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = dbus.business_id')
            ->where('dbus.business_id = ' . $aBusiness['business_id'])
            ->order('location_id ASC')
            ->execute('getSlaveRow');

        if (count($aBusinessLocation)) {
            $aBusiness['location_title'] = $aBusinessLocation['location_title'];
            $aBusiness['location_longitude'] = $aBusinessLocation['location_longitude'];
            $aBusiness['location_latitude'] = $aBusinessLocation['location_latitude'];
            $aBusiness['location_address'] = $aBusinessLocation['location_address'];
        } else {
            $aBusiness['location_title'] = '';
            $aBusiness['location_longitude'] = '';
            $aBusiness['location_latitude'] = '';
            $aBusiness['location_address'] = '';
        }

        /*get most reviewed*/
        $aBusinessTotalReview = $this->database()->select('COUNT(*) as total_reviews')
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->where('dr.business_id = ' . $aBusiness['business_id'])
            ->execute('getSlaveRow');

        if (count($aBusinessTotalReview)) {
            $aBusiness['total_reviews'] = $aBusinessTotalReview['total_reviews'];
        } else {
            $aBusiness['total_reviews'] = '';
        }

        /*expired or not*/
        if (($aBusiness['package_start_time'] >= PHPFOX_TIME || $aBusiness['package_end_time'] <= PHPFOX_TIME) &&
            ($aBusiness['package_start_time'] != 0 && $aBusiness['package_end_time'] != 0)
        ) {
            $aBusiness['expired'] = true;
        } else {
            $aBusiness['expired'] = false;
        }

        if ($aBusiness['expired'] ||
            $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft') ||
            $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.denied')
        ) {
            $aBusiness['can_payment'] = true;
        } else {
            $aBusiness['can_payment'] = false;
        }
        /*get featured or not*/
        if (
            $aBusiness['feature_start_time'] <= PHPFOX_TIME && $aBusiness['feature_end_time'] >= PHPFOX_TIME && ($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved') || $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running'))
        ) {
            $aBusiness['featured'] = true;
        } else {
            $aBusiness['featured'] = false;
        }

        /*get extra info of business*/
        $aPhoneInfo = $this->database()->select('dbp.phone_number')
            ->from(Phpfox::getT('directory_business_phone'), 'dbp')
            ->where('dbp.business_id = ' . $aBusiness['business_id'])
            ->execute('getRows');

        $aBusiness['phone_numbers'] = $aPhoneInfo;

        $aWebsiteInfo = $this->database()->select('dbw.website_text')
            ->from(Phpfox::getT('directory_business_website'), 'dbw')
            ->where('dbw.business_id = ' . $aBusiness['business_id'])
            ->execute('getSlaveRow');

        if (count($aWebsiteInfo)) {
            $aBusiness['website_text'] = $aWebsiteInfo['website_text'];
        } else {
            $aBusiness['website_text'] = '';
        }

        if (
        (($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved') || $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running')) && Phpfox::getUserParam('directory.can_feature_business'))
        ) {
            $aBusiness['can_feature'] = true;
        } else {
            $aBusiness['can_feature'] = false;
        }

        $aBusiness['business_phrase_status'] = Phpfox::getService('directory.helper')->getPhraseById('business.status', $aBusiness['business_status']);
        $aBusiness['is_pending_claiming'] = 0;
        $aBusiness['is_draft'] = 0;
        $aBusiness['is_closed'] = 0;
        switch ($aBusiness['business_status']) {
            case Phpfox::getService('directory.helper')->getConst('business.status.pending'):
                $aBusiness['business_phrase_status'] = _p('directory.pending');
                break;
            case Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming'):
                $aBusiness['business_phrase_status'] = _p('directory.claiming');
                break;
            case Phpfox::getService('directory.helper')->getConst('business.status.closed'):
                $aBusiness['is_closed'] = 1;
                $aBusiness['business_phrase_status'] = _p('directory.closed');
                break;
            case Phpfox::getService('directory.helper')->getConst('business.status.draft'):
                $aBusiness['is_draft'] = 1;
                if ($aBusiness['type'] == 'claiming') {
                    $aBusiness['is_pending_claiming'] = 1;
                    $aBusiness['business_phrase_status'] = _p('directory.pending_for_claiming');
                }
                break;
        }


        switch ($sType) {
            case 'most-liked':
                $aBusiness['statistics'] = _p('directory.number_total_likes', array('number_total' => (int) $aBusiness['total_like']));
                break;
            case 'most-popular':
                $aBusiness['statistics'] = _p('directory.number_total_views', array('number_total' => (int) $aBusiness['total_view']));
                break;
            case 'most-comment':
                $aBusiness['statistics'] = _p('directory.total_dicussions', array('total' => (int) $aBusiness['total_comment']));
                break;
            case 'most-rated':
                $aBusiness['statistics'] = '';
                break;
            case 'most-reviewed':
                $aBusiness['statistics'] = '';
                break;
            case 'most-checkin':
                $aBusiness['statistics'] = _p('directory.total_check_in', array('total' => (int) ($aBusiness['total_checkin'] != null) ? $aBusiness['total_checkin'] : 0));
                break;
            default:
                $aBusiness['statistics'] = '';
                break;
        }

        $aBusiness['block_stype'] = $sType;
        $total_score_around = floor($aBusiness['total_score'] / 2);
        $total_score = $aBusiness['total_score'] / 2;
        $aBusiness['total_score_text'] = '';

        $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']).'reviews';


        $aBusiness['total_score_text'] .= '<a href="' . $sLink . '" class="ync-outer-rating ync-outer-rating-row mini ync-rating-sm"><div class="ync-outer-rating-row"><div class="ync-rating-star">';

         for ($i = 0; $i < 5; $i++){
            if ($i < $total_score_around) {
                $aBusiness['total_score_text'] .= '<i class="ico ico-star" aria-hidden="true"></i>';
            } else if (($total_score - $total_score_around > 0) && ($total_score - $i) > 0) {
                $aBusiness['total_score_text'] .= '<i class="ico ico-star half-star" aria-hidden="true"></i>';
            } else {
                $aBusiness['total_score_text'] .= '<i class="ico ico-star disable" aria-hidden="true"></i>';
            }
        }

        $aBusiness['total_score_text'] .= '</div></div>';
        $aBusiness['total_score_text'] .= '<div class="ync-rating-count-review-wrapper"><span class="ync-rating-count-rating hidden"><i class="ico ico-star" aria-hidden="true"></i>' . (int)($aBusiness['total_score'] / 2) . '</span>';
        $aBusiness['total_score_text'] .= '<span class="ync-rating-count-review"><span class="item-number">'.(int)$aBusiness['total_review'].'</span>';

        $aBusiness['total_score_text'] .= '</span></div></a>';

        // get permission
        $aBusiness['bCanDelete'] = false;
        $aBusiness['bCanEdit'] = false;
        $aBusiness['bCanApprove'] = false;
        $aBusiness['bCanCheckinhere'] = false;
        $aBusiness['bCanRateBusiness'] = false;

        if (Phpfox::getService('directory.permission')->canDeleteBusiness($aBusiness['user_id'])) {
            $aBusiness['bCanDelete'] = true;
        }
        if (Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'], $aBusiness['business_id'])) {
            $aBusiness['bCanEdit'] = true;
        }
        if (Phpfox::getService('directory.permission')->canApproveBusiness()) {
            $aBusiness['bCanApprove'] = true;
        }
        if (Phpfox::getService('directory.permission')->canCheckinhere($aBusiness['business_id'])) {
            $aBusiness['bCanCheckinhere'] = true;
        }
        if (Phpfox::getService('directory.permission')->canReviewBusiness($aBusiness['user_id'], $aBusiness['business_id'])) {
            $aBusiness['bCanRateBusiness'] = true;
        }
        $aBusiness['setting_support'] = Phpfox::getService('directory.permission')->getSettingSupportInBusiness($aBusiness['business_id'], $aBusiness);

        return $aBusiness;
    }

    public function getQuickBusinessById($iBusinessId)
    {
        $aRow = $this->database()
            ->select('db.*')
            ->from(Phpfox::getT("directory_business"), 'db')
            ->where('db.business_id = ' . $iBusinessId)
            ->execute("getSlaveRow");

        return $aRow;
    }

    public function getBusinessPhone($iBusinessId)
    {
        $aPhoneInfo = $this->database()->select('dbp.phone_number')
            ->from(Phpfox::getT('directory_business_phone'), 'dbp')
            ->where('dbp.business_id = ' . $iBusinessId)
            ->execute('getRows');

        return $aPhoneInfo;
    }

    public function getBusinessFax($iBusinessId)
    {
        $aFaxInfo = $this->database()->select('dbfax.fax_number')
            ->from(Phpfox::getT('directory_business_fax'), 'dbfax')
            ->where('dbfax.business_id = ' . $iBusinessId)
            ->execute('getRows');

        return $aFaxInfo;
    }

    public function getBusinessVistingHour($iBusinessId)
    {
        $aVistingHourInfo = $this->database()->select('dbvh.*')
            ->from(Phpfox::getT('directory_business_vistinghour'), 'dbvh')
            ->where('dbvh.business_id = ' . $iBusinessId)
            ->execute('getRows');

        return $aVistingHourInfo;
    }

    public function getBusinessWebsite($iBusinessId)
    {

        $aWebsiteInfo = $this->database()->select('dbw.website_text')
            ->from(Phpfox::getT('directory_business_website'), 'dbw')
            ->where('dbw.business_id = ' . $iBusinessId)
            ->execute('getRows');

        return $aWebsiteInfo;
    }

    public function getBusinessLocation($iBusinessId)
    {

        $aBusinessLocation = $this->database()->select('dbl.location_title,dbl.location_address,dbl.location_longitude,dbl.location_latitude')
            ->from($this->_sTable, 'dbus')
            ->leftJoin(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = dbus.business_id')
            ->where('dbus.business_id = ' . $iBusinessId)
            ->order('location_id ASC')
            ->execute('getRows');

        return $aBusinessLocation;
    }

    public function getBusinessMainCategory($iBusinessId)
    {

        $aBusinessMainCategory = $this->database()->select('dcd.category_id')
            ->from(Phpfox::getT('directory_category_data'), 'dcd')
            ->where('dcd.business_id = ' . (int)$iBusinessId . ' AND dcd.is_main = 1')
            ->execute('getRow');

        return $aBusinessMainCategory;
    }

    public function getBusinessAdditionInfo($iBusinessId)
    {

        $aWebsiteInfo = $this->database()->select('dbusercf.usercustomfield_title,dbusercf.usercustomfield_content')
            ->from(Phpfox::getT('directory_business_usercustomfield'), 'dbusercf')
            ->where('dbusercf.business_id = ' . $iBusinessId)
            ->execute('getRows');

        return $aWebsiteInfo;
    }


    public function getBusinessForEmail($iBusinessId)
    {

        if (is_array($iBusinessId)) {
            $sWhere = 'db.business_id IN (' . implode(",", $iBusinessId) . ')';
        } else {

            $sWhere = 'db.business_id = ' . $iBusinessId;
        }

        $aRow = $this->database()
            ->select('db.name,db.package_name,db.business_id')
            ->from(Phpfox::getT("directory_business"), 'db')
            ->where($sWhere)
            ->execute("getSlaveRows");

        return $aRow;

    }

    public function getGlobalSetting()
    {
        $aRows = $this->database()
            ->select('gbs.*')
            ->from(Phpfox::getT("directory_global_setting"), 'gbs')
            ->execute("getSlaveRows");

        return $aRows;
    }

    public function countBusinessOfUserId($userId)
    {
        $sWhere = '';
        $sWhere .= ' AND bus.creator_id = ' . (int)$userId;
        $iCount = $this->database()
            ->select("COUNT(bus.business_id)")
            ->from(Phpfox::getT("directory_business"), 'bus')
            ->where('1=1' . $sWhere)
            ->innerJoin(Phpfox::getT('user'), 'userDelete', 'userDelete.user_id = bus.user_id')
            ->execute("getSlaveField");

        return $iCount;
    }

    public function getAllPackages($active = null)
    {
        $sWhere = '';
        if ($active !== null) {
            $sWhere .= ' AND pkg.active = ' . (int)$active;
        }
        $aRows = $this->database()
            ->select('pkg.*')
            ->from(Phpfox::getT("directory_package"), 'pkg')
            ->where('1=1' . $sWhere)
            ->execute("getSlaveRows");

        $aCurrentCurrencies = Phpfox::getService('directory.helper')->getCurrentCurrencies();
        $symbol = $aCurrentCurrencies[0]['symbol'];
        foreach ($aRows as $key => $value) {
            $package_id = $value['package_id'];
            $aRows[$key]['fee_display'] = Phpfox::getService('directory.helper')->getMoneyText($value['fee'], $symbol);

            $themes = $this->database()->select('pth.*, thm.*')
                ->from(Phpfox::getT("directory_package_theme"), 'pth')
                ->join(Phpfox::getT('directory_theme'), 'thm', 'thm.theme_id = pth.theme_id')
                ->where('pth.package_id = ' . (int)$package_id)
                ->execute('getSlaveRows');
            $aRows[$key]['themes'] = $themes;
            $modules = $this->database()->select('pmd.*, mdl.*')
                ->from(Phpfox::getT("directory_package_module"), 'pmd')
                ->join(Phpfox::getT('directory_module'), 'mdl', 'mdl.module_id = pmd.module_id')
                ->where('pmd.package_id = ' . (int)$package_id)
                ->execute('getSlaveRows');

            foreach ($modules as $iKey => $item)
            {
                $modules[$iKey]['module_phrase'] = str_replace('directory.','', $item['module_phrase']);
            }

            $aRows[$key]['modules'] = $modules;
            $settings = $this->database()->select('psm.*, pst.*')
                ->from(Phpfox::getT("directory_package_setting_mapping"), 'psm')
                ->join(Phpfox::getT('directory_package_setting'), 'pst', 'pst.setting_id = psm.setting_id')
                ->where('psm.package_id = ' . (int)$package_id)
                ->execute('getSlaveRows');

            foreach ($settings as $iKey => $item)
            {
                $settings[$iKey]['setting_phrase'] = str_replace('directory.','', $item['setting_phrase']);
            }

            $aRows[$key]['settings'] = $settings;
        }

        return $aRows;
    }

    public function getAllThemes()
    {
        $aRows = $this->database()
            ->select("*")
            ->from(Phpfox::getT("directory_theme"))
            ->execute("getSlaveRows");

        return $aRows;
    }

    public function getAllModule()
    {
        $aRows = $this->database()
            ->select("*")
            ->from(Phpfox::getT("directory_module"))
            ->execute("getSlaveRows");

        if (!empty($aRows)) {
            foreach ($aRows as $ikey => $aRow)
                $aRows[$ikey]['module_phrase'] = str_replace('directory.', '', $aRow['module_phrase']);
        }

        return $aRows;
    }

    public function getAllPackageSetting()
    {
        $aRows = $this->database()
            ->select("*")
            ->from(Phpfox::getT("directory_package_setting"))
            ->execute("getSlaveRows");

        if (!empty($aRows)) {
            foreach ($aRows as $ikey => $aRow)
                $aRows[$ikey]['setting_phrase'] = str_replace('directory.', '', $aRow['setting_phrase']);
        }

        return $aRows;
    }

    public function getFieldsComparison()
    {
        $aRows = $this->database()
            ->select("*")
            ->from(Phpfox::getT("directory_comparison"))
            ->execute("getSlaveRows");

        foreach ($aRows as $iKey => $aRow)
        {
            $aRows[$iKey]['comparison_name'] = str_replace('directory.', '', $aRow['comparison_name']);
        }

        return $aRows;
    }

    public function getBusinessCreator()
    {
        $aRows = $this->database()
            ->select("dc.*," . Phpfox::getUserField())
            ->from(Phpfox::getT("directory_creator"), 'dc')
            ->leftJoin(Phpfox::getT("user"), 'u', 'dc.user_id =  u.user_id')
            ->execute("getSlaveRows");

        return $aRows;
    }

    public function getBusinessCreatorId()
    {
        $aRows = $this->database()
            ->select("dc.user_id")
            ->from(Phpfox::getT("directory_creator"), 'dc')
            ->execute("getSlaveRows");
        $aUsers = array();
        foreach ($aRows as $key => $aRow) {
            $aUsers[] = $aRow['user_id'];
        }
        return implode(",", $aUsers);
    }


    public function getManageBusinessCreator($iPage = 0, $iLimit = NULL, $iCount = NULL)
    {
        $iCount = $this->database()
            ->select("COUNT(*)")
            ->from(Phpfox::getT("directory_creator"), 'dc')
            ->execute("getSlaveField");
        $aRows = array();
        if ($iCount) {

            $aRows = $this->database()
                ->select("dc.*," . Phpfox::getUserField())
                ->from(Phpfox::getT("directory_creator"), 'dc')
                ->leftJoin(Phpfox::getT("user"), 'u', 'dc.user_id =  u.user_id')
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getSlaveRows");
        }


        return array($iCount, $aRows);
    }

    public function getRecentReviews($iLimit = 3)
    {
        $aBusinesses = $this->database()->select('dr.*,dbus.*,' . Phpfox::getUserField())
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->join(Phpfox::getT('directory_business'), 'dbus', 'dbus.business_id = dr.business_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dr.user_id')
            ->limit(0, $iLimit)
            ->order('dr.timestamp DESC')
            ->execute('getSlaveRows');

        foreach ($aBusinesses as $key => $aBusiness) {
            $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name'], false, "").'reviews';

            $total_score_around = floor($aBusinesses[$key]['rating'] / 2);
            $total_score = $aBusinesses[$key]['rating'] / 2;
            $aBusinesses[$key]['total_score_text'] = '';
            $aBusinesses[$key]['total_score_text'] .= '<a href="'.$sLink .'" class="ync-outer-rating ync-outer-rating-row mini ync-rating-sm"><div class="ync-outer-rating-row"><div class="ync-rating-star">';

            for ($i = 0; $i < 5; $i++){
                if ($i < $total_score_around) {
                    $aBusinesses[$key]['total_score_text'] .= '<i class="ico ico-star" aria-hidden="true"></i>';
                } else if (($total_score - $total_score_around > 0) && ($total_score - $i) > 0) {
                    $aBusinesses[$key]['total_score_text'] .= '<i class="ico ico-star half-star" aria-hidden="true"></i>';
                } else {
                    $aBusinesses[$key]['total_score_text'] .= '<i class="ico ico-star disable" aria-hidden="true"></i>';
                }
            }

            $aBusinesses[$key]['total_score_text'] .= '</div></div></a>';
        }


        return $aBusinesses;
    }

    public function getReviewsByBusinessId($iBusinessId, $iPage = 0, $iLimit = 1)
    {

        $aBusinesses = $this->database()->select('dr.*,' . Phpfox::getUserField())
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dr.user_id')
            ->where('dr.business_id =' . (int)$iBusinessId)
            ->execute('getSlaveRows');

        $iCnt = count($aBusinesses);

        $aBusinesses = $this->database()->select('dr.*,' . Phpfox::getUserField())
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dr.user_id')
            ->where('dr.business_id =' . (int)$iBusinessId)
            ->limit($iPage, $iLimit, $iCnt)
            ->execute('getSlaveRows');


        foreach ($aBusinesses as $key => $aBusiness) {

            $total_score_around = (int)$aBusinesses[$key]['rating'] / 2;
            $aBusinesses[$key]['total_score_text'] = '';
            for ($i = 1; $i <= $total_score_around; $i++) {
                $aBusinesses[$key]['total_score_text'] .= '<div class="star-rating js_rating_star star-rating-applied star-rating-live star-rating-hover" ><a title=""></a></div>';
            }
            for ($i = 1; $i <= (5 - $total_score_around); $i++) {
                $aBusinesses[$key]['total_score_text'] .= '<div class="star-rating js_rating_star star-rating-applied star-rating-live" ><a title=""></a></div>';
            }

        }


        return array($iCnt, $aBusinesses);
    }

    public function getReviewsById($iReviewId)
    {

        $aReview = $this->database()->select('dr.*')
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->where('dr.review_id =' . (int)$iReviewId)
            ->execute('getSlaveRow');

        return $aReview;
    }

    public function getExistingReview($iBusinessId, $iUserId)
    {

        $aReview = $this->database()->select('dr.*')
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->where('dr.business_id =' . (int)$iBusinessId . ' AND dr.user_id =' . (int)$iUserId)
            ->execute('getSlaveRow');

        return $aReview;
    }


    public function getBusinessYouMayLike($iLimit)
    {
        $aLastSearch = $this->database()->select('dbsh.*')
            ->from(Phpfox::getT('directory_business_searchhistory'), 'dbsh')
            ->where('dbsh.user_id = ' . (int)Phpfox::getUserId())
            ->execute('getSlaveRow');

        $aBusinesses = array();

        $sType = 'most-checkin';
        $sConditions = 'dbus.privacy = 0 ';

        $sRun = "(" . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running') . ")";

        $sConditions .= ' AND dbus.package_start_time <= ' . PHPFOX_TIME . ' AND dbus.package_end_time >= ' . PHPFOX_TIME . ' AND dbus.business_status IN ' . $sRun;

        $sConditions .= " AND dcd.is_main = 1";

        /*show by lasting search*/
        $aCategories = array();
        if (isset($aLastSearch['data'])) {
            $aCategories = json_decode($aLastSearch['data'], true);
            foreach ($aCategories as $iKey => $iCategories) {
                if (!is_numeric($iCategories)) {
                    unset($aCategories[$iKey]);
                }
            }

            Phpfox::getService('directory.process')->saveLastingSearch($aCategories);
        }
        if (count($aCategories)) {
            $sConditions .= " AND dc.category_id IN (" . implode(",", $aCategories) . ")";
        }


        $sConditions .= " AND dbus.creator_id != " . Phpfox::getUserId() . " AND dbus.user_id != " . Phpfox::getUserId();

        $iCnt = $this->database()->select("COUNT(*)")
            ->from($this->_sTable, 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->leftJoin(Phpfox::getT('directory_category'), 'dc', 'dc.is_active = 1 AND dc.category_id = dcd.category_id')
            ->where($sConditions)
            ->execute('getSlaveField');

        $aBusinesses = $this->database()->select(Phpfox::getUserField() . ', dbus.*, dc.title as category_title, dc.category_id as category_id')
            ->from($this->_sTable, 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->leftJoin(Phpfox::getT('directory_category'), 'dc', 'dc.is_active = 1 AND dc.category_id = dcd.category_id')
            ->where($sConditions)
            ->execute('getSlaveRows');

        shuffle($aBusinesses);

        $aReturnBusiness = ($iCnt < $iLimit) ? (array_slice($aBusinesses, 0, $iCnt)) : (array_slice($aBusinesses, 0, $iLimit));

        foreach ($aReturnBusiness as $key => $aBusiness) {
            $aReturnBusiness[$key] = $this->retrieveMoreInfoFromBusiness($aBusinesses[$key], $sType);
            if (empty($aBusinesses[$key]['logo_path'])) {
                $aBusinesses[$key]['default_logo_path'] = Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png';
            }
        }

        return $aReturnBusiness;

    }

    public function getSuggestedBusiness($iBusinessId)
    {


        $aCategories = $this->database()->select('dc.category_id')
            ->from($this->_sTable, 'dbus')
            ->join(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->join(Phpfox::getT('directory_category'), 'dc', 'dc.category_id = dcd.category_id')
            ->where('dbus.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRows');

        $aSqlCategory = array();
        foreach ($aCategories as $key_category => $aCategory) {
            $aSqlCategory[] = $aCategory['category_id'];
        }

        $sType = '';
        $sConditions = 'dbus.privacy = 0 ';

        $sRun = "(" . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running') . ")";

        $sConditions .= ' AND dbus.package_start_time <= ' . PHPFOX_TIME . ' AND dbus.package_end_time >= ' . PHPFOX_TIME . ' AND dbus.business_status IN ' . $sRun . ' AND dbus.business_id != ' . (int)$iBusinessId . ' ';

        $sConditions .= " AND dcd.is_main = 1";

        if (count($aSqlCategory)) {
            $sConditions .= " AND dc.category_id IN (" . implode(",", $aSqlCategory) . ")";
        }


        $sConditions .= " AND dbus.creator_id != " . Phpfox::getUserId() . " AND dbus.user_id != " . Phpfox::getUserId();

        $iCnt = $this->database()->select("COUNT(*)")
            ->from($this->_sTable, 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->leftJoin(Phpfox::getT('directory_category'), 'dc', 'dc.is_active = 1 AND dc.category_id = dcd.category_id')
            ->where($sConditions)
            ->execute('getSlaveField');

        $aBusinesses = $this->database()->select(Phpfox::getUserField() . ', dbus.*, dc.title as category_title, dc.category_id as category_id')
            ->from($this->_sTable, 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->leftJoin(Phpfox::getT('directory_category'), 'dc', 'dc.is_active = 1 AND dc.category_id = dcd.category_id')
            ->where($sConditions)
            ->execute('getSlaveRows');

        shuffle($aBusinesses);

        $aReturnBusiness = ($iCnt < 4) ? (array_slice($aBusinesses, 0, $iCnt)) : (array_slice($aBusinesses, 0, 4));

        foreach ($aReturnBusiness as $key => $aBusiness) {
            $aReturnBusiness[$key] = $this->retrieveMoreInfoFromBusiness($aBusinesses[$key], $sType);
        }

        return $aReturnBusiness;


    }

    public function getBusinessOwnerId($iBusinessId)
    {
        return $this->database()->select('user_id')->from(Phpfox::getT('directory_business'))->where("business_id = {$iBusinessId}")->execute('getSlaveField');
    }

    public function getFollowerIds($iBusinessId)
    {
        $aFollowers = $this->database()->select('user_id')
            ->from(Phpfox::getT('directory_follow'))
            ->where("business_id = {$iBusinessId}")
            ->execute('getSlaveRows');

        return $aFollowers;
    }

    public function isFollowBusiness($iUserId, $iBusinessId)
    {
        $aFollower = $this->database()->select('user_id')
            ->from(Phpfox::getT('directory_follow'))
            ->where("business_id = {$iBusinessId} AND user_id = {$iUserId}")
            ->execute('getSlaveRow');

        return count($aFollower);
    }

    public function isFavoriteBusiness($iUserId, $iBusinessId)
    {
        $aFavorite = $this->database()->select('user_id')
            ->from(Phpfox::getT('directory_favorite'))
            ->where("business_id = {$iBusinessId} AND user_id = {$iUserId}")
            ->execute('getSlaveRow');

        return count($aFavorite);
    }

    private function __checkMapForClaimingBusiness($aConditions)
    {
        foreach ($aConditions as $aCondition) {
            $pos = strrpos($aCondition, "type = 'claiming'");
            if ($pos === false) {
                // not found...
            } else {
                return true;
            }
        }

        return false;
    }

    public function getBussinessForMap($aConditions, $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {

        $sConditions = '';

        if ($this->__checkMapForClaimingBusiness($aConditions) == false) {
            $sRun = "(" . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running') . ")";

            $sConditions .= ' dbus.business_status IN ' . $sRun . ' ';
        } else {
            $sConditions = ' 1=1 ';
        }

        foreach ($aConditions as $aCondition) {

            if (strpos($aCondition, '%PRIVACY%') !== false) {
                $sConditions .= str_replace('%PRIVACY%', '0', $aCondition);
            } else {
                $sConditions .= $aCondition;
            }
        }

        $sOrder = 'dbus.business_id DESC';

        $iCount = $this->database()
            ->select("COUNT( DISTINCT( dbus.business_id ) )")
            ->from($this->_sTable, 'dbus')
            ->join(Phpfox::getT('directory_business_text'), 'dbt', 'dbt.business_id = dbus.business_id')
            ->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->leftJoin(Phpfox::getT('directory_category'), 'dc', 'dc.is_active = 1 AND dc.category_id = dcd.category_id')
            ->join(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = dbus.business_id')
            ->where($sConditions)
            ->execute("getSlaveField");

        $aBusinesses = array();
        if ($iCount) {
            $aBusinesses = $this->database()->select('dbus.business_id,dbus.*, dc.title as category_title, dc.category_id as category_id,dbl.*,dbt.description_parsed AS description')
                ->from($this->_sTable, 'dbus')
                ->join(Phpfox::getT('directory_business_text'), 'dbt', 'dbt.business_id = dbus.business_id')
                ->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
                ->leftJoin(Phpfox::getT('directory_category'), 'dc', 'dc.is_active = 1 AND dc.category_id = dcd.category_id')
                ->join(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = dbus.business_id')
                ->leftjoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = dbus.business_id')
                ->where($sConditions)
                ->order($sOrder)
                ->group('dbus.business_id')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }

        if (count($aBusinesses)) {

            foreach ($aBusinesses as $key => $aBusiness) {
                $aBusinesses[$key] = $this->retrieveMoreInfoFromBusiness($aBusinesses[$key], '');
                if(!empty($aBusiness['logo_path'])){
                    $aBusinesses[$key]['url_image'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aBusiness['server_id'],
                        'path' => 'core.url_pic',
                        'file' => $aBusiness['logo_path'],
                        'suffix' => '_400_square',
                        'width' => 80,
                        'height' => 80,
                        'return_url' => true
                    ));
                }else{
                    $aBusinesses[$key]['url_image'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aBusiness['server_id'],
                        'path' => '',
                        'file' => Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png',
                        'suffix' => '_400_square',
                        'width' => 80,
                        'height' => 80,
                        'return_url' => true
                    ));
                }


            }

        }
        return $aBusinesses;
    }

    public function getImages($iBusinessId, $iLimit = null)
    {
        $aImages = $this->database()->select('di.*')
            ->from(Phpfox::getT('directory_image'), 'di')
            ->where('di.business_id = ' . $iBusinessId)
            ->order('di.ordering ASC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if ($aImages) {
            foreach ($aImages as $k => $aImage) {
                $aImages[$k]['image'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aImage['server_id'],
                        'file' => 'yndirectory/' . $aImage['image_path'],
                        'path' => 'core.url_pic',
                        'suffix' => '_120',
                        'max_width' => '120',
                        'max_height' => '120'
                    )
                );
            }
        }

        return $aImages;
    }

    public function getPageBusiness($iBusinessId)
    {

        $aPages = $this->database()->select('dbm.*')
            ->from(Phpfox::getT('directory_business_module'), 'dbm')
            ->where('dbm.business_id = ' . $iBusinessId)
            ->execute('getSlaveRows');

        /*        if(!count($aPages)){

            $aPages = $this->database()->select('dm.*')
					->from(Phpfox::getT('directory_module'),'dm')
					->execute('getSlaveRows');

        }
*/
        return $aPages;
    }

    public function getPageBusinessByDataId($iDataId)
    {
        $aPage = $this->database()->select('dbm.*')
            ->from(Phpfox::getT('directory_business_module'), 'dbm')
            ->where('dbm.data_id = ' . $iDataId)
            ->execute('getSlaveRow');
        return $aPage;
    }

    public function getPageByBusinessModuleId($iBusinessId, $iModuleId)
    {
        $aPage = $this->database()->select('dbm.*')
            ->from(Phpfox::getT('directory_business_module'), 'dbm')
            ->where('dbm.module_id = ' . $iModuleId . ' AND dbm.business_id = ' . $iBusinessId)
            ->execute('getSlaveRow');

        return $aPage;
    }

    public function getPageAboutUsByBusinessId($iBusinessId)
    {
        $aAboutUs = $this->database()->select('dbm.contentpage_parsed as contentpage')
            ->from(Phpfox::getT('directory_business_module'), 'dbm')
            ->where('dbm.business_id = ' . $iBusinessId . ' AND dbm.module_name = \'aboutus\'')
            ->execute('getSlaveRow');

        return $aAboutUs;
    }

    public function getFAQsByBusinessId($iBusinessId)
    {
        $aFAQs = $this->database()->select('dbf.*')
            ->from(Phpfox::getT('directory_business_faq'), 'dbf')
            ->where('dbf.business_id = ' . $iBusinessId)
            ->execute('getSlaveRows');

        return $aFAQs;
    }

    public function getFAQById($iFaqId)
    {
        $aFAQ = $this->database()->select('dbf.*')
            ->from(Phpfox::getT('directory_business_faq'), 'dbf')
            ->where('dbf.faq_id = ' . $iFaqId)
            ->execute('getSlaveRow');

        return $aFAQ;
    }

    public function getPageContactUsByBusinessId($iBusinessId)
    {
        $aContactUs = $this->database()->select('dbc.*')
            ->from(Phpfox::getT('directory_business_contactus'), 'dbc')
            ->where('dbc.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRow');

        return $aContactUs;
    }

    public function getMemberRolesByBusinessId($iBusinessId)
    {
        $aMemberRoles = $this->database()->select('dbmr.*')
            ->from(Phpfox::getT('directory_business_memberrole'), 'dbmr')
            ->where('dbmr.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRows');

        return $aMemberRoles;
    }


    public function getMemberRolesByBusinessIdWithType($iBusinessId, $sType = 'guest')
    {
        $aMemberRoles = $this->database()->select('dbmr.*')
            ->from(Phpfox::getT('directory_business_memberrole'), 'dbmr')
            ->where('dbmr.business_id = ' . (int)$iBusinessId . ' AND dbmr.type = \'' . $sType . '\'')
            ->execute('getSlaveRow');

        return $aMemberRoles;
    }

    public function getRoleMemberById($iRoleId)
    {
        $aRole = $this->database()->select('dbmr.*')
            ->from(Phpfox::getT('directory_business_memberrole'), 'dbmr')
            ->where('dbmr.role_id = ' . $iRoleId)
            ->execute('getSlaveRow');
        return $aRole;
    }

    public function getMemberRoleSettingByBusinessId($iBusinessId)
    {

        $aRoles = $this->getMemberRolesByBusinessId($iBusinessId);

        foreach ($aRoles as $key => $aRole) {

            $aRoleSetting = $this->database()->select('rs.*,rsd.status')
                ->from(Phpfox::getT('directory_business_memberrolesetting'), 'rs')
                ->join(Phpfox::getT('directory_business_memberrolesettingdata'), 'rsd', 'rsd.setting_id = rs.setting_id')
                ->where('rsd.role_id = ' . (int)$aRole['role_id'])
                ->execute('getSlaveRows');

            if (count($aRoleSetting)) {
                $aRoles[$key]['settings'] = $aRoleSetting;
            }
        }

        return $aRoles;
    }

    public function getAnnouncementsByBusinessId($iBusinessId)
    {
        $aAnnouncements = $this->database()->select('dba.*,dba.announcement_content_parse as content')
            ->from(Phpfox::getT('directory_business_announcement'), 'dba')
            ->where('dba.business_id = ' . $iBusinessId)
            ->execute('getSlaveRows');

        $sDateFormat = "F d, Y h:i A";
        foreach ($aAnnouncements as $key => $aAnnouncement) {
            $aAnnouncements[$key]['timestamp'] = Phpfox::getTime($sDateFormat, $aAnnouncements[$key]['timestamp'], false);
            //get user who already read
            $aReadBy = $this->database()->select('dbah.user_id,' . Phpfox::getUserField())
                ->from(Phpfox::getT('directory_business_announcement_hide'), 'dbah')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbah.user_id')
                ->where('dbah.announcement_id = ' . (int)$aAnnouncement['announcement_id'])
                ->execute('getSlaveRows');

            foreach ($aReadBy as $keyRead => $aVal) {
                $aReadBy[$keyRead]['avatar'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aVal['user_server_id'],
                        'title' => $aVal['full_name'],
                        'path' => 'core.url_user',
                        'file' => $aVal['user_image'],
                        'suffix' => '_50_square',
                        'max_width' => 75,
                        'max_height' => 75,
                        'no_default' => true,
                        'time_stamp' => true,
                        'class' => 'border'
                    )
                );
            }
            $aAnnouncements[$key]['iCntReadBy'] = count($aReadBy);


            $aAnnouncements[$key]['readby'] = $aReadBy;

        }


        $iCnt = count($aAnnouncements);
        return array($iCnt, $aAnnouncements);
    }

    public function getAnnouncementsByBusinessIdForDetail($iBusinessId)
    {

        $aAnnouncements = $this->database()->select('dba.*,dba.announcement_content_parse as content')
            ->from(Phpfox::getT('directory_business_announcement'), 'dba')
            ->where('dba.business_id = ' . $iBusinessId)
            ->execute('getSlaveRows');

        $aAnnouncementResults = array();
        foreach ($aAnnouncements as $key => $aAnnouncement) {
            $aReadBy = $this->database()->select('dbah.announcement_id')
                ->from(Phpfox::getT('directory_business_announcement_hide'), 'dbah')
                ->where('dbah.announcement_id = ' . (int)$aAnnouncement['announcement_id'] . ' AND dbah.user_id = ' . (int)Phpfox::getUserId())
                ->execute('getSlaveRows');

            if (count($aReadBy)) {
                unset($aAnnouncements[$key]);
            } else {
                $aAnnouncementResults[] = $aAnnouncements[$key];
            }
        }

        $iCnt = count($aAnnouncementResults);

        return array($iCnt, $aAnnouncementResults);
    }


    public function getAnnouncementsByIdForEdit($iId)
    {
        $aAnnouncements = $this->database()->select('dba.*,dba.announcement_content as content')
            ->from(Phpfox::getT('directory_business_announcement'), 'dba')
            ->where('dba.announcement_id = ' . (int)$iId)
            ->execute('getSlaveRow');


        return $aAnnouncements;
    }

    public function getLastestPayment($iBusinessId)
    {

        $aTransaction = $this->database()->select('dt.*')
            ->from(Phpfox::getT('directory_invoice'), 'dt')
            ->where('dt.item_id = ' . (int)$iBusinessId)
            ->order('dt.invoice_id DESC')
            ->execute('getSlaveRow');


        return $aTransaction;
    }

    public function getCountMemberOfBusiness($iBusinessId)
    {

        $iCount = $this->database()->select('COUNT(*) as count')
            ->from(Phpfox::getT('directory_business_userroledata'), 'e')
            ->join(Phpfox::getT('directory_business_memberrole'), 'mbr', 'mbr.role_id = e.role_id')
            ->where('mbr.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRow');

        return $iCount['count'];

    }

    public function getCountFollowerOfBusiness($iBusinessId)
    {

        $iCount = $this->database()->select('COUNT(*) as count')
            ->from(Phpfox::getT('directory_follow'), 'df')
            ->where('df.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRow');

        return $iCount['count'];


    }

    public function getCountReviewOfBusiness($iBusinessId)
    {

        $iCount = $this->database()->select('COUNT(*) as count')
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->where('dr.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRow');

        return $iCount['count'];

    }

    public function getCountRatingOfBusiness($iBusinessId)
    {

        $iCount = $this->database()->select('SUM(rating) as count')
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->where('dr.business_id = ' . (int)$iBusinessId)
            ->group('dr.business_id')
            ->execute('getSlaveRow');

        return $iCount['count'];

    }

    public function getChartData($iBusinessId, $type, $metric, $duration, $extraData = array())
    {

        //determine start end
        $start_time = 0;
        $end_time = 0;
        $period = array();
        switch ($duration) {
            case 'today':
                // Today
                $period = Phpfox::getService('directory.helper')->getPeriodByUserTimeZone('today');
                $start_time = $period['start'];
                break;
            case 'yesterday':
                // Yesterday
                $period = Phpfox::getService('directory.helper')->getPeriodByUserTimeZone('yesterday');

                $start_time = $period['start'];
                break;
            case 'last_week':
                // Last week
                $period = Phpfox::getService('directory.helper')->getPeriodByUserTimeZone('last_week');

                $start_time = $period['start'];
                $end_time = $period['end'];
                break;
            case 'range_of_dates':
                // Range of dates
                $js_end__datepicker = $extraData['js_end__datepicker'];
                $js_end__datepicker = explode("/", $js_end__datepicker);
                $js_start__datepicker = $extraData['js_start__datepicker'];
                $js_start__datepicker = explode("/", $js_start__datepicker);

                $period['start'] = mktime(0, 0, 0, $js_start__datepicker[0], $js_start__datepicker[1], $js_start__datepicker[2]);
                $period['end'] = mktime(0, 0, 0, $js_end__datepicker[0], $js_end__datepicker[1], $js_end__datepicker[2]);


                $start_time = $period['start'];
                $end_time = $period['end'];
                break;
            default:
                break;
        }

        switch ($metric) {
            case 'reviews':
                $data = $this->getChartDataForReviews($iBusinessId, $start_time, $end_time);
                break;
            case 'followers':
                $data = $this->getChartDataForFollower($iBusinessId, $start_time, $end_time);
                break;
            case 'likes':
                $data = $this->getChartDataForLike($iBusinessId, $start_time, $end_time);
                break;
            case 'comments':
                $data = $this->getChartDataForComment($iBusinessId, $start_time, $end_time);
                break;
            case 'members':
                $data = $this->getChartDataForMember($iBusinessId, $start_time, $end_time);
                break;
            default:
                $data = array();
                break;
        }

        switch ($duration) {
            case 'today':
                // Today
                $start_time = Phpfox::getTime('j/n/Y', $period['start']);

                /*				$data_today = isset($data[$start_time])?$data[$start_time]:array();
				$data = $data_today;
*/
                break;
            case 'yesterday':
                // Yesterday
                $start_time = Phpfox::getTime('j/n/Y', $period['start']);

                /*$data_yesterday = isset($data[$start_time])?$data[$start_time]:array();
				$data = $data_yesterday;
				*/
                break;
            case 'last_week':
                // Last week
                $start_time = $period['start'];
                $end_time = $period['end'];

                $data_last_weeks = array();
                while ($start_time <= $end_time) {

                    $start = Phpfox::getTime('j/n/Y', $start_time);

                    if (isset($data[$start])) {
                        $data_last_weeks[$start] = $data[$start];
                    } else {
                        $data[$start] = 0;
                        $data_last_weeks[$start] = $data[$start];
                    }

                    $start_time += 24 * 60 * 60;
                }
                $data = $data_last_weeks;
                break;
            case 'range_of_dates':

                $start_time = $period['start'];
                $end_time = $period['end'];

                // Range of dates
                $data_range_of_dates = array();

                while ($start_time <= $end_time) {

                    $start = Phpfox::getTime('j/n/Y', $start_time);


                    if (isset($data[$start])) {
                        $data_range_of_dates[$start] = $data[$start];
                    } else {
                        $data[$start] = 0;
                        $data_range_of_dates[$start] = $data[$start];
                    }

                    $start_time += 24 * 60 * 60;
                }
                $data = $data_range_of_dates;
                break;
            default:
                break;
        }


        //handle data for type
        switch ($type) {
            case 'normal':
                break;
            case 'cumulative':
                $old_value = 0;
                foreach ($data as $key => $value) {
                    $data[$key] += $old_value;
                    $old_value = $data[$key];
                }
                break;
            case 'changein':
                $old_value = 0;
                foreach ($data as $key => $value) {
                    $data[$key] -= $old_value;
                    $old_value = $value;
                }
                break;
        }

        return $data;

    }

    public function getChartDataForReviews($iBusinessId, $iStartTime, $iEndTime)
    {
        $condition_time = '';
        if ($iEndTime != 0) {
            $condition_time = ' AND dr.timestamp >= ' . $iStartTime . ' AND dr.timestamp <= ' . $iEndTime;
        } else {
            $condition_time = ' AND dr.timestamp >= ' . $iStartTime;
        }

        $aReviews = $this->database()->select('dr.*')
            ->from(Phpfox::getT('directory_review'), 'dr')
            ->where('dr.business_id = ' . (int)$iBusinessId . $condition_time)
            ->order('dr.timestamp ASC')
            ->execute('getSlaveRows');

        $aChartData = array();

        if (count($aReviews)) {
            foreach ($aReviews as $key => $value) {
                $timestamp = Phpfox::getTime('j/n/Y', $value['timestamp']);
                if (array_key_exists($timestamp, $aChartData)) {
                    $aChartData[$timestamp] += 1;
                } else {
                    $aChartData[$timestamp] = 1;
                }
            }
        }

        return $aChartData;
    }

    public function getChartDataForFollower($iBusinessId, $iStartTime, $iEndTime)
    {

        $condition_time = '';
        if ($iEndTime != 0) {
            $condition_time = ' AND df.time_stamp >= ' . $iStartTime . ' AND df.time_stamp <= ' . $iEndTime;
        } else {
            $condition_time = ' AND df.time_stamp >= ' . $iStartTime;
        }

        $aFollowers = $this->database()->select('df.*')
            ->from(Phpfox::getT('directory_follow'), 'df')
            ->where('df.business_id = ' . (int)$iBusinessId . $condition_time)
            ->order('df.time_stamp ASC')
            ->execute('getSlaveRows');

        $aChartData = array();

        if (count($aFollowers)) {
            foreach ($aFollowers as $key => $value) {
                $timestamp = Phpfox::getTime('j/n/Y', $value['time_stamp']);
                if (array_key_exists($timestamp, $aChartData)) {
                    $aChartData[$timestamp] += 1;
                } else {
                    $aChartData[$timestamp] = 1;
                }
            }
        }

        return $aChartData;
    }

    public function getChartDataForComment($iBusinessId, $iStartTime, $iEndTime)
    {
        $condition_time = '';
        if ($iEndTime != 0) {
            $condition_time = ' AND dfeed.time_stamp >= ' . $iStartTime . ' AND dfeed.time_stamp <= ' . $iEndTime;
        } else {
            $condition_time = ' AND dfeed.time_stamp >= ' . $iStartTime;
        }

        $aFollowers = $this->database()->select('dfeed.*')
            ->from(Phpfox::getT('directory_feed'), 'dfeed')
            ->where('dfeed.parent_user_id = ' . (int)$iBusinessId . ' AND dfeed.type_id = \'directory_comment\'' . $condition_time)
            ->order('dfeed.time_stamp ASC')
            ->execute('getSlaveRows');

        $aChartData = array();

        if (count($aFollowers)) {
            foreach ($aFollowers as $key => $value) {
                $timestamp = Phpfox::getTime('j/n/Y', $value['time_stamp']);
                if (array_key_exists($timestamp, $aChartData)) {
                    $aChartData[$timestamp] += 1;
                } else {
                    $aChartData[$timestamp] = 1;
                }
            }
        }

        return $aChartData;
    }

    public function getChartDataForLike($iBusinessId, $iStartTime, $iEndTime)
    {

        $condition_time = '';
        if ($iEndTime != 0) {
            $condition_time = ' AND l.time_stamp >= ' . $iStartTime . ' AND l.time_stamp <= ' . $iEndTime;
        } else {
            $condition_time = ' AND l.time_stamp >= ' . $iStartTime;
        }

        $aFollowers = $this->database()->select('l.*')
            ->from(Phpfox::getT('like'), 'l')
            ->where('l.item_id = ' . (int)$iBusinessId . $condition_time)
            ->order('l.time_stamp ASC')
            ->execute('getSlaveRows');

        $aChartData = array();

        if (count($aFollowers)) {
            foreach ($aFollowers as $key => $value) {
                $timestamp = Phpfox::getTime('j/n/Y', $value['time_stamp']);
                if (array_key_exists($timestamp, $aChartData)) {
                    $aChartData[$timestamp] += 1;
                } else {
                    $aChartData[$timestamp] = 1;
                }
            }
        }

        return $aChartData;
    }

    public function getChartDataForMember($iBusinessId, $iStartTime, $iEndTime)
    {

        $condition_time = '';
        if ($iEndTime != 0) {
            $condition_time = ' AND dbur.time_stamp >= ' . $iStartTime . ' AND dbur.time_stamp <= ' . $iEndTime;
        } else {
            $condition_time = ' AND dbur.time_stamp >= ' . $iStartTime;
        }

        $aFollowers = $this->database()->select('dbur.*')
            ->from(Phpfox::getT('directory_business_userroledata'), 'dbur')
            ->join(Phpfox::getT('directory_business_memberrole'), 'dbmr', 'dbmr.role_id =  dbur.role_id')
            ->where('dbmr.business_id = ' . (int)$iBusinessId . $condition_time)
            ->order('dbur.time_stamp ASC')
            ->execute('getSlaveRows');

        $aChartData = array();

        if (count($aFollowers)) {
            foreach ($aFollowers as $key => $value) {
                $timestamp = Phpfox::getTime('j/n/Y', $value['time_stamp']);
                if (array_key_exists($timestamp, $aChartData)) {
                    $aChartData[$timestamp] += 1;
                } else {
                    $aChartData[$timestamp] = 1;
                }
            }
        }

        return $aChartData;
    }


    public function getCountPageOfBusiness($iBusinessId)
    {
        $iCount = $this->database()->select('COUNT(*) as count')
            ->from(Phpfox::getT('directory_business_module'), 'e')
            ->where('e.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRow');

        return $iCount['count'];
    }

    public function checkAndUpdateStatus($aBusiness)
    {
        switch ($aBusiness['business_status']) {
            case Phpfox::getService('directory.helper')->getConst('business.status.approved'):
                if (($aBusiness['package_start_time'] <= PHPFOX_TIME && $aBusiness['package_end_time'] >= PHPFOX_TIME) &&
                    ($aBusiness['package_start_time'] != 0 && $aBusiness['package_end_time'] != 0)
                ) {
                    Phpfox::getService('directory.process')->updateBusinessStatus($aBusiness['business_id'], Phpfox::getService('directory.helper')->getConst('business.status.running'));
                } else if ($aBusiness['package_end_time'] < PHPFOX_TIME) {
                    Phpfox::getService('directory.process')->updateBusinessStatus($aBusiness['business_id'], Phpfox::getService('directory.helper')->getConst('business.status.completed'));
                }
                break;
            case Phpfox::getService('directory.helper')->getConst('business.status.running'):
                if (($aBusiness['package_start_time'] >= PHPFOX_TIME || $aBusiness['package_end_time'] <= PHPFOX_TIME) &&
                    ($aBusiness['package_start_time'] != 0 && $aBusiness['package_end_time'] != 0)
                ) {
                    Phpfox::getService('directory.process')->updateBusinessStatus($aBusiness['business_id'], Phpfox::getService('directory.helper')->getConst('business.status.completed'));
                }
                break;
            default:
                # code...
                break;
        }

        /*update category*/

        $sCategoryTextRelated = Phpfox::getService('directory.category')->getCategoryIds($aBusiness['business_id']);

        $this->updateCountBusinessForCategory($sCategoryTextRelated);

    }

    public function getTotalMenuMyBusiness()
    {
        $sWhere = 'dbus.user_id = ' . Phpfox::getUserId() . ' AND dbus.business_status != ' . (int)Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming') . ' AND dbus.business_status != ' . (int)Phpfox::getService('directory.helper')->getConst('business.status.deleted');
        $aRows = $this->database()
            ->select('distinct dbus.business_id')
            ->from(Phpfox::getT("directory_business"), 'dbus')
            ->join(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id')
            ->where($sWhere)
            ->execute("getSlaveRows");

        return count($aRows);
    }

    public function getUserMemberRole($iUserId, $iBusinessId)
    {

        $aRow = $this->database()->select('e.*')
            ->from(Phpfox::getT('directory_business_userroledata'), 'e')
            ->join(Phpfox::getT('directory_business_memberrole'), 'mbr', 'mbr.role_id = e.role_id')
            ->where('e.user_id = ' . (int)$iUserId . ' AND mbr.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRow');
        return $aRow;
    }

    public function getAllUserRoleOfMember($iUserId)
    {

        $aRows = $this->database()->select('e.*,mbr.*,db.name')
            ->from(Phpfox::getT('directory_business_userroledata'), 'e')
            ->join(Phpfox::getT('directory_business_memberrole'), 'mbr', 'mbr.role_id = e.role_id')
            ->leftJoin(Phpfox::getT('directory_business'), 'db', 'db.business_id = mbr.business_id')
            ->where('e.user_id = ' . (int)$iUserId)
            ->execute('getSlaveRows');

        return $aRows;
    }

    public function getCustomPageOfBusiness($iCustomPage)
    {
        $aPage = $this->database()->select('dbm.*')
            ->from(Phpfox::getT('directory_business_module'), 'dbm')
            ->where('dbm.data_id = ' . (int)$iCustomPage)
            ->execute('getSlaveRow');

        return $aPage;
    }

    public function isAlreadyInvited($iBusinessId, $aFriends)
    {
        if ((int)$iBusinessId === 0) {
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
                ->from(Phpfox::getT('directory_invite'))
                ->where('business_id = ' . (int)$iBusinessId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
                ->execute('getSlaveRows');

            $aCache = array();
            foreach ($aInvites as $aInvite) {
                $aCache[$aInvite['invited_user_id']] = ($aInvite['user_id'] > 0 ? _p('directory.signed') : _p('directory.invited'));
            }

            if (count($aCache)) {
                return $aCache;
            }
        }

        return false;
    }


    public function isHaveChildCategory($iBusinessId, $iParentCategory)
    {

        $aChildCategory = array();

        $aChildCategory = $this->database()
            ->select("dc.*")
            ->from(Phpfox::getT('directory_category'), 'dc')
            ->join(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.category_id = dc.category_id')
            ->where('dcd.business_id = ' . (int)$iBusinessId . ' AND dc.parent_id = ' . (int)$iParentCategory)
            ->execute('getSlaveRow');

        return $aChildCategory;
    }

    public function getUserForTransferOwner($mAllowCustom = false, $sUserSearch = false)
    {

        $mAllowCustom = false;
        if (Phpfox::getUserBy('profile_page_id')) {
            $mAllowCustom = true;
        }

        if ($sUserSearch != false) {
            $aRows = $this->database()->select('' . Phpfox::getUserField())
                ->from(Phpfox::getT('user'), 'u')
                ->where('u.full_name LIKE "%' . Phpfox::getLib('parse.input')->clean($sUserSearch) . '%" AND u.profile_page_id = 0')
                ->limit(Phpfox::getParam('friend.friend_cache_limit'))
                ->order('u.last_activity DESC')
                ->execute('getSlaveRows');
        } else {
            (($sPlugin = Phpfox_Plugin::get('friend.service_getfromcachequery')) ? eval($sPlugin) : false);

            if (!isset($bForceQuery)) {
                $aRows = $this->database()->select('f.*, ' . Phpfox::getUserField())
                    ->from($this->_sTable, 'f')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
                    ->where(($mAllowCustom ? '' : 'f.is_page = 0 AND') . ' f.user_id = ' . Phpfox::getUserId())
                    ->limit(Phpfox::getParam('friend.friend_cache_limit'))
                    ->order('u.last_activity DESC')
                    ->execute('getSlaveRows');
            }
        }

        foreach ($aRows as $iKey => $aRow) {
            if (Phpfox::getUserId() == $aRow['user_id']) {
                unset($aRows[$iKey]);

                continue;
            }

            $aRows[$iKey]['full_name'] = html_entity_decode(Phpfox::getLib('parse.output')->split($aRow['full_name'], 20), null, 'UTF-8');
            $aRows[$iKey]['user_profile'] = ($aRow['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aRow['profile_page_id'], '', $aRow['user_name']) : Phpfox::getLib('url')->makeUrl($aRow['user_name']));
            $aRows[$iKey]['is_page'] = ($aRow['profile_page_id'] ? true : false);
            $aRows[$iKey]['user_image'] = Phpfox::getLib('image.helper')->display(array(
                    'user' => $aRow,
                    'suffix' => '_50_square',
                    'max_height' => 50,
                    'max_width' => 50,
                    'return_url' => true
                )
            );
        }

        return $aRows;

    }

    public function getLiveSearchForBusinessCreator($mAllowCustom = false, $sUserSearch = false)
    {

        $mAllowCustom = false;
        if (Phpfox::getUserBy('profile_page_id')) {
            $mAllowCustom = true;
        }

        if ($sUserSearch != false) {
            $sCreator = Phpfox::getService('directory')->getBusinessCreatorId();

            if ($sCreator != '') {

                $aRows = $this->database()->select('' . Phpfox::getUserField())
                    ->from(Phpfox::getT('user'), 'u')
                    ->where('u.full_name LIKE "%' . Phpfox::getLib('parse.input')->clean($sUserSearch) . '%" AND u.profile_page_id = 0 AND u.user_id NOT IN (' . $sCreator . ')')
                    ->limit(Phpfox::getParam('friend.friend_cache_limit'))
                    ->order('u.last_activity DESC')
                    ->execute('getSlaveRows');

            } else {

                $aRows = $this->database()->select('' . Phpfox::getUserField())
                    ->from(Phpfox::getT('user'), 'u')
                    ->where('u.full_name LIKE "%' . Phpfox::getLib('parse.input')->clean($sUserSearch) . '%" AND u.profile_page_id = 0')
                    ->limit(Phpfox::getParam('friend.friend_cache_limit'))
                    ->order('u.last_activity DESC')
                    ->execute('getSlaveRows');

            }

        } else {
            (($sPlugin = Phpfox_Plugin::get('friend.service_getfromcachequery')) ? eval($sPlugin) : false);

            if (!isset($bForceQuery)) {
                $aRows = $this->database()->select('f.*, ' . Phpfox::getUserField())
                    ->from($this->_sTable, 'f')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
                    ->where(($mAllowCustom ? '' : 'f.is_page = 0 AND') . ' f.user_id = ' . Phpfox::getUserId())
                    ->limit(Phpfox::getParam('friend.friend_cache_limit'))
                    ->order('u.last_activity DESC')
                    ->execute('getSlaveRows');
            }
        }

        foreach ($aRows as $iKey => $aRow) {
            if (Phpfox::getUserId() == $aRow['user_id']) {
                unset($aRows[$iKey]);

                continue;
            }

            $aRows[$iKey]['full_name'] = html_entity_decode(Phpfox::getLib('parse.output')->split($aRow['full_name'], 20), null, 'UTF-8');
            $aRows[$iKey]['user_profile'] = ($aRow['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aRow['profile_page_id'], '', $aRow['user_name']) : Phpfox::getLib('url')->makeUrl($aRow['user_name']));
            $aRows[$iKey]['is_page'] = ($aRow['profile_page_id'] ? true : false);
            $aRows[$iKey]['user_image'] = Phpfox::getLib('image.helper')->display(array(
                    'user' => $aRow,
                    'suffix' => '_50_square',
                    'max_height' => 50,
                    'max_width' => 50,
                    'return_url' => true
                )
            );
        }

        return $aRows;

    }

    public function updateCountBusinessForCategory($sCategoryText)
    {


        $aCategoryInfo = array();
        if ($sCategoryText != '') {

            $oHelper = Phpfox::getService('directory.helper');

            $aCategoryInfo = Phpfox::getLib('database')->select('dc.category_id,COUNT(dcd.business_id) as total_business')
                ->from(Phpfox::getT('directory_category'), 'dc')
                ->leftjoin(Phpfox::getT("directory_category_data"), 'dcd', 'dc.category_id = dcd.category_id')
                ->leftjoin(Phpfox::getT("directory_business"), 'dbus', '(dbus.business_id = dcd.business_id)')
                ->where('dc.category_id IN (' . $sCategoryText . ') AND dbus.business_status IN (5,9)')
                ->group('dc.category_id')
                ->execute('getSlaveRows');

            /*update used related category with zero*/
            Phpfox::getLib('database')->update(Phpfox::getT('directory_category'), array('used' => 0), 'category_id IN (' . $sCategoryText . ')');
        }


        if (count($aCategoryInfo)) {
            foreach ($aCategoryInfo as $key => $category) {
                $this->database()->update(Phpfox::getT('directory_category'), array('used' => (int)$category['total_business']), ' category_id = ' . (int)$category['category_id']);
            }
        }

        $this->cache()->remove('directory_category', 'substr');

        return $aCategoryInfo;
    }
    public function getTagCloud($mMaxDisplay)
    {
        return get_from_cache(['directory','tags'],function() use ($mMaxDisplay){
            return array_map(function ($row){
                return [
                    'value' => $row['total'],
                    'key'=> $row['tag'],
                    'url' => $row['tag_url'],
                    'link'=> \Phpfox_Url::instance()->makeUrl('directory.tag',$row['tag'])
                ];
            },$this->database()->select('category_id, tag_text AS tag, tag_url, COUNT(item_id) AS total')
                ->from(Phpfox::getT('tag'))
                ->where('category_id=\'business\'')
                ->group('tag_text, tag_url')
                ->order('total DESC')
                ->limit($mMaxDisplay)
                ->execute('getSlaveRows'));

        }, 1);

    }

    public function getPendingTotal()
    {
        $sCond ="dbus.business_status = " . (int)Phpfox::getService('directory.helper')->getConst('business.status.pending');

        return $this->database()->select('COUNT(*)')
            ->from($this->_sTable, "dbus")
            ->where($sCond)
            ->execute('getSlaveField');
    }
}

?>