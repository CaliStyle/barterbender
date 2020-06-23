<?php

defined('PHPFOX') or exit('NO DICE!');

class Fanot_Service_Fanot extends Phpfox_Service
{

    private $_sTable1;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('notification');
        $this->_sTable1 = Phpfox::getT('friend_request');
    }

    public function getFriendRequests($iPage = 0, $iLimit = 5, $iRequestId = 0)
    {
        $iUserId = (int) Phpfox::getUserId();
        $aCond = array();

        $aCond[] = 'fr.is_seen = 0 AND fr.is_hide = 0 AND fr.user_id = ' . $iUserId . ' AND fr.is_ignore = 0';

        if ($iRequestId > 0)
        {
            $aCond[] = 'AND fr.request_id = ' . (int) $iRequestId;
        }

        $iCnt = $this->database()->select('COUNT(*)')
                ->from($this->_sTable1, 'fr')
                ->where($aCond)
                ->execute('getSlaveField');

        $aRows = $this->database()->select('fr.request_id, fr.is_seen, fr.message, fr.friend_user_id, fr.time_stamp, fr.relation_data_id , ' . Phpfox::getUserField())
                ->from($this->_sTable1, 'fr')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = fr.friend_user_id')
                ->where($aCond)
                ->limit($iPage, $iLimit, $iCnt)
                ->order('fr.time_stamp DESC')
                ->group('fr.request_id')
                ->execute('getSlaveRows');

        $sIds = '';
        $oUser = Phpfox::getService('user');
        foreach ($aRows as $iKey => $aRow)
        {
            $sIds .= $aRow['request_id'] . ',';
            $sFullName = "<a href='" . $oUser->getLink($aRow['user_id']) . "' title='" . $aRow['full_name'] . "'><strong>" . $aRow['full_name'] . "</strong></a>";
            if ($aRow['relation_data_id'] > 0)
            {
                $aRelation = $this->database()->select('phrase_var_name')
                        ->from(Phpfox::getT('custom_relation_data'), 'crd')
                        ->where('crd.relation_data_id = ' . $aRow['relation_data_id'])
                        ->join(Phpfox::getT('custom_relation'), 'cr', 'cr.relation_id = crd.relation_id')
                        ->order('crd.relation_data_id DESC')
                        ->limit(1)
                        ->execute('getSlaveField');

                if (!empty($aRelation))
                {
                    $aRows[$iKey]['message'] = _p('fanot.full_name_wants_to_list_you_both_as_relationship', array('full_name' => $sFullName, 'relationship' => _p($aRelation)));
                }
                else
                {
                    $aRows[$iKey]['message'] = _p('fanot.full_name_wants_to_list_you_in_a_relationship', array('full_name' => $sFullName));
                }
            }
            else
            {
                $aRows[$iKey]['message'] = _p('fanot.user_want_to_add_you_as_gender_friends', array('full_name' => $sFullName, 'gender' => ($aRow['gender'] == 2 ? _p('core.her') : _p('core.his'))));
            }
        }
        $sIds = rtrim($sIds, ',');

        if (!empty($sIds))
        {
            $this->database()->update(Phpfox::getT('friend_request'), array('is_hide' => '1'), 'user_id = ' . $iUserId . ' AND request_id IN(' . $sIds . ')');
        }

        return $aRows;
    }

    public function getNotifications($iLimit = 5)
    {
        $iUserId = (int) Phpfox::getUserId();
        $aGetRows = $this->database()->select('n.*, n.user_id as item_user_id, COUNT(n.notification_id) AS total_extra, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'n')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = n.owner_user_id')
                ->innerJoin('(SELECT * FROM ' . $this->_sTable . ' AS n WHERE n.user_id = ' . $iUserId . ' ORDER BY n.time_stamp DESC)', 'ninner', 'ninner.notification_id = n.notification_id')
                ->where('n.is_seen = 0 AND n.is_hide = 0 AND n.user_id = ' . $iUserId . '')
                ->group('n.type_id, n.item_id')
                ->order('n.time_stamp DESC')
                ->limit($iLimit)
                ->execute('getSlaveRows');

        $aRows = array();
        foreach ($aGetRows as $aGetRow)
        {
            $aRows[(int) $aGetRow['notification_id']] = $aGetRow;
        }

        arsort($aRows);

        $aNotifications = array();
        foreach ($aRows as $aRow)
        {
            $aParts1 = explode('.', $aRow['type_id']);
            $sModule = $aParts1[0];
            if (strpos($sModule, '_'))
            {
                $aParts = explode('_', $sModule);
                $sModule = $aParts[0];
            }

            if (Phpfox::isModule($sModule))
            {
                if ((int) $aRow['total_extra'] > 1)
                {
                    $aExtra = $this->database()->select('n.owner_user_id, n.time_stamp, n.is_seen, u.full_name')
                            ->from($this->_sTable, 'n')
                            ->join(Phpfox::getT('user'), 'u', 'u.user_id = n.owner_user_id')
                            ->where('n.type_id = \'' . $this->database()->escape($aRow['type_id']) . '\' AND n.item_id = ' . (int) $aRow['item_id'])
                            ->group('u.user_id')
                            ->order('n.time_stamp DESC')
                            ->limit(10)
                            ->execute('getSlaveRows');

                    foreach ($aExtra as $iKey => $aExtraUser)
                    {
                        if ($aExtraUser['owner_user_id'] == $aRow['user_id'])
                        {
                            unset($aExtra[$iKey]);
                        }

                        if (!$aRow['is_seen'] && $aExtraUser['is_seen'])
                        {
                            unset($aExtra[$iKey]);
                        }
                    }

                    if (count($aExtra))
                    {
                        $aRow['extra_users'] = $aExtra;
                    }
                }

                if (substr($aRow['type_id'], 0, 8) != 'comment_' && !Phpfox::hasCallback($aRow['type_id'], 'getNotification'))
                {
                    $aCallBack['link'] = '#';
                    $aCallBack['message'] = '2. Notification is missing a callback. [' . $aRow['type_id'] . '::getNotification]';
                }
                elseif (substr($aRow['type_id'], 0, 8) == 'comment_' && substr($aRow['type_id'], 0, 12) != 'comment_feed' && !Phpfox::hasCallback(substr_replace($aRow['type_id'], '', 0, 8), 'getCommentNotification'))
                {
                    $aCallBack['link'] = '#';
                    $aCallBack['message'] = 'Notification is missing a callback. [' . substr_replace($aRow['type_id'], '', 0, 8) . '::getCommentNotification]';
                }
                else
                {
                    $aCallBack = Phpfox::callback($aRow['type_id'] . '.getNotification', $aRow);

                    if ($aCallBack === false)
                    {
                        $this->database()->delete($this->_sTable, 'notification_id = ' . (int) $aRow['notification_id']);

                        continue;
                    }
                }
                
                $aCallBack['message'] = htmlentities($aCallBack['message'], ENT_QUOTES);
                $aCallBack['message'] = html_entity_decode($aCallBack['message']);

                $aNotifications[] = array_merge($aRow, (array) $aCallBack);
            }

            $this->database()->update($this->_sTable, array('is_hide' => '1'), 'type_id = \'' . $this->database()->escape($aRow['type_id']) . '\' AND user_id = ' . $iUserId . ' AND item_id = ' . (int) $aRow['item_id']);
        }
        return $aNotifications;
    }

    public function hide($iId, $iType)
    {
        if ((int) $iType == 1)
        {
            return $this->database()->update($this->_sTable, array('is_hide' => 1), 'notification_id = ' . (int) $iId . ' AND user_id = ' . Phpfox::getUserId());
        }
        else if ((int) $iType == 2)
        {
            return $this->database()->update($this->_sTable1, array('is_hide' => 1), 'request_id= ' . (int) $iId);
        }
        return false;
    }

    public function updateSeen($iId, $iType)
    {
        if ((int) $iType == 1)
        {
            return $this->database()->update($this->_sTable, array('is_seen' => 1), 'notification_id = ' . (int) $iId . ' AND user_id = ' . Phpfox::getUserId());
        }
        else if ((int) $iType == 2)
        {
            return $this->database()->update($this->_sTable1, array('is_seen' => 1), 'request_id = ' . (int) $iId);
        }
        return false;
    }
    
    public function isActiveSoundAlert($iUserId = null)
    {
        if($iUserId == null)
        {
            $iUserId = Phpfox::getUserId();
        }
        $sUserNotification = $this->database()->select('un.user_notification')
            ->from(Phpfox::getT('user_notification'), 'un')
            ->where('un.user_notification = "fanot.sound_alert_for_notification" and un.user_id = '.$iUserId)
            ->execute('getSlaveField');
        if(isset($sUserNotification) && $sUserNotification)
        {
            return false;
        }
        return true;
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
        if ($sPlugin = Phpfox_Plugin::get('fanot.service_fanot__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>