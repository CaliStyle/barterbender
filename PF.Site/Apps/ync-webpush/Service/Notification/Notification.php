<?php
namespace Apps\YNC_WebPush\Service\Notification;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Error;
use Phpfox_Service;

class Notification extends Phpfox_Service
{

    private $_sATable;

    /**
     * Notification constructor.
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncwebpush_notification');
        $this->_sATable = Phpfox::getT('yncwebpush_notification_audience');
    }

    public function getNotification($iId, $bDetail = false)
    {
        if (!$iId) {
            return false;
        }
        $aItem = db()->select('n.*, na.*')
            ->from($this->_sTable, 'n')
            ->join($this->_sATable, 'na', 'na.notification_id = n.notification_id')
            ->where('n.notification_id = ' . (int)$iId)
            ->execute('getRow');
        if ($aItem && $bDetail) {
            $aItem['audience'] = $aItem['audience_key'] = '';
            switch ($aItem['audience_type']) {
                case 'group':
                    $aItem['audience_key'] = _p('user_group');
                    $aGroup = Phpfox::getService('user.group')->getGroup($aItem['audience_id']);
                    if ($aGroup) {
                        $aItem['audience'] = _p($aGroup['title_var_name']);
                    }
                    break;
                case 'browser':
                    $aItem['audience_key'] = _p('browser_u');
                    $aItem['audience'] = $aItem['audience_title'];
                    break;
                case 'subscriber':
                    $aItem['audience_key'] = _p('particular_subscribers');
                    $aItem['audience'] = '';
                    break;

                case 'all':
                    $aItem['audience_key'] = _p('all_subscribers');
                    $aItem['audience'] = '';
                    break;
            }
        }
        return $aItem;
    }

    public function getForManage($sCond, $iPage = 1, $iLimit = 10, &$iCount)
    {
        $iCount = db()->select('COUNT(*)')
            ->from($this->_sTable, 'n')
            ->join($this->_sATable, 'na', 'na.notification_id = n.notification_id')
            ->where($sCond)
            ->execute('getField');
        $aItems = [];
        if ($iCount) {
            $aItems = db()->select('n.*')
                ->from($this->_sTable, 'n')
                ->join($this->_sATable, 'na', 'na.notification_id = n.notification_id')
                ->where($sCond)
                ->limit($iPage, $iLimit, $iCount)
                ->order('n.notification_id DESC')
                ->execute('getSlaveRows');
        }
        return $aItems;
    }

    public function getForEdit($iId)
    {
        if (!$iId) {
            return false;
        }
        $aItem = db()->select('n.*, na.*')
            ->from($this->_sTable, 'n')
            ->join($this->_sATable, 'na', 'na.notification_id = n.notification_id')
            ->where('n.notification_id = ' . (int)$iId)
            ->execute('getRow');
        if (!$aItem) {
            return Phpfox_Error::set(_p('notification_you_are_looking_for_does_not_exists_or_has_been_removed'));
        }
        $aItem['schedule_time'] = Phpfox::getLib('date')->convertFromGmt($aItem['schedule_time'],
            Phpfox::getTimeZone());

        $aItem['schedule_month'] = date('n', $aItem['schedule_time']);
        $aItem['schedule_day'] = date('j', $aItem['schedule_time']);
        $aItem['schedule_year'] = date('Y', $aItem['schedule_time']);
        $aItem['schedule_hour'] = date('H', $aItem['schedule_time']);
        $aItem['schedule_minute'] = date('i', $aItem['schedule_time']);

        return $aItem;

    }

    public function getSubscribersOfNotification($iId)
    {
        if (!$iId) {
            return false;
        }
        $sAudience = db()->select('audience_title')
            ->from($this->_sATable)
            ->where('notification_id = ' . (int)$iId)
            ->execute('getField');
        $aUsers = [];
        if ($sAudience) {
            $aUsers = db()->select(Phpfox::getUserField())
                ->from(':user', 'u')
                ->where('u.user_id IN (' . $sAudience . ')')
                ->execute('getSlaveRows');
        }
        return $aUsers;
    }
}