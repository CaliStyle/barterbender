<?php

/**
 * [PHPFOX_HEADER]
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		PhuongNV
 * @package  		yn_member
 */

namespace Apps\YNC_Member\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Locale;
use Phpfox_Search;
use Phpfox_Plugin;

class Member extends Phpfox_Service
{

    public function getFriendCount($iUserId)
    {
        $iCnt = $this->database()
            ->select('total_friend')
            ->from(Phpfox::getT('user_field'))
            ->where('user_id = ' . $iUserId)
            ->executeField();

        return $iCnt;
    }

    public function processUser(&$aRow = null, $aFields = [])
    {
        if (empty($aRow) || empty($aRow['user_id'])) {
            return;
        }

        // all available field this service can process
        // set an array of field to process or leave blank to get all fields
        $aAvailFields = [
            'CoverPhoto',
            'Places',
            'MutualFriends',
            'FriendStatus',
            'OnlineStatus',
            'Pages',
            'Groups',
            'FeatureStatus',
            'AboutMe',
            'FollowingStatus',
            'Review'
        ];

        foreach ($aAvailFields as $field) {
            if (empty($aFields) || in_array($field, $aFields)) {
                $this->{'process'.$field}($aRow);
            }
        }
    }

    public function processCoverPhoto(&$aRow)
    {
        $cover_photo = null;
        if (Phpfox::isModule('photo')) {
            $aCoverPhotoRow = $this->database()->select('*')
                ->from(Phpfox::getT('user_field'))
                ->where("user_id = '" . $aRow['user_id'] . "'")
                ->execute('getSlaveRow');

            if (!empty($aCoverPhotoRow['cover_photo']))
            {
                $aCoverPhoto = Phpfox::getService('photo')->getCoverPhoto($aCoverPhotoRow['cover_photo']);
                if ($aCoverPhoto && !empty($aCoverPhoto['destination'])) {
                    $cover_photo = $aCoverPhoto;
                }
            }
        }
        $aRow['cover_photo'] = $cover_photo;
    }

    public function processPlaces(&$aRow)
    {
        $aRow['places'] = [
            'living_name' => '',
            'living_place' => [],
            'work_name' => '',
            'work_place' => [],
            'study_name' => '',
            'study_place' => [],
        ];

        list($aStudyPlaces, $aWorkPlaces, $aLivingPlaces, $aLivedPlaces) = Phpfox::getService('ynmember.place.browse')->getPlacesOfUser($aRow['user_id']);
        if (Phpfox::isModule('jobposting')) {
            $aCompany = $this->database()->select('*')
                ->from(Phpfox::getT('jobposting_company'), 'jc')
                ->join(Phpfox::getT('user_field'), 'uf', 'jc.company_id = uf.company_id')
                ->where('uf.user_id =  ' . (int)$aRow['user_id'])
                ->limit(1)
                ->executeRow();
        }
        if (Phpfox::isModule('directory')) {
            $aBusiness = $this->database()->select('*')
                ->from(Phpfox::getT('directory_business'), 'db')
                ->join(Phpfox::getT('directory_business_memberrole'), 'dmu', 'db.business_id = dmu.business_id')
                ->join(Phpfox::getT('directory_business_userroledata'), 'dm', 'dmu.role_id = dm.role_id')
                ->where('dm.user_id =  ' . (int)$aRow['user_id'])
                ->limit(1)
                ->executeRow();
        }

        if (!empty($aStudyPlaces)) {
            $aRow['places']['study_name'] = $aStudyPlaces[0]['location_title'];
            $aRow['places']['study_place'] = $aStudyPlaces[0];
        }

        if (!empty($aWorkPlaces)) {
            $aRow['places']['work_name'] = $aWorkPlaces[0]['location_title'];
            $aRow['places']['work_place'] = $aWorkPlaces[0];
        }
        else if (!empty($aCompany)) {
            $aRow['places']['work_name'] = $aCompany['name'];
            $aRow['places']['work_place'] = $aCompany;
        }
        else if (!empty($aBusiness)) {
            $aRow['places']['work_name'] = $aBusiness['name'];
            $aRow['places']['work_place'] = $aBusiness;
        }

        if (!empty($aLivingPlaces)) {
            $aRow['places']['living_name'] = $aLivingPlaces[0]['location_title'];
            $aRow['places']['living_place'] = $aLivingPlaces[0];
        }
        else if (!empty($aLivedPlaces)) {
            $aRow['places']['living_name'] = $aLivedPlaces[0]['location_title'];
            $aRow['places']['living_place'] = $aLivedPlaces[0];
        }
    }

