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
use Phpfox_Plugin;
use Phpfox_Error;

class Browse extends \Phpfox_Service
{
    private $_aConditions = array();

    private $_sSort = 'u.joined DESC';

    private $_iPage = 0;

    private $_iLimit = 9;

    private $_bExtend = false;

    private $_aCallback = false;

    private $_mFeatured = null;

    private $_aCustom = false;

    private $_bLocation = false;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('user');
    }

    public function query()
    {

    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {

    }

    public function excludeBlockedConds($sUserJoinCond = '1 = 1')
    {
        if (Phpfox::isUser()) {
            $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $sUserJoinCond .= ' AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }
        }
        return $sUserJoinCond;
    }

    public function sample($iLimit, $sOrder = 'user_id ASC', $aWhere =  [])
    {
        $aRows = $this->database()->select('*')
            ->from($this->_sTable)
            ->order($sOrder)
            ->limit($iLimit)
            ->execute('getSlaveRows')
        ;
        return $aRows;
    }

    /*
     * Decorate result
     */
    public function processRows(&$aRows, $aFields = [])
    {
        if (empty($aRows))
            return;

        foreach($aRows as $index => $aRow) {
            Phpfox::getService('ynmember.member')->processUser($aRows[$index], $aFields);
        }
    }

    public function getBirthdayInRange($sStart, $sEnd, $iPage = 1, $iLimit = NULL)
    {
        $iCnt = $this->database()->select('COUNT(u.user_id)')
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')
            ->where('(uf.dob_setting != 2 AND uf.dob_setting != 3) AND uf.birthday_range >= ' . $sStart . ' AND uf.birthday_range <= ' . $sEnd)
            ->executeField();

        $aBirthdays = $this->database()->select('u.*, uf.dob_setting')
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')
            ->where('(uf.dob_setting != 2 AND uf.dob_setting != 3) AND uf.birthday_range >= ' . $sStart . ' AND uf.birthday_range <= ' . $sEnd)
            ->limit($iPage, $iLimit, $iCnt)
            ->order('u.birthday ASC')
            ->executeRows();

        return [$iCnt, $aBirthdays];
    }

    public function getBirthdays($iLimitToday = 0, $iLimitUpcoming = 0, $bGetSent = true)
    {
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

        $sUserJoinCond = '(' . $sBirthdays . ') AND (uf.dob_setting != 2 AND uf.dob_setting != 3)';
        if (Phpfox::isUser()) {
            $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $sUserJoinCond .= ' AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }
        }
        $aBirthdays = $this->database()->select('u.*, uf.dob_setting')
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')
            ->where($sUserJoinCond)
            ->order('uf.birthday_range ASC')
            ->execute('getSlaveRows');

        if (!is_array($aBirthdays))
        {
            $aBirthdays = [];
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
            // Format the birthday according to the profile
            $aBirthDay = Phpfox::getService('user')->getAgeArray($aFriend['birthday']);
            $aBirthdays[$iKey]['birthdate'] = $aBirthDay['day'] . ' ' . Phpfox::getLib('date')->getMonth($aBirthDay['month']);
            Phpfox::getService('ynmember.member')->processBirthdayWish($aBirthdays[$iKey]);
        }

        $iTotalTodayBirthdays = 0;
        $iTotalUpcomingBirthdays = 0;
        $aTodayBirthdays = [];
        $aUpcomingBirthdays = [];
        foreach ($aBirthdays as $aBirthData)
        {
            Phpfox::getService('ynmember.member')->processBirthdayWish($aBirthData);
            if ($aBirthData['days_left'] == 0) {
                $iTotalTodayBirthdays += 1;
                if (!$iLimitToday || count($aTodayBirthdays) < $iLimitToday) {
                    if ($bGetSent || (!$bGetSent && !$aBirthData['is_sent_birthday_wish'])) {
                        $aTodayBirthdays[] = $aBirthData;
                    }
                }
            } else {
                $iTotalUpcomingBirthdays += 1;
                if (!$iLimitUpcoming || count($aUpcomingBirthdays) < $iLimitUpcoming) {
                    $aUpcomingBirthdays[] = $aBirthData;
                }
            }
        }

        return [$iTotalTodayBirthdays, $aTodayBirthdays, $iTotalUpcomingBirthdays, $aUpcomingBirthdays];
    }

    public function getManageMember($aConds = [], $iPage = 0, $iLimit = NULL)
    {
        $sWhere = '1=1';
        $aRows= [];

        if (count($aConds) > 0) {
            $sCond = implode(' ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = $this->database()
            ->select("COUNT(u.user_id)")
            ->from($this->_sTable, 'u')
            ->leftJoin(Phpfox::getT("user_featured"), 'uf', 'u.user_id =  uf.user_id')
            ->where($sWhere)
            ->execute("getSlaveField");
        if($iCount){
            $aRows = $this->database()
                ->select("CASE WHEN uf.user_id IS NULL THEN false ELSE true END AS is_featured, CASE WHEN umod.user_id IS NULL THEN false ELSE true END AS is_mod, u.*")
                ->from($this->_sTable, 'u')
                ->leftJoin(Phpfox::getT("ynmember_mod"), 'umod', 'u.user_id =  umod.user_id')
                ->leftJoin(Phpfox::getT("user_featured"), 'uf', 'u.user_id =  uf.user_id')
                ->where($sWhere)
                ->order('u.user_id ASC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
            foreach ($aRows as $key => $aRow) {
                Phpfox::getService('ynmember.member')->processUser($aRows[$key]);
            }
        }
        return [$iCount, $aRows];
    }

    public function getMostReviewed($iLimit)
    {
        if ($iLimit <= 0) {
            return [];
        }
        $aUsers = $this->database()->select('u.*, ufield.ynmember_total_review as total_review')
            ->from(Phpfox::getT('user'), 'u')
            ->leftJoin(Phpfox::getT('user_field'), 'ufield', 'u.user_id = ufield.user_id')
            ->where($this->excludeBlockedConds() . ' AND ufield.ynmember_rating > 0')
            ->order('total_review DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows')
        ;

        return $aUsers;
    }

    public function getTopRated($iLimit)
    {
        if ($iLimit <= 0) {
            return [];
        }
        $aUsers = $this->database()->select('u.*, ufield.ynmember_rating as rating')
            ->from(Phpfox::getT('user'), 'u')
            ->leftJoin(Phpfox::getT('user_field'), 'ufield', 'u.user_id = ufield.user_id')
            ->where($this->excludeBlockedConds() . ' AND ufield.ynmember_rating > 0')
            ->order('rating DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows')
        ;

        return $aUsers;
    }

    public function getMemberOfDay()
    {
        $aRows = $this->database()->select('u.*')
            ->from($this->_sTable, 'u')
            ->join(Phpfox::getT("ynmember_mod"), 'umod', 'u.user_id = umod.user_id')
            ->where($this->excludeBlockedConds())
            ->limit(1)
            ->execute('getSlaveRows');

        return $aRows;
    }

    public function getSentBirthdayWishes()
    {
        $iYearStart = strtotime('Jan 1 00:00:00');
        $iYearEnd = strtotime('Dec 31 23:59:59');
        $iViewerId = Phpfox::getUserId();
        $aRows = $this->database()
            ->select('*')
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('ynmember_birthday_wish'), 'bw', 'bw.item_id = u.user_id AND bw.user_id = '. $iViewerId)
            ->where('bw.time_stamp > ' . $iYearStart . ' AND bw.time_stamp < ' . $iYearEnd . ' AND bw.birthday_wish_id IS NOT NULL')
            ->group('item_id')
            ->executeRows();
        return $aRows;
    }

    public function conditions($aConditions)
    {
        $this->_aConditions = $aConditions;

        return $this;
    }

    public function location($bLocation)
    {
        $this->_bLocation = $bLocation;

        return $this;
    }

    public function callback($aCallback)
    {
        $this->_aCallback = $aCallback;

        return $this;
    }

    public function sort($sSort)
    {
        $this->_sSort = $sSort;

        if ($this->_sSort == 'u.last_login ASC')
        {
            $this->_aConditions[] = 'AND u.last_login > 0';
        }

        return $this;
    }

    public function page($iPage)
    {
        $this->_iPage = $iPage;
        return $this;
    }

    public function featured($bFeatured)
    {
        $this->_mFeatured = $bFeatured;

        return $this;
    }

    public function friend($bFriend)
    {
        $this->_bFriend = $bFriend;

        return $this;
    }

    public function limit($iLimit)
    {
        $this->_iLimit = $iLimit;
        return $this;
    }

    public function extend($bExtend)
    {
        $this->_bExtend = $bExtend;

        return $this;
    }

    public function custom($mCustom)
    {
        $this->_aCustom = $mCustom;
        return $this;
    }

    /**
     * This function returns user_ids for those that match the search by custom fields
     * if the param $bIsCount is true then it only returns the count and not the user_ids
     * @param bool $bIsCount
     * @param bool $iCount
     * @param bool $bAddCondition
     *
     * @return array|bool|int|string
     */
    public function getCustom($bIsCount = true, $iCount = false, $bAddCondition = true)
    {
        if ($bIsCount)
        {
            $aUsers = $this->getCustom(false, false, false);
            return count($aUsers);
        }
        $sSelect = 'u.user_id';
        if ($bIsCount == true)
        {
            $sSelect = ('count(u.user_id)');
        }

        if (is_array($this->_aCustom) && !empty($this->_aCustom))
        {
            $sCondition = ' AND (';
            // When searching for more than one custom field searchFields will
            // return more than one join instruction
            $aAlias = array();
            $aCustomSearch = Phpfox::getService('custom')->searchFields($this->_aCustom);

            $iCustomCnt = 0;

            $iJoinsCount = 0;
            $aUserIds = array();
            if (count($aCustomSearch) > 0) {
                $this->database()->select($sSelect . ($bIsCount ? ' as total' : ''))->from(Phpfox::getT('user'), 'u');
            }

            $aAvoidDups = array();

            foreach($aCustomSearch as $iKey => $aSearch)
            {
                if (isset($aAvoidDups[$aSearch['on'] . $aSearch['where']]))
                {
                    unset($aCustomSearch[$iKey]);
                    continue;
                }

                $aAvoidDups[$aSearch['on'] . $aSearch['where']] = $iKey;
            }

            foreach ($aCustomSearch as $iKey => $aSearchParam)
            {
                $iCustomCnt++;
                if ($iCount !== false && is_numeric($iCount) && $iCount > 0)
                {
                    $this->database()->order($this->_sSort)
                        ->limit($this->_iPage, $this->_iLimit, $iCount);
                }
                if (is_array($aSearchParam))
                {
                    // The following replacements make sure that the joins are unique by using unique aliases
                    $sOldAlias = $aSearchParam['alias'];

                    $aSearchParam['alias'] = $sNewAlias = $aSearchParam['alias'] . $iCustomCnt;

                    $sNewOn = $aSearchParam['on'] = $aCustomSearch[$iKey]['on'] = str_replace($sOldAlias .'.', $sNewAlias .'.', $aSearchParam['on']);

                    $aCustomSearch[$iKey]['where'] = str_replace(array('mvc.', $sOldAlias .'.'), $sNewAlias .'.', $aSearchParam['where']);

                    $sNewWhere = $aCustomSearch[$iKey]['where'];

                    $sOn = ''.$sNewOn . ' AND ' . $sNewWhere;

                    $this->database()->join($aSearchParam['table'], $sNewAlias, $sOn);
                    $iJoinsCount++;

                } // end of is_array aSearchParam
                else
                {
                    $this->database()->join(Phpfox::getT('user_custom'), 'ucv', $aSearchParam);
                    $iJoinsCount++;
                    $sCondition .= ' '.$aSearchParam . ' AND ';
                }

                if ( $iJoinsCount > 2 && !$bIsCount)
                {
                    $aUsers = $this->database()->execute('getSlaveRows');

                    if (empty($aUsers) || (isset($aUsers[0]['total']) && $aUsers[0]['total'] <= 1))
                    {
                        $aUserIds[0] = 0;
                    }
                    else
                    {
                        foreach ($aUsers as $aUser)
                        {
                            $aUserIds[$aUser['user_id']] = $aUser['user_id'];
                        }
                    }

                    $this->database()->select($sSelect)->from(Phpfox::getT('user'), 'u')->where('u.user_id IN (' . implode(',',$aUserIds) .')');
                    $iJoinsCount = 0;
                }
            } // foreach
            if ($bIsCount == true)
            {
                $aCount = $this->database()->execute('getSlaveRows');
                $aCount = array_pop($aCount);

                return (count($aCustomSearch) ? $aCount['total'] : $aCount[$sSelect]);
            }
            if ($iJoinsCount > 0)
            {
                $aUsers = $this->database()->execute('getSlaveRows');

                foreach ($aUsers as $aUser)
                {
                    $aUserIds[$aUser['user_id']] = $aUser['user_id'];
                }
            }
            if (count($aUserIds))
            {
                $sCondition = 'AND (u.user_id IN (' . implode(',', $aUserIds) .')';
            }
            else if (($iJoinsCount > 0) && (empty($aUsers)))
            {
                $sCondition = 'AND (1=2';
            }
            $this->database()->clean();

            if ($sCondition != ' AND (' && $bAddCondition)
            {
                $this->_aConditions[] = rtrim($sCondition, ' AND ') . ')';
            }

        }
        if ($bAddCondition != true && isset($aUsers))
        {
            return $aUsers;
        }
        return false;
    }

    public function get()
    {
        $aReturnUsers = array();
        $aUsers = array();
        if ($sPlugin = Phpfox_Plugin::get('user.service_browse_get__start')){return eval($sPlugin);}
        if (!defined('PHPFOX_IS_ADMIN_SEARCH'))
        {
            // user groups that should be hidden
            $aHiddenFromBrowse = Phpfox::getService('user.group.setting')->getUserGroupsBySetting(');user.hide_from_browse');
        }

        if (($sPlugin = Phpfox_Plugin::get('user.service_browse_get__start_no_return')))
        {
            eval($sPlugin);
        }

        // If there are custom fields to look for we need to know how many users satisfy this criteria
        $iCount = $this->getCustom(true);

        if ($iCount !== false && $iCount < 1)
        {
            $bNoMatches = true;
        }
        else
        {
            $aUsers = $this->getCustom(false);

            if ($aUsers !== false)
            {
                foreach ($this->_aConditions as $iKey => $sCondition)
                {
                    if (preg_match('/u.user_id IN (\([0-9]+\))/', $sCondition, $aMatch) > 0)
                    {
                        $this->_aConditions[$iKey] = str_replace($aMatch[1], '(' . implode(',', $aUsers) . ')', $sCondition);
                    }
                }
            }
        }

        if (!isset($bNoMatches))
        {

            $this->database()->select('COUNT(*)');

            /*
             *
             */
            if ($this->_bFriend === true) {
                if (!defined('PHPFOX_IS_ADMIN_SEARCH') && Phpfox::isUser() && Phpfox::isModule('friend')) {
                    $this->database()->leftJoin(Phpfox::getT('friend'), 'friend', 'friend.user_id = u.user_id AND friend.friend_user_id = ' . Phpfox::getUserId());
                }
            }
            /*
             *
             */

            if ($iCount > 0)
            {
                $this->database()->leftJoin(Phpfox::getT('user_custom'), 'ucv', 'ucv.user_id = u.user_id');
            }

            // one page to display all, one page to display only featured.
            if ($this->_mFeatured === true)
            {
                // The purpose of this if is to filter users out, but instead of Joining we can get those users from the cache
                if (Phpfox::getParam('user.cache_featured_users'))
                {
                    $sCacheId = $this->cache()->set('featured_users');
                    if ( ($aCache = $this->cache()->get($sCacheId)) && is_array($aCache))
                    {
                        $aFeatured = array();
                        foreach ($aCache as $aCachedUser)
                        {
                            $aFeatured[] = $aCachedUser['user_id'];
                        }
                        if (!empty($aFeatured))
                        {
                            $this->_aConditions[] = 'AND u.user_id IN (' . implode(',', $aFeatured) . ')';
                        }
                    }
                }
                else
                {
                    $this->database()->join(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');
                }
            }

            if ($this->_aCallback !== false && isset($this->_aCallback['query']))
            {
                Phpfox::callback($this->_aCallback['module'] . '.getBrowseQueryCnt', $this->_aCallback);
            }

            if ($this->_bLocation === true)
            {
                $this->database()->leftJoin(Phpfox::getT('ynmember_place'), 'place', 'place.user_id = u.user_id');
            }

            if (!defined('PHPFOX_IS_ADMIN_SEARCH') && isset($aHiddenFromBrowse) && is_array($aHiddenFromBrowse) && !empty($aHiddenFromBrowse))
            {
                // skip users in these user groups that are invisible
                foreach ($aHiddenFromBrowse as $iGroupId => $aGroup)
                {
                    $this->_aConditions[] = 'AND (u.user_group_id != ' . $iGroupId . ' OR u.is_invisible != 1)';
                }
            }

            if ($sPlugin = Phpfox_Plugin::get('user.service_browse_get_1')){eval($sPlugin);}

            $iCnt = $this->database()->from($this->_sTable, 'u')
                ->join(Phpfox::getT('user_field'), 'ufield', 'ufield.user_id = u.user_id')
                ->forceIndex('status_id')
                ->where($this->_aConditions)
                ->execute('getSlaveField');
        }
        else
        {
            $iCnt = 0;
        }

        if ($iCnt > 0)
        {
            if ($sPlugin = Phpfox_Plugin::get('user.service_browse_get__cnt')){eval($sPlugin);}
            $aAlias = array();
            $iCustomCnt = 0;

            if ($iCount > 0)
            {
                $this->database()->leftJoin(Phpfox::getT('user_custom'), 'ucv', 'ucv.user_id = u.user_id');
            }

            if ($this->_aCallback !== false && isset($this->_aCallback['query']))
            {
                Phpfox::callback($this->_aCallback['module'] . '.getBrowseQuery', $this->_aCallback);
            }

            if (defined('PHPFOX_IS_ADMIN_SEARCH'))
            {
                $this->database()->select('ug.title AS user_group_title, ')->leftJoin(Phpfox::getT('user_group'), 'ug', 'ug.user_group_id = u.user_group_id');
            }

            if (!defined('PHPFOX_IS_ADMIN_SEARCH') && Phpfox::isModule('friend'))
            {
                $this->database()->select('ub.block_id as user_is_blocked, ')
                    ->leftJoin(Phpfox::getT('user_blocked'), 'ub', 'ub.user_id = u.user_id AND block_user_id = ' . Phpfox::getUserId());
            }

            if (!defined('PHPFOX_IS_ADMIN_SEARCH'))
            {
                $this->database()->select('ufield.ynmember_rating AS rating, ufield.ynmember_total_review AS total_review,');
            }

            if ($this->_mFeatured === true)
            {
                $this->database()
                    ->select('uf.user_id as is_featured, uf.ordering as featured_order, ')
                    ->join(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');
            }

            if ($this->_bLocation === true)
            {
                $this->database()->leftJoin(Phpfox::getT('ynmember_place'), 'place', 'place.user_id = u.user_id');
            }

            if (!defined('PHPFOX_IS_ADMIN_SEARCH') && Phpfox::isUser() && Phpfox::isModule('friend'))
            {
                $this->database()->select('friend.friend_id AS is_friend, ')
                    ->leftJoin(Phpfox::getT('friend'), 'friend', 'friend.user_id = u.user_id AND friend.friend_user_id = ' . Phpfox::getUserId());

                $this->database()->select('frequest.request_id AS is_friend_request, ')
                    ->leftJoin(Phpfox::getT('friend_request'), 'frequest', 'frequest.user_id = u.user_id AND frequest.friend_user_id = ' . Phpfox::getUserId());
            }

            $aReturnUsers = $this->database()->select('u.*, ufield.*')
                ->from($this->_sTable, 'u')
                ->join(Phpfox::getT('user_field'), 'ufield', 'ufield.user_id = u.user_id')
                ->where($this->_aConditions)
                ->group('u.user_id', true)
                ->order($this->_sSort)
                ->limit($this->_iPage, $this->_iLimit, $iCnt)
                ->execute('getSlaveRows');


            if (Phpfox::isModule('friend'))
            {
                foreach ($aReturnUsers as $iKey => $aUser)
                {
                    $aReturnUsers[$iKey]['mutual_friends'] = (Phpfox::getUserId() == $aUser['user_id'] ? 0 : $this->database()->select('COUNT(*)')
                        ->from(Phpfox::getT('friend'), 'f')
                        ->innerJoin('(SELECT friend_user_id FROM ' . Phpfox::getT('friend') . ' WHERE is_page = 0 AND user_id = ' . $aUser['user_id'] . ')', 'sf', 'sf.friend_user_id = f.friend_user_id')
                        ->where('f.user_id = ' . Phpfox::getUserId())
                        ->execute('getSlaveField'));
                }
            }

            if ($this->_bExtend)
            {
                foreach ($aReturnUsers as $iKey => $aUser)
                {
                    if (empty($aUser['dob_setting']))
                    {
                        switch (Phpfox::getParam('user.default_privacy_brithdate'))
                        {
                            case 'month_day':
                                $aReturnUsers[$iKey]['dob_setting'] =  '1';
                                break;
                            case 'show_age':
                                $aReturnUsers[$iKey]['dob_setting'] =  '2';
                                break;
                            case 'hide':
                                $aReturnUsers[$iKey]['dob_setting'] =  '3';
                                break;
                        }
                    }

                    $aBirthDay = Phpfox::getService('user')->getAgeArray($aUser['birthday']);

                    $aReturnUsers[$iKey]['month'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']);
                    $aReturnUsers[$iKey]['day'] = $aBirthDay['day'];
                    $aReturnUsers[$iKey]['year'] = $aBirthDay['year'];
                    if (isset($aUser['last_ip_address']))
                    {
                        $aReturnUsers[$iKey]['last_ip_address_search'] = str_replace('.', '-', $aUser['last_ip_address']);
                    }
                }
            }
        }

        if ($sPlugin = Phpfox_Plugin::get('user.service_browse_get__end')){eval($sPlugin);}

        return array($iCnt, $aReturnUsers);
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
        if ($sPlugin = Phpfox_Plugin::get('user.service_browse__call'))
        {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}