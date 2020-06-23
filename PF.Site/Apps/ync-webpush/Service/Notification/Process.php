<?php
namespace Apps\YNC_WebPush\Service\Notification;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Error;
use Phpfox_File;
use Phpfox_Image;
use Phpfox_Service;
use Phpfox_Template;

class Process extends Phpfox_Service
{
    /**
     * Notification constructor.
     */
    static $_aIconSize = ['50', '100'];
    static $_aPhotoSize = ['100', '200', '400'];
    private $_sATable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncwebpush_notification');
        $this->_sATable = Phpfox::getT('yncwebpush_notification_audience');
    }

    public function executeCronNotification($iId)
    {
        $aNotification = Phpfox::getService('yncwebpush.notification')->getNotification($iId);
        $aResultTokens = [];
        if (!$aNotification) {
            return Phpfox_Error::set(_p('notification_does_not_exists'));
        }
        if ($aNotification['status'] == 'sent') {
            return false;
        }
        db()->update($this->_sTable, ['status' => 'sending'], 'notification_id = ' . (int)$iId);

        $sCond = 'ut.is_active = 1';
        switch ($aNotification['audience_type']) {
            case 'all':
                //Do nothing, simply get all subscribers
                break;
            case 'subscriber':
                db()->join(':user', 'u',
                    'u.user_id = ut.user_id AND u.profile_page_id = 0 AND u.status_id = 0 AND u.user_id IN (' . $aNotification['audience_title'] . ')');
                break;
            case 'group':
                $iGroupId = $aNotification['audience_id'];
                if ($iGroupId == 3) {
                    $sCond .= ' AND ut.user_id = 0';
                } else {
                    db()->join(':user', 'u',
                        'u.user_id = ut.user_id AND u.profile_page_id = 0 AND u.status_id = 0 AND u.user_group_id =' . (int)$iGroupId);
                }
                break;
            case 'browser':
                $sCond .= ' AND ut.browser like \'' . $aNotification['audience_title'] . '\'';
                break;
            default:
                break;
        }
        $aTokens = db()->select('ut.token, ut.user_id, us.user_id as no_subscribe')
            ->from(':yncwebpush_user_token', 'ut')
            ->leftJoin(':yncwebpush_user_setting', 'us', 'us.user_id = ut.user_id')
            ->where($sCond)
            ->execute('getSlaveRows');

        if (count($aTokens)) {
            foreach ($aTokens as $aToken) {
                //Check if user is disabled subscribe
                if (!empty($aToken['no_subscribe'])) {
                    continue;
                }
                if (!isset($aResultTokens[$aToken['user_id']])) {
                    $aResultTokens[$aToken['user_id']] = [];
                    $aResultTokens[$aToken['user_id']] = [
                        'token' => [],
                        'noti_id' => 0
                    ];
                    $aResultTokens[$aToken['user_id']]['token'][] = $aToken['token'];
                } elseif (!in_array($aToken['token'], $aResultTokens[$aToken['user_id']]['token'])) {
                    $aResultTokens[$aToken['user_id']]['token'][] = $aToken['token'];
                }

                if ($aToken['user_id'] && !$aResultTokens[$aToken['user_id']]['noti_id']) {
                    $aResultTokens[$aToken['user_id']]['noti_id'] = $this->addSystemNotification('yncwebpush_admin_push', $iId, $aToken['user_id'],
                        $aNotification['user_id']);
                    //Send Mail
                    $this->sendMail($aNotification, $aToken['user_id']);
                }
            }
        }

        if (!empty($aResultTokens)) {
            if (!empty($aNotification['icon_path'])) {
                $sIcon = Phpfox::getLib('image.helper')->display([
                    'file' => $aNotification['icon_path'],
                    'path' => 'core.url_pic',
                    'server_id' => $aNotification['icon_server_id'],
                    'suffix' => '_100',
                    'return_url' => true
                ]);
                $sIcon = str_replace('http://', 'https://', $sIcon);
            } else {
                $sIcon = '';
            }

            if (!empty($aNotification['photo_path'])) {
                $sPhoto = Phpfox::getLib('image.helper')->display([
                    'file' => $aNotification['photo_path'],
                    'path' => 'core.url_pic',
                    'server_id' => $aNotification['photo_server_id'],
                    'suffix' => '_400',
                    'return_url' => true
                ]);
                $sPhoto = str_replace('http://', 'https://', $sPhoto);
            } else {
                $sPhoto = '';
            }
            $iTotal = 0;
            foreach ($aResultTokens as $aResult) {
                $this->pushNotification($aNotification['title'], $aNotification['message'], $aNotification['redirect_url'],
                    $sIcon, $aResult['token'], $sPhoto, $aResult['noti_id']);
                $iTotal = $iTotal + count($aResult['token']);
            }
            db()->update($this->_sTable, ['total_send' => $iTotal], 'notification_id =' . (int)$iId);
        }

        //Remove cron when done
        db()->delete(':cron', 'cron_id = ' . (int)$aNotification['cron_id']);
        //Update status
        db()->update($this->_sTable, ['status' => 'sent'], 'notification_id = ' . (int)$iId);

        return true;
    }

    public function pushNotification($sTitle, $sMessage, $sLink, $sIcon, $aTokens, $sImage = '', $iNotificationId = 0)
    {
        $sUrl = 'https://fcm.googleapis.com/fcm/send';
        if ($sIcon == '') {
            $sIcon = flavor()->active->favicon_url();
            if (empty($sIcon)) {
                $sIcon = Phpfox::getParam('core.path') . 'favicon.ico?v=' . Phpfox_Template::instance()->getStaticVersion();
            }
            $sIcon = str_replace('http://', 'https://', $sIcon);
        }

        $sAjaxUrl = Phpfox::getLib('url')->makeUrl('push-notification.mark-read-notification');
        $sAjaxUrl = str_replace('http://','https://', $sAjaxUrl);

        $aData = array(
            'title' => $sTitle,
            'body' => $sMessage,
            'sound' => 1,
            'color' => '#ffffff',
            'url' => $sLink,
            'icon' => $sIcon,
            'image' => $sImage,
            'vibrate' => 1,
            'ync_push_id' => $iNotificationId,
            'ync_ajax_url' => $sAjaxUrl,
            'test_token' => 0
        );
        $sFields = array(
            'registration_ids' => $aTokens,
            'data' => $aData,
            'time_to_live' => (int)setting('yncwebpush_notification_expiration_time'),
            'priority' => 7
        );
        $sFields = json_encode($sFields);

        $aHeaders = array(
            'Authorization: key=' . setting('yncwebpush_server_key'),
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sFields);

        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    public function delete($iId)
    {
        $aNotification = Phpfox::getService('yncwebpush.notification')->getNotification($iId);
        if (!$aNotification || $aNotification['status'] == 'sending') {
            return false;
        }
        //Remove cron
        db()->delete(':cron', 'cron_id = ' . (int)$aNotification['cron_id']);

        //Remove data
        db()->delete($this->_sTable, 'notification_id = ' . (int)$iId);
        db()->delete($this->_sATable, 'notification_id = ' . (int)$iId);

        return true;
    }

    public function stopSend($iId)
    {
        $aNotification = Phpfox::getService('yncwebpush.notification')->getNotification($iId);
        if (!$aNotification || $aNotification['status'] != 'sending') {
            return false;
        }
        //Remove cron
        db()->delete(':cron', 'cron_id = ' . (int)$aNotification['cron_id']);
        //Update status
        db()->update($this->_sTable, ['status' => 'sent'], 'notification_id = ' . (int)$iId);
        return true;
    }

    public function resendNotification($iId)
    {
        $aNotification = Phpfox::getService('yncwebpush.notification')->getNotification($iId);
        if (!$aNotification || $aNotification['status'] != 'sent') {
            return false;
        }
        $aData = $aNotification;

        if ($this->add($aData, false, true)) {
            return true;
        }
        return false;
    }

    public function add($aVals, $bIsUpdate = false, $bResend = false)
    {
        if (empty($aVals['title'])) {
            return Phpfox_Error::set(_p('provide_a_title_for_notification'));
        }
        if (!empty($aVals['redirect_url'])) {
            $sReg = '/(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\-\.@:%_\+~#=]+)+((\.[a-zA-Z])*)(\/([0-9A-Za-z-\-\.@:%_\+~#=\?])*)*/';
            if (!preg_match($sReg, $aVals['redirect_url'])) {
                return Phpfox_Error::set(_p('please_add_a_valid_redirect_url'));
            }
        } else {
            return Phpfox_Error::set(_p('provide_a_redirect_url_for_notification'));
        }
        $aEditItem = [];
        if ($bIsUpdate) {
            if (empty($aVals['notification_id'])) {
                return Phpfox_Error::set(_p('notification_you_are_looking_for_does_not_exists_or_has_been_removed'));
            } else {
                $aEditItem = Phpfox::getService('yncwebpush.notification')->getNotification($aVals['notification_id']);
                if ($aEditItem['status'] != 'scheduled') {
                    return Phpfox_Error::set(_p('edit_failed_you_can_edit_notification_with_status_scheduled_only'));
                }
            }
        }
        $iTemplateId = $aVals['template_id'];
        $bSaveTemplate = false;
        if (!empty($aVals['save_template'])) {
            if (empty($aVals['template_name'])) {
                return Phpfox_Error::set(_p('provide_a_name_for_template'));
            }
            if (Phpfox::getService('yncwebpush.template')->checkExistedTemplate($aVals['template_name'])) {
                return Phpfox_Error::set(_p('template_with_this_name_already_exists'));
            }
            $bSaveTemplate = true;
        }
        $oFile = Phpfox_File::instance();
        $oFilter = Phpfox::getLib('parse.input');
        $oImage = Phpfox_Image::instance();
        $sPicStorage = Phpfox::getParam('core.dir_pic') . 'yncwebpush/';
        $aInsert = [
            'title' => $oFilter->clean($aVals['title']),
            'message' => !empty($aVals['message']) ? $oFilter->clean($aVals['message']) : '',
            'redirect_url' => !empty($aVals['redirect_url']) ? $aVals['redirect_url'] : '',
        ];

        $bSchedule = false;
        if (!empty($aVals['is_schedule'])) {
            //Is schedule notification
            $aInsert['schedule_time'] = Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->mktime($aVals['schedule_hour'],
                $aVals['schedule_minute'], 0, $aVals['schedule_month'],
                $aVals['schedule_day'], $aVals['schedule_year']));
            if ($aInsert['schedule_time'] <= PHPFOX_TIME) {
                return Phpfox_Error::set(_p('you_must_set_the_scheduled_time_to_greater_than_the_current_time'));
            }
            $aInsert['status'] = 'scheduled';
            $bSchedule = true;
        } else {
            //Send now
            $aInsert['schedule_time'] = PHPFOX_TIME;
            $aInsert['status'] = 'sending';
        }
        if (!$bResend && isset($_FILES['icon']['name']) && ($_FILES['icon']['name'] != '')) {
            $aIcon = $oFile->load('icon', array('jpg', 'gif', 'png'));
            if (!Phpfox_Error::isPassed()) {
                return false;
            }
            if ($aIcon !== false) {
                $sIconName = $oFile->upload('icon', $sPicStorage, 'icon');
                foreach (self::$_aIconSize as $size) {
                    $oImage->createThumbnail($sPicStorage . sprintf($sIconName, ''),
                        $sPicStorage . sprintf($sIconName, '_' . $size), $size, $size);
                }
                $aInsert['icon_path'] = 'yncwebpush/' . $sIconName;
                $aInsert['icon_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
            }
        } elseif (($iTemplateId || $bResend || $bIsUpdate) && !empty($aVals['icon_path'])) {
            $aInsert['icon_path'] = $aVals['icon_path'];
            $aInsert['icon_server_id'] = $aVals['icon_server_id'];
        }
        if (!$bResend && isset($_FILES['photo']['name']) && ($_FILES['photo']['name'] != '')) {
            $aPhoto = $oFile->load('photo', array('jpg', 'gif', 'png'));
            if (!Phpfox_Error::isPassed()) {
                return false;
            }
            if ($aPhoto !== false) {
                $sPhotoName = $oFile->upload('icon', $sPicStorage, 'icon');
                foreach (self::$_aPhotoSize as $size) {
                    $oImage->createThumbnail($sPicStorage . sprintf($sPhotoName, ''),
                        $sPicStorage . sprintf($sPhotoName, '_' . $size), $size, $size);
                }
                $aInsert['photo_path'] = 'yncwebpush/' . $sPhotoName;
                $aInsert['photo_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
            }
        } elseif (($iTemplateId || $bResend || $bIsUpdate) && !empty($aVals['photo_path'])) {
            $aInsert['photo_path'] = $aVals['photo_path'];
            $aInsert['photo_server_id'] = $aVals['photo_server_id'];
        }

        if ($bIsUpdate) {
            db()->update($this->_sTable, $aInsert, 'notification_id = ' . $aVals['notification_id']);
            //Delete old cron
            db()->delete(':cron', 'cron_id = ' . (int)$aEditItem['cron_id']);
            //Insert new cron
            $this->addNotificationToCron($aVals['notification_id'], $aInsert['schedule_time']);
            if ($bSaveTemplate) {
                $aVals['used'] = 1;
                Phpfox::getService('yncwebpush.template.process')->add($aVals, false, $aInsert);
            }
            return array($aVals['notification_id'], $bSchedule);
        } else {
            $aInsert['audience_type'] = $aVals['audience_type'];
            $aInsert['template_id'] = $iTemplateId;
            $aInsert['time_stamp'] = PHPFOX_TIME;
            $aInsert['user_id'] = Phpfox::getUserId();
            $iId = db()->insert($this->_sTable, $aInsert);
            if ($iTemplateId) {
                //Update counter used for template
                db()->updateCounter('yncwebpush_template', 'used', 'template_id', $iTemplateId);
            }
            if ($iId) {
                //Add audience
                $this->addNotificationAudience($iId, $aVals, $bResend);
                if ($bSaveTemplate) {
                    $aVals['used'] = 1;
                    Phpfox::getService('yncwebpush.template.process')->add($aVals, false, $aInsert);
                }
                $this->addNotificationToCron($iId, $aInsert['schedule_time']);
                if ($bResend) {
                    return $iId;
                }
                return array($iId, $bSchedule);
            }
        }
        if ($bResend) {
            return false;
        }
        return array(false, false);
    }

    public function addNotificationToCron($iId, $iTimeToRun)
    {
        $aInsert = [
            'module_id' => 'yncwebpush',
            'product_id' => 'phpfox',
            'next_run' => $iTimeToRun - 1,
            'type_id' => 1,
            'every' => 5,
            'is_active' => 1,
            'php_code' => 'Phpfox::getService(\'yncwebpush.notification.process\')->executeCronNotification(' . $iId . ');'
        ];
        $iCronId = db()->insert(':cron', $aInsert);
        if ($iCronId) {
            db()->update($this->_sTable, ['cron_id' => $iCronId], 'notification_id =' . (int)$iId);
        }
        return true;
    }

    public function addNotificationAudience($iId, $aVals, $bResend = false)
    {
        if (!$iId || empty($aVals['audience_type'])) {
            return false;
        }
        if ($bResend) {
            $aAudiances = db()->select('*')->from($this->_sATable)->where('notification_id =' . (int)$aVals['notification_id'])->execute('getSlaveRows');
            foreach ($aAudiances as $aAudiance) {
                $aInsert = [
                    'notification_id' => $iId,
                    'audience_type' => $aAudiance['audience_type'],
                    'audience_id' => $aAudiance['audience_id'],
                    'audience_title' => $aAudiance['audience_title'],
                ];
                db()->insert($this->_sATable, $aInsert);
            }
            return true;
        } else {
            $aInsert = [
                'notification_id' => $iId,
                'audience_type' => $aVals['audience_type'],
                'audience_id' => $aVals['audience_type'] == 'group' ? $aVals['audience'] : 0,
                'audience_title' => (in_array($aVals['audience_type'],
                    ['browser', 'subscriber'])) ? $aVals['audience'] : '',
            ];
        }
        return db()->insert($this->_sATable, $aInsert);
    }

    public function sendNow($iId)
    {
        $aNotification = Phpfox::getService('yncwebpush.notification')->getNotification($iId);
        if (!$aNotification || $aNotification['status'] != 'scheduled') {
            return false;
        }
        //Remove cron
        db()->update(':cron', ['next_run' => (PHPFOX_TIME - 1)], 'cron_id = ' . (int)$aNotification['cron_id']);
        //Update status
        db()->update($this->_sTable, ['status' => 'sending','schedule_time' => PHPFOX_TIME], 'notification_id = ' . (int)$iId);
        return true;
    }

    public function sendMail($aNotification, $iUserId)
    {
        $sSubject = $aNotification['title'];
        $sContent = $aNotification['message'];
        $sContent .= '<br/>'._p('to_learn_more_about_this_follow_the_link_below',['link' => $aNotification['redirect_url']]);
        $sContent .= '<br/>'._p('you_received_this_email_because_you_subscribed_for_announcements_from_website_if_you_no_longer_wish_to_receive_these_emails_you_may_un_subscribe_at_any_time',[
            'link' => Phpfox::getLib('url')->makeUrl('push-notification',['tab' => 'subscribe'])
            ]);
        $aUser = Phpfox::getService('user')->getUser($iUserId);
        if ($aUser) {
            Phpfox::getLib('mail')->to($aUser['email'])
                ->aUser($aUser)
                ->subject($sSubject)
                ->message($sContent)
                ->send();
        }
        return true;
    }

    public function updateSeenNotification($iId)
    {
        db()->update(':notification',['is_seen' => 1], 'notification_id ='.(int)$iId);
        return true;
    }

    public function addSystemNotification($sType, $iItemId, $iOwnerUserId, $iSenderUserId = null, $force = false)
    {
        if ($force === false && $iOwnerUserId == Phpfox::getUserId())
        {
            return true;
        }

        if (isset($bDoNotInsert) || defined('SKIP_NOTIFICATION'))
        {
            return true;
        }

        $aInsert = array(
            'type_id' => $sType,
            'item_id' => $iItemId,
            'user_id' => $iOwnerUserId,
            'owner_user_id' => ($iSenderUserId === null ? Phpfox::getUserId() : $iSenderUserId),
            'time_stamp' => time()
        );

        // Edit code for cloud message.
        $iId = $this->database()->insert(':notification', $aInsert);

        return $iId;
    }
}