<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

$sPathPPFunctions = PHPFOX_DIR . 'module' . PHPFOX_DS . 'profilepopup' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'extras' . PHPFOX_DS . 'functions.php';

if (file_exists($sPathPPFunctions)) {
    require_once($sPathPPFunctions);
}

/**
 *
 *
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
class ProfilePopup_Service_ProfilePopup extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = '';

        (($sPlugin = Phpfox_Plugin::get('profilepopup.service_profilepopup___construct')) ? eval($sPlugin) : false);
    }

    public function __call($sMethod, $aArguments)
    {
        if ($sPlugin = Phpfox_Plugin::get('profilepopup.service_profilepopup__call')) {
            return eval($sPlugin);
        }

        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * Get custom field records in profile popup item
     *
     * @param int $iIsActive active status
     * @return array list of profile popup item with custom field type
     */
    public function getItemsIsCustomField($iIsActive = null, $sItemType = 'user')
    {
        $sWhere = '';
        if ($iIsActive !== null) {
            $sWhere .= 'AND ppi.is_active = ' . (int)$iIsActive;
        }
        if (isset($sItemType) === false || $sItemType === null) {
            $sItemType = 'user';
        }
        return $this->database()->select("ppi.*")
            ->from(Phpfox::getT('profilepopup_item'), 'ppi')
            ->where('ppi.is_custom_field = 1 ' . $sWhere . ' AND ppi.item_type = \'' . $this->database()->escape($sItemType) . '\'')
            ->execute('getSlaveRows');
    }

    /**
     * Get all custom field in system
     *
     * @return array list of custom field in system
     */
    public function getAllCustomFieldInSystem()
    {
        if (Phpfox::isModule('custom')) {
            return $this->database()->select("ctg.group_id, ctg.is_active as 'ctg_is_active', ctf.field_id, ctf.field_name, ctf.phrase_var_name, ctf.is_active as 'ctf_is_active'")
                ->from(Phpfox::getT('custom_group'), 'ctg')
                ->join(Phpfox::getT('custom_field'), 'ctf', 'ctf.group_id = ctg.group_id')
                ->order('ctf.field_id ASC')
                ->execute('getSlaveRows');
        } else {
            return array();
        }
    }

    /**
     * Get max order in profile popup item
     *
     * @return int max number of odering
     */
    public function getMaxOrdering($sItemType = 'user')
    {
        if (isset($sItemType) === false || $sItemType === null) {
            $sItemType = 'user';
        }
        return $this->database()->select("MAX(ppi.ordering) as 'max_ordering' ")
            ->from(Phpfox::getT('profilepopup_item'), 'ppi')
            ->where(' 1=1 ' . ' AND ppi.item_type = \'' . $this->database()->escape($sItemType) . '\'')
            ->execute('getSlaveField');
    }

    /**
     * Get all item in profile popup item with specific active status
     *
     * @param int $iIsActive active status
     * @return array list of items
     */
    public function getAllItems($iIsActive = null, $sItemType = 'user')
    {
        $sWhere = ' 1=1 ';
        if ($iIsActive !== null) {
            $sWhere .= ' AND ppi.is_active = ' . (int)$iIsActive;
        }
        if (isset($sItemType) === false || $sItemType === null) {
            $sItemType = 'user';
        }

        return $this->database()->select("ppi.*")
            ->from(Phpfox::getT('profilepopup_item'), 'ppi')
            ->where($sWhere . ' AND ppi.item_type = \'' . $this->database()->escape($sItemType) . '\'')
            ->order('ppi.name = \'cover_photo\' DESC, ordering ASC')
            ->execute('getSlaveRows');
    }

    /**
     * Get latest status of user
     *
     * @param int $iUserID user ID
     * @return array status record
     */
    public function getLatestStatusByUserID($iUserID)
    {
        //	get 1 user_status feed

        if (Phpfox::isUser() && $iUserID != Phpfox::getUserId() && Phpfox::isModule('privacy') && Phpfox::getUserParam('privacy.can_view_all_items')) {
            $iLastActiveTimeStamp = ((int)Phpfox::getParam('feed.feed_limit_days') <= 0 ? 0 : (PHPFOX_TIME - (86400 * Phpfox::getParam('feed.feed_limit_days'))));
            $aRows = $this->database()->select('feed.*, f.friend_id AS is_friend, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('feed'), 'feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->leftJoin(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                ->order('feed.time_stamp DESC')
                ->group('feed.feed_id')
                ->limit(0, 1)
                ->where('feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.type_id = \'user_status\' AND feed.feed_reference = 0')
                ->execute('getSlaveRows');
        } else {
            $aCond = array();
            Phpfox::getService('user')->get($iUserID);
            if ($iUserID == Phpfox::getUserId()) {
                $aCond[] = 'AND feed.privacy IN(0,1,2,3,4)';
            } else {
                if (Phpfox::getService('user')->getUserObject($iUserID)->is_friend) {
                    $aCond[] = 'AND feed.privacy IN(0,1,2)';
                } else if (Phpfox::getService('user')->getUserObject($iUserID)->is_friend_of_friend) {
                    $aCond[] = 'AND feed.privacy IN(0,2)';
                } else {
                    $aCond[] = 'AND feed.privacy IN(0)';
                }
            }
            $this->database()->select('feed.*')
                ->from(Phpfox::getT('feed'), 'feed')
                ->where(array_merge($aCond, array(' AND feed.type_id = \'user_status\'  AND feed.user_id = ' . (int)$iUserID . '')))
                ->union();

            $this->database()->select('feed.*')
                ->from(Phpfox::getT('feed'), 'feed')
                ->where(array_merge($aCond, array('AND feed.user_id = ' . (int)$iUserID . '  AND feed.type_id = \'user_status\' AND feed.feed_reference = 0 AND feed.parent_user_id = 0')))
                ->union();

            if (Phpfox::isUser()) {
                if (Phpfox::isModule('privacy')) {
                    $this->database()->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
                        ->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '');
                }
                $this->database()->select('feed.*')
                    ->from(Phpfox::getT('feed'), 'feed')
                    ->where('feed.privacy IN(4) AND feed.user_id = ' . (int)$iUserID . '  AND feed.type_id = \'user_status\' AND feed.feed_reference = 0')
                    ->union();
            }

            $this->database()->select('feed.*')
                ->from(Phpfox::getT('feed'), 'feed')
                ->where(array_merge($aCond, array(' AND feed.type_id = \'user_status\' AND feed.parent_user_id = ' . (int)$iUserID)))
                ->union();

            $aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField())
                ->unionFrom('feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->order('feed.time_stamp DESC')
                ->group('feed.feed_id')
                ->limit(0, 1)
                ->execute('getSlaveRows');

        }


        $latestStatus = $this->database()->getSlaveRow("
				SELECT 
                                * 
                              FROM
                                " . Phpfox::getT('user_status') . " urs 
                                INNER JOIN 
                                  (SELECT 
                                    user_id,
                                    MAX(`time_stamp`) AS 'max_time_stamp' 
                                  FROM
                                    " . Phpfox::getT('user_status') . " 
                                  WHERE 1 = 1 
                                    AND `user_id` = " . $this->database()->escape((int)$iUserID) . "
                                  GROUP BY `user_id`) AS exc 
                                  ON urs.`user_id` = exc.user_id 
                                  AND urs.`time_stamp` = exc.max_time_stamp 
			");

        if (isset($aRows[0]) && isset($aRows[0]['feed_id']) && isset($latestStatus) && isset($latestStatus['status_id'])) {
            if ($latestStatus['status_id'] == $aRows[0]['item_id']
                && $latestStatus['time_stamp'] == $aRows[0]['time_stamp']
                && $latestStatus['user_id'] == $aRows[0]['user_id']
            ) {
                return $latestStatus;
            } else {
                return null;
            }
        } else {
            return null;
        }

    }

    /**
     * Get relationship status of user
     *
     * @param int $iUserID user ID
     * @return array information record
     */
    public function getRelationshipStatusByUserID($iUserID)
    {
        if (Phpfox::isModule('custom')) {
            return $this->database()->select("crd.relation_data_id, crd.user_id, ctr.relation_id, ctr.phrase_var_name, crd.with_user_id")
                ->from(Phpfox::getT('custom_relation_data'), 'crd')
                ->join(Phpfox::getT('custom_relation'), 'ctr', 'ctr.relation_id = crd.relation_id')
                ->where('crd.user_id = ' . $this->database()->escape((int)$iUserID))
                ->order('relation_data_id DESC')
                ->execute('getSlaveRow');
        }

        return null;
    }

    /**
     * Get custom field data by user
     *
     * @param int $iUserID user ID
     * @return array information record
     */
    public function getDataUserCustomFieldByUserID($iUserID)
    {
        if (Phpfox::isModule('custom') && Phpfox::isModule('user')) {
            return $this->database()->select("usc.*")
                ->from(Phpfox::getT('user_custom'), 'usc')
                ->where('usc.user_id = ' . $this->database()->escape((int)$iUserID))
                ->execute('getSlaveRow');
        }

        return null;
    }

    /**
     * Get custom field data with mutiple value by user
     *
     * @param int $iUserID user ID
     * @return array information record
     */
    public function getDataUserCutomFieldMutipleValueByUserID($iUserID)
    {
        if (Phpfox::isModule('custom') && Phpfox::isModule('user')) {
            return $this->database()->select("ctf.`field_id`, ctf.`group_id`, ctf.`field_name`, cto.`phrase_var_name`, ucmv.`user_id` ")
                ->from(Phpfox::getT('user_custom_multiple_value'), 'ucmv')
                ->join(Phpfox::getT('custom_option'), 'cto', '(cto.`field_id` = ucmv.`field_id` AND cto.`option_id` = ucmv.`option_id`)')
                ->join(Phpfox::getT('custom_field'), 'ctf', '(ctf.`field_id` = ucmv.`field_id`)')
                ->where('ucmv.`user_id` = ' . $this->database()->escape((int)$iUserID))
                ->execute('getSlaveRows');
        }

        return null;
    }

    /**
     * Get information of user by username
     *
     * @param string $sUser username
     * @return array information record
     */
    public function getByUserName($sUser)
    {
        $iActiveSession = PHPFOX_TIME - (Phpfox::getParam('log.active_session') * 60);
        $aRow = $this->database()->select('u.*, user_field.*, ls.user_id AS is_online')
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('user_field'), 'user_field', 'user_field.user_id = u.user_id')
            ->leftJoin(Phpfox::getT('log_session'), 'ls', 'ls.user_id = u.user_id AND ls.im_hide = 0 AND ls.last_activity > ' . (int)$iActiveSession)
            ->where("u.user_name = '" . $this->database()->escape($sUser) . "'")
            ->execute('getSlaveRow');

        $aRow['is_online'] = $aRow['is_online'] ? 1 : 0;

        return $aRow;
    }


    /**
     * Get information friend by user and friend
     *
     * @param int $iUserID user ID
     * @param int $iFriendID friend ID
     * @return array information record
     */
    public function getFriendByUserIDAndFriendID($iUserID = null, $iFriendID = null)
    {
        if ($iUserID == null || $iFriendID == null) {
            return null;
        }
        return $this->database()->select("frd.*")
            ->from(Phpfox::getT('friend'), 'frd')
            ->where('frd.user_id = ' . (int)$iUserID . ' AND frd.friend_user_id = ' . (int)$iFriendID)
            ->execute('getSlaveRow');
    }

    /**
     * Get joined friend in pages
     *
     * @param int $iPagesID page ID
     * @param int $iLimit limit joined friend
     * @param boolean $bNoCount count results or not
     * @return array joined friend result
     */
    public function getJoinedFriendInPages($iPagesID, $iLimit = 7, $bNoCount = false)
    {
        $aRows = $this->database()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('like'), 'l', 'l.user_id = f.friend_user_id  AND l.`type_id` = \'pages\'  AND l.`item_id` = ' . $this->database()->escape((int)$iPagesID))
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
            ->group('f.friend_user_id')
            ->order('f.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (!$bNoCount) {
            $iCnt = $this->database()->getField('SELECT FOUND_ROWS()');
        }

        return array($iCnt, $aRows);
    }

    /**
     * Get joined friend in pages
     *
     * @param int $iGroupId group ID
     * @param int $iLimit limit joined friend
     * @param boolean $bNoCount count results or not
     * @return array joined friend result
     */
    public function getJoinedFriendInGroups($iGroupId, $iLimit = 7, $bNoCount = false)
    {
        $aRows = $this->database()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('like'), 'l', 'l.user_id = f.friend_user_id  AND l.`type_id` = \'groups\'  AND l.`item_id` = ' . $this->database()->escape((int)$iGroupId))
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
            ->group('f.friend_user_id')
            ->order('f.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (!$bNoCount) {
            $iCnt = $this->database()->getField('SELECT FOUND_ROWS()');
        }

        return array($iCnt, $aRows);
    }

    /**
     * Get joined friend with paging
     *
     * @param int $iPagesID page ID
     * @param int $iPage order page
     * @param string $sLimit limit result
     * @param boolean $bCount count result or not
     * @return array joined friend result with limiting
     */
    public function getJoinedFriendInPagesWithPaging($iPagesID, $iPage = '', $sLimit = '', $bCount = true)
    {
        $iCnt = 0;
        if ($bCount === true) {
            $iCnt = $this->database()->select('COUNT(DISTINCT f.friend_user_id)')
                ->from(Phpfox::getT('friend'), 'f')
                ->join(Phpfox::getT('like'), 'l', 'l.user_id = f.friend_user_id  AND l.`type_id` = \'pages\'  AND l.`item_id` = ' . $this->database()->escape((int)$iPagesID))
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
                ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
                ->execute('getSlaveField');
        }

        $aRows = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('like'), 'l', 'l.user_id = f.friend_user_id  AND l.`type_id` = \'pages\'  AND l.`item_id` = ' . $this->database()->escape((int)$iPagesID))
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
            ->group('f.friend_user_id')
            ->order('f.time_stamp DESC')
            ->limit($iPage, $sLimit, $iCnt)
            ->execute('getSlaveRows');

        if ($bCount === false) {
            return $aRows;
        }

        return array($iCnt, $aRows);
    }

    /**
     * Get joined friend with paging
     *
     * @param int $iGroupsID group ID
     * @param int $iPage order page
     * @param string $sLimit limit result
     * @param boolean $bCount count result or not
     * @return array joined friend result with limiting
     */
    public function getJoinedFriendInGroupsWithPaging($iGroupsID, $iPage = '', $sLimit = '', $bCount = true)
    {
        $iCnt = 0;
        if ($bCount === true) {
            $iCnt = $this->database()->select('COUNT(DISTINCT f.friend_user_id)')
                ->from(Phpfox::getT('friend'), 'f')
                ->join(Phpfox::getT('like'), 'l', 'l.user_id = f.friend_user_id  AND l.`type_id` = \'groups\'  AND l.`item_id` = ' . $this->database()->escape((int)$iGroupsID))
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
                ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
                ->execute('getSlaveField');
        }

        $aRows = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('like'), 'l', 'l.user_id = f.friend_user_id  AND l.`type_id` = \'groups\'  AND l.`item_id` = ' . $this->database()->escape((int)$iGroupsID))
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
            ->group('f.friend_user_id')
            ->order('f.time_stamp DESC')
            ->limit($iPage, $sLimit, $iCnt)
            ->execute('getSlaveRows');

        if ($bCount === false) {
            return $aRows;
        }

        return array($iCnt, $aRows);
    }

    /**
     * Get joined friend in event
     *
     * @param int $iEventID event ID
     * @param int $iLimit limit joined friend
     * @param boolean $bNoCount count results or not
     * @return array joined friend result
     */
    public function getJoinedFriendInEvent($iEventID, $iLimit = 7, $bNoCount = false)
    {
        $aRows = $this->database()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('event_invite'), 'ei', 'ei.event_id = ' . $this->database()->escape((int)$iEventID) . ' AND ei.rsvp_id = 1 AND ei.invited_user_id = f.friend_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
            ->group('f.friend_user_id')
            ->order('f.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (!$bNoCount) {
            $iCnt = $this->database()->getField('SELECT FOUND_ROWS()');
        }

        return array($iCnt, $aRows);
    }

    /**
     * Get joined friend in event
     *
     * @param int $iEventID event ID
     * @param int $iLimit limit joined friend
     * @param boolean $bNoCount count results or not
     * @return array joined friend result
     */
    public function getJoinedFriendInFEvent($iEventID, $iLimit = 7, $bNoCount = false)
    {
        $aRows = $this->database()->select(($bNoCount ? '' : 'SQL_CALC_FOUND_ROWS ') . Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = ' . $this->database()->escape((int)$iEventID) . ' AND ei.rsvp_id = 1 AND ei.invited_user_id = f.friend_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
            ->group('f.friend_user_id')
            ->order('f.time_stamp DESC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (!$bNoCount) {
            $iCnt = $this->database()->getField('SELECT FOUND_ROWS()');
        }

        return array($iCnt, $aRows);
    }

    /**
     * Get joined friend with paging
     *
     * @param int $iEventID event ID
     * @param int $iPage order page
     * @param string $sLimit limit result
     * @param boolean $bCount count result or not
     * @return array joined friend result with limiting
     */
    public function getJoinedFriendInEventWithPaging($iEventID, $iPage = '', $sLimit = '', $bCount = true)
    {
        $iCnt = 0;
        if ($bCount === true) {
            $iCnt = $this->database()->select('COUNT(DISTINCT f.friend_user_id)')
                ->from(Phpfox::getT('friend'), 'f')
                ->join(Phpfox::getT('event_invite'), 'ei', 'ei.event_id = ' . $this->database()->escape((int)$iEventID) . ' AND ei.rsvp_id = 1 AND ei.invited_user_id = f.friend_user_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
                ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
                ->execute('getSlaveField');
        }

        $aRows = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('event_invite'), 'ei', 'ei.event_id = ' . $this->database()->escape((int)$iEventID) . ' AND ei.rsvp_id = 1 AND ei.invited_user_id = f.friend_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
            ->group('f.friend_user_id')
            ->order('f.time_stamp DESC')
            ->limit($iPage, $sLimit, $iCnt)
            ->execute('getSlaveRows');

        if ($bCount === false) {
            return $aRows;
        }

        return array($iCnt, $aRows);
    }

    /**
     * Get joined friend with paging
     *
     * @param int $iEventID event ID
     * @param int $iPage order page
     * @param string $sLimit limit result
     * @param boolean $bCount count result or not
     * @return array joined friend result with limiting
     */
    public function getJoinedFriendInFEventWithPaging($iEventID, $iPage = '', $sLimit = '', $bCount = true)
    {
        $iCnt = 0;
        if ($bCount === true) {
            $iCnt = $this->database()->select('COUNT(DISTINCT f.friend_user_id)')
                ->from(Phpfox::getT('friend'), 'f')
                ->join(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = ' . $this->database()->escape((int)$iEventID) . ' AND ei.rsvp_id = 1 AND ei.invited_user_id = f.friend_user_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
                ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
                ->execute('getSlaveField');
        }

        $aRows = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = ' . $this->database()->escape((int)$iEventID) . ' AND ei.rsvp_id = 1 AND ei.invited_user_id = f.friend_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . $this->database()->escape((int)Phpfox::getUserId()))
            ->group('f.friend_user_id')
            ->order('f.time_stamp DESC')
            ->limit($iPage, $sLimit, $iCnt)
            ->execute('getSlaveRows');

        if ($bCount === false) {
            return $aRows;
        }

        return array($iCnt, $aRows);
    }

    public function getAllAdminUser()
    {
        return $this->database()->select("usr.user_id, usr.user_name")
            ->from(Phpfox::getT('user'), 'usr')
            ->where('usr.user_group_id = ' . (int)ADMIN_USER_ID)
            ->execute('getSlaveRows');
    }

    public function initThemeTemplateBodyPlugin()
    {
        $aRet = array();
        $aRet['iOpeningDelayTime'] = intval(Phpfox::getParam('profilepopup.opening_delay_time'));
        $aRet['iClosingDelayTime'] = intval(Phpfox::getParam('profilepopup.closing_delay_time'));
        $aRet['sEnableCache'] = Phpfox::getParam('profilepopup.enable_cache_popup') ? 'true' : 'false';
        $rewriteData = $this->getRewriteData();
        $rewriteData[] = array('url' => 'browse-photo', 'replacement' => 'browse-photo');
        $rewriteData[] = array('url' => 'arcade-game', 'replacement' => 'arcade-game');
        $aRet['rewriteData'] = json_encode($rewriteData);
        return $aRet;
    }

    public function havingPublishedResumeByUserID($userID = null)
    {
        if (null == $userID || Phpfox::isModule('resume') == false) {
            return false;
        }

        return Phpfox::getService('resume')->hasPublishedResume($userID);
    }

    /**
     *
     *
     */
    public function canViewResumeByUserID($viewingUserID = null, $viewedUserID = null)
    {
        if (null == $viewingUserID || null == $viewedUserID || Phpfox::isModule('resume') == false) {
            return false;
        }

        if ($this->havingPublishedResumeByUserID($viewedUserID) == false) {
            return false;
        }

        $aResume = $this->getPublishedResumeByUserID($viewedUserID);
        if (null == $aResume) {
            return false;
        }

        $check_permission = Phpfox::getService('resume.permission')->canViewResume($aResume);
        if (!$check_permission) {
            return false;
        }
        if (!Phpfox::isAdmin() && $aResume['user_id'] != $viewingUserID && (!$aResume['is_published'] || $aResume['status'] != 'approved' || !$aResume['is_completed'])) {
            return false;
        }

        return true;
    }

    /**
     * If =1, get it
     * If >1, prioritize for which is checked 'Show in Profile Info'
     * If >1 and there is not any resume which is checked 'Show in Profile Info', prioritize for which is latest
     *
     */
    public function getPublishedResumeByUserID($userID = null)
    {
        if (null == $userID || Phpfox::isModule('resume') == false) {
            return null;
        }

        $aPublishedResume = $this->database()->select('*')
            ->from(Phpfox::getT('resume_basicinfo'), 'bi')
            ->where("bi.is_completed = 1 AND bi.is_published = 1 AND bi.status = 'approved' AND bi.user_id = {$userID}")
            ->order('time_stamp DESC')
            ->execute('getRows');

        if (!$aPublishedResume || !is_array($aPublishedResume)) {
            return null;
        }

        //If =1, get it
        if (count($aPublishedResume) == 1) {
            return $aPublishedResume[0];
        }

        if (count($aPublishedResume) > 1) {
            //If >1, prioritize for which is checked 'Show in Profile Info' 
            foreach ($aPublishedResume as $resume) {
                if ($resume['is_show_in_profile'] == '1') {
                    return $resume;
                }
            }

            //If >1 and there is not any resume which is checked 'Show in Profile Info', prioritize for which is latest 
            return $aPublishedResume[0];
        }
    }

    public function getItemsByModule($iIsActive = null, $sItemType = 'user', $sModule = null)
    {
        if (null == $sModule) {
            return array();
        }

        $sWhere = ' 1=1 ';
        if ($iIsActive !== null) {
            $sWhere .= ' AND ppi.is_active = ' . (int)$iIsActive;
        }
        if (isset($sItemType) === false || $sItemType === null) {
            $sItemType = 'user';
        }
        return $this->database()->select("ppi.*")
            ->from(Phpfox::getT('profilepopup_module_item'), 'ppi')
            ->where($sWhere . ' AND ppi.item_type = \'' . $this->database()->escape($sItemType) . '\'' . ' AND ppi.module_id = \'' . $this->database()->escape($sModule) . '\'')
            ->order('ordering ASC')
            ->execute('getSlaveRows');
    }

    public function getItemByName($sItemType = 'user', $sName = null)
    {
        if (null == $sName) {
            return null;
        }

        $sWhere = '';
        if (isset($sItemType) === false || $sItemType === null) {
            $sItemType = 'user';
        }
        return $this->database()->select("ppi.*")
            ->from(Phpfox::getT('profilepopup_item'), 'ppi')
            ->where(' ppi.name = \'' . $this->database()->escape($sName) . '\' ' . $sWhere . ' AND ppi.item_type = \'' . $this->database()->escape($sItemType) . '\'')
            ->execute('getSlaveRow');
    }

    public function getRewriteData()
    {
        return Phpfox::getLib('database')->select('r.url, r.replacement')
            ->from(Phpfox::getT('rewrite'), 'r')
            ->execute('getRows');
    }

}

