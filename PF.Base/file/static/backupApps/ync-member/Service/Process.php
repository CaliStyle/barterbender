<?php

namespace Apps\YNC_Member\Service;

use Phpfox;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Process extends Phpfox_Service
{
    public function setMod($iUserId, $bIsMod = 1)
    {
        Phpfox::isAdmin(true);

        $this->clearMod();
        if (!$bIsMod) {
            $this->database()->insert(Phpfox::getT('ynmember_mod'), [
                'user_id' => $iUserId,
                'time_stamp' => PHPFOX_TIME
            ]);
        }

        return true;
    }

    public function clearMod()
    {
        $this->database()->delete(Phpfox::getT('ynmember_mod'), '1=1');
    }

    public function addFollow($iItemId)
    {
        $aSql = [
            'item_id' => $iItemId,
            'user_id' => Phpfox::getUserId(),
            'time_stamp' => PHPFOX_TIME
        ];

        $iInsertId = $this->database()->insert(Phpfox::getT('ynmember_follow'), $aSql);

        if (!$iInsertId) {
            return false;
        }

        return $iInsertId;
    }

    public function removeFollow($iItemId)
    {
        $this->database()->delete(phpfox::getT('ynmember_follow'), "item_id = $iItemId AND user_id = " . Phpfox::getUserId());
        return true;
    }

    public function addBirthdayWish($aVals)
    {
        if (!Phpfox::isUser()) {
            $success = 0;
            $message = _p('you_have_to_login_to_send_birthday_wish');
        } else {
            $iYearStart = strtotime('Jan 1 00:00:00');
            $iYearEnd = strtotime('Dec 31 23:59:00');
            $aMyBirthdayWish = $this->database()->select('bw.*')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('ynmember_birthday_wish'), 'bw', 'bw.item_id = ' . $aVals['user_id'] . ' AND bw.user_id = ' . Phpfox::getUserId())
                ->where('bw.time_stamp > ' . $iYearStart . ' AND bw.time_stamp < ' . $iYearEnd)
                ->executeRow();
            if (!empty($aMyBirthdayWish['birthday_wish_id'])) {
                $success = 0;
                $message = _p('you_already_sent_birthday_wish_to_this_member');
            } else {
        $aInsert = [
            'user_id' => Phpfox::getUserId(),
            'item_id' => $aVals['user_id'],
            'message' => $aVals['message'],
            'time_stamp' => PHPFOX_TIME,
        ];

        $aCommentVals = [
            'action' => 'upload_photo_via_share',
            'user_status' => $aVals['message'],
            'parent_user_id' => $aVals['user_id'],
            'method' => 'simple'
        ];

        $iId = $this->database()->insert(Phpfox::getT('ynmember_birthday_wish'), $aInsert);
        Phpfox::getService('feed.process')->addComment($aCommentVals);

                $success = 1;
                $message = _p('birthday_wish_sent');
            }
        }
        return array($success, $message);
    }

    public function addNotificationMap($aVals)
    {
        $iId = $this->database()->insert(Phpfox::getT('ynmember_birthday_wish'), $aVals);
    }
}