    public function processMutualFriends(&$aRow)
    {
        $aRow['mutual_friends'] = [];
        $aRow['total_mutual_friends'] = 0;

        if (!Phpfox::isModule('friend') || Phpfox::getUserId() == $aRow['user_id']) {
            return;
        }
        list($total, $aMutualFriends) = Phpfox::getService('friend')->getMutualFriends($aRow['user_id']);
        $aRow['mutual_friends'] = $aMutualFriends;
        $aRow['total_mutual_friends'] = $total;
    }

    public function processFriendStatus(&$aRow)
    {
        if (!Phpfox::isModule('friend')) {
            return;
        }
        $aRow['is_friend'] = Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aRow['user_id']) ? true : false;
        $aRow['is_friend_of_friend'] = Phpfox::getService('friend')->isFriendOfFriend($aRow['user_id']) ? true : false;
        $aRow['is_friend_request'] = false;
        if (!$aRow['is_friend']) {
            $iRequestId = Phpfox::getService('friend.request')->isRequested(Phpfox::getUserId(), $aRow['user_id'], true);
            $aRow['is_friend_request'] = $iRequestId ? 2 : false;
            if (!$aRow['is_friend_request']) {
                $iRequestId = Phpfox::getService('friend.request')->isRequested($aRow['user_id'], Phpfox::getUserId(), true);
                $aRow['is_friend_request'] = $iRequestId ? 3 : false;
            }
            $aRow['is_friend_request_id'] = $iRequestId;
        }
    }

    public function processOnlineStatus(&$aRow)
    {
        $iActiveSession = PHPFOX_TIME - (Phpfox::getParam('log.active_session') * 60);
        $sSessionTable = $this->getSessionTable();

        $aOnlineRow = $this->database()->select('*')
            ->from($sSessionTable)
            ->where($sSessionTable . '.user_id = ' . $aRow['user_id'] . ' AND last_activity > ' . $iActiveSession . ' AND im_hide = 0')
            ->executeRow();

        return $aRow['is_online'] = $aOnlineRow ? 1 : 0;
    }

    public function processGroups(&$aRow)
    {
        $aRow['total_groups'] = 0;
        $aRow['groups'] = [];
        if (Phpfox::isModule('groups')) {
            $sExtraConds = (Phpfox::getUserParam('core.can_view_private_items') || $aRow['user_id'] == Phpfox::getUserId()) ? "" : " AND (p.reg_method <> 2)";

            list($iTotal, $aPages) = \Core\Lib::appsGroup()->getForProfile($aRow['user_id'], 10, false, $sExtraConds);
        }
        $aRow['total_groups'] = $iTotal;
        $aRow['groups'] = $aPages;
    }

    public function processPages(&$aRow)
    {
        $aRow['total_pages'] = 0;
        $aRow['pages'] = [];
        if (Phpfox::isModule('pages')) {
            list($iTotal, $aPages) = Phpfox::getService('pages')->getForProfile($aRow['user_id'], 10);
            $aRow['total_pages'] = $iTotal;
            $aRow['pages'] = $aPages;
        }
    }

    public function processFeatureStatus(&$aRow)
    {
        $aFeaturedRow = $this->database()->select('user_id')
            ->from(Phpfox::getT('user_featured'))
            ->where('user_id = "' . $aRow['user_id'] . '"')
            ->execute('getSlaveRow');

        $aRow['is_featured'] = isset($aFeaturedRow['user_id']) ? 1 : 0;
    }

    public function processAboutMe(&$aRow)
    {
        $sAlias = Phpfox::getService('custom')->getAlias();
        $sAboutMe = Phpfox::getService('custom')->getUserCustomValue($aRow['user_id'], $sAlias . 'about_me');
        $aRow['about_me'] = $sAboutMe;
    }

    public function processReview(&$aRow)
    {
        $iMyReviewCount = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('ynmember_review'))
            ->where(['item_id' => $aRow['user_id'], 'user_id' => Phpfox::getUserId()])
            ->execute('getSlaveField');

        if (isset($aRow['rating'])) {
            $iRating = $aRow['rating'];
        } else {
            $iRating = $this->database()->select('IFNULL(SUM(rating)/COUNT(review_id),0) as rating')
                ->from(Phpfox::getT('ynmember_review'))
                ->where(['item_id' => $aRow['user_id']])
                ->execute('getSlaveField');
        }

        if (isset($aRow['total_review'])) {
            $iReviewCount = $aRow['total_review'];
        } else {
            $iReviewCount = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('ynmember_review'))
                ->where(['item_id' => $aRow['user_id']])
                ->execute('getSlaveField');
        }

        $aRow['is_review_written'] = $iMyReviewCount ? true : false;
        $aRow['total_review'] = $iReviewCount;
        $aRow['rating'] = $iRating;
    }

    public function processFollowingStatus(&$aRow)
    {
        $aRow['is_following'] = $this->isFollowingMember(Phpfox::getUserId(), $aRow['user_id']) ? true : false;
    }

    public function processBirthdayWish(&$aRow)
    {
        $aRow['is_sent_birthday_wish'] = false;
        $aRow['birthday_message'] = '';
        $aRow['birthday_today'] = false;

        $iYearStart = strtotime('Jan 1 00:00:00');
        $iYearEnd = strtotime('Dec 31 23:59:00');

        if (Phpfox::isUser()) {
            $aMyBirthdayWish = $this->database()->select('bw.*')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('ynmember_birthday_wish'), 'bw', 'bw.item_id = ' . $aRow['user_id'] . ' AND bw.user_id = ' . Phpfox::getUserId())
                ->where('bw.time_stamp > ' . $iYearStart . ' AND bw.time_stamp < ' . $iYearEnd)
                ->executeRow();

            if (!empty($aMyBirthdayWish['birthday_wish_id'])) {
                $aRow['is_sent_birthday_wish'] = true;
                $aRow['birthday_message'] = $aMyBirthdayWish['message'];
            }
        }

        if (substr($aRow['birthday'], 0, 4) == date('md')) {
            $aRow['birthday_today'] = true;
        }
    }
    public function processBirthdate(&$aRow)
    {
        $aBirthDay = Phpfox::getService('user')->getAgeArray($aRow['birthday']);
        $aRow['birthdate'] = $aBirthDay['day'] . ' ' . Phpfox::getLib('date')->getMonth($aBirthDay['month']);
    }
    public function isFollowingMember($iUserId, $iItemId)
    {
        $iId = $this->database()->select('follow_id')
            ->from(Phpfox::getT('ynmember_follow'))
            ->where("item_id = {$iItemId} AND user_id = {$iUserId}")
            ->execute('getSlaveField');

        return $iId ? true : false;
    }


    public function getSearchFilter($formAction = '')
    {
        // paging
        $aPages = array(6, 12, 24);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt)
        {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }
        if (defined('PHPFOX_IS_ADMIN_SEARCH')){
            $iDisplay = 12;
        } else {
            $iDisplay = 21;
        }

        // sorting
        $aSorts = array(
            'u.joined DESC' => _p('Oldest'),
            'u.joined ASC' => _p('Newest'),
            'u.full_name ASC' => _p('A-Z'),
            'u.full_name DESC' => _p('Z-A'),
            'total_review' => _p('name'),
            'rating' => _p('Highest Rated')
        );

        $sDefaultOrderName = 'u.joined DESC';
        $sDefaultSort = 'ASC';
        if (Phpfox::getParam('user.user_browse_default_result') == 'last_login')
        {
            $sDefaultOrderName = 'u.last_login';
            $sDefaultSort = 'DESC';
        }

        $aUserGroups = array();
        foreach (Phpfox::getService('user.group')->get() as $aUserGroup)
        {
            $aUserGroups[$aUserGroup['user_group_id']] = Phpfox_Locale::instance()->convert($aUserGroup['title']);
        }

        $aGenders = Phpfox::getService('core')->getGenders();
        $aGenders[''] = (count($aGenders) == '2' ? _p('both') : _p('all'));

        if (($sPlugin = Phpfox_Plugin::get('user.component_controller_browse_genders')))
        {
            eval($sPlugin);
        }

        $aAge = array();
        for ($i = Phpfox::getService('user')->age( Phpfox::getService('user')->buildAge(1, 1, Phpfox::getParam('user.date_of_birth_end'))); $i <=  Phpfox::getService('user')->age( Phpfox::getService('user')->buildAge(1, 1, Phpfox::getParam('user.date_of_birth_start'))); $i++)
        {
            $aAge[$i] = $i;
        }

        $aFilters = array(
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => $iDisplay
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => $sDefaultOrderName
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => $sDefaultSort
            ),
            'keyword' => array(
                'type' => 'input:text',
                'size' => 15,
                'class' => 'txt_input'
            ),
            'type' => array(
                'type' => 'select',
                'options' => array(
                    '0' => array(_p('email_name'), 'AND ((u.full_name LIKE \'%[VALUE]%\' OR (u.email LIKE \'%[VALUE]@%\' OR u.email = \'[VALUE]\'))' . (defined('PHPFOX_IS_ADMIN_SEARCH') ? ' OR u.email LIKE \'%[VALUE]\'' : '') .')'),
                    '1' => array(_p('email'), 'AND ((u.email LIKE \'%[VALUE]@%\' OR u.email = \'[VALUE]\'' . (defined('PHPFOX_IS_ADMIN_SEARCH') ? ' OR u.email LIKE \'%[VALUE]%\'' : '') .'))'),
                    '2' => array(_p('name'), 'AND (u.full_name LIKE \'%[VALUE]%\')')
                ),
                'depend' => 'keyword'
            ),
            'group' => array(
                'type' => 'select',
                'options' => $aUserGroups,
                'add_any' => true,
                'search' => 'AND u.user_group_id = \'[VALUE]\''
            ),
            'gender' => array(
                'type' => 'select',
                'options' => $aGenders,
                'default_view' => '',
                'search' => 'AND (u.gender = \'[VALUE]\' OR \'\' = \'[VALUE]\')',
                'suffix' => '<br />'
            ),
            'from' => array(
                'type' => 'select',
                'options' => $aAge,
                'select_value' => _p('from')
            ),
            'to' => array(
                'type' => 'select',
                'options' => $aAge,
                'select_value' => _p('to')
            ),
            'country' => array(
                'type' => 'select',
                'options' =>  Phpfox::getService('core.country')->get(),
                'search' => 'AND u.country_iso = \'[VALUE]\'',
                'add_any' => true,
                // 'style' => 'width:150px;',
                'id' => 'country_iso'
            ),
            'country_child_id' => array(
                'type' => 'select',
                'search' => 'AND ufield.country_child_id = \'[VALUE]\'',
                'clone' => true
            ),
            'location' => array(
                'type' => 'input:text',
//                'search' => 'AND ufield.postal_code = \'[VALUE]\''
            ),
            'location_latitude' => array(
                'type' => 'input:text',
            ),
            'location_longitude' => array(
                'type' => 'input:text',
            ),
            'within' => array(
                'type' => 'input:text',
//                'search' => 'AND distance < \'[VALUE]\''
            ),
            'city' => array(
                'type' => 'input:text',
                'size' => 15,
                'search' => 'AND ufield.city_location LIKE \'%[VALUE]%\''
            ),
            'zip' => array(
                'type' => 'input:text',
                'size' => 10,
                'search' => 'AND ufield.postal_code = \'[VALUE]\''
            ),
            'show' => array(
                'type' => 'select',
                'options' => array(
                    '1' => _p('name_and_photo_only'),
                    '2' => _p('name_photo_and_users_details')
                ),
                'default_view' => (Phpfox::getParam('user.user_browse_display_results_default') == 'name_photo_detail' ? '2' : '1')
            ),
            'ip' => array(
                'type' => 'input:text',
                'size' => 10
            ),
        );

        if (!Phpfox::getUserParam('user.can_search_by_zip'))
        {
            unset ($aFilters['zip']);
        }

        if ($sPlugin = Phpfox_Plugin::get('user.component_controller_browse_filter'))
        {
            eval($sPlugin);
        }

        $aSearchParams = [
            'type' => 'browse',
//            'field' => 'user.user_id',
            'filters' => $aFilters,
            'custom_search' => true,
            'search_tool' => [
                'table_alias' => 'u',
                'when_field' => 'joined',
                'search' => [
                    'action' => $formAction,
                    'default_value' => _p('Search...'),
                    'name' => 'search',
                    'field' => ['u.full_name']
                ],
                'sort' => [
                    'latest' => ['u.joined', _p('Newest')],
                    'oldest' => ['u.joined', _p('Oldest'), 'ASC'],
                    'atoz' => ['u.full_name',_p('A-Z'), 'ASC'],
                    'ztoa' => ['u.full_name', _p('Z-A')],
                    'review' => ['total_review', _p('Most Reviewed')],
                    'rating' => ['rating', _p('Highest Rated')]
                ],
                'show' => [6, 12, 24]
            ]
        ];

        return Phpfox_Search::instance()->set($aSearchParams);
    }

    public function getFollowingMembers($iUserId)
    {
        $aRows = $this->database()
            ->select('*')
            ->from(Phpfox::getT('ynmember_follow'))
            ->where('item_id = ' . $iUserId)
            ->executeRows();

        return $aRows;
    }

    public function canGetNotification($iUserId, $iFromUserId)
    {
        $sPrivacy = 'ynmember.follow';
        static $aPrivacy = array();
        static $aIsFriend = array();
        static $aUserAge = array();

        if (Phpfox::getUserParam('user.can_override_user_privacy'))
        {
            return true;
        }

        if ($iUserId == $iFromUserId)
        {
            return false;
        }

        $iUserAgeLimit = Phpfox::getParam('user.user_profile_private_age');

        if ($iUserAgeLimit > 0)
        {
            if (!isset($aUserAge[$iUserId]))
            {
                $aUserAge[$iUserId] = (int) Phpfox::getService('user')->age($this->database()->select('birthday')->from(Phpfox::getT('user'))->where('user_id = ' . (int) $iUserId)->execute('getSlaveField'));
            }

            if ($aUserAge[$iUserId] < $iUserAgeLimit)
            {
                if (!Phpfox::isUser())
                {
                    return false;
                }

                if (!isset($aIsFriend[$iUserId][$iFromUserId]) && Phpfox::isModule('friend'))
                {
                    $aIsFriend[$iUserId][$iFromUserId] = Phpfox::getService('friend')->isFriend($iUserId, $iFromUserId);
                }

                return $aIsFriend[$iUserId][$iFromUserId];
            }
        }

        $bPass = true;
        if (!isset($aPrivacy[$iUserId]))
        {
            $aSettings = $this->database()->select('user_id, user_privacy, user_value')
                ->from(Phpfox::getT('user_privacy'))
                ->where('user_id = ' . (int) $iUserId)
                ->execute('getSlaveRows');
            foreach ($aSettings as $aSetting) {
                $aPrivacy[$aSetting['user_id']][$aSetting['user_privacy']] = $aSetting['user_value'];
            }
        }

        if (isset($aPrivacy[$iUserId][$sPrivacy]))
        {
            switch ($aPrivacy[$iUserId][$sPrivacy])
            {
                // Network (Logged in users)
                case '1':
                    if (!Phpfox::isUser())
                    {
                        $bPass = false;
                    }
                    break;
                // Friends Only
                case '2':
                    if (!Phpfox::isUser())
                    {
                        $bPass = false;
                    }
                    else
                    {
                        if (!isset($aIsFriend[$iUserId][$iFromUserId]) && Phpfox::isModule('friend'))
                        {
                            $aIsFriend[$iUserId][$iFromUserId] = Phpfox::getService('friend')->isFriend($iUserId, $iFromUserId);
                        }

                        if (isset($aIsFriend[$iUserId]) && !$aIsFriend[$iUserId][$iFromUserId])
                        {
                            $bPass = false;
                        }
                    }
                    break;
                // Preferred List
                case '3':

                    break;
                // No one
                case '4':
                    $bPass = false;
                    break;
            }
        }

        if (Phpfox::getService('user.block')->isBlocked($iUserId, $iFromUserId))
        {
            $bPass = false;
        }

        return $bPass;
    }

    public function getSessionTable()
    {
        if (!empty($this->_sSessionTable)) {
            return $this->_sSessionTable;
        }
        $this->_sSessionTable = Phpfox::getParam('core.store_only_users_in_session') ? Phpfox::getT('session') : Phpfox::getT('log_session');
        return $this->_sSessionTable;
    }
}