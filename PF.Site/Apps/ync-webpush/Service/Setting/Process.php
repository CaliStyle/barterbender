<?php
namespace Apps\YNC_WebPush\Service\Setting;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;

class Process extends Phpfox_Service
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

    public function update($aVals, $iUserId = null)
    {
        if ($iUserId == null) {
            $iUserId = Phpfox::getUserId();
        }
        db()->delete($this->_sNotiTable, 'user_id = ' . $iUserId);
        db()->delete($this->_sTable, 'user_id = ' . $iUserId);

        $aSaved = [];
        //Save push notification setting
        foreach ($aVals['notification'] as $sVar => $iVal) {
            if (!$iVal) {
                continue;
            }
            $aSaved[$sVar] = $iVal;
            db()->insert($this->_sNotiTable, array(
                    'user_id' => $iUserId,
                    'user_notification' => $sVar,
                    'time_stamp' => PHPFOX_TIME
                )
            );
        }

        //Save subscribe setting
        if (isset($aVals['subscribe_setting']) && $aVals['subscribe_setting']) {
            db()->insert($this->_sTable, array(
                    'user_id' => $iUserId,
                    'time_stamp' => PHPFOX_TIME
                )
            );
        }

        //Remove setting cached of this user
        $this->cache()->remove('yncwebpush_push_notification_setting_' . $iUserId);

        //Save cache again
        $sCacheId = $this->cache()->set('yncwebpush_push_notification_setting_' . $iUserId);
        $this->cache()->save($sCacheId, $aSaved);
        return true;
    }
}