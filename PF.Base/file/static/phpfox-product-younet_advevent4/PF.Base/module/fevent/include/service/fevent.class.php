<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Service_Fevent extends Phpfox_Service {

    private $_aCallback = false;
    private $_iImageLimit = 6;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->_sTable = Phpfox::getT('fevent');
    }

    public function callback($aCallback) {
        $this->_aCallback = $aCallback;

        return $this;
    }

    public function getQuickEvent($iEvent){
        $aEvent = $this->database()->select('e.*')
                ->from($this->_sTable, 'e')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
                ->join(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')
                ->where('e.event_id = ' . (int) $iEvent)
                ->execute('getRow');
                
        return $aEvent;
    }
    public function getEvent($sEvent, $bUseId = false, $bNoCache = false) {
        
        $oHelper = Phpfox::getService('fevent.helper'); 
        

        static $aEvent = null;

        if ($aEvent !== null && $bNoCache === false) {
            return $aEvent;
        }

        $bUseId = true;

        if (Phpfox::isUser()) {
            $this->database()->select('ei.invite_id, ei.rsvp_id, ')->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = e.event_id AND ei.invited_user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'fevent\' AND l.item_id = e.event_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = e.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        } else {
            $this->database()->select('0 as is_friend, ');
        }

        $aEvent = $this->database()->select('e.*, e.country_iso as event_country_iso, ' . (Phpfox::getParam('core.allow_html') ? 'et.description_parsed' : 'et.description') . ' AS description, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'e')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
                ->join(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')
                ->where('e.event_id = ' . (int) $sEvent)
                ->execute('getRow');

        if (!isset($aEvent['event_id'])) {
            return false;
        }

        if (!Phpfox::isUser()) {
            $aEvent['invite_id'] = 0;
            $aEvent['rsvp_id'] = 0;
        }

        if ($aEvent['view_id'] == '1') {
            if ($aEvent['user_id'] == Phpfox::getUserId() || Phpfox::getUserParam('fevent.can_approve_events') || Phpfox::getUserParam('fevent.can_view_pirvate_events')) {
                
            } else {
                return false;
            }
        }

        $aEvent['event_date'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aEvent['start_time']);
        if ($aEvent['start_time'] < $aEvent['end_time']) {
            if($aEvent['isrepeat']==-1)
            {
                $aEvent['event_date'] .= ' - ';
                if (date('dmy', $aEvent['start_time']) === date('dmy', $aEvent['end_time'])) {
                    $aEvent['event_date'] .= Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time_short'), $aEvent['end_time']);
                } else {
                    $aEvent['event_date'] .= Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aEvent['end_time']);
                }
            }
        }

        // Get custom values
        $aCustom = $this->database()->select('cv.value, cf.phrase_var_name')
                ->from(Phpfox::getT('fevent_custom_value'), 'cv')
                ->innerJoin(Phpfox::getT('fevent_custom_field'), 'cf', 'cf.field_id = cv.field_id')
                ->innerJoin(Phpfox::getT('fevent_category_data'), 'cd', 'cd.category_id = cf.category_id AND cd.event_id = cv.event_id')
                ->where('cv.event_id = ' . $aEvent['event_id'] . ' AND cf.is_active = 1')
                ->execute('getSlaveRows');
        if (isset($aCustom[0])) {
            foreach ($aCustom as $iKey => $aField) {
                $sValue = $aField['value'];
                if (preg_match("/^\[.*?\]$/", $sValue)) {
                    $aValues = explode(",", trim($sValue, '[]'));
                    $sValue = "";
                    foreach ($aValues as $sVal) {
                        $sVal = trim($sVal, '"');
                        $sValue .= "<li>$sVal</li>";
                    }
                    $sValue = '<ul>' . $sValue . '</ul>';
                }
                $aField['value'] = $sValue;
                $aCustom[$iKey] = $aField;
            }
            $aEvent["custom"] = $aCustom;
        } else {
            $aEvent["custom"] = array();
        }

        if (isset($aEvent['gmap']) && !empty($aEvent['gmap'])) {
            $aEvent['gmap'] = unserialize($aEvent['gmap']);
        }

        $aEvent['categories'] = Phpfox::getService('fevent.category')->getCategoriesById($aEvent['event_id']);

        if (!empty($aEvent['address'])) {
            $aEvent['map_location'] = $aEvent['address'];
            if (!empty($aEvent['city'])) {
                $aEvent['map_location'] .= ',' . $aEvent['city'];
            }
            if (!empty($aEvent['postal_code'])) {
                $aEvent['map_location'] .= ',' . $aEvent['postal_code'];
            }
            if (!empty($aEvent['country_child_id'])) {
                $aEvent['map_location'] .= ',' . Phpfox::getService('core.country')->getChild($aEvent['country_child_id']);
            }
            if (!empty($aEvent['country_iso'])) {
                $aEvent['map_location'] .= ',' . Phpfox::getService('core.country')->getCountry($aEvent['country_iso']);
            }

            $aEvent['map_location'] = urlencode($aEvent['map_location']);
        }
		
		$aEvent['d_start_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aEvent['start_time']); //time hour
        $aEvent['detail_start_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time'), (int)$aEvent['start_time']); //day
        $aEvent['D_start_time'] = $oHelper->displayTimeByFormat('D', (int)$aEvent['start_time']); //day
		
        $aEvent['d_end_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aEvent['end_time']); // time hour
        $aEvent['detail_end_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time'), (int)$aEvent['end_time']); //day
        $aEvent['D_end_time'] = $oHelper->displayTimeByFormat('D', (int)$aEvent['end_time']); //day

        $aEvent['bookmark'] = Phpfox::getLib('url')->permalink('fevent',$aEvent['event_id'], $aEvent['title']);
        return $aEvent;
    }

    public function getEventCoordinates() {
        return $this->database()->select('event_id, lat, lng')
                        ->from($this->_sTable)
                        ->execute('getRows');
    }

    public function getEventsByIds($aIds) {
        $sIds = join(',', $aIds);
        $aRows = $this->database()->select('event_id, lat, lng, title, start_time, start_gmt_offset, location, address, city')
                ->from($this->_sTable)
                ->where("event_id IN ($sIds)")
                ->execute('getRows');
        foreach ($aRows as $iKey => $aEvent) {
            $aEvent['start_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['start_time'], $aEvent['start_gmt_offset']);
            $aEvent['start_time'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_browse_time_stamp'), $aEvent['start_time']);
            $aEvent['link'] = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);
            $aRows[$iKey] = $aEvent;
        }
        return $aRows;
    }

    public function getTimeLeft($iId) {
        $aEvent = $this->getEvent($iId, true);

        return ($aEvent['mass_email'] + (Phpfox::getUserParam('fevent.total_mass_emails_per_hour') * 60));
    }

    public function canSendEmails($iId, $bNoCache = false) {
        if (Phpfox::getUserParam('fevent.total_mass_emails_per_hour') === 0) {
            return true;
        }

        $aEvent = $this->getEvent($iId, true, $bNoCache);

        return (($aEvent['mass_email'] + (Phpfox::getUserParam('fevent.total_mass_emails_per_hour') * 60) > PHPFOX_TIME) ? false : true);
    }

    public function getForEdit($iId, $bForce = false) {
        $aEvent = $this->database()->select('e.*, et.description')
                ->from($this->_sTable, 'e')
                ->join(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')
                ->where('e.event_id = ' . (int) $iId)
                ->execute('getRow');

        if (empty($aEvent)) {
            return false;
        }

        #Admins
        $aEvent['admins'] = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('fevent_admin'), 'ca')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')
            ->where('ca.event_id = '.$iId)
            ->execute('getSlaveRows');

        if (Phpfox::getService('fevent.helper')->canEditEvent($iId, Phpfox::getUserId(), $aEvent['user_id']) 
                || $bForce === true
                ) {
            $aEvent['timerepeat_tousertimezone'] = Phpfox::getService('fevent.helper')->convertToUserTimeZone($aEvent['timerepeat']);
            $aEvent['timerepeat_hour'] = Phpfox::getTime('H', $aEvent['timerepeat_tousertimezone'], false);
            $aEvent['timerepeat_minute'] = Phpfox::getTime('i', $aEvent['timerepeat_tousertimezone'], false);

            $aEvent['gmt_start_time'] = $aEvent['start_time'];
            $aEvent['gmt_end_time'] = $aEvent['end_time'];
            $aEvent['start_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['start_time'], $aEvent['start_gmt_offset']);
            $aEvent['end_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['end_time'], $aEvent['end_gmt_offset']);

            $aEvent['start_month'] = Phpfox::getTime('n', $aEvent['start_time'], false);
            $aEvent['start_day'] = Phpfox::getTime('j', $aEvent['start_time'], false);
            $aEvent['start_year'] = Phpfox::getTime('Y', $aEvent['start_time'], false);
            $aEvent['start_hour'] = Phpfox::getTime('H', $aEvent['start_time'], false);
            $aEvent['start_minute'] = Phpfox::getTime('i', $aEvent['start_time'], false);

            $aEvent['end_month'] = Phpfox::getTime('n', $aEvent['end_time'], false);
            $aEvent['end_day'] = Phpfox::getTime('j', $aEvent['end_time'], false);
            $aEvent['end_year'] = Phpfox::getTime('Y', $aEvent['end_time'], false);
            $aEvent['end_hour'] = Phpfox::getTime('H', $aEvent['end_time'], false);
            $aEvent['end_minute'] = Phpfox::getTime('i', $aEvent['end_time'], false);

            $aEvent['repeat_section_start_month'] = $aEvent['start_month'];
            $aEvent['repeat_section_start_day'] = $aEvent['start_day'];
            $aEvent['repeat_section_start_year'] = $aEvent['start_year'];
            $aEvent['repeat_section_start_hour'] = $aEvent['start_hour'];
            $aEvent['repeat_section_start_minute'] = $aEvent['start_minute'];

            $aEvent['repeat_section_end_month'] = $aEvent['end_month'];
            $aEvent['repeat_section_end_day'] = $aEvent['end_day'];
            $aEvent['repeat_section_end_year'] = $aEvent['end_year'];
            $aEvent['repeat_section_end_hour'] = $aEvent['end_hour'];
            $aEvent['repeat_section_end_minute'] = $aEvent['end_minute'];

            $aEvent['repeat_section_repeatuntil_month'] = Phpfox::getTime('n');
            $aEvent['repeat_section_repeatuntil_day'] = Phpfox::getTime('j');
            $aEvent['repeat_section_repeatuntil_year'] = Phpfox::getTime('Y');
            $aEvent['repeat_section_after_number_event'] = 0;

            $aEvent['categories'] = Phpfox::getService('fevent.category')->getCategoryIds($aEvent['event_id']);

            $aEvent['gmt_org_start_time'] = $aEvent['org_start_time'];
            $aEvent['gmt_org_end_time'] = $aEvent['org_end_time'];
            $aEvent['org_start_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['org_start_time'], $aEvent['start_gmt_offset']);
            $aEvent['org_end_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['org_end_time'], $aEvent['end_gmt_offset']);

            $aEvent['event_type'] = 'one_time';
            $aEvent['total_image'] = $this->countImages($iId);
            $aEvent['image_limit'] = $this->_iImageLimit;
            $aEvent['params'] = [
                'id' => $aEvent['event_id']
            ];
            if((int)$aEvent['isrepeat'] > -1){
                $aEvent['event_type'] = 'repeat';

                switch ($aEvent['isrepeat']) {
                    case '0':
                        $aEvent['repeat_section_type'] = 'repeat';
                        break;
                    
                    case '1':
                        $aEvent['repeat_section_type'] = 'weekly';
                        break;
                    
                    case '2':
                        $aEvent['repeat_section_type'] = 'monthly';
                        break;
                }


                if((int)$aEvent['after_number_event'] > 0){
                    $aEvent['repeat_section_end_repeat'] = 'after_number_event';
                    $aEvent['repeat_section_after_number_event'] = $aEvent['after_number_event'];
                } elseif($aEvent['timerepeat_tousertimezone'] != 0) {
                    $aEvent['repeat_section_end_repeat'] = 'repeat_until';
                    $aEvent['repeat_section_repeatuntil_month'] = Phpfox::getTime('n', $aEvent['timerepeat_tousertimezone'], false);
                    $aEvent['repeat_section_repeatuntil_day'] = Phpfox::getTime('j', $aEvent['timerepeat_tousertimezone'], false);
                    $aEvent['repeat_section_repeatuntil_year'] = Phpfox::getTime('Y', $aEvent['timerepeat_tousertimezone'], false);
                }
            }            

            return $aEvent;
        }
        Phpfox_Error::display(_p('you_don_t_have_permission_to_action_this_item',
            ['action' => _p('edit__l'), 'item' => _p('event__l')]));
        return false;
    }

    public function getNumbersOfAttendee($iEvent, $iRsvp){
        $iCnt = $this->database()->select('COUNT(invite_id)')
                ->from(Phpfox::getT('fevent_invite'))
                ->where('event_id = ' . (int) $iEvent . ' AND rsvp_id = ' . (int) $iRsvp)
                ->execute('getSlaveField');

        return $iCnt;
    }

    public function getInvites($iEvent, $iRsvp, $iPage = 0, $iPageSize = 8) {
        $aInvites = array();
        $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('fevent_invite'), 'ei')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ei.invited_user_id')
                ->where('event_id = ' . (int) $iEvent . ' AND rsvp_id = ' . (int) $iRsvp)
                ->execute('getSlaveField');

        if ($iCnt) {
            $aInvites = $this->database()->select('ei.*, ' . Phpfox::getUserField())
                    ->from(Phpfox::getT('fevent_invite'), 'ei')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = ei.invited_user_id')
                    ->where('ei.event_id = ' . (int) $iEvent . ' AND ei.rsvp_id = ' . (int) $iRsvp)
                    ->limit($iPage, $iPageSize, $iCnt)
                    ->order('ei.invite_id DESC')
                    ->execute('getSlaveRows');
        }

        return array($iCnt, $aInvites);
    }

    public function getInviteForUser($iLimit = 6) {
        $aRows = $this->database()->select('e.*')
                ->from(Phpfox::getT('fevent_invite'), 'ei')
                ->join(Phpfox::getT('fevent'), 'e', 'e.event_id = ei.event_id')
                ->where('ei.rsvp_id = 0 AND ei.invited_user_id = ' . Phpfox::getUserId())
                ->limit($iLimit)
                ->execute('getRows');

        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['start_time_phrase'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_browse_time_stamp'), $aRow['start_time']);
            $aRows[$iKey]['start_time_phrase_stamp'] = Phpfox::getTime('h:i a', $aRow['start_time']);
        }

        return $aRows;
    }

    public function getForProfileBlock($iUserId, $iLimit = 5) {
        $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

        $aEvents = $this->database()->select('m.*')
                ->from($this->_sTable, 'm')
                ->join(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = m.event_id AND ei.rsvp_id = 1 AND ei.invited_user_id = ' . (int) $iUserId)
                ->where('m.view_id = 0 AND m.start_time >= \'' . $iTimeDisplay . '\'')
                ->limit($iLimit)
                ->order('m.start_time ASC')
                ->execute('getSlaveRows');

        foreach ($aEvents as $iKey => $aEvent) {
            $aEvents[$iKey]['url'] = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);
            $aEvents[$iKey]['start_time_stamp'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aEvent['start_time']);
            $aEvents[$iKey]['location_clean'] = Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->clean($aEvent['location']), 10);
        }

        return $aEvents;
    }

    public function getForParentBlock($sModule, $iItemId, $iLimit = 5) {
        $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

        $aEvents = $this->database()->select('m.event_id, m.title, m.tag_line, m.image_path, m.server_id, m.start_time, m.location, m.country_iso, m.city, m.module_id, m.item_id')
                ->from($this->_sTable, 'm')
                ->where('m.view_id = 0 AND m.module_id = \'' . $this->database()->escape($sModule) . '\' AND m.item_id = ' . (int) $iItemId . ' AND m.start_time >= \'' . $iTimeDisplay . '\'')
                ->limit($iLimit)
                ->order('m.start_time ASC')
                ->execute('getSlaveRows');

        foreach ($aEvents as $iKey => $aEvent) {
            $aEvents[$iKey]['url'] = Phpfox::getLib('url')->makeUrl('fevent', array('redirect' => $aEvent['event_id']));
            $aEvents[$iKey]['start_time_stamp'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aEvent['start_time']);
            $aEvents[$iKey]['location_clean'] = Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->clean($aEvent['location']), 10);
        }

        return $aEvents;
    }

    public function getPendingTotal() {
        $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

        return $this->database()->select('COUNT(m.event_id)')
                        ->from($this->_sTable, 'm')
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                        //->where('view_id = 1 AND start_time >= \'' . $iTimeDisplay . '\'')
                        ->where('m.view_id = 1')
                        ->execute('getSlaveField');
    }
    
    public function wherePages(){
                $wherePages = " AND ( ( m.item_id = 0 )";
        $pages = Phpfox::getService('fevent.helper')->getListOfPagesWhichJoinedByUserID(Phpfox::getUserId());
                            if(isset($pages) && is_array($pages) && count($pages) > 0){
                            $pagesLen = count($pages);                
                  
                            $wherePages .= ' or ( m.module_id = \'pages\' AND m.item_id IN ( ';
                            
                            $wherePages .= $pages[0]['page_id'];
                            for($i = 1; $i < $pagesLen; $i ++){
                                $wherePages .= ', ' . $pages[$i]['page_id'];
                            }
                            $wherePages .= ' )) ';
                        }  
                        $wherePages .= ')';
                        return $wherePages;
    }
    
    
    public function getFeaturedTotal() {

        $wherePages = $this->getConditionsForSettingPageGroup('m');
        return $this->database()->select('COUNT(*)')
                        ->from($this->_sTable,' m')
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                        ->where('m.is_featured = 1 and m.view_id = 0 and m.privacy = 0 '. $wherePages)
                        ->execute('getSlaveField');
    }
    /**
     * @return int
     */
    public function getMyTotal()
    {
        $sWhere = 'user_id = ' . Phpfox::getUserId();
        $aModules = ['user'];
        if (!Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (!Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }
        $sWhere .= ' AND (module_id NOT IN ("' . implode('","', $aModules) . '") OR module_id = \'fevent\')';

        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($sWhere)
            ->execute('getSlaveField');
    }
    /**
     * @param $iRsvpId
     * @return array|int|string
     */
    public function getAttendingTotal($iRsvpId)
    {
        $sWhere = 'e.view_id = 0 AND ei.rsvp_id = '.$iRsvpId.' AND ei.invited_user_id =' . (int)Phpfox::getUserId();
        $aModules = ['user'];
        if (!Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (!Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }
        $sWhere .= ' AND (module_id NOT IN ("' . implode('","', $aModules) . '") OR module_id = \'fevent\')';

        return db()->select('COUNT(*)')
            ->from($this->_sTable, 'e')
            ->join(':fevent_invite', 'ei', 'ei.event_id = e.event_id')
            ->where($sWhere)
            ->execute('getSlaveField');
    }

    public function getRandomSponsored() {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        $sCacheId = $this->cache()->set('fevent_sponsored_' . $iToday);
        if (!($aEvents = $this->cache()->get($sCacheId))) {
            $aEvents = $this->database()->select('s.*, s.country_iso AS sponsor_country_iso, e.*, ' .Phpfox::getUserField())
                    ->from($this->_sTable, 'e')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
                    ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = e.event_id')
                    ->where('e.view_id = 0 AND e.privacy = 0 AND e.is_sponsor = 1 AND s.module_id = \'fevent\' AND (e.start_time >= \'' . $iToday . '\' OR (e.start_time < \''.$iToday.'\' AND e.end_time > \''.$iToday.'\'))')
                    ->execute('getRows');
            $this->cache()->save($sCacheId, $aEvents);
        }
        shuffle($aEvents);
        foreach ($aEvents as $iKey => $aEvent) {
            $aEvents[$iKey]['categories'] = Phpfox::getService('fevent.category')->getCategoriesById($aEvent['event_id']);
            $aEvents[$iKey]['event_start_time'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aEvent['start_time']);
            $aEvents[$iKey]['event_date'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aEvent['start_time']) . ' - ';
            if (date('dmy', $aEvent['start_time']) === date('dmy', $aEvent['end_time'])) {
                $aEvents[$iKey]['event_date'] .= Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time_short'), $aEvent['end_time']);
                $aEvents[$iKey]['event_end_time'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time_short'), $aEvent['end_time']);
            } else {
                $aEvents[$iKey]['event_date'] .= Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aEvent['end_time']);
                $aEvents[$iKey]['event_end_time'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aEvent['end_time']);
            }
        }
        $aEvents = Phpfox::getService('ad')->filterSponsor($aEvents);

        if ($aEvents === true || (is_array($aEvents) && !count($aEvents))) {
            return false;
        }


        // Randomize to get a event
        return $aEvents[rand(0, (count($aEvents) - 1))];
    }

    public function isAlreadyInvited($iItemId, $aFriends) {
        if ((int) $iItemId === 0) {
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

            $aInvites = $this->database()->select('invite_id, rsvp_id, invited_user_id')
                    ->from(Phpfox::getT('fevent_invite'))
                    ->where('event_id = ' . (int) $iItemId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
                    ->execute('getSlaveRows');

            $aCache = array();
            foreach ($aInvites as $aInvite) {
                $aCache[$aInvite['invited_user_id']] = ($aInvite['rsvp_id'] > 0 ? _p('responded') : _p('invited'));
            }

            if (count($aCache)) {
                return $aCache;
            }
        }

        return false;
    }

    public function getSiteStatsForAdmins() {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array(
            'phrase' => _p('events'),
            'value' => $this->database()->select('COUNT(*)')
                    ->from(Phpfox::getT('fevent'))
                    ->where('view_id = 0 AND time_stamp >= ' . $iToday)
                    ->execute('getSlaveField')
        );
    }

    public function getUpcoming($bIsPage = false, $bIsProfile = false,$iLimit = 3) {
        static $aUpcoming = null;
        static $iTotal = null;

        if ($aUpcoming !== null) {
            return array($iTotal, $aUpcoming);
        }
        
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        
        $aUpcoming = array();
        $repeatday = "( v.isrepeat>-1 and v.timerepeat>" . ($iToday) . ")";
        $repeattime = "(v.isrepeat>-1 and (v.timerepeat=0 or " . $repeatday . "))";

        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('fevent'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->where('v.view_id = 0 AND ( v.start_time > \'' . PHPFOX_TIME . '\')')
                ->order('v.start_time ASC')                
                ->execute('getSlaveRows');
        
        // Check privacy
        $aRows = $this->checkPrivacy($aRows, $bIsPage, $bIsProfile);
        $iTotal = 0;
        if (is_array($aRows) && count($aRows)) {
            $iTotal = count($aRows);
            //shuffle($aRows);
            $iIndex = 0;
            foreach ($aRows as $iKey => $aRow) {
                if ($iIndex === $iLimit) {
                    break;
                }
                $iIndex++;
                $aUpcoming[] = $aRow;
            }
        }
        
        return array($iTotal, $aUpcoming);
    }

    public function getRepeatEventForUpdateDuration()
    {
        return $this->database()->select('v.event_id
                                        , v.time_stamp
                                        , v.start_time
                                        , v.org_start_time
                                        , v.end_time
                                        , v.org_end_time
                                        , v.start_gmt_offset
                                        , v.end_gmt_offset
                                        , v.isrepeat
                                        , v.timerepeat
                                        , v.duration_days
                                        , v.duration_hours
                                        , v.is_delete_user_past_repeat_event
                                        ')
                ->from(Phpfox::getT('fevent'), 'v')
                ->where(' v.isrepeat > -1 AND v.timerepeat > ' . PHPFOX_TIME . ' AND v.end_time < ' . PHPFOX_TIME)
                ->execute('getSlaveRows');
    }

    public function getEventWithLessInfo($eventID){
        return $this->database()->select('v.event_id
                                        , v.time_stamp
                                        , v.start_time
                                        , v.org_start_time
                                        , v.end_time
                                        , v.org_end_time
                                        , v.start_gmt_offset
                                        , v.end_gmt_offset
                                        , v.isrepeat
                                        , v.timerepeat
                                        , v.duration_days
                                        , v.duration_hours
                                        , v.is_delete_user_past_repeat_event
                                        ')
                ->from(Phpfox::getT('fevent'), 'v')
                ->where('v.event_id = ' . (int) $eventID)
                ->execute('getRow');
    }

    public function getPast($bIsPage = false, $bIsProfile = false,$iLimit = 3) {
        static $aPast = null;
        static $iTotal = null;

        if ($aPast !== null) {
            return array($iTotal, $aPast);
        }

        $aPast = array();

        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('fevent'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->where('v.view_id = 0 ' . ' AND ( v.end_time < ' . (int)PHPFOX_TIME . ' ) ')
                ->order('v.start_time DESC')
                ->execute('getSlaveRows');

        // Check privacy
        $aRows = $this->checkPrivacy($aRows, $bIsPage, $bIsProfile);

        $iTotal = 0;
        if (is_array($aRows) && count($aRows)) {
            $iTotal = count($aRows);
            //shuffle($aRows);
            $iIndex = 0;
            foreach ($aRows as $iKey => $aRow) {
                if ($iIndex === $iLimit) {
                    break;
                }
                $iIndex++;
                $aPast[] = $aRow;
            }
        }

        return array($iTotal, $aPast);
    }

    public function getJsEvents($bIsPage = false, $bIsProfile = false)
    {

        $wherePages = '';
        
        $aRows = $this->database()->select('*')
                ->from($this->_sTable,'m')
                ->where('m.view_id = 0 AND m.item_id = 0 '.$wherePages)
                ->execute('getRows');
    

        $aRows = $this->checkPrivacy($aRows, $bIsPage, $bIsProfile);
        return $aRows;
    }


    public function getBirthdays($iUser, $year)
    {
        $iUser = (int)$iUser;

        // Calculate how many days in advance to check and
        $iDaysInAdvance = Phpfox::getParam('friend.days_to_check_for_birthday') >= 0 ? Phpfox::getParam('friend.days_to_check_for_birthday') : 0;
        $iThisMonth = date('m', Phpfox::getTime());
        $iToday = date('d', Phpfox::getTime());

        $iThisYear = date('Y', Phpfox::getTime());
        $iLastDayOfMonth = Phpfox::getLib('date')->lastDayOfMonth($iThisMonth);

        $sMonthUntil = $iThisMonth;
        $sDayUntil = $iToday;
        while($iDaysInAdvance > 0)
        {
            if ($sDayUntil > $iLastDayOfMonth)
            {
                if ($sMonthUntil < 12)
                {
                    $sMonthUntil++;
                }
                else
                {
                    $sMonthUntil = 1;
                    $iLastDayOfMonth = Phpfox::getLib('date')->lastDayOfMonth($sMonthUntil, $iThisYear);
                }
                $sDayUntil = 0;
            }
            $iDaysInAdvance--;
            $sDayUntil++;
        }
        $sMonthUntil = $sMonthUntil[0] != '0' ? ($sMonthUntil < 10) ? '0'.$sMonthUntil : $sMonthUntil : $sMonthUntil;
        $sDayUntil = ($sDayUntil < 10) ? '0' . $sDayUntil : $sDayUntil;
        if ($sMonthUntil < $iThisMonth) // its next year
        {
            $sBirthdays =  '\''.$iThisMonth . ''.$iToday . '\' <= uf.birthday_range OR \''. $sMonthUntil . $sDayUntil . '\' >= uf.birthday_range';
        }
        else{
            $sBirthdays =  '\''.$iThisMonth . ''.$iToday . '\' <= uf.birthday_range AND \''. $sMonthUntil . $sDayUntil . '\' >= uf.birthday_range';
        }

        // cache this query
        $sCacheId = $this->cache()->set('friend_birthday_' . $iUser);
        if (false === ($aBirthdays = $this->cache()->get($sCacheId, 5*60*60))) // cache is in 5 hours
        {
            $aBirthdays = $this->database()->select(Phpfox::getUserField() . ', uf.dob_setting, fb.birthday_user_receiver')
                ->from(Phpfox::getT('friend'), 'f')
                ->join(Phpfox::getT('user'),' u', 'u.user_id = f.friend_user_id')
                ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')
                ->leftJoin(Phpfox::getT('friend_birthday'), 'fb', 'fb.birthday_user_receiver = u.user_id AND fb.time_stamp > '. (PHPFOX_TIME - 2629743)) /* Fixes (SHB-989762) */
                ->where('f.user_id = ' . $iUser . ' AND (' . $sBirthdays . ') AND (uf.dob_setting != 2 AND uf.dob_setting != 3) AND fb.birthday_user_receiver IS NULL')
                ->order('uf.birthday_range ASC')
                ->limit(15)
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aBirthdays);
            Phpfox::getLib('cache')->group('friend', $sCacheId);
        }
        if (!is_array($aBirthdays))
        {
            $aBirthdays = array();
        }

        foreach ($aBirthdays as $iKey => $aFriend)
        {
            // add when is their birthday and how old are they going to be
            $iAge = Phpfox::getService('user')->age($aFriend['birthday']);

            if (substr($aFriend['birthday'],0,2).'-'.substr($aFriend['birthday'],2,2) == date('m-d', Phpfox::getTime()))
            {
                $aBirthdays[$iKey]['new_age'] = $iAge;
            }
            else
            {
                $aBirthdays[$iKey]['new_age'] = ($iAge + 1);
            }

            if (!isset($aFriend['birthday']) || empty($aFriend['birthday']))
            {
                $iDays = -1;
            }
            else
            {
                $iDays = Phpfox::getLib('date')->daysToDate($aFriend['birthday'], null, false);
            }

            if ($iDays < 0 || $aFriend['dob_setting'] == 2 || $aFriend['dob_setting'] == 3)
            {
                unset($aBirthdays[$iKey]);
                continue;
            }
            else
            {
                $aBirthdays[$iKey]['days_left'] = floor($iDays);
            }

            // do we show the age?
            if (($aFriend['dob_setting'] < 3 & $aFriend['dob_setting'] != 1) || ($aFriend['dob_setting'] == 4)) // 0 => age and dob; 1 => age and day/month; 2 => age
            {
                $aBirthdays[$iKey]['show_age'] = true;
            }
            else
            {
                $aBirthdays[$iKey]['show_age'] = false;
            }
            // fail safe
            $aBirthdays[$iKey]['birthdate'] = '';
            // Format the birthdate according to the profile
            $aBirthDay = Phpfox::getService('user')->getAgeArray($aFriend['birthday']);
            if ($aFriend['dob_setting'] == 4)// just copy the arbitrary format on the browse.html
            {
                unset($aBirthDay['year']);
            }
            elseif($aFriend['dob_setting'] == 0)
            {
                $aBirthdays[$iKey]['birthdate'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . ', ' . $aBirthDay['year'];
                $aBirthdays[$iKey]['birthdate1'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . ', ' . $year;
                $aBirthdays[$iKey]['day'] = $aBirthDay['day'];
                $aBirthdays[$iKey]['month'] = $aBirthDay['month'];
                if ($aBirthDay['month'] < 10) {
                    $aBirthDay['month'] = '0'.$aBirthDay['month'];
                }
                if ($aBirthDay['day'] < 10) {
                    $aBirthDay['day'] = '0'.$aBirthDay['day'];
                }
                $aBirthdays[$iKey]['bday'] = $year . '-' . $aBirthDay['month'] . '-' . $aBirthDay['day'];
                $aBirthdays[$iKey]['bday1'] = $year . '/' . $aBirthDay['month'] . '/' . $aBirthDay['day'];
            }
            elseif ($aFriend['dob_setting'] == 1)
            {
                $aBirthdays[$iKey]['birthdate'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'];
                $aBirthdays[$iKey]['birthdate1'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . ', ' . $year;
                $aBirthdays[$iKey]['day'] = $aBirthDay['day'];
                $aBirthdays[$iKey]['month'] = $aBirthDay['month'];
                if ($aBirthDay['month'] < 10) {
                    $aBirthDay['month'] = '0'.$aBirthDay['month'];
                }
                if ($aBirthDay['day'] < 10) {
                    $aBirthDay['day'] = '0'.$aBirthDay['day'];
                }
                $aBirthdays[$iKey]['bday'] = $year . '-' . $aBirthDay['month'] . '-' . $aBirthDay['day'];
                $aBirthdays[$iKey]['bday1'] = $year . '/' . $aBirthDay['month'] . '/' . $aBirthDay['day'];
            }
        }

        $aReturnBirthday = array();
        foreach ($aBirthdays as $iBirthKey => $aBirthData)
        {
            $aReturnBirthday[$aBirthData['days_left']][] = $aBirthData;
        }

        ksort($aReturnBirthday);

        return $aReturnBirthday;
    }
    public function getJsEventsForCalendar($month, $year)
    {

        $start_month = mktime(0,0,0,$month,1,$year);
        $end_month = mktime(0,0,0,$month+1,1,$year);

        $aRows = $this->database()->select('m.*')
                ->from($this->_sTable,'m')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                ->where('m.view_id = 0 AND m.item_id = 0 AND start_time > '.$start_month .' AND start_time < '.$end_month)
                ->execute('getRows');

        $aRows = $this->checkPrivacy($aRows, false, false);

        $aBirthdays = $this->getBirthdays(Phpfox::getuserId(), $year);

        $aTemp = array();
        foreach ($aBirthdays as $iKey => $aFriend)
        {
            $aTemp = array_merge($aTemp,$aBirthdays[$iKey]);
        }

        return array_merge($aRows,$aTemp);
    }

    public function getFeatured($bIsPage = false, $bIsProfile) {
        static $aFeatured = null;
        static $iTotal = null;

        if ($aFeatured !== null) {
            return array($iTotal, $aFeatured);
        }

        $oHelper = Phpfox::getService('fevent.helper');


        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        
        $aFeatured = array();
        $repeatday = "( v.isrepeat>-1 and v.timerepeat>" . ($iToday) . ")";
        $repeattime = "(v.isrepeat>-1 and (v.timerepeat=0 or " . $repeatday . "))";
        $sParentCond = $this->getConditionsForSettingPageGroup('v');
        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('fevent'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->where('v.view_id = 0 AND v.is_featured = 1 '. $sParentCond)
                ->order('v.start_time ASC')                
                ->execute('getSlaveRows');

        // Check privacy
        $aRows = $this->checkPrivacy($aRows, $bIsPage, $bIsProfile);

        $iTotal = 0;
        if (is_array($aRows) && count($aRows)) {
            $iTotal = count($aRows);
            shuffle($aRows);
            $iIndex = 0;
            foreach ($aRows as $iKey => $aRow) 
            {              

                $oHelper->getImageDefault($aRow,'slide');

                $aRow['convert_start_time'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aRow['start_time']);
            
                $aRow['d_start_time'] = Phpfox::getTime('d', (int)$aRow['start_time']); //day
                $aRow['M_start_time'] = Phpfox::getTime('M', (int)$aRow['start_time']); //month
                $aRow['short_start_time'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aRow['start_time']); //hour

                if($aRow['isrepeat']==-1){
                    $aRow['convert_end_time'] = Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aRow['end_time']);
                }
                else
                {
                    $content_repeat="";
                    $until="";
                    if($aRow['isrepeat']==0)
                    {
                        $content_repeat=_p('daily');
                    }
                    else if($aRow['isrepeat']==1)
                    {
                        $content_repeat=_p('weekly');
                    }
                    else if($aRow['isrepeat']==2)
                    {
                        $content_repeat=_p('monthly');
                    }
                    if($content_repeat!="")
                    {
                        if($aRow['timerepeat']!=0)
                        {
                            $sDefault = null;
                            $until = Phpfox::getTime("M j, Y", $aRow['timerepeat']);
                            $content_repeat .= ", " . _p('until') . " " . $until;
                        }
                    }       
                    $aRow['convert_end_time'] = $content_repeat;            
                }
                                        
                if ($iIndex === 7) {
                    break;
                }
                $iIndex++;
                $aFeatured[] = $aRow;
            }
        }
        
        return array($iTotal, $aFeatured);
    }

    public function getForRssFeed() {
        $iTimeDisplay = Phpfox::getLib('phpfox.date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
        $aConditions = array();
        $aConditions[] = "e.view_id = 0 AND e.module_id = 'fevent' AND e.item_id = 0";
        $aConditions[] = "AND e.start_time >= '" . $iTimeDisplay . "'";

        $aRows = $this->database()->select('e.*, et.description_parsed AS description, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('fevent'), 'e')
                ->join(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
                ->where($aConditions)
                ->order('e.time_stamp DESC')
                ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['link'] = Phpfox::permalink('fevent', $aRow['event_id'], $aRow['title']);
            $aRows[$iKey]['creator'] = $aRow['full_name'];
        }

        return $aRows;
    }

    public function getImages($iId, $iLimit = null) {
        return $this->database()->select('image_id, image_path, server_id')
                        ->from(Phpfox::getT('fevent_image'))
                        ->where('event_id = ' . (int) $iId)
                        ->order('ordering ASC')
                        ->limit($iLimit)
                        ->execute('getSlaveRows');
    }
    public function getImagesByIds($sIds) {
        return $this->database()->select('image_id, image_path, server_id')
                        ->from(Phpfox::getT('fevent_image'))
                        ->where('image_id IN ('.rtrim($sIds,',').')')
                        ->execute('getSlaveRows');
    }

    public function getCustomFields($iParentId = 0) {
        $aFields = $this->database()->select('cf.*, fec.name AS category_name')
                ->from(Phpfox::getT('fevent_custom_field'), 'cf')
                ->leftJoin(Phpfox::getT('fevent_category'), 'fec', 'fec.category_id = cf.category_id')
                ->where("fec.parent_id = $iParentId")
                ->order('cf.ordering ASC')
                ->execute('getRows');

        $aCustomFields = array();
        foreach ($aFields as $aField) {
            $aCustomFields[$aField['category_id']][] = $aField;
        }

        $aCategories = $this->database()->select('fec.*')
                ->from(Phpfox::getT('fevent_category'), 'fec')
                ->where("fec.parent_id = $iParentId")
                ->order('fec.ordering ASC')
                ->execute('getRows');

        foreach ($aCategories as $iKey => $aCategory) {
            if (isset($aCustomFields[$aCategory['category_id']])) {
                $aCategories[$iKey]['child'] = $aCustomFields[$aCategory['category_id']];
                $aCategories[$iKey]['name'] = \Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : $aCategory['name'];
            }
            $aSubs = $this->getCustomFields($aCategory['category_id']);
            if (isset($aSubs[0])) {
                $aCategories[$iKey]['subs'] = $aSubs;
            }
        }

        if (isset($aCustomFields[0])) {
            $aCategories['PHPFOX_EMPTY_GROUP']['child'] = $aCustomFields[0];
        }

        return $aCategories;
    }

    public function execute($iPageId = 0, $aCallback = false)
    {
        $oHelper = Phpfox::getService('fevent.helper'); 

        $aActualConditions = (array)Phpfox::getLib('search')->getConditions();
        $this->_aConditions = array();
        $sWhen = Phpfox::getLib('request')->get('when');

        $this->_sView = Phpfox::getLib('request')->get('view', 'all');
        foreach ($aActualConditions as $iKey => $sCond)
        {
            switch ($this->_sView)
            {
                case 'friend':
                    $sCond = str_replace('%PRIVACY%', '0,1,2', $sCond);
                    break;
                case 'my':
                    $sCond = str_replace('%PRIVACY%', '0,1,2,3,4', $sCond);
                    break;
                default:
                    $sCond = str_replace('%PRIVACY%', '0', $sCond);
                    break;
            }
            if ($sWhen == "upcoming")
            {
                $position = strpos($sCond, "AND m.start_time");
                if ($position !== false)
                {
                    $sCond = ' AND ( m.start_time > ' . (int)$oHelper->convertFromUserTimeZone(PHPFOX_TIME) . ' ) ';
                }
            }

            if ($sWhen == 'this-month')
            {
                $position = strpos($sCond, "AND m.start_time");
                if ($position !== false)
                {
                    $startEndTime = Phpfox::getService('fevent.helper')->getStartEndTimeByTypeWithGMT0($sWhen);
                    if(isset($startEndTime['start']) == true 
                        && isset($startEndTime['end']) == true
                        && (int)$startEndTime['start'] > 0
                        && (int)$startEndTime['end'] > 0
                        ){

                        $condStartTime = (int)$oHelper->convertFromUserTimeZone($startEndTime['start']);
                        $condEndTime = (int)$oHelper->convertFromUserTimeZone($startEndTime['end']);

                        $sCond = " AND ( m.start_time >= " . $condStartTime . " AND m.start_time <= " . $condEndTime . " ) ";
                    }                    
                }
            }
            
            if ($sWhen == 'this-week')
            {
                $position = strpos($sCond, "AND m.start_time");
                if ($position !== false)
                {
                    $startEndTime = Phpfox::getService('fevent.helper')->getStartEndTimeByTypeWithGMT0($sWhen);
                    if(isset($startEndTime['start']) == true 
                        && isset($startEndTime['end']) == true
                        && (int)$startEndTime['start'] > 0
                        && (int)$startEndTime['end'] > 0
                        ){
                        $condStartTime = (int)$oHelper->convertFromUserTimeZone($startEndTime['start']);
                        $condEndTime = (int)$oHelper->convertFromUserTimeZone($startEndTime['end']);

                        $sCond = " AND ( m.start_time >= " . $condStartTime . " AND m.start_time <= " . $condEndTime . " ) ";
                    }
                }
            }
            
            if ($sWhen == 'today')
            {
                $position = strpos($sCond, "AND (m.start_time");
                if ($position !== false)
                {
                    $startEndTime = Phpfox::getService('fevent.helper')->getStartEndTimeByTypeWithGMT0($sWhen);
                    if(isset($startEndTime['start']) == true 
                        && isset($startEndTime['end']) == true
                        && (int)$startEndTime['start'] > 0
                        && (int)$startEndTime['end'] > 0
                        ){

                        $condStartTime = (int)$oHelper->convertFromUserTimeZone($startEndTime['start']);
                        $condEndTime = (int)$oHelper->convertFromUserTimeZone($startEndTime['end']);

                        $sCond = " AND ( m.start_time >= " . $condStartTime . " AND m.start_time <= " . $condEndTime . " ) ";
                    }
                }
            }
            
            $this->_aConditions[] = $sCond;
        }

        if ($sWhen == 'past')
        {
            $this->_aConditions[] = ' AND ( m.end_time < ' . (int)$oHelper->convertFromUserTimeZone(PHPFOX_TIME) . ' ) ';            
        }
        if ($sWhen == "ongoing")
        {
            $this->_aConditions[] = ' AND ( m.end_time > ' . (int)$oHelper->convertFromUserTimeZone(PHPFOX_TIME) . ' AND m.start_time < ' . (int)$oHelper->convertFromUserTimeZone(PHPFOX_TIME) . ' ) ';            
        }

        $sSearch = urldecode(Phpfox::getLib('request')->get('search'));
        if (strlen(trim($sSearch))>0)
        {
            $sSearch = Phpfox::getLib('parse.input')->prepare($sSearch);

            $this->_aConditions[] = " AND (m.title LIKE '%$sSearch%' OR ft.description LIKE '%$sSearch%')";
        }

        $sAddress = urldecode(Phpfox::getLib('request')->get('address'));
        if (strlen(trim($sAddress))>0)
        {
            $sAddress = Phpfox::getLib('parse.input')->prepare($sAddress);
            
            $this->_aConditions[] = " AND (m.address LIKE '%$sAddress%')";
        }
        
        $sCity = urldecode(Phpfox::getLib('request')->get('city'));
        if (strlen(trim($sCity))>0)
        {
            $sCity = Phpfox::getLib('parse.input')->prepare($sCity);

            $this->_aConditions[] = " AND (m.city LIKE '%$sCity%')";
        }

        $sZipCode = urldecode(Phpfox::getLib('request')->get('zipcode'));
        if (strlen(trim($sZipCode))>0)
        {
            $sZipCode = Phpfox::getLib('parse.input')->prepare($sZipCode);

            $this->_aConditions[] = " AND (m.postal_code LIKE '%$sZipCode%')";
        }
       
        $srangevaluefrom = urldecode(Phpfox::getLib('request')->get('rangevaluefrom'));
        if (strlen(trim($srangevaluefrom))>0)
        {
            $rangevaluefrom = Phpfox::getLib('parse.input')->prepare($srangevaluefrom);
            preg_match("/[0-9]*/", $rangevaluefrom,$kq);
           
            if($kq==null || strlen(trim($kq[0]))<strlen(trim($rangevaluefrom)))
            {
                $this->_aConditions[] = " AND (1=0)";
            }
            else
            {
                $rangevaluefrom = floatval($rangevaluefrom);
                $rangetype = Phpfox::getLib('request')->get('rangetype');
                $glat = floatval(base64_decode(Phpfox::getLib('request')->get('glat')));
                $glong = floatval(base64_decode(Phpfox::getLib('request')->get('glong')));

                if ($rangetype == 1)
                {
                    // 1km = (1000 / 1609) miles = 0.6215 miles
                    $rangevaluefrom = $rangevaluefrom * 0.6215;
                }
                elseif($rangetype == 0)
                {
                    $rangevaluefrom = $rangevaluefrom;
                }

                $this->_aConditions[] = " AND (
                        (3959 * acos(
                                cos( radians('{$glat}')) 
                                * cos( radians( m.lat ) ) 
                                * cos( radians( m.lng ) - radians('{$glong}') ) 
                                + sin( radians('{$glat}') ) * sin( radians( m.lat ) ) 
                            ) < {$rangevaluefrom} 
                        )                     
                    )";

            }
        }
        
        if ($sCountry = Phpfox::getLib('request')->get('country'))
        {
            $sCountry = Phpfox::getLib('parse.input')->prepare($sCountry);

            $this->_aConditions[] = " AND (m.country_iso LIKE '%$sCountry%')";
        }

        if ($icountry_child_id = Phpfox::getLib('request')->get('childid'))
        {
            if ($icountry_child_id > 0)
            {
                $this->_aConditions[] = " AND (m.country_child_id = " . $icountry_child_id . ")";
            }
        }

        if (Phpfox::getLib('request')->get('date'))
        {
            $sDate = Phpfox::getLib('request')->get('date');
            preg_match('/(\d+)\-(\d+)\-(\d+)/', $sDate, $aMatches);
            if (!empty($aMatches[3]))
            {
                $iStartDay = Phpfox::getLib('date')->mktime(0, 0, 0, intval($aMatches[2]), intval($aMatches[3]), intval($aMatches[1]));
                $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, intval($aMatches[2]), intval($aMatches[3]), intval($aMatches[1]));
                $gmt_offset = Phpfox::getTimeZone()*3600;
                $iStartDay = $oHelper->convertFromUserTimeZone($iStartDay);
                $iEndDay = $oHelper->convertFromUserTimeZone($iEndDay);
                $condition_date = "(m.start_time <= $iEndDay AND m.start_time >= $iStartDay)";
                $this->_aConditions[] = " AND (" .$condition_date . ")";
            }
        }
		
        Phpfox::getService('fevent.browse')->getQueryJoins(true);
        $this->database()->select('COUNT(m.event_id) as count')
            ->from($this->_sTable, 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where($this->_aConditions);
        if (Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')){
            $this->database()->union();
            $this->_aConditions[] = ' AND m.user_id ='. (int)Phpfox::getUserId();
            $this->database()->select('COUNT(m.event_id) as count')
                ->from($this->_sTable, 'm')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                ->where($this->_aConditions)
                ->union();
        }
        $aCnt = $this->database()->execute('getSlaveRows');
        if($aCnt){
            $this->_iCnt = 0;
            foreach($aCnt as $iCnt)
            {
                $this->_iCnt += $iCnt['count'];
            }
        }
        $this->_aRows = array();
        if ($this->_iCnt)
        {
            Phpfox::getService('fevent.browse')->getQueryJoins();
            $this->database()->from($this->_sTable, 'm')->where($this->_aConditions);
            Phpfox::getService('fevent.browse')->query();
            
            if($sWhen == 'past')
            {
                $order = 'm.start_time DESC';
            }
            else
            {
                $order = Phpfox::getLib('search')->getSort();
            }


            $pageSelect = '';
            if(($this->_sView == 'pagesevents')
                || ((isset($aCallback) == false || $aCallback == false) 
                    && Phpfox::getService('fevent.helper')->canViewEventInPageWhichJoined() == true))
                {
                $this->database()
                    ->leftJoin(Phpfox::getT('pages'), 'pag', 'pag.page_id = m.item_id');
                $pageSelect = 'pag.page_id, pag.title as page_title, ';
            }

            $this->database()->select('m.*, ' . $pageSelect . 'u.*')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                ->order($order)
                ->limit(Phpfox::getLib('search')->getPage(), Phpfox::getLib('search')->getDisplay(), $this->_iCnt);

            if (Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')){
                $this->database()->union();
                $this->_aConditions[] = ' AND m.user_id ='. (int)Phpfox::getUserId();
                Phpfox::getService('fevent.browse')->getQueryJoins(false,true);
                Phpfox::getService('fevent.browse')->query();
                $this->database()->select('m.*, ' . $pageSelect . 'u.*')
                    ->from($this->_sTable, 'm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                    ->where($this->_aConditions)
                    ->order($order)
                    ->limit(Phpfox::getLib('search')->getPage(), Phpfox::getLib('search')->getDisplay(), $this->_iCnt)
                    ->union();
            }
            $this->_aRows = $this->database()->execute('getSlaveRows');
        }
        Phpfox::getService('fevent.browse')->processRows($this->_aRows);
        return $this->_aRows;
    }

    public function getCount() {
        if(!isset($this->_iCnt))
        {
            $this->_iCnt = 0;
        }
        return $this->_iCnt;
    }

    public function checkPrivacy($aRows, $bIsPage = false, $bIsProfile = false)
    {
        if (!is_array($aRows))
        {
            return $aRows;
        }
        
        $iUserId = Phpfox::getUserId();
        
        $sView = $this->request()->get('view', 'all');
        
        $aOutput = array();
        foreach ($aRows as $iKey => $aRow)
        {
            $bIsPage = $bIsPage ? $bIsPage : 0;
            
            if ($bIsProfile !== false && $aRow['user_id'] != $bIsProfile)
            {
                continue;
            }
          
            if ($bIsProfile === false && $aRow['item_id'] != $bIsPage && $bIsPage!=0)
            {
                continue;
            }
            
            $iRsvp = $this->database()->select('rsvp_id')->from(Phpfox::getT('fevent_invite'))->where('event_id = '.(int)$aRow['event_id'].' AND invited_user_id = '.(int)$iUserId)->execute('getField');
            if(!in_array($iRsvp, array('0', '1', '2', '3')))
            {
                $iRsvp = '-1';
            }

            switch($sView)
            {
                case 'my':
                    if($iUserId == $aRow['user_id'])
                    {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'friend':
                    if(in_array($aRow['privacy'], array('0', '1', '2')) && Phpfox::getService('friend')->isFriend($aRow['user_id'], $iUserId))
                    {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'featured':
                    if($aRow['is_featured'] && $aRow['privacy'] == '0')
                    {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'attending':
                    if($iRsvp == '1')
                    {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'may-attend':
                    if($iRsvp == '2')
                    {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'not-attending':
                    if($iRsvp == '3')
                    {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'invites':
                    if($iRsvp == '0')
                    {
                        $aOutput[] = $aRow;
                    }
                    break;
                default:
                    if($aRow['privacy'] == '0')
                    {
                        $aOutput[] = $aRow;
                    }
            }
        }

        return $aOutput;
    }

    public function updateSetting($name, $default_value) {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('fevent_setting'))
                ->where('name="' . $name . '"')
                ->execute('getSlaveRows');
        $oFilter = Phpfox::getLib('parse.input');

        if (count($aRows) == 0) {
            $aInserts = array();
            $aInserts['name'] = $name;
            $aInserts['default_value'] = $oFilter->clean($default_value);
            phpfox::getLib("database")->insert(phpfox::getT('fevent_setting'), $aInserts);
        } else {
            $aUpdates = array();
            $aUpdates['default_value'] = $oFilter->clean($default_value);
            phpfox::getLib("database")->update(phpfox::getT('fevent_setting'), $aUpdates, 'name="' . $name . '"');
        }
    }

    public function getSetting($name) {
        $aRow = phpfox::getLib("database")->select('*')->from(phpfox::getT('fevent_setting'))
                ->where('name="' . $name . '"')
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getAllEventPhpfox() {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('event'))
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getAllCategorydataPhpfox($event_id) {
        $aRow = phpfox::getLib("database")->select('*')
                ->from(phpfox::getT('event_category_data'))
                ->where('event_id=' . $event_id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getAllFeedEventPhpfox($event_id) {
        $aRows = phpfox::getLib("database")->select('*')
                ->from(phpfox::getT('event_feed'))
                ->where('parent_user_id=' . $event_id)
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getFeedCommentPhpfox($item_id) {
        $aRow = phpfox::getLib("database")->select('*')
                ->from(phpfox::getT('event_feed_comment'))
                ->where('feed_comment_id=' . $item_id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getEventTextPhpfox($event_id) {
        $aRow = phpfox::getLib("database")->select('*')
                ->from(phpfox::getT('event_text'))
                ->where('event_id=' . $event_id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getInviteEventPhpfox($event_id) {
        $aRows = phpfox::getLib("database")->select('*')
                ->from(phpfox::getT('event_invite'))
                ->where('event_id=' . $event_id)
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getInviteAdvancedEvent($event_id) {
        $aRows = phpfox::getLib("database")->select('*')
                ->from(phpfox::getT('fevent_invite'))
                ->where('event_id=' . $event_id)
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getIdEventLast() {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('fevent'))
                ->limit(1)
                ->order('event_id desc')
                ->execute('getSlaveRows');
        if (count($aRows) == 0)
            return 0;
        else
            return $aRows[0]['event_id'] + 10;
    }

    public function buildRRule($aEvent)
    {
        $rRule = "";
        $gmt_offset = $aEvent['start_gmt_offset']*3600;
        $start = $aEvent['start_time'] + $gmt_offset;
        $end = $aEvent['timerepeat'] + $gmt_offset;
        $isrepeat = $aEvent['isrepeat'];
        
        switch($isrepeat)
        {
            case 0:
                $rRule = "\nRRULE:FREQ=DAILY;COUNT=".(floor(($end - $start)/86400) + 1);
                break;
            case 1:
                $by_day = strtoupper(substr(date('D', $start), 0, 2));
                $rRule = "\nRRULE:FREQ=WEEKLY;COUNT=".(floor(($end - $start)/604800) + 1).";BYDAY=".$by_day;
                break;
            case 2:
                $hour = date('H', $start);
                $minute = date('i', $start);
                $day = date('d', $start);
                $month = date('m', $start);
                $year = date('Y', $start);
                
                $cnt = 0;
                while ($start <= $end)
                {
                    $cnt++;
                    do
                    {
                        $month++;
                        if($month == 13)
                        {
                            $month = 1;
                            $year++;
                        }
                        $start = mktime($hour, $minute, 0, $month, $day, $year);
                    }
                    while(date('d', $start) != $day);
                }
                
                $rRule = "\nRRULE:FREQ=MONTHLY;COUNT=".$cnt.";BYMONTHDAY=".$day;
        }
        
        return $rRule;
    }
    
    public function getJdpickerPhrases()
    {
        $sPhrases = "";
        $aVarNames = array(
            'fevent.january',
            'fevent.february',
            'fevent.march',
            'fevent.april',
            'fevent.may',
            'fevent.june',
            'fevent.july',
            'fevent.august',
            'fevent.september',
            'fevent.october',
            'fevent.november',
            'fevent.december',
            'fevent.jan',
            'fevent.feb',
            'fevent.mar',
            'fevent.apr',
            'fevent.jun',
            'fevent.jul',
            'fevent.aug',
            'fevent.sep',
            'fevent.oct',
            'fevent.nov',
            'fevent.dec',
            'fevent.weekday_sunday',
            'fevent.weekday_monday',
            'fevent.weekday_tuesday',
            'fevent.weekday_wednesday',
            'fevent.weekday_thursday',
            'fevent.weekday_friday',
            'fevent.weekday_saturday');
        
        foreach ($aVarNames as $sVarName)
        {
            $sPhrases .= "\noTranslations['$sVarName'] = '" . str_replace("'", "\\'", _p($sVarName)) . "';";
        }
        
        return $sPhrases;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing 
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments) {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_event__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function getTopEvent($sType = 'viewed', $iLimit = 4, $bIsPage = false, $bIsProfile = false, $bNoCount = false, $pageID = -1)
    {
        switch ($sType)
        {
            case 'liked':
                $sOrder = 'v.total_like DESC';
                break;
            case 'viewed':
                $sOrder = 'v.total_view DESC';
                break;
            case 'discussed':
                $sOrder = 'v.total_comment DESC';
                break;
            default:
                $sOrder = 'v.total_view DESC';
        }

        $iTotal = 0;
        $aRows = array(); 
        if($bIsPage){
            $aRows = $this->database()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . 'v.*, ' . Phpfox::getUserField())
                    ->from(Phpfox::getT('fevent'), 'v')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                    ->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = v.event_id AND ei.invited_user_id = ' . Phpfox::getUserId())
                    ->where(' 1=1 AND v.view_id = 0 AND v.privacy IN(0) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageID)
                    ->order($sOrder)
                    ->limit($iLimit)
                    ->execute('getSlaveRows');
            if (!$bNoCount)
            {
                $iTotal = $this->database()->getField('SELECT FOUND_ROWS()');
            }
        } else 
        {
            //  check permissin view event in page which joined/liked
            $wherePages = '';
            if(Phpfox::getService('fevent.helper')->canViewEventInPageWhichJoined() == true){
                $pages = Phpfox::getService('fevent.helper')->getListOfPagesWhichJoinedByUserID(Phpfox::getUserId());
                if(isset($pages) && is_array($pages) && count($pages) > 0){
                    $pagesLen = count($pages);                
                    $wherePages .= ' OR ( v.module_id = \'pages\' AND v.item_id IN ( ';
                    $wherePages .= $pages[0]['page_id'];
                    for($i = 1; $i < $pagesLen; $i ++){
                        $wherePages .= ', ' . $pages[$i]['page_id'];
                    }
                    $wherePages .= ' )) ';
                }
            }

            //  build sql 
            $groupByFriend = '';
            $andWherePrivacy = '';
            $sView = $this->request()->get('view', 'all');
            switch($sView)
            {
                case 'my':
                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' v.user_id = ' . (int)Phpfox::getUserId();
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'friend':
                    $this->database()->join(Phpfox::getT('friend'), 'friends'
                            , ' friends.user_id = v.user_id AND friends.friend_user_id = ' . (int)Phpfox::getUserId()); 
                    $groupByFriend .= ' v.event_id ';
                    $this->database()->group($groupByFriend);

                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' v.privacy IN (0, 1, 2) ';
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'featured':
                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' v.is_featured = 1 AND v.privacy IN(0) ';
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'attending':
                    $this->database()->join(Phpfox::getT('fevent_invite'), 'fei'
                            , ' fei.event_id = v.event_id AND fei.invited_user_id = ' . (int)Phpfox::getUserId()); 

                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' fei.rsvp_id = 1 ';
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'may-attend':
                    $this->database()->join(Phpfox::getT('fevent_invite'), 'fei'
                            , ' fei.event_id = v.event_id AND fei.invited_user_id = ' . (int)Phpfox::getUserId()); 
                    
                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' fei.rsvp_id = 2 ';
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'not-attending':
                    $this->database()->join(Phpfox::getT('fevent_invite'), 'fei'
                            , ' fei.event_id = v.event_id AND fei.invited_user_id = ' . (int)Phpfox::getUserId()); 
                    
                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' fei.rsvp_id = 3 ';
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'invites':
                    $this->database()->join(Phpfox::getT('fevent_invite'), 'fei'
                            , ' fei.event_id = v.event_id AND fei.invited_user_id = ' . (int)Phpfox::getUserId()); 
                    
                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' fei.rsvp_id = 0 ';
                    $andWherePrivacy .= ' ) ';
                    break;
                default:
                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' v.privacy IN(0) ';
                    $andWherePrivacy .= ' ) ';
                    break; 
            }        

            $aRows = $this->database()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . 'v.*, ' . Phpfox::getUserField())
                    ->from(Phpfox::getT('fevent'), 'v')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                    ->where(' 1=1 AND v.view_id = 0 AND ( (v.item_id = 0) ' . $wherePages . ') ' . $andWherePrivacy)
                    ->order($sOrder)
                    ->limit($iLimit)
                    ->execute('getSlaveRows');
            if (!$bNoCount)
            {
                $iTotal = $this->database()->getField('SELECT FOUND_ROWS()');
            }
        }

        return array($iTotal, $aRows);
    }

    public function getOnHomepageByType($sType = 'upcoming', $iLimit = 4, $bIsPage = false, $bIsProfile = false, $bNoCount = false, $pageID = -1)
    {
        $whereType = '';
        switch ($sType)
        {
            case 'upcoming':
                $whereType = ' AND ( v.start_time > ' . (int)PHPFOX_TIME . ' ) ';
                $sOrder = 'v.start_time ASC';
                break;
            case 'ongoing':
                $sOrder = 'v.start_time ASC';
                $whereType = ' AND ( v.end_time > ' . (int)PHPFOX_TIME . ' AND v.start_time < ' . (int)PHPFOX_TIME . ' ) ';
                break;
            default:
                $sOrder = 'v.start_time ASC';
                $whereType = '';
        }

        $iTotal = 0;
        $aRows = array(); 
        if($bIsPage){
            if (!$bNoCount)
            {
                $iTotal = $this->database()->select(' COUNT(v.event_id) ')
                    ->from(Phpfox::getT('fevent'), 'v')
                    ->where(' 1=1 AND v.view_id = 0 AND v.privacy IN(0) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageID . $whereType)
                    ->execute('getSlaveField');
            }

            if((int)$iTotal > 0){
                $aRows = $this->database()->select('v.*, pag.page_id, pag.title as page_title, vt.description_parsed,' .Phpfox::getUserField())
                        ->from(Phpfox::getT('fevent'), 'v')
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                        ->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = v.event_id AND ei.invited_user_id = ' . Phpfox::getUserId())
                        ->leftJoin(Phpfox::getT('fevent_text'), 'vt', 'vt.event_id = v.event_id')
                        ->join(Phpfox::getT('pages'), 'pag', 'pag.page_id = v.item_id')
                        ->where(' 1=1 AND v.view_id = 0 AND v.privacy IN(0) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageID . $whereType)
                        ->order($sOrder)
                        ->limit($iLimit)
                        ->execute('getSlaveRows');
            }
        } else 
        {
            //  check permissin view event in page which joined/liked
            $wherePages = $this->getConditionsForSettingPageGroup('v');

            //  build sql 
            $andWherePrivacy = '';
            $andWherePrivacy .= ' AND ( ';
            $andWherePrivacy .= ' v.privacy IN(0) ';
            $andWherePrivacy .= ' ) ';

            if (!$bNoCount)
            {
                $iTotal = $this->database()->select(' COUNT(v.event_id) ')
                    ->from(Phpfox::getT('fevent'), 'v')
                    ->where(' 1=1 AND v.view_id = 0 AND ( (v.item_id = 0) ' . $wherePages . ') ' . $andWherePrivacy . $whereType)
                    ->execute('getSlaveField');
            }

            if((int)$iTotal > 0){
                $aRows = $this->database()->select('v.*, pag.page_id, pag.title as page_title,vt.description_parsed,'. Phpfox::getUserField())
                        ->from(Phpfox::getT('fevent'), 'v')
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                        ->leftJoin(Phpfox::getT('pages'), 'pag', 'pag.page_id = v.item_id')
                        ->leftJoin(Phpfox::getT('fevent_text'), 'vt', 'vt.event_id = v.event_id')
                        ->where(' 1=1 AND v.view_id = 0 ' . $wherePages . $andWherePrivacy . $whereType)
                        ->order($sOrder)
                        ->limit($iLimit)
                        ->execute('getSlaveRows');
            }
        }

        /*caculate end date of recurrent event by(after number event)*/
        if(count($aRows)){
            foreach ($aRows as $keyEvent => $aEvent) {
                if( ($aEvent['isrepeat'] == 0) || ($aEvent['isrepeat'] == 1) || ($aEvent['isrepeat'] == 2)){
                    if($aEvent['after_number_event'] > 0){
                        /*get start date of last instance event*/

                        $aLastInstance = $this->getLastInstanceEvent($aEvent['org_event_id']);
                        if(!empty($aLastInstance)){
                           $aRows[$keyEvent]['timerepeat'] = $aLastInstance['start_time'] ;    
                        }

                    }
                }
            }
        }

        return array($iTotal, $aRows);
    }

    public function getMapEventForDeatail($iEventId)
    {
        $whereType = '';

        $sOrder = 'v.start_time ASC';


        $iTotal = 0;
        $aRows = array(); 

        //  check permissin view event in page which joined/liked
        $wherePages = '';

        //  build sql 
        $andWherePrivacy = '';

        $aRows = $this->database()->select('v.*, pag.page_id, pag.title as page_title,vt.description_parsed,'. Phpfox::getUserField())
                ->from(Phpfox::getT('fevent'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->leftJoin(Phpfox::getT('pages'), 'pag', 'pag.page_id = v.item_id')
                ->leftJoin(Phpfox::getT('fevent_text'), 'vt', 'vt.event_id = v.event_id')
                ->where(' 1=1 AND v.event_id = '.$iEventId . $andWherePrivacy . $whereType)
                ->order($sOrder)
                ->execute('getSlaveRows');
        
        /*caculate end date of recurrent event by(after number event)*/
        if(count($aRows)){
            foreach ($aRows as $keyEvent => $aEvent) {
                if( ($aEvent['isrepeat'] == 0) || ($aEvent['isrepeat'] == 1) || ($aEvent['isrepeat'] == 2)){
                    if($aEvent['after_number_event'] > 0){
                        /*get start date of last instance event*/

                        $aLastInstance = $this->getLastInstanceEvent($aEvent['org_event_id']);
                        if(!empty($aLastInstance)){
                           $aRows[$keyEvent]['timerepeat'] = $aLastInstance['start_time'] ;    
                        }

                    }
                }
            }
        }

        return $aRows;
    }


    public function getAdminsByEventID($iId)
    {
        $aRows = $this->database()->select('user_id')
            ->from(Phpfox::getT('fevent_admin'))
            ->where('event_id = '.(int)$iId)
            ->execute('getSlaveRows');
        
        $aAdmin = array();
        if (!empty($aRows))
        {
            foreach ($aRows as $aRow)
            {
                $aAdmin[] = $aRow['user_id'];
            }
        }
        
        return $aAdmin;
    }    

    public function getEventByID($eventID)
    {
        return $this->database()->select('*')->from(Phpfox::getT('fevent'))->where('event_id = '.(int)$eventID)->execute('getSlaveRow');
    }

    public function getAllDataEventById($event_id){
        // tables: fevent, fevent_text, fevent_category_data
        return $this->database()->select('e.*, txt.description, txt.description_parsed, GROUP_CONCAT(cdt.category_id) as list_category_id')
            ->from(Phpfox::getT('fevent'), 'e')
            ->leftJoin(Phpfox::getT('fevent_text'), 'txt', 'txt.event_id = e.event_id')
            ->leftJoin(Phpfox::getT('fevent_category_data'), 'cdt', 'cdt.event_id = e.event_id')
             ->where('e.event_id = '.(int)$event_id)
             ->group('e.event_id')
             ->execute('getSlaveRow');
    }

    public function getLastInstanceEvent($event_id){

         return $this->database()->select('e.*')
                                 ->from(Phpfox::getT('fevent'), 'e')
                                 ->where('e.org_event_id = '.(int)$event_id)
                                 ->order('e.event_id DESC')             
                                 ->execute('getSlaveRow');
    }

    public function getBrotherEventByEventId($event_id, $org_event_id, $aConds = array()){
        $sWhere = ' 1=1 ';
        $sWhere .= ' AND e.event_id != '.(int)$event_id;
        $sWhere .= ' AND e.org_event_id = '.(int)$org_event_id;
        if(count($aConds) > 0){
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        return $this->database()->select('e.event_id')
            ->from(Phpfox::getT('fevent'), 'e')
             ->where($sWhere)
             ->execute('getSlaveRows');
    }

    public function getFollowingEventByEventId($event_id, $org_event_id, $aConds = array()){
        $sWhere = ' 1=1 ';
        $sWhere .= ' AND e.org_event_id = '.(int)$org_event_id;
        $sWhere .= ' AND e.event_id > '.(int)$event_id;
        if(count($aConds) > 0){
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        return $this->database()->select('e.event_id')
            ->from(Phpfox::getT('fevent'), 'e')
             ->where($sWhere)
             ->execute('getSlaveRows');
    }

    public function executeCron(){
        $aCronLogDefault = $this -> database()
            ->select("cronlog.*")
            ->from(Phpfox::getT("fevent_cronlog"),'cronlog')
            ->where('cronlog.type = \'default\'')
            ->order('cronlog.cronlog_id DESC')
            ->limit(1)
            ->execute("getSlaveRow");

        $oldRunTimestamp = 0;
        $newRunTimestamp = PHPFOX_TIME;
        if(isset($aCronLogDefault['cronlog_id'])){
            $oldRunTimestamp = (int)$aCronLogDefault['timestamp'];
        }
        $this->database()->insert(Phpfox::getT('fevent_cronlog'), array(
            'type' => 'default',
            'timestamp' => (int)$newRunTimestamp,
        ));

/*        $oldRunTimestamp = 0;
        $newRunTimestamp = PHPFOX_TIME;*/

        $this->cronSendSubscribeMail($oldRunTimestamp, $newRunTimestamp);
    }

    public function cronSendSubscribeMail($oldRunTimestamp, $newRunTimestamp){
        
        $iTimeSetting = Phpfox::getParam('fevent.subscribe_within_day') * 24 * 3600;   

        $iTimeCronJob =  $newRunTimestamp - $oldRunTimestamp;

/*        $cd = '';

        $cd1 = 'fevent.time_stamp >= '.$oldRunTimestamp.' AND fevent.start_time >= '.$oldRunTimestamp.' AND fevent.start_time < '.($oldRunTimestamp + $iTimeSetting);
        $cd2 = 'fevent.start_time >= '.($oldRunTimestamp + $iTimeSetting - $iTimeCronJob).' AND fevent.start_time < '.($oldRunTimestamp + $iTimeSetting).' AND fevent.time_stamp < '.($oldRunTimestamp);
      
        $cd3 = 'fevent.start_time >= ' . (int)$oldRunTimestamp;
        $cd4 = 'fevent.time_stamp < '.$newRunTimestamp.' AND fevent.start_time >= '.$newRunTimestamp.' AND fevent.start_time < '.($newRunTimestamp + $iTimeSetting);
*/

        //if $iTimeSetting  

        $aSubscribeEmail = $this->database()->select('fse.*')
            ->from(Phpfox::getT("fevent_subscribe_email"), 'fse')
            ->execute('getSlaveRows');

        $aEvents = $this->database()->select('fevent.*,fc.name as category_name,fc.category_id')
            ->from(Phpfox::getT("fevent_category_data"), 'fcd')
            ->join(Phpfox::getT('fevent'), 'fevent', 'fevent.event_id = fcd.event_id ')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fevent.user_id')
            ->join(Phpfox::getT('fevent_category'), 'fc', 'fc.category_id = fcd.category_id')
            ->where('fevent.view_id = 0')
            ->group('fevent.event_id')
            ->execute('getSlaveRows');

        foreach ($aSubscribeEmail as $keyaSubscribeEmail => $valueaSubscribeEmail) {
            $data = (array)json_decode($valueaSubscribeEmail['data']);
            $aConds = array();




            $aFilterEvent = array();

                // ==> cd1
               // $cd = $cd4;
               if(count($aEvents)){
                    foreach ($aEvents as $keyEvent => $aEvent) {


                            if(  
                                 $data['categories'] == 'null' || 
                                (in_array($aEvent['category_id'], explode(",", $data['categories']))) 
                              ) {
                                

                                    if(
                                    !empty($data['location_lat']) && !empty($data['location_lng']) && !empty($data['radius']) 
                                    && floatval($data['location_lat']) > 0 && floatval($data['location_lng']) > 0 && floatval($data['radius']) > 0
                                    )
                                    {
                                        $rangevaluefrom = Phpfox::getLib('parse.input')->prepare($data['radius']);
                                        preg_match("/[0-9]*/", $rangevaluefrom,$kq);

                                        $rangevaluefrom = floatval($rangevaluefrom);
                                        $glat = floatval($data['location_lat']);
                                        $glong = floatval($data['location_lng']);

                                         if(!$this->distance($aEvent['lat'],$aEvent['lng'],$glat,$glong,$rangevaluefrom)){
                                            continue;
                                         }
                                    }


                                    if($oldRunTimestamp == 0){

                                            //cd4
                                          if($aEvent['time_stamp'] < $newRunTimestamp && $aEvent['start_time'] >= $newRunTimestamp && $aEvent['start_time'] < ($newRunTimestamp + $iTimeSetting)){

                                              $aFilterEvent[] = $aEvent;

                                          }
                                     }
                                     else{
                                            if($iTimeCronJob < $iTimeSetting){
                                                //cd1,cd2
                                                //$cd = $cd1.' OR '.$cd2;
                                                 //$cd1 = 'fevent.time_stamp >= '.$oldRunTimestamp.' AND fevent.start_time >= '.$oldRunTimestamp.' AND fevent.start_time < '.($oldRunTimestamp + $iTimeSetting);
                                                 //$cd2 = 'fevent.start_time >= '.($oldRunTimestamp + $iTimeSetting - $iTimeCronJob).' AND fevent.start_time < '.($oldRunTimestamp + $iTimeSetting).' AND fevent.time_stamp < '.($oldRunTimestamp);
  
                                                if(
                                                    ($aEvent['time_stamp'] >= $oldRunTimestamp && $aEvent['start_time'] >= $oldRunTimestamp && $aEvent['start_time'] < ($oldRunTimestamp + $iTimeSetting))
                                                    ||
                                                    ($aEvent['start_time'] >= ($oldRunTimestamp + $iTimeSetting - $iTimeCronJob) && $aEvent['start_time'] < ($oldRunTimestamp + $iTimeSetting) && $aEvent['time_stamp'] < $oldRunTimestamp)

                                                ){

                                                    $aFilterEvent[] = $aEvent;

                                                }

                                            }
                                            else{
                                               
                                                //cd3
                                                if($aEvent['start_time'] >= $oldRunTimestamp){

                                                    $aFilterEvent[] = $aEvent;

                                                }
                                            }
                                       }

                            }

                    }
                }

            $formatTime = Phpfox::getParam('fevent.fevent_browse_time_stamp');
            if(count($aFilterEvent) > 0){
                $subject = _p('event_you_may_interested_in_site_name', array(
                    'site_name' => Phpfox::getParam('core.site_title'), 
                ));
                $email = $valueaSubscribeEmail['email'];
                $data_event = '';
                foreach ($aFilterEvent as $keyaEvent => $aEvent) {
                    $name = Phpfox::getLib('phpfox.parse.output')->clean($aEvent['title']);

                    $sTextCategories = $aEvent['category_name'];
                    $sTextCategories = trim($sTextCategories);

                    $location = $aEvent['location'];
                    $location = trim($location);

                    $sLink = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);        

                    $aUser = Phpfox::getService('user')->getUser($aEvent['user_id']);
                    $aOwnerName = "<a href='".Phpfox::getLib('url')->makeUrl('',$aUser['user_name'])."'>".$aUser['full_name']."</a>";

                    $data = '';
                    $data .= '- <a href="' . $sLink . '">' . $name . '</a><br />';
                    $data .= _p('category') . ': ' . $sTextCategories . '<br />';
                    $data .= _p('location') . ': ' . $location . '<br />';
                    $data .= _p('start_time') . ': ' . Phpfox::getService('fevent.helper')->displayTimeByFormat($formatTime,$aEvent['start_time']) . '<br />';
                    $data .= _p('end_time') . ': ' . Phpfox::getService('fevent.helper')->displayTimeByFormat($formatTime,$aEvent['end_time']) . '<br />';
                    $data .= _p('owner') . ': ' .$aOwnerName. '<br /><br />';

                    $data_event .= $data;
                }

                
                $subject = _p('email_newsletter_events_will_be_started_within_within_day_days', array(
                    'within_day' => Phpfox::getParam('fevent.subscribe_within_day'), 
                ));

                $message = _p('dear_sir_or_madam_here_are_businesses_you_may_interested_in_site_name_data_event_regards_site_nam', array(
                    'within_day' => Phpfox::getParam('fevent.subscribe_within_day'), 
                    'site_name' => Phpfox::getParam('core.site_title'), 
                    'data_event' => $data_event, 
                ));

                Phpfox::getLib('mail')->to($email)
                    ->subject($subject)
                    ->message($message)
                    ->send();   

            }
        }
    }
    function distance($lat1, $lon1, $lat2, $lon2,$radius) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;

      if($radius > $miles){
        return true;
      }
      else{
        return false;
      }

    }
    public function getAjaxBrotherEventByEventId($event_id, $org_event_id,$iPage = 0,$iLimit = null){
        $sWhere = ' 1=1 ';
        $sWhere .= ' AND e.event_id != '.(int)$event_id;
        $sWhere .= ' AND e.org_event_id = '.(int)$org_event_id;

        $iCnt = $this->database()->select('COUNT(e.event_id)')
            ->from(Phpfox::getT('fevent'), 'e')
             ->where($sWhere)
             ->execute('getSlaveField');

        $oHelper = Phpfox::getService('fevent.helper');
        $aRows = array();
        if($iCnt > 0){
            $aRows = $this->database()->select('e.*')
                    ->from(Phpfox::getT('fevent'), 'e')
                     ->where($sWhere)
                     ->limit($iPage+1,$iLimit)
                     ->execute('getSlaveRows');                     
            if(count($aRows)){
                foreach ($aRows as $keyEvent => $aEvent) {
                    $aRows[$keyEvent]['url'] = Phpfox::getLib('url')->makeUrl('fevent',array($aEvent['event_id'],$aEvent['title']));
                    list($iAttending, $aInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 1, 1);
                    $aRows[$keyEvent]['number_attending'] = $iAttending;

                    $aRows[$keyEvent]['d_start_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aEvent['start_time']);
                    $aRows[$keyEvent]['M_start_time'] = $oHelper->displayTimeByFormat('M', (int)$aEvent['start_time']);
                    $aRows[$keyEvent]['d_Y_start_time'] = $oHelper->displayTimeByFormat('d, Y', (int)$aEvent['start_time']);
                    $aRows[$keyEvent]['recuring_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_browse_time_stamp'), (int)$aEvent['start_time']);
                }
            }                     
        }

        return array($iCnt,$aRows);

    }

    public function getManageEvent($aConds = array(), $iPage = 0, $iLimit = NULL, $iCount = NULL){
        $sWhere = '';
        $sWhere .= '1=1';
        if(count($aConds) > 0){
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        $iCount = $this -> database()
                    ->select("COUNT(dbus.event_id)")
                    ->from(Phpfox::getT("fevent"),'dbus')
                    ->join(Phpfox::getT("user"),'u','dbus.user_id =  u.user_id')
                    ->leftjoin(Phpfox::getT('fevent_category_data'), 'dcd', 'dcd.event_id = dbus.event_id')
                    ->leftjoin(Phpfox::getT('fevent_category'), 'dc', 'dc.category_id = dcd.category_id AND dc.parent_id = 0')
                    ->where($sWhere)
                    ->execute("getSlaveField");
        $aList = array();
        $aCategoryData = $this->database()->select("dcd.*,dc.name")
                              ->from(Phpfox::getT('fevent_category_data'),'dcd')
                              ->join(Phpfox::getT('fevent_category'), 'dc', 'dc.category_id = dcd.category_id AND dc.parent_id = 0')
                              ->execute("getSlaveRows");
        if($iCount){
            $aList = $this -> database()
                ->select("dbus.*,".Phpfox::getUserField())
                ->from(Phpfox::getT("fevent"),'dbus')
                ->join(Phpfox::getT("user"),'u','dbus.user_id =  u.user_id')
                ->leftjoin(Phpfox::getT('fevent_category_data'), 'dcd', 'dcd.event_id = dbus.event_id')
                ->leftjoin(Phpfox::getT('fevent_category'), 'dc', 'dc.category_id = dcd.category_id AND dc.parent_id = 0')
                ->where($sWhere)
                ->order('dbus.event_id DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getSlaveRows");
            foreach($aList as &$value)
            {
                foreach($aCategoryData as $aCate)
                    if($aCate['event_id'] == $value['event_id'])
                    {
                       $value['category_title'] = \Core\Lib::phrase()->isPhrase($aCate['name']) ? _p($aCate['name']) :$aCate['name'];
                       break;
                    }
                if(!isset($value['category_title']))
                    $value['category_title'] = _p('No Category');
                if(Phpfox::isAdmin() == 1)
                {
                    $value['can_sponsor_event'] = 1;
                }
            }
            Phpfox::getService('fevent.browse')->processRows($aList);
        }
        //die(d($aList));
        return array($iCount,$aList);     
    }

    /**
     * @param bool $bSquare
     * @return mixed|null|string
     */
    public function getDefaultPhoto($bSquare = true)
    {
        $sDefaultEventPhoto = flavor()->active->default_photo('fevent_default_photo', true);
        if (!$sDefaultEventPhoto) {
            $sDefaultEventPhoto = setting('core.path_actual') . 'PF.Base/module/fevent/static/image/' . ($bSquare ? 'default_home.png' : 'default_slide.png');
        }
        return $sDefaultEventPhoto;
    }

    /**
     * Apply settings show music of pages / groups
     * @param string $sPrefix
     * @return string
     */
    public function getConditionsForSettingPageGroup($sPrefix = 'e')
    {
        $aModules = [];
        // Apply settings show blog of pages / groups
        if (Phpfox::getParam('fevent.fevent_display_event_created_in_group') && Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (Phpfox::getParam('fevent.fevent_display_event_created_in_page') && Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }
        if (count($aModules)) {
            return ' AND (' . $sPrefix . '.module_id IN (\'' . implode('\',\'',
                    $aModules) . '\') OR ' . $sPrefix . '.module_id IN (\'fevent\',\'event\'))';
        } else {
            return ' AND ' . $sPrefix . '.module_id IN (\'fevent\',\'event\')';
        }
    }
    /**
     * @param  int $iId
     *
     * @return int
     */
    public function countImages($iId)
    {
        return $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('fevent_image'))
            ->where('event_id = ' . (int)$iId)
            ->order('ordering ASC')
            ->execute('getSlaveField');
    }

    public function getUploadParams($aParams)
    {
        if (isset($aParams['id'])) {
            $iTotalImage = $this->countImages($aParams['id']);
            $iRemainImage = Phpfox::getUserParam('fevent.max_upload_image_event') - $iTotalImage;
        }
        else {
            $iRemainImage = Phpfox::getUserParam('fevent.max_upload_image_event');
        }
        $iMaxFileSize = Phpfox::getUserParam('fevent.max_upload_size_event');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize/1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'addedfile' => 'ynfeAddPage.dropzoneOnAddedFile',
            'sending' => 'ynfeAddPage.dropzoneOnSending',
            'success' => 'ynfeAddPage.dropzoneOnSuccess',
            'queuecomplete' => 'ynfeAddPage.dropzoneQueueComplete',
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('fevent.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'submit_button' => '',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('event.dir_image'),
            'upload_path' => Phpfox::getParam('event.url_image'),
            'update_space' => true,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number',['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => array(50, 120, 200),
            'no_square' => true
        ];
    }

    public function getEventForMap($aConditions, $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {

        $sConditions = ' 1=1 ';

        foreach ($aConditions as $aCondition) {

            if (strpos($aCondition, '%PRIVACY%') !== false) {
                $sConditions .= str_replace('%PRIVACY%', '0', $aCondition);
            } else {
                $sConditions .= $aCondition;
            }
        }

        $sOrder = 'm.event_id DESC';

        $iCount = $this->database()
            ->select("COUNT( DISTINCT( m.event_id) )")
            ->from($this->_sTable, 'm')
            ->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = m.event_id')
            ->leftJoin(Phpfox::getT('fevent_category_data'), 'fcd', 'fcd.event_id = m.event_id')
            ->leftJoin(Phpfox::getT('fevent_category'), 'fc', 'fc.is_active = 1 AND fc.category_id = fcd.category_id')
            ->where($sConditions)
            ->execute("getSlaveField");

        $aEvents = array();
        if ($iCount) {
            $aEvents = $this->database()->select('m.event_id,m.*, ft.description_parsed AS description')
                ->from($this->_sTable, 'm')
                ->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = m.event_id')
                ->leftJoin(Phpfox::getT('fevent_category_data'), 'fcd', 'fcd.event_id = m.event_id')
                ->leftJoin(Phpfox::getT('fevent_category'), 'fc', 'fc.is_active = 1 AND fc.category_id = fcd.category_id')
                ->where($sConditions)
                ->order($sOrder)
                ->group('m.event_id')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }


        return $aEvents;
    }

}

?>