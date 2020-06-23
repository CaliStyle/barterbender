<?php
namespace Apps\YNC_WebPush\Service\Setting;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Service;

class Setting extends Phpfox_Service
{

    /**
     * Setting constructor.
     */
    private $_sNotiTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncwebpush_user_setting');
        $this->_sNotiTable = Phpfox::getT('yncwebpush_user_notification');
    }

    public function get($iUserId = null)
    {
        $aUserSettings = $this->getUserSettings($iUserId);
        $aNotifications = Phpfox::massCallback('getNotificationSettings');

        if (is_array($aNotifications)) {
            foreach ($aNotifications as $sModule => $aModules) {
                if (!is_array($aModules)) {
                    continue;
                }
                //Don't setting for 3rd apps
                if (!in_array($sModule, ['comment', 'feed', 'friend', 'forum', 'like', 'mail'])) {
                    unset($aNotifications[$sModule]);
                    continue;
                }
                foreach ($aModules as $sKey => $aNotification) {
                    if (isset($aUserSettings['notification'][$sKey])) {
                        $aNotifications[$sModule][$sKey]['default'] = 0;
                    }
                }
            }
        }

        if ($sPlugin = Phpfox_Plugin::get('yncwebpush.service_setting_get')) {
            eval($sPlugin);
        }
        return array(
            'subscribe_setting' => $aUserSettings['subscribe_setting'],
            'notification_setting' => $aNotifications,
        );
    }

    public function getUserSettings($iUserId = null)
    {
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }

        $aNotifications = array();
        $aRows = $this->database()->select('user_notification')
            ->from($this->_sNotiTable)
            ->where('user_id = ' . (int)$iUserId)
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            $aNotifications[$aRow['user_notification']] = true;
        }

        //Subscribe is disable if have data in this table
        $iSetting = $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)$iUserId)
            ->execute('getField');

        return array(
            'notification' => $aNotifications,
            'subscribe_setting' => $iSetting
        );
    }

    public function checkPushNotificationSetting($iUserId, $sVarName)
    {
        $sCacheId = $this->cache()->set('yncwebpush_push_notification_setting_' . $iUserId);

        if (!$aUserSetting = $this->cache()->get($sCacheId)) {
            $aRows = db()->select('user_notification')
                ->from($this->_sNotiTable)
                ->where('user_id = ' . (int)$iUserId)
                ->execute('getSlaveRows');
            $aUserSetting = [];
            if (count($aRows)) {
                foreach ($aRows as $aRow) {
                    $aUserSetting[$aRow['user_notification']] = 1;
                }
            }
            $this->cache()->save($sCacheId, $aUserSetting);
        }

        return isset($aUserSetting[$sVarName]) ? true : false;
    }

    public function getAllUnSubscribersIds()
    {
        $aRows = db()->select('user_id')
            ->from($this->_sTable)
            ->execute('getSlaveRows');
        $sUnSub = '';
        if (count($aRows)) {
            $aUnSub = array_map(function ($arr) {
                return $arr['user_id'];
            }, $aRows);
            $sUnSub = implode(',', $aUnSub);
        }
        return $sUnSub;
    }
}