<?php

namespace Apps\P_AdvEvent\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;

class FEvent extends Phpfox_Service
{
    private $_aCallback = false;
    private $_iImageLimit = 6;
    public static $defaultRangeValueFrom = 50;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent');
    }

    public function checkBlockExistOutOfApp($controller, $component = 'event-list')
    {
        $check = db()->select('block_id')->from(Phpfox::getT('block'))->where('m_connection = "' . $controller . '" AND component = "' . $component . '" AND is_active = 1')->execute('getSlaveField');
        return !!$check;
    }

    public function getSubscriberByCode($code)
    {
        if (empty($code)) {
            return false;
        }
        return db()->select('subscribe_id')->from(Phpfox::getT('fevent_subscribe_email'))->where('code = "' . $code . '"')->execute('getSlaveField');
    }

    public function callback($aCallback)
    {
        $this->_aCallback = $aCallback;

        return $this;
    }

    public function getSimpleEventForStatusIcon($eventId)
    {
        $row = db()->select('is_featured, is_sponsor, view_id')->from($this->_sTable)->where('event_id = ' . (int)$eventId)->execute('getSlaveRow');
        return $row;
    }

    public function getBirthdayBackgroundImage()
    {
        $cacheId = $this->cache()->set('fevent_birthday_background_image');
        if (($rowParsed = $this->cache()->get($cacheId)) === false) {
            $row = db()->select('name, default_value')->from(Phpfox::getT('fevent_setting'))->where('name = "fevent_birthday_photo_image_path" OR name = "fevent_birthday_photo_server_id"')->execute('getSlaveRows');
            $rowParsed = [];
            foreach ($row as $item) {
                if ($item['name'] == 'fevent_birthday_photo_image_path') {
                    $rowParsed['image_path'] = $item['default_value'];
                } elseif ($item['name'] == 'fevent_birthday_photo_server_id') {
                    $rowParsed['image_server_id'] = $item['default_value'];
                }
            }
            $this->cache()->saveBoth($cacheId, $rowParsed);
        }
        return $rowParsed;
    }

    public function checkSentWishes($userIds = [])
    {
        if (empty($userIds)) {
            return false;
        }

        $startTime = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
        $endTime = Phpfox::getLib('date')->mktime(23, 59, 59, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

        $extraSelect = '';
        if (Phpfox::isAppActive('YNC_Member')) {
            $extraSelect = ', ybw.birthday_wish_id AS is_send_wish_from_adv_member, ybw.message AS message_from_adv_member';
            db()->leftJoin(Phpfox::getT('ynmember_birthday_wish'), 'ybw', 'ybw.item_id = u.user_id AND ybw.user_id = ' . Phpfox::getUserId() . ' AND (ybw.time_stamp BETWEEN ' . $startTime . ' AND ' . $endTime . ')');
        }

        $rows = db()->select('u.user_id, fbw.birthday_wish_id AS is_send_wish, fbw.message' . $extraSelect)->from(Phpfox::getT('user'), 'u')->leftJoin(Phpfox::getT('fevent_birthday_wish'), 'fbw', 'fbw.target_user_id = u.user_id AND fbw.user_id = ' . Phpfox::getUserId() . ' AND (fbw.time_stamp BETWEEN ' . $startTime . ' AND ' . $endTime . ')')->where('u.user_id IN (' . implode(',', $userIds) . ')')->execute('getSlaveRows');

        $results = [];
        foreach ($rows as $row) {
            if (!empty($row['is_send_wish_from_adv_member'])) {
                $results[$row['user_id']] = Phpfox::getLib('parse.output')->clean($row['message_from_adv_member']);
            } elseif (!empty($row['is_send_wish'])) {
                $results[$row['user_id']] = Phpfox::getLib('parse.output')->clean($row['message']);
            }
        }

        return $results;
    }

    /**
     * This function returns information about $iUser's friends' upcoming birthdays
     * @param Int $iUser
     * @return array
     */
    public function getBirthdaysInCurrentYear($iUser)
    {
        static $storedBirthdays = [];
        $iUser = (int)$iUser;

        if (!empty($storedBirthdays[$iUser])) {
            return $storedBirthdays[$iUser];
        }

        // cache this query
        $sCacheId = $this->cache()->set('friend_birthday_' . $iUser);
        if (false === ($aBirthdays = $this->cache()->get($sCacheId, 5 * 60 * 60))) // cache is in 5 hours
        {
            // Calculate how many days in advance to check and
            $iDaysInAdvance = Phpfox::getParam('friend.days_to_check_for_birthday') >= 0 ? Phpfox::getParam('friend.days_to_check_for_birthday') : 0;
            $iThisMonth = date('m', Phpfox::getTime());
            $iToday = date('d', Phpfox::getTime());

            $iThisYear = date('Y', Phpfox::getTime());
            $iLastDayOfMonth = Phpfox::getLib('date')->lastDayOfMonth($iThisMonth);

            $sMonthUntil = $iThisMonth;
            $sDayUntil = $iToday;
            while ($iDaysInAdvance > 0) {
                if ($sDayUntil > $iLastDayOfMonth) {
                    if ($sMonthUntil < 12) {
                        $sMonthUntil++;
                    } else {
                        $sMonthUntil = 1;
                        $iLastDayOfMonth = Phpfox::getLib('date')->lastDayOfMonth($sMonthUntil, $iThisYear);
                    }
                    $sDayUntil = 0;
                }
                $iDaysInAdvance--;
                $sDayUntil++;
            }
            $sMonthUntil = $sMonthUntil[0] != '0' ? ($sMonthUntil < 10) ? '0' . $sMonthUntil : $sMonthUntil : $sMonthUntil;
            $sDayUntil = ($sDayUntil < 10) ? '0' . $sDayUntil : $sDayUntil;
            if ($sMonthUntil < $iThisMonth) // its next year
            {
                $sBirthdays = '\'' . $iThisMonth . '' . $iToday . '\' <= uf.birthday_range OR \'' . $sMonthUntil . $sDayUntil . '\' >= uf.birthday_range';
            } else {
                $sBirthdays = '\'' . $iThisMonth . '' . $iToday . '\' <= uf.birthday_range AND \'' . $sMonthUntil . $sDayUntil . '\' >= uf.birthday_range';
            }

            $aBirthdays = db()->select(Phpfox::getUserField() . ', u.custom_gender, uf.dob_setting, fb.birthday_user_receiver')->from(Phpfox::getT('friend'), 'f')->join(Phpfox::getT('user'), ' u', 'u.user_id = f.friend_user_id')->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')->leftJoin(Phpfox::getT('friend_birthday'), 'fb', 'fb.birthday_user_receiver = u.user_id AND fb.time_stamp > ' . (PHPFOX_TIME - 2629743))/* Fixes (SHB-989762) */
                ->where('f.user_id = ' . $iUser . ' AND (' . $sBirthdays . ') AND (uf.dob_setting != 2 AND uf.dob_setting != 3) AND fb.birthday_user_receiver IS NULL')->order('uf.birthday_range ASC')->limit(15)->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aBirthdays);
            Phpfox::getLib('cache')->group('friend', $sCacheId);
        }

        if (!is_array($aBirthdays)) {
            $aBirthdays = [];
        }


        foreach ($aBirthdays as $iKey => $aFriend) {
            // add when is their birthday and how old are they going to be
            $iAge = Phpfox::getService('user')->age($aFriend['birthday']);

            if (substr($aFriend['birthday'], 0, 2) . '-' . substr($aFriend['birthday'], 2, 2) == date('m-d', Phpfox::getTime())) {
                $aBirthdays[$iKey]['new_age'] = $iAge;
            } else {
                $aBirthdays[$iKey]['new_age'] = ($iAge + 1);
            }

            if (!isset($aFriend['birthday']) || empty($aFriend['birthday'])) {
                $iDays = -1;
            } else {
                $iDays = Phpfox::getLib('date')->daysToDate($aFriend['birthday'], null, false);
            }

            if ($iDays < 0 || $aFriend['dob_setting'] == 2 || $aFriend['dob_setting'] == 3) {
                unset($aBirthdays[$iKey]);
                continue;
            } else {
                $aBirthdays[$iKey]['days_left'] = floor($iDays);
            }

            // do we show the age?
            if (($aFriend['dob_setting'] < 3 & $aFriend['dob_setting'] != 1) || ($aFriend['dob_setting'] == 4)) // 0 => age and dob; 1 => age and day/month; 2 => age
            {
                $aBirthdays[$iKey]['show_age'] = true;
            } else {
                $aBirthdays[$iKey]['show_age'] = false;
            }
            // fail safe
            $aBirthdays[$iKey]['birthdate'] = '';
            // Format the birthdate according to the profile
            $aBirthDay = Phpfox::getService('user')->getAgeArray($aFriend['birthday']);
            if ($aFriend['dob_setting'] == 4)// just copy the arbitrary format on the browse.html
            {
                unset($aBirthDay['year']);
            } elseif ($aFriend['dob_setting'] == 0) {
                $aBirthdays[$iKey]['birthdate'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . ', ' . $aBirthDay['year'];
            } elseif ($aFriend['dob_setting'] == 1) {
                $aBirthdays[$iKey]['birthdate'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'];
            }
        }

        $aReturnBirthday = [];
        foreach ($aBirthdays as $iBirthKey => $aBirthData) {
            $aReturnBirthday[$aBirthData['days_left']][] = $aBirthData;
        }

        ksort($aReturnBirthday);
        $storedBirthdays[$iUser] = $aReturnBirthday;

        return $aReturnBirthday;
    }


    /**
     * @param $type
     * @param bool $isPage
     * @param $isProfile
     * @param int $limit
     * @return array
     */
    public function getBlockData($type, $isPage = false, $isProfile, $limit = 3, $params = [])
    {
        static $storedEvents = null;

        if (!empty($storedEvents[$type])) {
            return [$storedEvents[$type]['total'], $storedEvents[$type]['events']];
        }

        $events = [];
        $eventTable = Phpfox::getT('fevent');
        $inviteTable = Phpfox::getT('fevent_invite');
        $userTalble = Phpfox::getT('user');
        $textTable = Phpfox::getT('fevent_text');

        switch ($type) {
            case 'suggest':
                {
                    $limitFriend = 2;
                    db()->select('COUNT(f.event_id) AS total_friend_attending, f.event_id')
                        ->from($this->_sTable, 'f')
                        ->join($inviteTable, 'fi', 'fi.event_id = f.event_id AND fi.rsvp_id = 1')
                        ->join(Phpfox::getT('friend'), 'friend', 'fi.invited_user_id = friend.friend_user_id AND friend.user_id = ' . Phpfox::getUserId())
                        ->where('f.view_id = 0 AND ((f.start_time <= ' . PHPFOX_TIME . ' AND f.end_time > ' . PHPFOX_TIME . ') OR (f.start_time > ' . PHPFOX_TIME . ')) AND f.event_id NOT IN (SELECT f.event_id FROM `' . $eventTable . '` f JOIN `' . $inviteTable . '` fi ON fi.event_id = f.event_id AND fi.invited_user_id = ' . Phpfox::getUserId() . ' AND fi.rsvp_id = 1)')
                        ->group('f.event_id')
                        ->having('total_friend_attending > ' . $limitFriend)
                        ->union()
                        ->unionFrom('fevent');

                    $events = db()->select('e.*, fi.rsvp_id, fi.user_id AS inviter_id, fi.invited_user_id AS invitee_id, ft.description_parsed, ' . Phpfox::getUserField())->join($this->_sTable, 'e', 'e.event_id = fevent.event_id')->join($userTalble, 'u', 'u.user_id = e.user_id')->join($textTable, 'ft', 'ft.event_id = fevent.event_id')->leftJoin($inviteTable, 'fi', 'fi.event_id = fevent.event_id AND fi.invited_user_id = ' . Phpfox::getUserId())->limit($limit)->execute('getSlaveRows');
                    break;
                }
            case 'popular':
                {
                    $friendTable = Phpfox::getT('friend');

                    db()->select('COUNT(*) AS total_friend, 0 AS total_mutual_friend, 0 AS total_people, fi.event_id')->from($inviteTable, 'fi')->join($friendTable, 'f', 'f.friend_user_id = fi.invited_user_id')->where('fi.rsvp_id = 1 AND f.is_page = 0 AND f.user_id = ' . Phpfox::getUserId())->group('fi.event_id')->union();

                    $whereInFriendList = ':field :in_condition (SELECT friend_user_id from :friend WHERE is_page=0 AND user_id=:user_id)';
                    db()->select('0 AS total_friend , COUNT(*) AS total_mutual_friend, 0 AS total_people, fi.event_id')->from($inviteTable, 'fi')->join($friendTable, 'f', 'f.user_id = fi.invited_user_id')->join($friendTable, 'sf', 'sf.friend_user_id = f.friend_user_id AND sf.user_id = ' . Phpfox::getUserId())->where('fi.rsvp_id = 1 AND (f.user_id != ' . Phpfox::getUserId() . ' AND ' . strtr($whereInFriendList, [':friend' => $friendTable, ':user_id' => Phpfox::getUserId(), ':field' => 'f.user_id', ':in_condition' => 'NOT IN']) . ') AND ' . strtr($whereInFriendList, [':friend' => $friendTable, ':user_id' => Phpfox::getUserId(), ':field' => 'f.friend_user_id', ':in_condition' => 'IN']))->group('f.friend_user_id')->union();

                    db()->select('0 AS total_friend , 0 AS total_mutual_friend, COUNT(*) AS total_people, fi.event_id')->from($inviteTable, 'fi')->where('fi.rsvp_id = 1')->group('fi.event_id')->union();
                    db()->unionFrom('fevent');

                    $events = db()->select('SUM(fevent.total_friend) AS total_friend, SUM(total_mutual_friend) AS total_mutual_friend, SUM(total_people) AS total_people, e.*, fi.rsvp_id, fi.user_id AS inviter_id, fi.invited_user_id AS invitee_id, ft.description_parsed, ' . Phpfox::getUserField())->join($eventTable, 'e', 'e.event_id = fevent.event_id AND e.view_id = 0')->join($userTalble, 'u', 'u.user_id = e.user_id')->join($textTable, 'ft', 'ft.event_id = fevent.event_id')->leftJoin(Phpfox::getT('fevent_invite'), 'fi', 'fi.event_id = fevent.event_id AND fi.invited_user_id = ' . Phpfox::getUserId())->group('fevent.event_id')->order('total_people DESC, total_friend DESC, total_mutual_friend DESC, e.start_time ASC, e.end_time ASC')->limit($limit)->execute('getSlaveRows');
                    break;
                }
            case 'related':
                {
                    if (empty($params['event_id'])) {
                        break;
                    }
                    db()->select('fcd.category_id')->from($eventTable, 'f')->join(Phpfox::getT('fevent_category_data'), 'fcd', 'fcd.event_id = f.event_id')->where('f.event_id = ' . (int)$params['event_id'] . ' AND f.view_id = 0')->union()->unionFrom('fevent');

                    $events = db()->select('f.*, fi.rsvp_id, fi.user_id AS inviter_id, fi.invited_user_id AS invitee_id, ft.description_parsed, ' . Phpfox::getUserField())->join(Phpfox::getT('fevent_category'), 'fc', '(fevent.category_id IS NOT NULL OR fevent.category_id != "") AND fevent.category_id = fc.category_id')->join(Phpfox::getT('fevent_category_data'), 'fcd', 'fcd.category_id = fc.category_id')->join($eventTable, 'f', 'f.event_id = fcd.event_id AND f.view_id = 0 AND f.event_id != ' . $params['event_id'])->join($userTalble, 'u', 'u.user_id = f.user_id')->join($textTable, 'ft', 'ft.event_id = f.event_id')->leftJoin(Phpfox::getT('fevent_invite'), 'fi', 'fi.event_id = f.event_id AND fi.invited_user_id = ' . Phpfox::getUserId())->limit($limit)->order('f.start_time ASC, f.end_time ASC')->execute('getSlaveRows');
                    break;
                }
            case 'reminder':
                {
                    $rsvpReminder = [1, 2];
                    $events = db()->select('f.*, fi.rsvp_id, fi.user_id AS inviter_id, fi.invited_user_id AS invitee_id, ft.description_parsed, ' . Phpfox::getUserField())->from($eventTable, 'f')->join($inviteTable, 'fi', 'fi.event_id = f.event_id AND fi.rsvp_id IN (' . implode(',', $rsvpReminder) . ')')->join($userTalble, 'u', 'u.user_id = fi.invited_user_id')->join($textTable, 'ft', 'ft.event_id = f.event_id')->where('f.start_time > ' . PHPFOX_TIME . ' AND f.view_id = 0 AND fi.invited_user_id = ' . Phpfox::getUserId())->order('fi.rsvp_id ASC, f.start_time ASC')->limit($limit)->execute('getSlaveRows');
                    break;
                }
            case 'invited':
                {
                    $events = $this->getInviteForUser($limit);
                    break;
                }
        }

        $events = $this->checkPrivacy($events, $isPage, $isProfile, [0,1,2]);

        if (!empty($events)) {
            foreach ($events as $key => $suggestedEvent) {
                if ($type == 'invited') {
                    $events[$key]['is_invited'] = true;
                } else {
                    $events[$key]['is_invited'] = !empty($suggestedEvent['inviter_id']) && !empty($suggestedEvent['invitee_id']) ? ($suggestedEvent['inviter_id'] == $suggestedEvent['user_id'] ? true : ($suggestedEvent['inviter_id'] != $suggestedEvent['invitee_id'] ? true : false)) : false;
                }
            }
        }

        $storedEvents[$type] = ['total' => (!empty($events) ? count($events) : 0), 'events' => $events];

        return [$storedEvents[$type]['total'], $storedEvents[$type]['events']];
    }

    /**
     * @param $event
     */
    public function getMoreInfoForEventItem(&$event, $getSimpleCount = false)
    {
        $mainTable = Phpfox::getT('fevent_invite');
        $friendTable = Phpfox::getT('friend');

        db()->select('COUNT(*) AS total_attending, 0 AS total_friend')->from($mainTable)->where('event_id = ' . (int)$event['event_id'] . ' AND rsvp_id IN (1,2)')->union();
        db()->select('0 AS total_attending, COUNT(*) AS total_friend')->from($mainTable, 'fi')->join($friendTable, 'f', 'fi.invited_user_id = f.friend_user_id AND f.user_id = ' . Phpfox::getUserId())->where('fi.event_id = ' . (int)$event['event_id'] . ' AND fi.rsvp_id IN (1,2)')->union()->unionFrom('t', true);
        $count = db()->select('SUM(total_attending) AS total_attending, SUM(total_friend) AS total_friend')->execute('getSlaveRow');

        $totalCount = !empty($count) ? (int)$count['total_attending'] : 0;

        if ($getSimpleCount) {
            $event['attending_statistic'] = ['total_attending' => $totalCount, 'total_friend_attending' => (int)$count['total_friend'], 'total_other_people_attending' => (int)$totalCount - (int)$count['total_friend'],];
            return true;
        }

        $limit = 3;
        if ($totalCount > 0) {

            db()->select('fi.invited_user_id AS user_id')->from($mainTable, 'fi')->join($friendTable, 'f', 'f.friend_user_id = fi.invited_user_id')->where('fi.event_id = ' . $event['event_id'] . ' AND fi.rsvp_id IN (1,2) AND f.is_page = 0 AND f.user_id = ' . Phpfox::getUserId())->limit($limit)->union()->unionFrom('fevent_invite');

            $data = db()->select('fevent_invite.*, ' . Phpfox::getUserField())->join(Phpfox::getT('user'), 'u', 'u.user_id = fevent_invite.user_id')->limit($limit)->execute('getSlaveRows');
        }

        $event['attending_statistic'] = ['people' => $data, 'total_attending' => $totalCount, 'total_friend_attending' => (int)$count['total_friend'], 'total_other_people_attending' => (int)$totalCount - (int)$count['total_friend'], 'other_people' => !empty($count['total_friend']) && ($count['total_friend'] > $limit) ? ((int)$count['total_friend'] - $limit) : 0];
    }

    /**
     * @param $eventId
     * @param $iOwnerUserId
     * @param $iInvitedUserId
     * @return bool
     */
    public function isInvitedByOwner($eventId, $iOwnerUserId = 0, $iInvitedUserId = 0)
    {
        if (empty($eventId)) {
            return false;
        }
        if (!empty($iOwnerUserId) && !empty($iInvitedUserId)) {
            $count = db()->select('COUNT(*)')->from(Phpfox::getT('fevent_invite'))->where('event_id = ' . (int)$eventId . ' AND user_id = ' . (int)$iOwnerUserId . ' AND invited_user_id = ' . (int)$iInvitedUserId)->execute('getSlaveField');
        } else {
            $count = db()->select('e.event_id')->from(Phpfox::getT('fevent'), 'e')->leftJoin(Phpfox::getT('fevent_invite'), 'fi', 'fi.event_id = e.event_id AND ((fi.user_id != e.user_id AND fi.user_id != fi.invited_user_id) OR (fi.user_id = e.user_id)) AND fi.invited_user_id = ' . Phpfox::getUserId())->where('e.event_id = ' . $eventId . ' AND (fi.user_id IS NOT NULL)')->execute('getSlaveField');
        }

        return !!$count;
    }

    public function getQuickEvent($iEvent)
    {
        $aEvent = db()->select('e.*')->from($this->_sTable, 'e')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->join(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')->where('e.event_id = ' . (int)$iEvent)->execute('getRow');

        return $aEvent;
    }

    public function getEvent($sEvent, $bUseId = false, $bNoCache = false)
    {

        $oHelper = Phpfox::getService('fevent.helper');


        static $aEvent = null;

        if ($aEvent !== null && $bNoCache === false) {
            return $aEvent;
        }

        $bUseId = true;

        if (Phpfox::isUser()) {
            db()->select('ei.invite_id, ei.rsvp_id, ')->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = e.event_id AND ei.invited_user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            db()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'fevent\' AND l.item_id = e.event_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('friend')) {
            db()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = e.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        } else {
            db()->select('0 as is_friend, ');
        }

        $aEvent = db()->select('e.*, e.country_iso as event_country_iso, ' . (Phpfox::getParam('core.allow_html') ? 'et.description_parsed' : 'et.description') . ' AS description, fi.user_id AS inviter_id, fi.invited_user_id AS invitee_id, ' . Phpfox::getUserField())->from($this->_sTable, 'e')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->join(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')->leftJoin(Phpfox::getT('fevent_invite'), 'fi', 'fi.event_id = e.event_id AND fi.invited_user_id = ' . Phpfox::getUserId())->where('e.event_id = ' . (int)$sEvent)->execute('getRow');

        if (!isset($aEvent['event_id'])) {
            return false;
        }

        $aEvent['is_invited'] = !empty($aEvent['inviter_id']) && !empty($aEvent['invitee_id']) ? ($aEvent['inviter_id'] == $aEvent['user_id'] ? true : ($aEvent['inviter_id'] != $aEvent['invitee_id'] ? true : false)) : false;

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

        // Get custom values
        $aCustom = db()->select('cv.value, cf.phrase_var_name')->from(Phpfox::getT('fevent_custom_value'), 'cv')->innerJoin(Phpfox::getT('fevent_custom_field'), 'cf', 'cf.field_id = cv.field_id')->innerJoin(Phpfox::getT('fevent_category_data'), 'cd', 'cd.category_id = cf.category_id AND cd.event_id = cv.event_id')->where('cv.event_id = ' . $aEvent['event_id'] . ' AND cf.is_active = 1')->order('ordering')->execute('getSlaveRows');

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
            $aEvent["custom"] = [];
        }

        if (isset($aEvent['gmap']) && !empty($aEvent['gmap'])) {
            $aEvent['gmap'] = unserialize($aEvent['gmap']);
        }

        $aEvent['categories'] = Phpfox::getService('fevent.category')->getCategoriesById($aEvent['event_id']);

        if (!empty($aEvent['location'])) {
            $aEvent['map_location'] = $aEvent['location'];
            if (!empty($aEvent['city'])) {
                $aEvent['map_location'] .= ',' . $aEvent['city'];
            }
            if (!empty($aEvent['postal_code'])) {
                $aEvent['map_location'] .= ',' . $aEvent['postal_code'];
            }
            if (!empty($aEvent['event_country_iso'])) {
                $aEvent['map_location'] .= ',' . Phpfox::getService('core.country')->getCountry($aEvent['event_country_iso']);
            }

            $aEvent['map_location'] = urlencode($aEvent['map_location']);
        }

        $aEvent['detail_start_time'] = $oHelper->displayTimeByFormat('<b>g:i a</b> l M j, Y', (int)$aEvent['start_time']); //day
        $aEvent['D_start_time'] = $oHelper->displayTimeByFormat('D', (int)$aEvent['start_time']); //day
        $aEvent['detail_end_time'] = $oHelper->displayTimeByFormat('<b>g:i a</b> l M j, Y', (int)$aEvent['end_time']); //day
        $aEvent['D_end_time'] = $oHelper->displayTimeByFormat('D', (int)$aEvent['end_time']); //day

        $aEvent['bookmark'] = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);
        return $aEvent;
    }

    public function getEventCoordinates()
    {
        return db()->select('event_id, lat, lng')->from($this->_sTable)->execute('getRows');
    }

    public function getEventsByIds($aIds)
    {
        $sIds = join(',', $aIds);
        $aRows = db()->select('event_id, lat, lng, title, start_time, start_gmt_offset, location, address, city')->from($this->_sTable)->where("event_id IN ($sIds)")->execute('getRows');

        // TODO: replace this format with core format
        $formatTime = 'F j, Y';
        foreach ($aRows as $iKey => $aEvent) {
            $aEvent['start_time'] = Phpfox::getTime($formatTime, Phpfox::getLib('date')->convertFromGmt($aEvent['start_time'], $aEvent['start_gmt_offset']));
            $aEvent['link'] = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);
            $aRows[$iKey] = $aEvent;
        }
        return $aRows;
    }

    public function getTimeLeft($iId)
    {
        $aEvent = $this->getEvent($iId, true);

        return ($aEvent['mass_email'] + (Phpfox::getUserParam('fevent.total_mass_emails_per_hour') * 60));
    }

    public function canSendEmails($iId, $bNoCache = false)
    {
        if (Phpfox::getUserParam('fevent.total_mass_emails_per_hour') === 0) {
            return true;
        }

        $aEvent = $this->getEvent($iId, true, $bNoCache);

        return (($aEvent['mass_email'] + (Phpfox::getUserParam('fevent.total_mass_emails_per_hour') * 60) > PHPFOX_TIME) ? false : true);
    }

    public function getForEdit($iId)
    {
        $aEvent = db()->select('e.*, et.description')->from($this->_sTable, 'e')->join(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')->where('e.event_id = ' . (int)$iId)->execute('getRow');

        if (empty($aEvent)) {
            return false;
        }

        #Admins
        $aEvent['admins'] = db()->select(Phpfox::getUserField())->from(Phpfox::getT('fevent_admin'), 'ca')->join(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')->where('ca.event_id = ' . $iId)->execute('getSlaveRows');

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

        $aEvent['start_time'] = Phpfox::getTime('H', $aEvent['start_time'], false) . ':' . Phpfox::getTime('i', $aEvent['start_time'], false);
        $aEvent['end_time'] = Phpfox::getTime('H', $aEvent['end_time'], false) . ':' . Phpfox::getTime('i', $aEvent['end_time'], false);

        $aEvent['repeat_section_repeatuntil_month'] = Phpfox::getTime('n');
        $aEvent['repeat_section_repeatuntil_day'] = Phpfox::getTime('j');
        $aEvent['repeat_section_repeatuntil_year'] = Phpfox::getTime('Y');
        $aEvent['repeat_section_after_number_event'] = 0;

        $aEvent['categories'] = Phpfox::getService('fevent.category')->getCategoryIds($aEvent['event_id']);
        $aEvent['category'] = Phpfox::getService('fevent.category')->getCategoryId($aEvent['event_id']);

        $aEvent['gmt_org_start_time'] = $aEvent['org_start_time'];
        $aEvent['gmt_org_end_time'] = $aEvent['org_end_time'];
        $aEvent['org_start_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['org_start_time'], $aEvent['start_gmt_offset']);
        $aEvent['org_end_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['org_end_time'], $aEvent['end_gmt_offset']);

        $aEvent['event_type'] = 'one_time';
        $aEvent['total_image'] = $this->countImages($iId);
        $aEvent['image_limit'] = $this->_iImageLimit;
        $aEvent['params'] = ['id' => $aEvent['event_id']];
        if ((int)$aEvent['isrepeat'] > -1) {
            $aEvent['event_type'] = 'repeat';

            if ((int)$aEvent['after_number_event'] > 0) {
                $aEvent['repeat_section_end_repeat'] = 'after_number_event';
                $aEvent['repeat_section_after_number_event'] = $aEvent['after_number_event'];
            } elseif ($aEvent['timerepeat_tousertimezone'] != 0) {
                $aEvent['repeat_section_end_repeat'] = 'repeat_until';
                $aEvent['repeat_section_repeatuntil_month'] = Phpfox::getTime('n', $aEvent['timerepeat_tousertimezone'], false);
                $aEvent['repeat_section_repeatuntil_day'] = Phpfox::getTime('j', $aEvent['timerepeat_tousertimezone'], false);
                $aEvent['repeat_section_repeatuntil_year'] = Phpfox::getTime('Y', $aEvent['timerepeat_tousertimezone'], false);
            }
        }

        $aEvent['location_lat'] = $aEvent['lat'];
        $aEvent['location_lng'] = $aEvent['lng'];

        return $aEvent;
    }

    public function getNumbersOfAttendee($iEvent, $iRsvp)
    {
        $iCnt = db()->select('COUNT(invite_id)')->from(Phpfox::getT('fevent_invite'))->where('event_id = ' . (int)$iEvent . ' AND rsvp_id = ' . (int)$iRsvp)->execute('getSlaveField');

        return $iCnt;
    }

    public function getAllNumbersOfAttendee(&$aEvent)
    {
        $aEvent = array_merge($aEvent, array('iAttendingCnt' => $this->getNumbersOfAttendee($aEvent['event_id'], 1), 'iMaybeCnt' => $this->getNumbersOfAttendee($aEvent['event_id'], 2), 'iAwaitingCnt' => $this->getNumbersOfAttendee($aEvent['event_id'], 0)));
    }

    public function getInvites($iEvent, $iRsvp, $iPage = 0, $iPageSize = 8)
    {
        $aInvites = [];
        $iRsvp = (int)$iRsvp;

        if ($iRsvp == 4) {
            db()->join(Phpfox::getT('friend'), 'friend', 'friend.friend_user_id = ei.invited_user_id AND friend.user_id = ' . Phpfox::getUserId());
        }

        $iCnt = db()->select('COUNT(*)')->from(Phpfox::getT('fevent_invite'), 'ei')->join(Phpfox::getT('user'), 'u', 'u.user_id = ei.invited_user_id')->where('event_id = ' . (int)$iEvent . ($iRsvp == 4 ? ' AND rsvp_id IN (1,2)' : ' AND rsvp_id = ' . $iRsvp))->execute('getSlaveField');

        if ($iCnt) {

            if ($iRsvp == 4) {
                db()->join(Phpfox::getT('friend'), 'friend', 'friend.friend_user_id = ei.invited_user_id AND friend.user_id = ' . Phpfox::getUserId());
            }

            $aInvites = db()->select('ei.*, ' . Phpfox::getUserField())->from(Phpfox::getT('fevent_invite'), 'ei')->join(Phpfox::getT('user'), 'u', 'u.user_id = ei.invited_user_id')->where('ei.event_id = ' . (int)$iEvent . ($iRsvp == 4 ? ' AND ei.rsvp_id IN (1,2)' : ' AND ei.rsvp_id = ' . $iRsvp))->limit($iPage, $iPageSize, $iCnt)->order('ei.invite_id DESC')->execute('getSlaveRows');
        }

        return array($iCnt, $aInvites);
    }

    public function getInviteForUser($iLimit = 6)
    {
        $aRows = db()->select('e.*, ei.rsvp_id, ei.user_id AS inviter_id, ei.invited_user_id AS invitee_id, ' . Phpfox::getUserField())->from(Phpfox::getT('fevent_invite'), 'ei')->join(Phpfox::getT('fevent'), 'e', 'e.event_id = ei.event_id AND e.view_id = 0')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = ei.event_id')->where('ei.rsvp_id = 0 AND ei.invited_user_id = ' . Phpfox::getUserId() . ' AND ei.user_id != ' . Phpfox::getUserId())->limit($iLimit)->execute('getRows');

        return $aRows;
    }

    public function getForProfileBlock($iUserId, $iLimit = 5)
    {
        $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

        $aEvents = db()->select('m.*')->from($this->_sTable, 'm')->join(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = m.event_id AND ei.rsvp_id = 1 AND ei.invited_user_id = ' . (int)$iUserId)->where('m.view_id = 0 AND m.start_time >= \'' . $iTimeDisplay . '\'')->limit($iLimit)->order('m.start_time ASC')->execute('getSlaveRows');

        foreach ($aEvents as $iKey => $aEvent) {
            $aEvents[$iKey]['url'] = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);
            $aEvents[$iKey]['start_time_stamp'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aEvent['start_time']);
            $aEvents[$iKey]['location_clean'] = Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->clean($aEvent['location']), 10);
        }

        return $aEvents;
    }

    public function getForParentBlock($sModule, $iItemId, $iLimit = 5)
    {
        $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

        $aEvents = db()->select('m.event_id, m.title, m.tag_line, m.image_path, m.server_id, m.start_time, m.location, m.country_iso, m.city, m.module_id, m.item_id')->from($this->_sTable, 'm')->where('m.view_id = 0 AND m.module_id = \'' . db()->escape($sModule) . '\' AND m.item_id = ' . (int)$iItemId . ' AND m.start_time >= \'' . $iTimeDisplay . '\'')->limit($iLimit)->order('m.start_time ASC')->execute('getSlaveRows');

        foreach ($aEvents as $iKey => $aEvent) {
            $aEvents[$iKey]['url'] = Phpfox::getLib('url')->makeUrl('fevent', array('redirect' => $aEvent['event_id']));
            $aEvents[$iKey]['start_time_stamp'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aEvent['start_time']);
            $aEvents[$iKey]['location_clean'] = Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->clean($aEvent['location']), 10);
        }

        return $aEvents;
    }

    public function getPendingTotal()
    {
        $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));

        return db()->select('COUNT(m.event_id)')->from($this->_sTable, 'm')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->where('m.view_id = 1')->execute('getSlaveField');
    }

    public function wherePages()
    {
        $wherePages = " AND ( ( m.item_id = 0 )";
        $pages = Phpfox::getService('fevent.helper')->getListOfPagesWhichJoinedByUserID(Phpfox::getUserId());
        if (isset($pages) && is_array($pages) && count($pages) > 0) {
            $pagesLen = count($pages);

            $wherePages .= ' or ( m.module_id = \'pages\' AND m.item_id IN ( ';

            $wherePages .= $pages[0]['page_id'];
            for ($i = 1; $i < $pagesLen; $i++) {
                $wherePages .= ', ' . $pages[$i]['page_id'];
            }
            $wherePages .= ' )) ';
        }
        $wherePages .= ')';
        return $wherePages;
    }


    public function getFeaturedTotal()
    {

        $wherePages = $this->getConditionsForSettingPageGroup('m');
        return db()->select('COUNT(*)')->from($this->_sTable, ' m')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->where('m.is_featured = 1 and m.view_id = 0 and m.privacy = 0 ' . $wherePages)->execute('getSlaveField');
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

        return db()->select('COUNT(*)')->from($this->_sTable)->where($sWhere)->execute('getSlaveField');
    }

    /**
     * @param $iRsvpId
     * @return array|int|string
     */
    public function getAttendingTotal($iRsvpId)
    {
        $sWhere = 'e.view_id = 0 AND ei.rsvp_id = ' . $iRsvpId . ' AND ei.invited_user_id =' . (int)Phpfox::getUserId();
        $aModules = ['user'];
        if (!Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (!Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }
        $sWhere .= ' AND (module_id NOT IN ("' . implode('","', $aModules) . '") OR module_id = \'fevent\')';

        return db()->select('COUNT(*)')->from($this->_sTable, 'e')->join(':fevent_invite', 'ei', 'ei.event_id = e.event_id')->where($sWhere)->execute('getSlaveField');
    }

    public function getRandomSponsored($limit = 1)
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        $sCacheId = $this->cache()->set('fevent_sponsored_' . $iToday);
        if (($eventIds = $this->cache()->get($sCacheId)) === false) {
            $tempEvents = db()->select('e.event_id')
                ->from($this->_sTable, 'e')
                ->where('e.view_id = 0 AND e.privacy IN (0,1,2) AND e.is_sponsor = 1 AND (e.start_time >= \'' . $iToday . '\' OR (e.start_time < \'' . $iToday . '\' AND e.end_time > \'' . $iToday . '\'))')
                ->execute('getSlaveRows');
            $eventIds = array_column($tempEvents, 'event_id');
            $this->cache()->removeGroup('fevent_sponsored');
            $this->cache()->save($sCacheId, $eventIds);
            $this->cache()->group('fevent_sponsored', $sCacheId);
        }

        if(!empty($eventIds)) {
            if(isset($eventIds[0]['event_id'])) {
                $eventIds = array_column($eventIds, 'event_id');
            }
            $events = db()->select('e.*, s.*, s.country_iso AS sponsor_country_iso, s.gender AS sponsor_gender, ' . Phpfox::getUserField())
                        ->from($this->_sTable, 'e')
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
                        ->join(Phpfox::getT('better_ads_sponsor'), 's', 's.item_id = e.event_id AND s.module_id = \'fevent\' AND s.is_custom = 3 AND s.is_active = 1')
                        ->where('e.event_id IN (' . implode(',', $eventIds) . ')')
                        ->execute('getSlaveRows');

            shuffle($events);

            $count = 0;
            $returnedEvents = [];
            $currentUserId = Phpfox::getUserId();

            foreach ($events as $iKey => $aEvent) {
                if($count == $limit) {
                    break;
                }

                if(in_array($aEvent['privacy'], [1,2]) && $aEvent['user_id'] != $currentUserId && !Phpfox::getService('friend')->isFriend($aEvent['user_id'], $currentUserId)) {
                    continue;
                }

                $aEvent = array_merge($aEvent, [
                    'categories' => Phpfox::getService('fevent.category')->getCategoriesById($aEvent['event_id']),
                    'country_iso' => $aEvent['sponsor_country_iso'],
                    'gender' => $aEvent['sponsor_gender']
                ]);
                $returnedEvents[] = $aEvent;
                $count++;
            }

            if (Phpfox::isAppActive('Core_BetterAds')) {
                $returnedEvents = Phpfox::getService('ad')->filterSponsor($returnedEvents);
            }

            // Randomize to get a event
            return array_slice($returnedEvents, 0, $limit);
        }

        return false;


    }

    public function isAlreadyInvited($iItemId, $aFriends)
    {
        if ((int)$iItemId === 0) {
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

            $aInvites = db()->select('invite_id, rsvp_id, invited_user_id')->from(Phpfox::getT('fevent_invite'))->where('event_id = ' . (int)$iItemId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')->execute('getSlaveRows');

            $aCache = [];
            foreach ($aInvites as $aInvite) {
                $aCache[$aInvite['invited_user_id']] = ($aInvite['rsvp_id'] > 0 ? _p('responded') : _p('invited'));
            }

            if (count($aCache)) {
                return $aCache;
            }
        }

        return false;
    }

    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array('phrase' => _p('events'), 'value' => db()->select('COUNT(*)')->from(Phpfox::getT('fevent'))->where('view_id = 0 AND time_stamp >= ' . $iToday)->execute('getSlaveField'));
    }

    public function getUpcoming($bIsPage = false, $bIsProfile = false, $iLimit = 3)
    {
        static $aUpcoming = null;
        static $iTotal = null;

        if ($aUpcoming !== null) {
            return array($iTotal, $aUpcoming);
        }

        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        $aUpcoming = [];
        $repeatday = "( v.isrepeat>-1 and v.timerepeat>" . ($iToday) . ")";
        $repeattime = "(v.isrepeat>-1 and (v.timerepeat=0 or " . $repeatday . "))";

        $aRows = db()->select('v.*, ' . Phpfox::getUserField())->from(Phpfox::getT('fevent'), 'v')->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')->where('v.view_id = 0 AND ( v.start_time > \'' . PHPFOX_TIME . '\')')->order('v.start_time ASC')->execute('getSlaveRows');

        // Check privacy
        $aRows = $this->checkPrivacy($aRows, $bIsPage, $bIsProfile);
        $iTotal = 0;
        if (is_array($aRows) && count($aRows)) {
            $iTotal = count($aRows);
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
        return db()->select('v.event_id
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
                                        ')->from(Phpfox::getT('fevent'), 'v')->where(' v.isrepeat > -1 AND v.timerepeat > ' . PHPFOX_TIME . ' AND v.end_time < ' . PHPFOX_TIME)->execute('getSlaveRows');
    }

    public function getEventWithLessInfo($eventID)
    {
        return db()->select('v.event_id
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
                                        ')->from(Phpfox::getT('fevent'), 'v')->where('v.event_id = ' . (int)$eventID)->execute('getRow');
    }

    public function getPast($bIsPage = false, $bIsProfile = false, $iLimit = 3)
    {
        static $aPast = null;
        static $iTotal = null;

        if ($aPast !== null) {
            return [$iTotal, $aPast];
        }

        $aRows = db()->select('v.*, ft.description_parsed, ei.rsvp_id, ei.user_id AS inviter_id, ei.invited_user_id AS invitee_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('fevent'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = v.event_id')
            ->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = v.event_id AND ei.invited_user_id = ' . Phpfox::getUserId())
            ->where('v.view_id = 0 ' . ' AND ( v.end_time < ' . (int)PHPFOX_TIME . ' ) ')
            ->order('v.start_time DESC')
            ->execute('getSlaveRows');

        // Check privacy
        $aRows = $this->checkPrivacy($aRows, $bIsPage, $bIsProfile, [0,1,2]);

        $iTotal = 0;
        $aPast = [];

        if (is_array($aRows) && count($aRows)) {
            $iTotal = count($aRows);
            $iIndex = 0;
            foreach ($aRows as $iKey => $aRow) {
                if ($iIndex == $iLimit) {
                    break;
                }
                $iIndex++;
                $aRow['is_invited'] = !empty($aRow['invitee_id']) && !empty($aRow['inviter_id']) ? ($aRow['inviter_id'] != $aRow['invitee_id'] ? true : ($aRow['user_id'] == $aRow['invitee_id'] ? true : false)) : false;
                $aPast[] = $aRow;
            }
        }

        return [$iTotal, $aPast];
    }


    public function getJsEvents($bIsPage = false, $bIsProfile = false)
    {
        $moduleIds = ['fevent'];
        if(Phpfox::getParam('fevent.fevent_display_event_created_in_page')) {
            $moduleIds[] = 'pages';
        }
        if(Phpfox::getParam('fevent.fevent_display_event_created_in_group')) {
            $moduleIds[] = 'groups';
        }

        $aRows = db()->select('*')->from($this->_sTable, 'm')->where('m.view_id = 0 AND m.module_id IN ("' . implode('","', $moduleIds) . '")')->execute('getRows');

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
        while ($iDaysInAdvance > 0) {
            if ($sDayUntil > $iLastDayOfMonth) {
                if ($sMonthUntil < 12) {
                    $sMonthUntil++;
                } else {
                    $sMonthUntil = 1;
                    $iLastDayOfMonth = Phpfox::getLib('date')->lastDayOfMonth($sMonthUntil, $iThisYear);
                }
                $sDayUntil = 0;
            }
            $iDaysInAdvance--;
            $sDayUntil++;
        }
        $sMonthUntil = $sMonthUntil[0] != '0' ? ($sMonthUntil < 10) ? '0' . $sMonthUntil : $sMonthUntil : $sMonthUntil;
        $sDayUntil = ($sDayUntil < 10) ? '0' . $sDayUntil : $sDayUntil;
        if ($sMonthUntil < $iThisMonth) // its next year
        {
            $sBirthdays = '\'' . $iThisMonth . '' . $iToday . '\' <= uf.birthday_range OR \'' . $sMonthUntil . $sDayUntil . '\' >= uf.birthday_range';
        } else {
            $sBirthdays = '\'' . $iThisMonth . '' . $iToday . '\' <= uf.birthday_range AND \'' . $sMonthUntil . $sDayUntil . '\' >= uf.birthday_range';
        }

        // cache this query
        $sCacheId = $this->cache()->set('friend_birthday_' . $iUser);
        if (false === ($aBirthdays = $this->cache()->get($sCacheId, 0))) // cache is in 5 hours
        {
            $aBirthdays = db()->select(Phpfox::getUserField() . ', uf.dob_setting, fb.birthday_user_receiver, uf.first_name')->from(Phpfox::getT('friend'), 'f')->join(Phpfox::getT('user'), ' u', 'u.user_id = f.friend_user_id')->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')->leftJoin(Phpfox::getT('friend_birthday'), 'fb', 'fb.birthday_user_receiver = u.user_id AND fb.time_stamp > ' . (PHPFOX_TIME - 2629743))/* Fixes (SHB-989762) */
                ->where('f.user_id = ' . $iUser . ' AND (' . $sBirthdays . ') AND (uf.dob_setting != 2 AND uf.dob_setting != 3) AND fb.birthday_user_receiver IS NULL')->order('uf.birthday_range ASC')->limit(15)->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aBirthdays);
            Phpfox::getLib('cache')->group('friend', $sCacheId);
        }
        if (!is_array($aBirthdays)) {
            $aBirthdays = [];
        }

        foreach ($aBirthdays as $iKey => $aFriend) {
            // add when is their birthday and how old are they going to be
            $iAge = Phpfox::getService('user')->age($aFriend['birthday']);

            if (substr($aFriend['birthday'], 0, 2) . '-' . substr($aFriend['birthday'], 2, 2) == date('m-d', Phpfox::getTime())) {
                $aBirthdays[$iKey]['new_age'] = $iAge;
            } else {
                $aBirthdays[$iKey]['new_age'] = ($iAge + 1);
            }

            if (!isset($aFriend['birthday']) || empty($aFriend['birthday'])) {
                $iDays = -1;
            } else {
                $iDays = Phpfox::getLib('date')->daysToDate($aFriend['birthday'], null, false);
            }

            if ($iDays < 0 || $aFriend['dob_setting'] == 2 || $aFriend['dob_setting'] == 3) {
                unset($aBirthdays[$iKey]);
                continue;
            } else {
                $aBirthdays[$iKey]['days_left'] = floor($iDays);
            }

            // do we show the age?
            if (($aFriend['dob_setting'] < 3 & $aFriend['dob_setting'] != 1) || ($aFriend['dob_setting'] == 4)) // 0 => age and dob; 1 => age and day/month; 2 => age
            {
                $aBirthdays[$iKey]['show_age'] = true;
            } else {
                $aBirthdays[$iKey]['show_age'] = false;
            }
            // fail safe
            $aBirthdays[$iKey]['birthdate'] = '';
            // Format the birthdate according to the profile
            $aBirthDay = Phpfox::getService('user')->getAgeArray($aFriend['birthday']);
            if ($aFriend['dob_setting'] == 4)// just copy the arbitrary format on the browse.html
            {
                unset($aBirthDay['year']);
            } elseif ($aFriend['dob_setting'] == 0) {
                $aBirthdays[$iKey]['birthdate'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . ', ' . $aBirthDay['year'];
                $aBirthdays[$iKey]['birthdate1'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . ', ' . $year;
                $aBirthdays[$iKey]['day'] = $aBirthDay['day'];
                $aBirthdays[$iKey]['month'] = $aBirthDay['month'];
                if ($aBirthDay['month'] < 10) {
                    $aBirthDay['month'] = '0' . $aBirthDay['month'];
                }
                if ($aBirthDay['day'] < 10) {
                    $aBirthDay['day'] = '0' . $aBirthDay['day'];
                }
                $aBirthdays[$iKey]['bday'] = $year . '-' . $aBirthDay['month'] . '-' . $aBirthDay['day'];
                $aBirthdays[$iKey]['bday1'] = $year . '/' . $aBirthDay['month'] . '/' . $aBirthDay['day'];
            } elseif ($aFriend['dob_setting'] == 1) {
                $aBirthdays[$iKey]['birthdate'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'];
                $aBirthdays[$iKey]['birthdate1'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . ', ' . $year;
                $aBirthdays[$iKey]['day'] = $aBirthDay['day'];
                $aBirthdays[$iKey]['month'] = $aBirthDay['month'];
                if ($aBirthDay['month'] < 10) {
                    $aBirthDay['month'] = '0' . $aBirthDay['month'];
                }
                if ($aBirthDay['day'] < 10) {
                    $aBirthDay['day'] = '0' . $aBirthDay['day'];
                }
                $aBirthdays[$iKey]['bday'] = $year . '-' . $aBirthDay['month'] . '-' . $aBirthDay['day'];
                $aBirthdays[$iKey]['bday1'] = $year . '/' . $aBirthDay['month'] . '/' . $aBirthDay['day'];
            }
            $aBirthdays[$iKey]['birthday_display_name'] = _p('fevent.user_birthday', ['name' => Phpfox::getParam('user.split_full_name') && !empty($aFriend['first_name']) ? $aFriend['first_name'] : $aFriend['full_name']]);
        }

        $aReturnBirthday = [];
        foreach ($aBirthdays as $iBirthKey => $aBirthData) {
            $aReturnBirthday[$aBirthData['days_left']][] = $aBirthData;
        }

        ksort($aReturnBirthday);

        return $aReturnBirthday;
    }

    public function getJsEventsForCalendar($from, $to, $conds = '')
    {
        if ($conds) {
            $conds = ' AND (' . $conds . ')';
        } else {
            $conds = ' AND 1 = 0';
        }

        $moduleIds = ['fevent'];
        if(Phpfox::getParam('fevent.fevent_display_event_created_in_page')) {
            $moduleIds[] = 'pages';
        }
        if(Phpfox::getParam('fevent.fevent_display_event_created_in_group')) {
            $moduleIds[] = 'groups';
        }

        $aRows = db()->select('fe.*')->from($this->_sTable, 'fe')->leftJoin(Phpfox::getT('fevent_invite'), 'fei', 'fe.event_id = fei.event_id')->where('fe.view_id = 0 AND fe.module_id IN ("' . implode('","', $moduleIds) . '") AND ((start_time > ' . $from . ' AND start_time < ' . $to . ')' . ' OR (end_time > ' . $from . ' AND end_time < ' . $to . ')' . ' OR (start_time < ' . $from . ' AND end_time > ' . $to . '))' . $conds)->group('fe.event_id')->execute('getRows');

        $aRows = $this->checkPrivacy($aRows, false, false);

        $birthdays = $this->getBirthdays(Phpfox::getuserId(), Phpfox::getTime('Y', $from));
        $birtdaysParsed = [];
        foreach ($birthdays as $key => $birthdaySection) {
            foreach ($birthdaySection as $personBirthday) {
                $time = strtotime($personBirthday['bday']);
                $personBirthday['start_time'] = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m', $time), Phpfox::getTime('d', $time), Phpfox::getTime('Y', $time));
                $personBirthday['end_time'] = Phpfox::getLib('date')->mktime(23, 59, 59, Phpfox::getTime('m', $time), Phpfox::getTime('d', $time), Phpfox::getTime('Y', $time));
                $birtdaysParsed[] = $personBirthday;
            }
        }

        $aRows = array_merge($aRows, $birtdaysParsed);

        return $aRows;
    }

    public function getFeatured($bIsPage = false, $bIsProfile = false, $limit = 7, $getNew = false)
    {
        static $aFeatured = null;
        static $iTotal = null;

        if ($aFeatured !== null && !$getNew) {
            return [$iTotal, $aFeatured];
        }

        $aFeatured = [];
        $sParentCond = $this->getConditionsForSettingPageGroup('v');
        $aRows = db()->select('v.*, ei.rsvp_id, ft.description_parsed, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('fevent'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = v.event_id')
            ->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = v.event_id AND ei.invited_user_id = ' . Phpfox::getUserId())
            ->where('v.view_id = 0 AND v.is_featured = 1 ' . $sParentCond)
            ->order('v.start_time ASC')
            ->limit($limit)
            ->execute('getSlaveRows');

        // Check privacy
        $aRows = $this->checkPrivacy($aRows, $bIsPage, $bIsProfile, [0,1,2]);

        $iTotal = 0;
        if (is_array($aRows) && count($aRows)) {
            $iTotal = count($aRows);
            shuffle($aRows);
            $iIndex = 0;
            foreach ($aRows as $iKey => $aRow) {
                $aRow['d_start_time'] = Phpfox::getTime('d', (int)$aRow['start_time']); //day
                $aRow['M_start_time'] = Phpfox::getTime('M', (int)$aRow['start_time']); //month

                if ($iIndex === $limit) {
                    break;
                }
                $iIndex++;
                $aFeatured[] = $aRow;
            }
        }

        return [$iTotal, $aFeatured];
    }

    public function getForRssFeed()
    {
        $iTimeDisplay = Phpfox::getLib('phpfox.date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
        $aConditions = [];
        $aConditions[] = "e.view_id = 0 AND e.module_id IN ('fevent','pages','groups')";
        $aConditions[] = "AND e.start_time >= '" . $iTimeDisplay . "'";

        $aRows = db()->select('e.*, et.description_parsed AS description, ' . Phpfox::getUserField())->from(Phpfox::getT('fevent'), 'e')->join(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->where($aConditions)->order('e.time_stamp DESC')->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            if(!empty($aRow['item_id'])) {
                if(($aRow['module_id'] == 'pages' && (!Phpfox::isAppActive('Core_Pages') || !Phpfox::getParam('fevent.fevent_display_event_created_in_page') || !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'fevent.view_browse_events'))) || ($aRow['module_id'] == 'groups' && (!Phpfox::isAppActive('PHPfox_Groups') || !Phpfox::getParam('fevent.fevent_display_event_created_in_group') || !Phpfox::getService('groups')->hasPerm($aRow['item_id'], 'fevent.view_browse_events')))) {
                    unset($aRows[$iKey]);
                    continue;
                }
            }
            $aRows[$iKey]['link'] = Phpfox::permalink('fevent', $aRow['event_id'], $aRow['title']);
            $aRows[$iKey]['creator'] = $aRow['full_name'];
        }

        return $aRows;
    }

    public function getImages($iId, $iLimit = null)
    {
        return db()->select('image_id, image_path, server_id')->from(Phpfox::getT('fevent_image'))->where('event_id = ' . (int)$iId)->order('ordering ASC')->limit($iLimit)->execute('getSlaveRows');
    }

    public function getImagesByIds($sIds)
    {
        return db()->select('image_id, image_path, server_id')->from(Phpfox::getT('fevent_image'))->where('image_id IN (' . rtrim($sIds, ',') . ')')->execute('getSlaveRows');
    }

    public function getCustomFields($iParentId = 0)
    {
        $aFields = db()->select('cf.*, fec.name AS category_name')->from(Phpfox::getT('fevent_custom_field'), 'cf')->leftJoin(Phpfox::getT('fevent_category'), 'fec', 'fec.category_id = cf.category_id')->where("fec.parent_id = $iParentId")->order('cf.ordering ASC')->execute('getRows');

        $aCustomFields = [];
        foreach ($aFields as $aField) {
            $aCustomFields[$aField['category_id']][] = $aField;
        }

        $aCategories = db()->select('fec.*')->from(Phpfox::getT('fevent_category'), 'fec')->where("fec.parent_id = $iParentId")->order('fec.ordering ASC')->execute('getRows');

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

    public function execute($aCallback = false, $searchParams = [])
    {
        $oHelper = Phpfox::getService('fevent.helper');

        $aActualConditions = (array)Phpfox::getLib('search')->getConditions();
        $this->_aConditions = [];
        $request = Phpfox::getLib('request');

        $this->_sView = $request->get('view', 'all');
        foreach ($aActualConditions as $iKey => $sCond) {
            switch ($this->_sView) {
                case 'friend':
                    $sCond = str_replace('%PRIVACY%', '0,1,2', $sCond);
                    break;
                case 'my':
                    $sCond = str_replace('%PRIVACY%', '0,1,2,3,4', $sCond);
                    break;
                default:
                    $sCond = str_replace('%PRIVACY%', Phpfox::getUserParam('core.can_view_private_items') ? '0,1,2,3,4' : '0', $sCond);
                    break;
            }

            $hasStartTimeCondition = preg_match('/AND m.start_time/', $sCond) || preg_match('/AND (m.start_time/', $sCond) || preg_match('/AND m.end_time/', $sCond) || preg_match('/AND (m.end_time/', $sCond);
            if ($hasStartTimeCondition) {
                $sCond = '';
            }

            if (!empty($sCond)) {
                $this->_aConditions[] = $sCond;
            }
        }

        if (!empty($searchParams['stime'])) {
            $startTimeArray = explode('/', trim($searchParams['stime']));
            if (count($startTimeArray) == 3) {
                $startTime = Phpfox::getLib('date')->mktime(0, 0, 0, $startTimeArray[0], $startTimeArray[1], $startTimeArray[2]);
                $startTime = Phpfox::getLib('date')->convertToGmt($startTime);
                $this->_aConditions[] = ' AND (m.start_time >= ' . $startTime . ') ';
            }
        }

        if (!empty($searchParams['etime'])) {
            $endTimeArray = explode('/', trim($searchParams['etime']));
            if (count($endTimeArray) == 3) {
                $endTime = Phpfox::getLib('date')->mktime(23, 59, 59, $endTimeArray[0], $endTimeArray[1], $endTimeArray[2]);
                $endTime = Phpfox::getLib('date')->convertToGmt($endTime);
                $this->_aConditions[] = ' AND (m.start_time <= ' . $endTime . ') ';
            }
        }

        $status = isset($searchParams['status']) ? $searchParams['status'] : '';
        if (!empty($status)) {
            switch ($searchParams['status']) {
                case 'upcoming':
                    {
                        $this->_aConditions[] = ' AND (m.start_time > ' . PHPFOX_TIME . ' ) ';
                        break;
                    }
                case 'ongoing':
                    {
                        $this->_aConditions[] = ' AND (m.end_time >= ' . PHPFOX_TIME . ' AND m.start_time <= ' . PHPFOX_TIME . ' ) ';
                        break;
                    }
                case 'past':
                    {
                        $this->_aConditions[] = ' AND (m.end_time < ' . PHPFOX_TIME . ' ) ';
                        break;
                    }
            }
        }

        $sSearch = !empty($searchParams['search']) ? urldecode($searchParams['search']) : '';
        if (strlen(trim($sSearch)) > 0) {
            $sSearch = Phpfox::getLib('parse.input')->prepare($sSearch);

            $this->_aConditions[] = " AND (m.title LIKE '%$sSearch%' OR ft.description LIKE '%$sSearch%')";
        }

        $rangeValueFrom = !empty($searchParams['rangevaluefrom']) ? urldecode($searchParams['rangevaluefrom']) : self::$defaultRangeValueFrom;

        if (strlen(trim($rangeValueFrom)) > 0 && !empty($searchParams['glat']) && !empty($searchParams['glong'])) {
            $rangeValueFromParsed = Phpfox::getLib('parse.input')->prepare($rangeValueFrom);
            preg_match("/[0-9]*/", $rangeValueFromParsed, $kq);

            if ($kq == null || strlen(trim($kq[0])) < strlen(trim($rangeValueFromParsed))) {
                $this->_aConditions[] = " AND (1=0)";
            } else {
                $rangeValueFromParsed = floatval($rangeValueFromParsed);
                $rangetype = $searchParams['rangetype'];
                $glat = floatval($searchParams['glat']);
                $glong = floatval($searchParams['glong']);

                if ($rangetype == 1) {
                    // 1km = (1000 / 1609) miles = 0.6215 miles
                        $rangeValueFromParsed = $rangeValueFromParsed * 0.6215;
                } elseif ($rangetype == 0) {
                    $rangeValueFromParsed = $rangeValueFromParsed;
                }

                $this->_aConditions[] = " AND (
                        (3959 * acos(
                                cos( radians('{$glat}')) 
                                * cos( radians( m.lat ) ) 
                                * cos( radians( m.lng ) - radians('{$glong}') ) 
                                + sin( radians('{$glat}') ) * sin( radians( m.lat ) ) 
                            ) < {$rangeValueFromParsed} 
                        )                     
                    )";

            }
        }

        if (!empty($searchParams['country_iso'])) {
            $sCountry = $searchParams['country_iso'];
            $sCountry = Phpfox::getLib('parse.input')->prepare($sCountry);

            $this->_aConditions[] = " AND (m.country_iso LIKE '%$sCountry%')";
        }

        if ($sDate = $request->get('date')) {
            preg_match('/(\d+)\-(\d+)\-(\d+)/', $sDate, $aMatches);
            if (!empty($aMatches[3])) {
                $iStartDay = Phpfox::getLib('date')->mktime(0, 0, 0, intval($aMatches[2]), intval($aMatches[3]), intval($aMatches[1]));
                $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, intval($aMatches[2]), intval($aMatches[3]), intval($aMatches[1]));
                $iStartDay = $oHelper->convertFromUserTimeZone($iStartDay);
                $iEndDay = $oHelper->convertFromUserTimeZone($iEndDay);
                $condition_date = "(m.start_time <= $iEndDay AND m.start_time >= $iStartDay)";
                $this->_aConditions[] = " AND (" . $condition_date . ")";
            }
        }

        Phpfox::getService('fevent.browse')->getQueryJoins(true);
        db()->select('COUNT(m.event_id) as count')->from($this->_sTable, 'm')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->where($this->_aConditions);
        if (Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
            db()->union();
            $this->_aConditions[] = ' AND m.user_id =' . (int)Phpfox::getUserId();
            db()->select('COUNT(m.event_id) as count')->from($this->_sTable, 'm')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->where($this->_aConditions)->union();
        }

        $aCnt = db()->execute('getSlaveRows');

        if ($aCnt) {
            $this->_iCnt = 0;
            foreach ($aCnt as $iCnt) {
                $this->_iCnt += $iCnt['count'];
            }
        }

        $this->_aRows = [];

        if ($this->_iCnt) {
            Phpfox::getService('fevent.browse')->getQueryJoins();
            db()->from($this->_sTable, 'm')->where($this->_aConditions);
            Phpfox::getService('fevent.browse')->query();

            $order = Phpfox::getLib('search')->getSort();

            $pageSelect = '';
            if ($this->_sView == 'pagesevents') {
                db()->leftJoin(Phpfox::getT('pages'), 'pag', 'pag.page_id = m.item_id');
                $pageSelect = 'pag.page_id, pag.title as page_title, ';
            }

            db()->select('m.*, ' . $pageSelect . 'u.*')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->order($order)->limit(Phpfox::getLib('search')->getPage(), Phpfox::getLib('search')->getDisplay(), $this->_iCnt);

            if (Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
                db()->union();
                $this->_aConditions[] = ' AND m.user_id =' . (int)Phpfox::getUserId();
                Phpfox::getService('fevent.browse')->getQueryJoins(false, true);
                Phpfox::getService('fevent.browse')->query();
                db()->select('m.*, ' . $pageSelect . 'u.*')->from($this->_sTable, 'm')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->where($this->_aConditions)->order($order)->limit(Phpfox::getLib('search')->getPage(), Phpfox::getLib('search')->getDisplay(), $this->_iCnt)->union();
            }
            $this->_aRows = db()->execute('getSlaveRows');
        }
        Phpfox::getService('fevent.browse')->processRows($this->_aRows);
        return $this->_aRows;
    }

    public function getCount()
    {
        if (!isset($this->_iCnt)) {
            $this->_iCnt = 0;
        }
        return $this->_iCnt;
    }

    public function checkPrivacy($aRows, $bIsPage = false, $bIsProfile = false, $privacyForDefault = [0])
    {
        if (!is_array($aRows)) {
            return $aRows;
        }

        $iUserId = Phpfox::getUserId();

        $sView = $this->request()->get('view', 'all');

        $aOutput = [];
        foreach ($aRows as $iKey => $aRow) {
            $bIsPage = $bIsPage ? $bIsPage : 0;

            if ($bIsProfile !== false && $aRow['user_id'] != $bIsProfile) {
                continue;
            }

            if ($bIsProfile === false && $aRow['item_id'] != $bIsPage && $bIsPage != 0) {
                continue;
            }

            if(!empty($aRow['item_id'])) {
                if(($aRow['module_id'] == 'pages' && (!Phpfox::isAppActive('Core_Pages') || !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'fevent.view_browse_events'))) || ($aRow['module_id'] == 'groups' && (!Phpfox::isAppActive('PHPfox_Groups') || !Phpfox::getService('groups')->hasPerm($aRow['item_id'], 'fevent.view_browse_events')))) {
                    continue;
                }
            }

            $iRsvp = db()->select('rsvp_id')->from(Phpfox::getT('fevent_invite'))->where('event_id = ' . (int)$aRow['event_id'] . ' AND invited_user_id = ' . (int)$iUserId)->execute('getField');
            if (!in_array($iRsvp, array('0', '1', '2', '3'))) {
                $iRsvp = '-1';
            }

            switch ($sView) {
                case 'my':
                    if ($iUserId == $aRow['user_id']) {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'friend':
                    if (in_array($aRow['privacy'], array('0', '1', '2')) && Phpfox::getService('friend')->isFriend($aRow['user_id'], $iUserId)) {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'featured':
                    if ($aRow['is_featured'] && $aRow['privacy'] == '0') {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'attending':
                    if ($iRsvp == '1') {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'may-attend':
                    if ($iRsvp == '2') {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'not-attending':
                    if ($iRsvp == '3') {
                        $aOutput[] = $aRow;
                    }
                    break;
                case 'invites':
                    if ($iRsvp == '0') {
                        $aOutput[] = $aRow;
                    }
                    break;
                default:
                    if (in_array($aRow['privacy'], $privacyForDefault)) {
                        if($aRow['privacy'] == 0 || (in_array($aRow['privacy'], [1,2]) && ($aRow['user_id'] == $iUserId || Phpfox::getService('friend')->isFriend($aRow['user_id'], $iUserId)))) {
                            $aOutput[] = $aRow;
                        }
                    }
            }
        }

        return $aOutput;
    }

    public function updateSetting($name, $default_value)
    {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('fevent_setting'))->where('name="' . $name . '"')->execute('getSlaveRows');
        $oFilter = Phpfox::getLib('parse.input');

        if (count($aRows) == 0) {
            $aInserts = [];
            $aInserts['name'] = $name;
            $aInserts['default_value'] = $oFilter->clean($default_value);
            phpfox::getLib("database")->insert(phpfox::getT('fevent_setting'), $aInserts);
        } else {
            $aUpdates = [];
            $aUpdates['default_value'] = $oFilter->clean($default_value);
            phpfox::getLib("database")->update(phpfox::getT('fevent_setting'), $aUpdates, 'name="' . $name . '"');
        }
    }

    public function getSetting($name)
    {
        $aRow = phpfox::getLib("database")->select('*')->from(phpfox::getT('fevent_setting'))->where('name="' . $name . '"')->execute('getSlaveRow');
        return $aRow;
    }

    public function getAllEventPhpfox()
    {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('event'))->execute('getSlaveRows');
        return $aRows;
    }

    public function getAllCategorydataPhpfox($event_id)
    {
        $aRow = phpfox::getLib("database")->select('*')->from(phpfox::getT('event_category_data'))->where('event_id=' . $event_id)->execute('getSlaveRow');
        return $aRow;
    }

    public function getAllFeedEventPhpfox($event_id)
    {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('event_feed'))->where('parent_user_id=' . $event_id)->execute('getSlaveRows');
        return $aRows;
    }

    public function getFeedCommentPhpfox($item_id)
    {
        $aRow = phpfox::getLib("database")->select('*')->from(phpfox::getT('event_feed_comment'))->where('feed_comment_id=' . $item_id)->execute('getSlaveRow');
        return $aRow;
    }

    public function getEventTextPhpfox($event_id)
    {
        $aRow = phpfox::getLib("database")->select('*')->from(phpfox::getT('event_text'))->where('event_id=' . $event_id)->execute('getSlaveRow');
        return $aRow;
    }

    public function getInviteEventPhpfox($event_id)
    {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('event_invite'))->where('event_id=' . $event_id)->execute('getSlaveRows');
        return $aRows;
    }

    public function getInviteAdvancedEvent($event_id)
    {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('fevent_invite'))->where('event_id=' . $event_id)->execute('getSlaveRows');
        return $aRows;
    }

    public function getIdEventLast()
    {
        $aRows = phpfox::getLib("database")->select('*')->from(phpfox::getT('fevent'))->limit(1)->order('event_id desc')->execute('getSlaveRows');
        if (count($aRows) == 0) return 0; else
            return $aRows[0]['event_id'] + 10;
    }

    public function buildRRule($aEvent)
    {
        $rRule = "";
        $gmt_offset = $aEvent['start_gmt_offset'] * 3600;
        $start = $aEvent['start_time'] + $gmt_offset;
        $end = $aEvent['timerepeat'] + $gmt_offset;
        $isrepeat = $aEvent['isrepeat'];

        switch ($isrepeat) {
            case 0:
                $rRule = "\nRRULE:FREQ=DAILY;COUNT=" . (floor(($end - $start) / 86400) + 1);
                break;
            case 1:
                $by_day = strtoupper(substr(date('D', $start), 0, 2));
                $rRule = "\nRRULE:FREQ=WEEKLY;COUNT=" . (floor(($end - $start) / 604800) + 1) . ";BYDAY=" . $by_day;
                break;
            case 2:
                $hour = date('H', $start);
                $minute = date('i', $start);
                $day = date('d', $start);
                $month = date('m', $start);
                $year = date('Y', $start);

                $cnt = 0;
                while ($start <= $end) {
                    $cnt++;
                    do {
                        $month++;
                        if ($month == 13) {
                            $month = 1;
                            $year++;
                        }
                        $start = mktime($hour, $minute, 0, $month, $day, $year);
                    } while (date('d', $start) != $day);
                }

                $rRule = "\nRRULE:FREQ=MONTHLY;COUNT=" . $cnt . ";BYMONTHDAY=" . $day;
        }

        return $rRule;
    }

    public function getJdpickerPhrases()
    {
        $sPhrases = "";
        $aVarNames = array('fevent.january', 'fevent.february', 'fevent.march', 'fevent.april', 'fevent.may', 'fevent.june', 'fevent.july', 'fevent.august', 'fevent.september', 'fevent.october', 'fevent.november', 'fevent.december', 'fevent.jan', 'fevent.feb', 'fevent.mar', 'fevent.apr', 'fevent.jun', 'fevent.jul', 'fevent.aug', 'fevent.sep', 'fevent.oct', 'fevent.nov', 'fevent.dec', 'fevent.weekday_sunday', 'fevent.weekday_monday', 'fevent.weekday_tuesday', 'fevent.weekday_wednesday', 'fevent.weekday_thursday', 'fevent.weekday_friday', 'fevent.weekday_saturday');

        foreach ($aVarNames as $sVarName) {
            $sPhrases .= "\noTranslations['$sVarName'] = '" . str_replace("'", "\\'", _p($sVarName)) . "';";
        }

        return $sPhrases;
    }

    public function getTopEvent($sType = 'viewed', $iLimit = 4, $bIsPage = false, $bNoCount = false, $pageId = -1)
    {
        switch ($sType) {
            case 'liked':
                $sOrder = 'v.total_like DESC';
                $cond = ' AND v.total_like > 0';
                break;
            case 'viewed':
                $sOrder = 'v.total_view DESC';
                $cond = ' AND v.total_view > 0';
                break;
            case 'discussed':
                $sOrder = 'v.total_comment DESC';
                $cond = ' AND v.total_comment > 0';
                break;
            default:
                $sOrder = 'v.total_view DESC';
        }

        $iTotal = 0;
        if ($bIsPage) {
            if(Phpfox::isUser()) {
                db()->select('v.event_id')
                    ->from($this->_sTable, 'v')
                    ->where('v.user_id = ' . Phpfox::getUserId() . ' AND v.view_id = 0 AND v.privacy IN(1,2) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $cond)
                    ->union();
                db()->select('v.event_id')
                    ->from($this->_sTable, 'v')
                    ->join(Phpfox::getT('friend'), 'f', 'f.is_page = 0 AND f.user_id = v.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                    ->where('v.view_id = 0 AND v.privacy IN(1,2) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $cond)
                    ->union();
            }

            db()->select('v.event_id')
                ->from($this->_sTable, 'v')
                ->where('v.view_id = 0 AND v.privacy IN(0) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $cond)
                ->union()
                ->unionFrom('sub_v');

            $aRows = db()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . 'v.*, ft.description_parsed, ei.rsvp_id, ei.user_id AS inviter_id, ei.invited_user_id AS invitee_id, ' . Phpfox::getUserField())
                ->join(Phpfox::getT('fevent'), 'v', 'v.event_id = sub_v.event_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = v.event_id')
                ->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = v.event_id AND ei.invited_user_id = ' . Phpfox::getUserId())
                ->limit($iLimit)
                ->execute('getSlaveRows');
            if (!$bNoCount) {
                $iTotal = db()->getField('SELECT FOUND_ROWS()');
            }
        } else {
            //  check permissin view event in page which joined/liked
            $wherePages = '';

            //  build sql
            $groupByFriend = '';
            $andWherePrivacy = '';
            $sView = $this->request()->get('view', 'all');
            $default = false;
            switch ($sView) {
                case 'my':
                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' v.user_id = ' . (int)Phpfox::getUserId();
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'friend':
                    db()->join(Phpfox::getT('friend'), 'friends', ' friends.user_id = v.user_id AND friends.friend_user_id = ' . (int)Phpfox::getUserId());
                    $groupByFriend .= ' v.event_id ';
                    db()->group($groupByFriend);

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
                    db()->join(Phpfox::getT('fevent_invite'), 'fei', ' fei.event_id = v.event_id AND fei.invited_user_id = ' . (int)Phpfox::getUserId());

                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' fei.rsvp_id = 1 ';
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'may-attend':
                    db()->join(Phpfox::getT('fevent_invite'), 'fei', ' fei.event_id = v.event_id AND fei.invited_user_id = ' . (int)Phpfox::getUserId());

                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' fei.rsvp_id = 2 ';
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'not-attending':
                    db()->join(Phpfox::getT('fevent_invite'), 'fei', ' fei.event_id = v.event_id AND fei.invited_user_id = ' . (int)Phpfox::getUserId());

                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' fei.rsvp_id = 3 ';
                    $andWherePrivacy .= ' ) ';
                    break;
                case 'invites':
                    db()->join(Phpfox::getT('fevent_invite'), 'fei', ' fei.event_id = v.event_id AND fei.invited_user_id = ' . (int)Phpfox::getUserId());

                    $andWherePrivacy .= ' AND ( ';
                    $andWherePrivacy .= ' fei.rsvp_id = 0 ';
                    $andWherePrivacy .= ' ) ';
                    break;
                default:
                    $default = true;
                    $andWherePrivacy = ' AND ( v.privacy IN (%PRIVACY%) ) ';

                    if(Phpfox::isUser()) {
                        db()->select('v.event_id')
                            ->from($this->_sTable, 'v')
                            ->where('v.user_id = ' . Phpfox::getUserId() . ' AND v.view_id = 0 AND ( (v.item_id = 0) ' . $wherePages . ') ' . str_replace('%PRIVACY%', '1,2', $andWherePrivacy) . $cond)
                            ->union();
                        db()->select('v.event_id')
                            ->from($this->_sTable, 'v')
                            ->join(Phpfox::getT('friend'), 'f', 'f.is_page = 0 AND f.user_id = v.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                            ->where('v.view_id = 0 AND ( (v.item_id = 0) ' . $wherePages . ') ' . str_replace('%PRIVACY%', '1,2', $andWherePrivacy) . $cond)
                            ->union();
                    }

                    db()->select('v.event_id')
                        ->from($this->_sTable, 'v')
                        ->where('v.view_id = 0 AND ( (v.item_id = 0) ' . $wherePages . ') ' . str_replace('%PRIVACY%', '0', $andWherePrivacy) . $cond)
                        ->union()
                        ->unionFrom('sub_v');

                    break;
            }

            if($default) {
                $aRows = db()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . 'v.*, ft.description_parsed, ei.rsvp_id, ei.user_id AS inviter_id, ei.invited_user_id AS invitee_id, ' . Phpfox::getUserField())
                    ->join(Phpfox::getT('fevent'), 'v', 'v.event_id = sub_v.event_id')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                    ->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = v.event_id')
                    ->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = v.event_id AND ei.invited_user_id = ' . Phpfox::getUserId())
                    ->order($sOrder)
                    ->limit($iLimit)
                    ->execute('getSlaveRows');
            }
            else {
                $aRows = db()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . 'v.*, ft.description_parsed, ei.rsvp_id, ei.user_id AS inviter_id, ei.invited_user_id AS invitee_id, ' . Phpfox::getUserField())
                    ->from(Phpfox::getT('fevent'), 'v')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                    ->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = v.event_id')
                    ->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = v.event_id AND ei.invited_user_id = ' . Phpfox::getUserId())
                    ->where('v.view_id = 0 AND ( (v.item_id = 0) ' . $wherePages . ') ' . $andWherePrivacy . $cond)
                    ->order($sOrder)
                    ->limit($iLimit)
                    ->execute('getSlaveRows');
            }

            if (!$bNoCount) {
                $iTotal = db()->getField('SELECT FOUND_ROWS()');
            }
        }

        foreach ($aRows as $key => $row) {
            $aRows[$key]['is_invited'] = !empty($aRows[$key]['invitee_id']) && !empty($aRows[$key]['inviter_id']) ? ($aRows[$key]['inviter_id'] != $aRows[$key]['invitee_id'] ? true : ($aRows[$key]['user_id'] == $aRows[$key]['invitee_id'] ? true : false)) : false;
        }

        return array($iTotal, $aRows);
    }

    public function getOnHomepageByType($sType = 'upcoming', $iLimit = 4, $bIsPage = false, $bNoCount = false, $pageId = -1)
    {
        switch ($sType) {
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
        $aRows = [];
        $eventTable = Phpfox::getT('fevent');
        if ($bIsPage) {
            if (!$bNoCount) {
                if(Phpfox::isUser()) {
                    db()->select('v.event_id')
                        ->from($eventTable, 'v')
                        ->where('v.user_id = ' . Phpfox::getUserId() . ' AND v.view_id = 0 AND v.privacy IN(1,2) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $whereType)->union();
                    db()->select('v.event_id')->from($eventTable, 'v')->join(Phpfox::getT('friend'), 'f', 'f.is_page = 0 AND f.user_id = v.user_id AND f.friend_user_id = ' . Phpfox::getUserId())->where('v.view_id = 0 AND v.privacy IN(1,2) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $whereType)->union();
                }
                db()->select('v.event_id')->from($eventTable, 'v')->where('v.view_id = 0 AND v.privacy IN(0) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $whereType)->union()->unionFrom('sub_v');
                $iTotal = db()->select('COUNT(sub_v.event_id)')->execute('getSlaveField');
            }
            if ($bNoCount || (!$bNoCount && (int)$iTotal > 0)) {
                if(Phpfox::isUser()) {
                    db()->select('v.event_id')
                        ->from($eventTable, 'v')
                        ->where('v.user_id = ' . Phpfox::getUserId() . ' AND v.view_id = 0 AND v.privacy IN(1,2) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $whereType)->union();
                    db()->select('v.event_id')->from($eventTable, 'v')->join(Phpfox::getT('friend'), 'f', 'f.is_page = 0 AND f.user_id = v.user_id AND f.friend_user_id = ' . Phpfox::getUserId())->where('v.view_id = 0 AND v.privacy IN(1,2) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $whereType)->union();
                }
                db()->select('v.event_id')->from($eventTable, 'v')->where('v.view_id = 0 AND v.privacy IN(0) AND v.module_id = \'pages\' AND v.item_id = ' . (int)$pageId . $whereType)->union()->unionFrom('sub_v');

                $aRows = db()->select('v.*, pag.page_id, pag.title as page_title, vt.description_parsed,' . Phpfox::getUserField())->join(Phpfox::getT('fevent'), 'v.event_id = sub_v.event_id')->join(Phpfox::getT('pages'), 'pag', 'pag.page_id = v.item_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = v.event_id AND ei.invited_user_id = ' . Phpfox::getUserId())->leftJoin(Phpfox::getT('fevent_text'), 'vt', 'vt.event_id = v.event_id')->where('1=1')->order($sOrder)->limit($iLimit)->execute('getSlaveRows');
            }
        } else {
            //  check permission view event in page which joined/liked
            $wherePages = $this->getConditionsForSettingPageGroup('v');

            //  build sql
            $andWherePrivacy = ' AND ( v.privacy IN(%PRIVACY%) ) ';

            if (!$bNoCount) {
                if(Phpfox::isUser()) {
                    db()->select('v.event_id')->from($eventTable, 'v')->where('v.user_id = ' . Phpfox::getUserId() . ' AND v.view_id = 0 ' . $wherePages . ' ' . str_replace('%PRIVACY%', '1,2', $andWherePrivacy) . $whereType)->union();
                    db()->select('v.event_id')->from($eventTable, 'v')->join(Phpfox::getT('friend'), 'f', 'f.is_page = 0 AND f.user_id = v.user_id AND f.friend_user_id = ' . Phpfox::getUserId())->where('v.view_id = 0 ' . $wherePages . ' ' . str_replace('%PRIVACY%', '1,2', $andWherePrivacy) . $whereType)->union();
                }
                db()->select('v.event_id')->from($eventTable, 'v')->where('v.view_id = 0 ' . $wherePages . ' ' . str_replace('%PRIVACY%', '0', $andWherePrivacy) . $whereType)->union()->unionFrom('sub_v');
                $iTotal = db()->select('COUNT(sub_v.event_id)')->execute('getSlaveField');
            }

            if ($bNoCount || (!$bNoCount && (int)$iTotal > 0)) {
                if(Phpfox::isUser()) {
                    db()->select('v.event_id')->from($eventTable, 'v')->where('v.user_id = ' . Phpfox::getUserId() . ' AND v.view_id = 0 ' . $wherePages . ' ' . str_replace('%PRIVACY%', '1,2', $andWherePrivacy) . $whereType)->union();
                    db()->select('v.event_id')->from($eventTable, 'v')->join(Phpfox::getT('friend'), 'f', 'f.is_page = 0 AND f.user_id = v.user_id AND f.friend_user_id = ' . Phpfox::getUserId())->where('v.view_id = 0 ' . $wherePages . ' ' . str_replace('%PRIVACY%', '1,2', $andWherePrivacy) . $whereType)->union();
                }

                db()->select('v.event_id')->from($eventTable, 'v')->where('v.view_id = 0 ' . $wherePages . ' ' . str_replace('%PRIVACY%', '0', $andWherePrivacy) . $whereType)->union()->unionFrom('sub_v');

                $aRows = db()->select('v.*, pag.page_id, pag.title as page_title, vt.description_parsed, fi.rsvp_id, fi.user_id AS inviter_id, fi.invited_user_id AS invitee_id, ' . Phpfox::getUserField())->join(Phpfox::getT('fevent'), 'v', 'v.event_id = sub_v.event_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')->leftJoin(Phpfox::getT('pages'), 'pag', 'pag.page_id = v.item_id')->leftJoin(Phpfox::getT('fevent_text'), 'vt', 'vt.event_id = v.event_id')->leftJoin(Phpfox::getT('fevent_invite'), 'fi', 'fi.event_id = v.event_id AND fi.invited_user_id = ' . Phpfox::getUserId())->order($sOrder)->limit($iLimit)->execute('getSlaveRows');
            }
        }

        /*caculate end date of recurrent event by(after number event)*/
        if (count($aRows)) {
            foreach ($aRows as $keyEvent => $aEvent) {
                if (($aEvent['isrepeat'] == 0) || ($aEvent['isrepeat'] == 1) || ($aEvent['isrepeat'] == 2)) {
                    if ($aEvent['after_number_event'] > 0) {
                        /*get start date of last instance event*/
                        $aLastInstance = $this->getLastInstanceEvent($aEvent['org_event_id']);
                        if (!empty($aLastInstance)) {
                            $aRows[$keyEvent]['timerepeat'] = $aLastInstance['start_time'];
                        }
                    }
                }
                $aRows[$keyEvent]['is_invited'] = !empty($aRows[$keyEvent]['invitee_id']) && !empty($aRows[$keyEvent]['inviter_id']) ? ($aRows[$keyEvent]['inviter_id'] != $aRows[$keyEvent]['invitee_id'] ? true : ($aRows[$keyEvent]['user_id'] == $aRows[$keyEvent]['invitee_id'] ? true : false)) : false;
            }
        }

        return [$iTotal, $aRows];
    }

    public function getMapEventForDeatail($iEventId)
    {
        $sOrder = 'v.start_time ASC';

        $aRows = db()->select('v.*, pag.page_id, pag.title as page_title,vt.description_parsed,' . Phpfox::getUserField())->from(Phpfox::getT('fevent'), 'v')->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')->leftJoin(Phpfox::getT('pages'), 'pag', 'pag.page_id = v.item_id')->leftJoin(Phpfox::getT('fevent_text'), 'vt', 'vt.event_id = v.event_id')->where('v.event_id = ' . $iEventId)->order($sOrder)->execute('getSlaveRows');

        /*caculate end date of recurrent event by(after number event)*/
        if (count($aRows)) {
            foreach ($aRows as $keyEvent => $aEvent) {
                if (($aEvent['isrepeat'] == 0) || ($aEvent['isrepeat'] == 1) || ($aEvent['isrepeat'] == 2)) {
                    if ($aEvent['after_number_event'] > 0) {
                        /*get start date of last instance event*/

                        $aLastInstance = $this->getLastInstanceEvent($aEvent['org_event_id']);
                        if (!empty($aLastInstance)) {
                            $aRows[$keyEvent]['timerepeat'] = $aLastInstance['start_time'];
                        }

                    }
                }
            }
        }

        return $aRows;
    }


    public function getAdminsByEventID($iId)
    {
        $aRows = db()->select('user_id')->from(Phpfox::getT('fevent_admin'))->where('event_id = ' . (int)$iId)->execute('getSlaveRows');

        $aAdmin = [];
        if (!empty($aRows)) {
            foreach ($aRows as $aRow) {
                $aAdmin[] = $aRow['user_id'];
            }
        }

        return $aAdmin;
    }

    public function getEventByID($eventID)
    {
        return db()->select('*')->from(Phpfox::getT('fevent'))->where('event_id = ' . (int)$eventID)->execute('getSlaveRow');
    }

    public function getAllDataEventById($event_id)
    {
        // tables: fevent, fevent_text, fevent_category_data
        return db()->select('e.*, txt.description, txt.description_parsed, GROUP_CONCAT(cdt.category_id) as list_category_id')->from(Phpfox::getT('fevent'), 'e')->leftJoin(Phpfox::getT('fevent_text'), 'txt', 'txt.event_id = e.event_id')->leftJoin(Phpfox::getT('fevent_category_data'), 'cdt', 'cdt.event_id = e.event_id')->where('e.event_id = ' . (int)$event_id)->group('e.event_id')->execute('getSlaveRow');
    }

    public function getLastInstanceEvent($event_id)
    {

        return db()->select('e.*')->from(Phpfox::getT('fevent'), 'e')->where('e.org_event_id = ' . (int)$event_id)->order('e.event_id DESC')->execute('getSlaveRow');
    }

    public function getBrotherEventByEventId($event_id, $org_event_id, $aConds = [])
    {
        $sWhere = ' 1=1 ';
        $sWhere .= ' AND e.event_id != ' . (int)$event_id;
        $sWhere .= ' AND e.org_event_id = ' . (int)$org_event_id;
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        return db()->select('e.event_id')->from(Phpfox::getT('fevent'), 'e')->where($sWhere)->execute('getSlaveRows');
    }

    public function getFollowingEventByEventId($event_id, $org_event_id, $aConds = [])
    {
        $sWhere = ' 1=1 ';
        $sWhere .= ' AND e.org_event_id = ' . (int)$org_event_id;
        $sWhere .= ' AND e.event_id > ' . (int)$event_id;
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        return db()->select('e.event_id')->from(Phpfox::getT('fevent'), 'e')->where($sWhere)->execute('getSlaveRows');
    }

    public function executeCron()
    {
        $aCronLogDefault = db()->select("cronlog.*")->from(Phpfox::getT("fevent_cronlog"), 'cronlog')->where('cronlog.type = \'default\'')->order('cronlog.cronlog_id DESC')->limit(1)->execute("getSlaveRow");

        $oldRunTimestamp = 0;
        $newRunTimestamp = PHPFOX_TIME;
        if (isset($aCronLogDefault['cronlog_id'])) {
            $oldRunTimestamp = (int)$aCronLogDefault['timestamp'];
        }
        db()->insert(Phpfox::getT('fevent_cronlog'), array('type' => 'default', 'timestamp' => (int)$newRunTimestamp,));

        $this->cronSendSubscribeMail($oldRunTimestamp, $newRunTimestamp);
        $this->cronSendNotification();
    }

    public function cronSendNotification()
    {
        $conds = 'has_notification = 1 AND is_notified = 0 AND (notification_time < ' . PHPFOX_TIME . ' AND ' . PHPFOX_TIME . ' < start_time)';
        $notified_events = db()->select('*')->from($this->_sTable)->where($conds)->executeRows();
        foreach ($notified_events as $event) {
            $invites = db()->select('invite_id, invited_user_id, user_id')->from(Phpfox::getT('fevent_invite'))->where('(rsvp_id = 1 OR rsvp_id = 2) AND event_id = ' . (int)$event['event_id'])->execute('getSlaveRows');
            $link = Phpfox::getLib('url')->permalink('fevent', $event['event_id'], $event['title']);
            foreach ($invites as $invite) {
                Phpfox::getService('notification.process')->add('fevent_notify', $event['event_id'], $invite['user_id'], $event['user_id']);
                Phpfox::getLib('mail')
                    ->to($invite['invited_user_id'])
                    ->subject(['fevent_reminder_email_subject', ['title' => $event['title'], 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $event['start_time'])]])
                    ->message(['fevent_reminder_email_message', [
                            'title' => $event['title'],
                            'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $event['start_time']),
                            'link' => '<a href="' . $link . '" target="_blank">' . $link . '</a>'
                        ]
                    ])
                    ->send();
            }
            db()->update(':fevent', array('is_notified' => 1), 'event_id = ' . $event['event_id']);
        }
    }

    public function cronSendSubscribeMail($oldRunTimestamp, $newRunTimestamp)
    {
        $iTimeSetting = Phpfox::getParam('fevent.subscribe_within_day') * 24 * 3600;

        $iTimeCronJob = $newRunTimestamp - $oldRunTimestamp;

        //if $iTimeSetting

        $aSubscribeEmail = db()->select('fse.*')->from(Phpfox::getT("fevent_subscribe_email"), 'fse')->execute('getSlaveRows');

        $aEvents = db()->select('fevent.*,fc.name as category_name,fc.category_id')->from(Phpfox::getT("fevent_category_data"), 'fcd')->join(Phpfox::getT('fevent'), 'fevent', 'fevent.event_id = fcd.event_id ')->join(Phpfox::getT('user'), 'u', 'u.user_id = fevent.user_id')->join(Phpfox::getT('fevent_category'), 'fc', 'fc.category_id = fcd.category_id')->where('fevent.view_id = 0')->group('fevent.event_id')->execute('getSlaveRows');

        foreach ($aSubscribeEmail as $keyaSubscribeEmail => $valueaSubscribeEmail) {
            $data = (array)json_decode($valueaSubscribeEmail['data']);
            $aConds = [];

            $aFilterEvent = [];

            if (count($aEvents)) {
                foreach ($aEvents as $keyEvent => $aEvent) {
                    if ($data['categories'] == 'null' || (in_array($aEvent['category_id'], explode(",", $data['categories'])))) {
                        if (!empty($data['location_lat']) && !empty($data['location_lng']) && !empty($data['radius']) && floatval($data['location_lat']) > 0 && floatval($data['location_lng']) > 0 && floatval($data['radius']) > 0) {
                            $rangevaluefrom = Phpfox::getLib('parse.input')->prepare($data['radius']);
                            preg_match("/[0-9]*/", $rangevaluefrom, $kq);

                            $rangevaluefrom = floatval($rangevaluefrom);
                            $glat = floatval($data['location_lat']);
                            $glong = floatval($data['location_lng']);

                            if (!$this->distance($aEvent['lat'], $aEvent['lng'], $glat, $glong, $rangevaluefrom)) {
                                continue;
                            }
                        }
                        if ($oldRunTimestamp == 0) {

                            //cd4
                            if ($aEvent['time_stamp'] < $newRunTimestamp && $aEvent['start_time'] >= $newRunTimestamp && $aEvent['start_time'] < ($newRunTimestamp + $iTimeSetting)) {
                                $aFilterEvent[] = $aEvent;
                            }
                        } else {
                            if ($iTimeCronJob < $iTimeSetting) {
                                if (($aEvent['time_stamp'] >= $oldRunTimestamp && $aEvent['start_time'] >= $oldRunTimestamp && $aEvent['start_time'] < ($oldRunTimestamp + $iTimeSetting)) || ($aEvent['start_time'] >= ($oldRunTimestamp + $iTimeSetting - $iTimeCronJob) && $aEvent['start_time'] < ($oldRunTimestamp + $iTimeSetting) && $aEvent['time_stamp'] < $oldRunTimestamp)) {
                                    $aFilterEvent[] = $aEvent;
                                }
                            } else {
                                if ($aEvent['start_time'] >= $oldRunTimestamp) {
                                    $aFilterEvent[] = $aEvent;
                                }
                            }
                        }
                    }
                }
            }

            // TODO: replace this format with core format
            $formatTime = 'F j, Y g:i a';
            if (count($aFilterEvent) > 0) {
                $subject = _p('event_you_may_interested_in_site_name', array('site_name' => Phpfox::getParam('core.site_title'),));
                $email = $valueaSubscribeEmail['email'];
                $data_event = '';
                foreach ($aFilterEvent as $keyaEvent => $aEvent) {
                    $name = Phpfox::getLib('phpfox.parse.output')->clean($aEvent['title']);

                    $sTextCategories = \Core\Lib::phrase()->isPhrase($aEvent['category_name']) ? _p($aEvent['category_name']) : $aEvent['category_name'];
                    $sTextCategories = trim($sTextCategories);

                    $location = $aEvent['location'];
                    $location = trim($location);

                    $sLink = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);

                    $aUser = Phpfox::getService('user')->getUser($aEvent['user_id']);
                    $aOwnerName = "<a href='" . Phpfox::getLib('url')->makeUrl('', $aUser['user_name']) . "'>" . $aUser['full_name'] . "</a>";

                    $data = '';
                    $data .= '- <a href="' . $sLink . '">' . $name . '</a><br />';
                    $data .= _p('category') . ': ' . $sTextCategories . '<br />';
                    $data .= _p('location') . ': ' . $location . '<br />';
                    $data .= _p('start_time') . ': ' . Phpfox::getService('fevent.helper')->displayTimeByFormat($formatTime, $aEvent['start_time']) . '<br />';
                    $data .= _p('end_time') . ': ' . Phpfox::getService('fevent.helper')->displayTimeByFormat($formatTime, $aEvent['end_time']) . '<br />';
                    $data .= _p('owner') . ': ' . $aOwnerName . '<br /><br />';

                    $data_event .= $data;
                }


                $subject = _p('email_newsletter_events_will_be_started_within_within_day_days', array('within_day' => Phpfox::getParam('fevent.subscribe_within_day'),));

                $unsubscribeLink = \Phpfox_Url::instance()->makeUrl('fevent.unsubscribe', ['code' => $valueaSubscribeEmail['code']]);

                $message = _p('dear_sir_or_madam_here_are_businesses_you_may_interested_in_site_name_data_event_to_unsubscribe_follow_the_link_below_regards_site_name', array('within_day' => Phpfox::getParam('fevent.subscribe_within_day'), 'site_name' => Phpfox::getParam('core.site_title'), 'data_event' => $data_event, 'unsubscribe_link' => '<a href="' . $unsubscribeLink . '">' . $unsubscribeLink . '</a>'));

                Phpfox::getLib('mail')->to($email)->subject($subject)->message($message)->send();

            }
        }
    }

    function distance($lat1, $lon1, $lat2, $lon2, $radius)
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        if ($radius > $miles) {
            return true;
        } else {
            return false;
        }

    }

    public function getManageEvent($aConds = [], $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {
        $sWhere = '';
        $sWhere .= '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        $iCount = db()->select("COUNT(dbus.event_id)")->from(Phpfox::getT("fevent"), 'dbus')->join(Phpfox::getT("user"), 'u', 'dbus.user_id =  u.user_id')->leftjoin(Phpfox::getT('fevent_category_data'), 'dcd', 'dcd.event_id = dbus.event_id')->leftjoin(Phpfox::getT('fevent_category'), 'dc', 'dc.category_id = dcd.category_id AND dc.parent_id = 0')->where($sWhere)->execute("getSlaveField");
        $aList = [];
        $aCategoryData = db()->select("dcd.*,dc.name")->from(Phpfox::getT('fevent_category_data'), 'dcd')->join(Phpfox::getT('fevent_category'), 'dc', 'dc.category_id = dcd.category_id AND dc.parent_id = 0')->execute("getSlaveRows");
        if ($iCount) {
            $aList = db()->select("dbus.*," . Phpfox::getUserField())->from(Phpfox::getT("fevent"), 'dbus')->join(Phpfox::getT("user"), 'u', 'dbus.user_id =  u.user_id')->leftjoin(Phpfox::getT('fevent_category_data'), 'dcd', 'dcd.event_id = dbus.event_id')->leftjoin(Phpfox::getT('fevent_category'), 'dc', 'dc.category_id = dcd.category_id AND dc.parent_id = 0')->where($sWhere)->order('dbus.event_id DESC')->limit($iPage, $iLimit, $iCount)->execute("getSlaveRows");
            foreach ($aList as &$value) {
                foreach ($aCategoryData as $aCate) if ($aCate['event_id'] == $value['event_id']) {
                    $value['category_title'] = \Core\Lib::phrase()->isPhrase($aCate['name']) ? _p($aCate['name']) : $aCate['name'];
                    break;
                }
                if (!isset($value['category_title'])) $value['category_title'] = _p('No Category');
                if (Phpfox::isAdmin() == 1) {
                    $value['can_sponsor_event'] = 1;
                }
            }
            Phpfox::getService('fevent.browse')->processRows($aList);
        }
        //die(d($aList));
        return array($iCount, $aList);
    }

    /**
     * @param bool $bSquare
     * @return mixed|null|string
     */
    public function getDefaultPhoto($bSquare = true)
    {
        $sDefaultEventPhoto = flavor()->active->default_photo('fevent_default_photo', true);
        if (!$sDefaultEventPhoto) {
            $sDefaultEventPhoto = setting('core.path_actual') . 'PF.Site/Apps/p-advevent/assets/image/' . ($bSquare ? 'default_home.png' : 'default_slide.png');
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
            return ' AND (' . $sPrefix . '.module_id IN (\'' . implode('\',\'', $aModules) . '\') OR ' . $sPrefix . '.module_id IN (\'fevent\',\'event\'))';
        } else {
            return ' AND ' . $sPrefix . '.module_id IN (\'fevent\',\'event\')';
        }
    }

    /**
     * @param int $iId
     *
     * @return int
     */
    public function countImages($iId)
    {
        return db()->select('COUNT(*)')->from(Phpfox::getT('fevent_image'))->where('event_id = ' . (int)$iId)->order('ordering ASC')->execute('getSlaveField');
    }

    public function getUploadParams($aParams)
    {
        if (isset($aParams['id'])) {
            $iTotalImage = $this->countImages($aParams['id']);
            $iRemainImage = Phpfox::getUserParam('fevent.max_upload_image_event') - $iTotalImage;
        } else {
            $iRemainImage = Phpfox::getUserParam('fevent.max_upload_image_event');
        }
        $iMaxFileSize = Phpfox::getUserParam('fevent.max_upload_size_event');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = ['addedfile' => 'ynfeAddPage.dropzoneOnAddedFile', 'sending' => 'ynfeAddPage.dropzoneOnSending', 'success' => 'ynfeAddPage.dropzoneOnSuccess', 'queuecomplete' => 'ynfeAddPage.dropzoneQueueComplete',];
        return ['max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize), 'upload_url' => Phpfox::getLib('url')->makeUrl('fevent.frame-upload'), 'component_only' => true, 'max_file' => $iRemainImage, 'js_events' => $aEvents, 'upload_now' => "true", 'submit_button' => '#js_fevent_done_upload', 'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'), 'upload_dir' => Phpfox::getParam('event.dir_image'), 'upload_path' => Phpfox::getParam('event.url_image'), 'update_space' => true, 'type_list' => ['jpg', 'jpeg', 'gif', 'png'], 'style' => '', 'extra_description' => [_p('maximum_photos_you_can_upload_is_number', ['number' => $iRemainImage])], 'thumbnail_sizes' => Phpfox::getParam('fevent.thumbnail_sizes'), 'no_square' => true];
    }

    public function getUploadDefaultParams($aParams = null)
    {
        $iMaxFileSize = Phpfox::getUserParam('fevent.max_upload_size_event');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        return ['max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize), 'type_list' => ['jpg', 'jpeg', 'gif', 'png'], 'upload_dir' => Phpfox::getParam('event.dir_image'), 'upload_path' => Phpfox::getParam('event.url_image'), 'thumbnail_sizes' => Phpfox::getParam('fevent.thumbnail_sizes'), 'label' => _p('featured_photo') . ' <span class="p-text-danger">*</span></label>', 'type_description' => _p('this_is_a_default_photo_which_you_will_see_in_listing_and_detail_page_you_can_upload_more_photos_in_the_next_step') . '<br>' . _p('fevent.allowed_file_type_jpg_gif_or_png') . ' ' . _p('the_file_size_limit_is_file_size_kb_strong', ['file_size' => $iMaxFileSize * 1048576 / 1024]) . ' ' . _p('recommended_dimension_for_the_best_view_x_px'), 'max_size_description' => ''];
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

        $iCount = db()->select("COUNT( DISTINCT( m.event_id) )")->from($this->_sTable, 'm')->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = m.event_id')->leftJoin(Phpfox::getT('fevent_category_data'), 'fcd', 'fcd.event_id = m.event_id')->leftJoin(Phpfox::getT('fevent_category'), 'fc', 'fc.is_active = 1 AND fc.category_id = fcd.category_id')->where($sConditions)->execute("getSlaveField");

        $aEvents = [];
        if ($iCount) {
            $aEvents = db()->select('m.event_id,m.*, ft.description_parsed AS description')->from($this->_sTable, 'm')->join(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = m.event_id')->leftJoin(Phpfox::getT('fevent_category_data'), 'fcd', 'fcd.event_id = m.event_id')->leftJoin(Phpfox::getT('fevent_category'), 'fc', 'fc.is_active = 1 AND fc.category_id = fcd.category_id')->where($sConditions)->order($sOrder)->group('m.event_id')->limit($iPage, $iLimit, $iCount)->execute('getSlaveRows');
        }


        return $aEvents;
    }

    public function getRsvp($iEvent){
        $iRsvp = db()->select('rsvp_id')
            ->from(Phpfox::getT('fevent_invite'))
            ->where('event_id = ' . (int)$iEvent . ' AND invited_user_id = ' . (int)Phpfox::getUserId())
            ->execute('getField');
        return $iRsvp;
    }
}