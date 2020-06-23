<?php
namespace Apps\YNC_WebPush\Service;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Url;

class Yncwebpush extends Phpfox_Service
{
    static $_aSettingMap = [
        'friend_accepted' => 'friend.new_friend_accepted',
        'comment_user_approve' => 'comment.approve_new_comment',
        'forum_subscribed_post' => 'forum.subscribe_new_post'
    ];
    private $_aConditions = array();
    private $_sSort = 'u.joined DESC';
    private $_iPage = 0;
    private $_iLimit = 9;
    private $_bIsOnline = false;
    private $_bExtend = false;
    private $_aCallback = false;
    /**
     * boolean show featured or non featured | null: show all
     * @var mixed
     */
    private $_mFeatured = null;
    private $_aCustom = false;
    private $_bIsGender = false;
    private $_sIp = null;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('user');
    }

    public function conditions($aConditions)
    {
        $this->_aConditions = $aConditions;

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

        if ($this->_sSort == 'u.last_login ASC') {
            $this->_aConditions[] = 'AND u.last_login > 0';
        }

        return $this;
    }

    public function page($iPage)
    {
        $this->_iPage = $iPage;
        return $this;
    }

    public function limit($iLimit)
    {
        $this->_iLimit = $iLimit;
        return $this;
    }

    public function online($bIsOnline)
    {
        $this->_bIsOnline = $bIsOnline;

        return $this;
    }

    public function custom($mCustom)
    {
        $this->_aCustom = $mCustom;
        return $this;
    }

    public function gender($bGender)
    {
        $this->_bIsGender = $bGender;

        return $this;
    }

    public function ip($sIp)
    {
        $this->_sIp = '%' . str_replace('&#42;', '', $sIp) . '%';
    }

    public function extend($bExtend)
    {
        $this->_bExtend = $bExtend;

        return $this;
    }

    public function featured($bFeatured)
    {
        $this->_mFeatured = $bFeatured;

        return $this;
    }

    public function getWaitingTimeBeforeShowBanner($iUserId = null)
    {
        if ($iUserId === null) {
            $iUserId = Phpfox::getUserId();
        }
        $sCookie = Phpfox::getCookie('ync_web_push_' . $iUserId);
        $iWaitingTime = 10;
        if (!empty($sCookie)) {
            $iDelayTime = $this->getPeriodToAppearBannerTime();
            $aCookie = json_decode($sCookie, true);
            if (isset($aCookie['last_skip']) && $iDelayTime !== false) {
                if ((PHPFOX_TIME - $aCookie['last_skip']) < $iDelayTime) {
                    $iWaitingTime += ($iDelayTime - (PHPFOX_TIME - $aCookie['last_skip']));
                }
            }
        }
        return $iWaitingTime;
    }

    public function getPeriodToAppearBannerTime()
    {
        $sValue = setting('yncwebpush_time_period_to_appear_banner', '10m');

        $aValue = explode(' ', $sValue);
        if (empty($aValue)) {
            return false;
        }
        $iTime = 0;
        $sMustNext = '';
        foreach ($aValue as $sVal) {
            if (preg_match_all('/[^h|m|s|\d]/', $sVal, $aMatch)) {
                $iTime = false;
                break;
            }
            if ($sMustNext == '' || $sMustNext == 'h') {
                $iNotMatch = preg_match('/^h/', $sVal, $aMatch);
                $iMatch = preg_match_all('/h/', $sVal, $aMatch);
                if ($iMatch > 1 || $iNotMatch) {
                    $iTime = false;
                    break;
                } elseif ($iMatch == 1) {
                    $iTime += (int)str_replace('h', '', $sVal) * 3600;
                    $sMustNext = 'm';
                    continue;
                }
                $sMustNext = 'm';
            }

            if ($sMustNext == 'm' || $sMustNext == '') {
                if ($sMustNext == 'm' && preg_match_all('/h/', $sVal, $aMatch)) {
                    $iTime = false;
                    break;
                }
                $iNotMatch = preg_match('/^m/', $sVal, $aMatch);
                $iMatch = preg_match_all('/m/', $sVal, $aMatch);
                if ($iMatch > 1 || $iNotMatch) {
                    $iTime = false;
                    break;
                } elseif ($iMatch == 1) {
                    $iTime += (int)str_replace('m', '', $sVal) * 60;
                    $sMustNext = 's';
                    continue;
                }
                $sMustNext = 's';
            }

            if ($sMustNext == 's' || $sMustNext == '') {
                if ($sMustNext == 's' && preg_match_all('/m/', $sVal, $aMatch)) {
                    $iTime = false;
                    break;
                }
                $iNotMatch = preg_match('/^s/', $sVal, $aMatch);
                $iMatch = preg_match_all('/s/', $sVal, $aMatch);
                if ($iMatch > 1 || $iNotMatch) {
                    $iTime = false;
                    break;
                } elseif ($iMatch == 1) {
                    $iTime += (int)str_replace('s', '', $sVal);
                    $sMustNext = 'end';
                    continue;
                }
                $sMustNext = 'end';
            }
        }
        return $iTime;
    }

    public function isDisablePushNotification($sType, $iUserId, $sSettingName = null)
    {
        $sCheckVar = '';
        if (!empty($sSettingName)) {
            $sCheckVar = $sSettingName;
        }
        if (isset(self::$_aSettingMap[$sType])) {
            $sCheckVar = self::$_aSettingMap[$sType];
        } else {
            if (strpos($sType, '_like') !== false) {
                $sCheckVar = 'like.new_like';
            } elseif (strpos($sType, 'comment_') !== false) {
                $sCheckVar = 'comment.add_new_comment';
            }
        }
        if ($sCheckVar) {
            return Phpfox::getService('yncwebpush.setting')->checkPushNotificationSetting($iUserId, $sCheckVar);
        }
        return false;
    }

    public function get()
    {
        $aReturnUsers = array();
        $sUnSubs = Phpfox::getService('yncwebpush.setting')->getAllUnSubscribersIds();
        if (!empty($sUnSubs)) {
            $this->_aConditions[] = 'AND u.user_id NOT IN (' . $sUnSubs . ')';
        }
        if (($sPlugin = Phpfox_Plugin::get('yncwebpush.service_yncwebpush_get__start_no_return'))) {
            eval($sPlugin);
        }

        // If there are custom fields to look for we need to know how many users satisfy this criteria
        $iCount = $this->getCustom(true);

        if ($iCount !== false && $iCount < 1) {
            $bNoMatches = true;
        } else {
            $aUsers = $this->getCustom(false);

            if ($aUsers !== false) {
                foreach ($this->_aConditions as $iKey => $sCondition) {
                    if (preg_match('/u.user_id IN (\([0-9]+\))/', $sCondition, $aMatch) > 0) {
                        $this->_aConditions[$iKey] = str_replace($aMatch[1], '(' . implode(',', $aUsers) . ')',
                            $sCondition);
                    }
                }
            }

        }

        if (!isset($bNoMatches)) {
            if ($this->_bIsOnline === true) {
                $iActiveSession = PHPFOX_TIME - (Phpfox::getParam('log.active_session') * 60);
                $this->database()->select('COUNT(DISTINCT u.user_id)')->join((Phpfox::getParam('core.store_only_users_in_session') ? Phpfox::getT('session') : Phpfox::getT('log_session')),
                    'ls', 'ls.user_id = u.user_id AND ls.last_activity > ' . $iActiveSession);
            } else {
                if ($this->_sIp !== null) {
                    $this->database()->select('COUNT(DISTINCT u.user_id)');
                } else {
                    $this->database()->select('COUNT(*)');
                }
            }

            if ($iCount > 0) {
                $this->database()->leftJoin(Phpfox::getT('user_custom'), 'ucv', 'ucv.user_id = u.user_id');
            }

            // one page to display all, one page to display only featured.
            if ($this->_mFeatured === true) {
                // The purpose of this if is to filter users out, but instead of Joining we can get those users from the cache
                if (Phpfox::getParam('user.cache_featured_users')) {
                    $sCacheId = $this->cache()->set('featured_users');
                    if (($aCache = $this->cache()->get($sCacheId)) && is_array($aCache)) {
                        $aFeatured = array();
                        foreach ($aCache as $aCachedUser) {
                            $aFeatured[] = $aCachedUser['user_id'];
                        }
                        if (!empty($aFeatured)) {
                            $this->_aConditions[] = 'AND u.user_id IN (' . implode(',', $aFeatured) . ')';
                        }
                    }
                } else {
                    $this->database()->join(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');
                }
            }

            if ($this->_aCallback !== false && isset($this->_aCallback['query'])) {
                Phpfox::callback($this->_aCallback['module'] . '.getBrowseQueryCnt', $this->_aCallback);
            }

            if ($this->_sIp !== null) {
                $this->database()->join(Phpfox::getT('user_ip'), 'uip',
                    'uip.user_id = u.user_id AND uip.ip_address LIKE \'' . $this->database()->escape($this->_sIp) . '\'');
            }

            if ($sPlugin = Phpfox_Plugin::get('yncwebpush.service_yncwebpush_get_1')) {
                eval($sPlugin);
            }

            $iCnt = $this->database()->from($this->_sTable, 'u')
                ->join(Phpfox::getT('user_field'), 'ufield', 'ufield.user_id = u.user_id')
                ->forceIndex('status_id')
                ->where($this->_aConditions)
                ->execute('getSlaveField');
        } else {
            $iCnt = 0;
        }

        if ($iCnt > 0) {
            if ($iCount > 0) {
                $this->database()->leftJoin(Phpfox::getT('user_custom'), 'ucv', 'ucv.user_id = u.user_id');
            }

            if ($this->_bIsOnline === true) {
                $this->database()->join((Phpfox::getParam('core.store_only_users_in_session') ? Phpfox::getT('session') : Phpfox::getT('log_session')),
                    'ls', 'ls.user_id = u.user_id AND ls.last_activity > ' . $iActiveSession)->group('u.user_id');
            }

            if ($this->_aCallback !== false && isset($this->_aCallback['query'])) {
                Phpfox::callback($this->_aCallback['module'] . '.getBrowseQuery', $this->_aCallback);
            }

            $this->database()->select('ug.title AS user_group_title, ')->leftJoin(Phpfox::getT('user_group'), 'ug',
                'ug.user_group_id = u.user_group_id');

            // display the Unfeature/Feature option when landing on the Search page.
            // using bIsOnline as its not defined in the admincp but it is on the user browse page
            if ($this->_mFeatured !== true || (Phpfox::getUserParam('user.can_feature') && $this->_bIsOnline)) {
                $this->database()
                    ->select('uf.user_id as is_featured, uf.ordering as featured_order, ')
                    ->leftJoin(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');
            }

            // display the Unfeature/Feature option when landing on the Search page.
            if ($this->_mFeatured === true && !$this->_bIsOnline) {
                $this->database()
                    ->select('uf.user_id as is_featured, uf.ordering as featured_order, ')
                    ->join(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');
            }

            if ($this->_sIp !== null) {
                $this->database()->join(Phpfox::getT('user_ip'), 'uip',
                    'uip.user_id = u.user_id AND uip.ip_address LIKE \'' . $this->database()->escape($this->_sIp) . '\'');
            }

            $aReturnUsers = $this->database()->select('u.status_id as unverified, yus.user_id as no_subscriber, ' . ($this->_bExtend ? 'u.*, ufield.*' : Phpfox::getUserField()))
                ->from($this->_sTable, 'u')
                ->join(Phpfox::getT('user_field'), 'ufield', 'ufield.user_id = u.user_id')
                ->leftJoin(':yncwebpush_user_setting', 'yus', 'yus.user_id = u.user_id')
                ->where($this->_aConditions)
                ->group('u.user_id', true)
                ->order($this->_sSort)
                ->limit($this->_iPage, $this->_iLimit, $iCnt)
                ->execute('getSlaveRows');


            foreach ($aReturnUsers as $iKey => $aUser) {
                $aReturnUsers[$iKey]['profile_url'] = Phpfox_Url::instance()->makeUrl('profile', [$aUser['user_name']]);
                $aReturnUsers[$iKey]['browsers'] = Phpfox::getService('yncwebpush.token')->countUserBrowsers($aUser['user_id']);
            }
            if ($this->_bExtend) {
                foreach ($aReturnUsers as $iKey => $aUser) {
                    if (empty($aUser['dob_setting'])) {
                        switch (Phpfox::getParam('user.default_privacy_brithdate')) {
                            case 'month_day':
                                $aReturnUsers[$iKey]['dob_setting'] = '1';
                                break;
                            case 'show_age':
                                $aReturnUsers[$iKey]['dob_setting'] = '2';
                                break;
                            case 'hide':
                                $aReturnUsers[$iKey]['dob_setting'] = '3';
                                break;
                        }
                    }

                    $aBirthDay = Phpfox::getService('user')->getAgeArray($aUser['birthday']);

                    $aReturnUsers[$iKey]['month'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']);
                    $aReturnUsers[$iKey]['day'] = $aBirthDay['day'];
                    $aReturnUsers[$iKey]['year'] = $aBirthDay['year'];
                    if (isset($aUser['last_ip_address'])) {
                        $aReturnUsers[$iKey]['last_ip_address_search'] = str_replace('.', '-',
                            $aUser['last_ip_address']);
                    }
                }
            }
        }

        return array($iCnt, $aReturnUsers);
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
        if ($bIsCount) {
            $aUsers = $this->getCustom(false, false, false);
            return count($aUsers);
        }
        $sSelect = 'u.user_id';
        if ($bIsCount == true) {
            $sSelect = ('count(u.user_id)');
        }

        if (is_array($this->_aCustom) && !empty($this->_aCustom)) {
            $sCondition = ' AND (';
            // When searching for more than one custom field searchFields will
            // return more than one join instruction
            $aCustomSearch = Phpfox::getService('custom')->searchFields($this->_aCustom);

            $iCustomCnt = 0;

            $iJoinsCount = 0;
            $aUserIds = array();
            if (count($aCustomSearch) > 0) {
                $this->database()->select($sSelect . ($bIsCount ? ' as total' : ''))->from(Phpfox::getT('user'), 'u');
            }

            $aAvoidDups = array();

            foreach ($aCustomSearch as $iKey => $aSearch) {
                if (isset($aAvoidDups[$aSearch['on'] . $aSearch['where']])) {
                    unset($aCustomSearch[$iKey]);
                    continue;
                }

                $aAvoidDups[$aSearch['on'] . $aSearch['where']] = $iKey;
            }

            foreach ($aCustomSearch as $iKey => $aSearchParam) {
                $iCustomCnt++;
                if ($iCount !== false && is_numeric($iCount) && $iCount > 0) {
                    $this->database()->order($this->_sSort)
                        ->limit($this->_iPage, $this->_iLimit, $iCount);
                }
                if (is_array($aSearchParam)) {
                    // The following replacements make sure that the joins are unique by using unique aliases
                    $sOldAlias = $aSearchParam['alias'];

                    $aSearchParam['alias'] = $sNewAlias = $aSearchParam['alias'] . $iCustomCnt;

                    $sNewOn = $aSearchParam['on'] = $aCustomSearch[$iKey]['on'] = str_replace($sOldAlias . '.',
                        $sNewAlias . '.', $aSearchParam['on']);

                    $aCustomSearch[$iKey]['where'] = str_replace(array('mvc.', $sOldAlias . '.'), $sNewAlias . '.',
                        $aSearchParam['where']);

                    $sNewWhere = $aCustomSearch[$iKey]['where'];

                    $sOn = '' . $sNewOn . ' AND ' . $sNewWhere;

                    $this->database()->join($aSearchParam['table'], $sNewAlias, $sOn);
                    $iJoinsCount++;

                } // end of is_array aSearchParam
                else {
                    $this->database()->join(Phpfox::getT('user_custom'), 'ucv', $aSearchParam);
                    $iJoinsCount++;
                    $sCondition .= ' ' . $aSearchParam . ' AND ';
                }

                if ($iJoinsCount > 2 && !$bIsCount) {
                    $aUsers = $this->database()->execute('getSlaveRows');

                    if (empty($aUsers) || (isset($aUsers[0]['total']) && $aUsers[0]['total'] <= 1)) {
                        $aUserIds[0] = 0;
                    } else {
                        foreach ($aUsers as $aUser) {
                            $aUserIds[$aUser['user_id']] = $aUser['user_id'];
                        }
                    }

                    $this->database()->select($sSelect)->from(Phpfox::getT('user'),
                        'u')->where('u.user_id IN (' . implode(',', $aUserIds) . ')');
                    $iJoinsCount = 0;
                }
            } // foreach
            if ($bIsCount == true) {
                $aCount = $this->database()->execute('getSlaveRows');
                $aCount = array_pop($aCount);

                return (count($aCustomSearch) ? $aCount['total'] : $aCount[$sSelect]);
            }
            if ($iJoinsCount > 0) {
                $aUsers = $this->database()->execute('getSlaveRows');

                foreach ($aUsers as $aUser) {
                    $aUserIds[$aUser['user_id']] = $aUser['user_id'];
                }
            }
            if (count($aUserIds)) {
                $sCondition = 'AND (u.user_id IN (' . implode(',', $aUserIds) . ')';
            } else {
                if (($iJoinsCount > 0) && (empty($aUsers))) {
                    $sCondition = 'AND (1=2';
                }
            }
            $this->database()->clean();

            if ($sCondition != ' AND (' && $bAddCondition) {
                $this->_aConditions[] = rtrim($sCondition, ' AND ') . ')';
            }

        }
        if ($bAddCondition != true && isset($aUsers)) {
            return $aUsers;
        }
        return false;
    }


}