<?php

namespace Apps\YNC_WebPush\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class ManageNotificationsController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);
        $bIsSearch = false;
        $sCond = '1 = 1';
        $iLimit = 15;
        $aSearch = $this->request()->getArray('val');
        $iPage = $this->request()->getInt('page');

        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('yncwebpush.notification.process')->delete($iDelete)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'], _p('notification_deleted_successfully'));
            } else {
                $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'], _p('delete_failed_please_try_again'));
            }
        }

        if ($iStop = $this->request()->getInt('stop')) {
            if (Phpfox::getService('yncwebpush.notification.process')->stopSend($iStop)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'], _p('notification_stopped_successfully'));
            } else {
                $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'], _p('stopped_failed_please_try_again'));
            }
        }

        if ($iResend = $this->request()->getInt('resend')) {
            if (Phpfox::getService('yncwebpush.notification.process')->resendNotification($iResend)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'], _p('notification_resent_successfully'));
            } else {
                $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'], _p('resend_failed_please_try_again'));
            }
        }

        if ($iSendNow = $this->request()->getInt('now')) {
            if (Phpfox::getService('yncwebpush.notification.process')->sendNow($iSendNow)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'],
                    _p('notification_has_been_sent_successfully'));
            } else {
                $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'], _p('send_now_failed_please_try_again'));
            }
        }

        if ($aIds = $this->request()->getArray('ids')) {
            foreach ($aIds as $iId) {
                Phpfox::getService('yncwebpush.notification.process')->delete($iId);
            }
            $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'], _p('notification_s_deleted_successfully'));
        }
        if (!empty($aSearch['title'])) {
            $bIsSearch = true;
            $sCond .= ' AND n.title LIKE \'%' . $aSearch['title'] . '%\'';
        }
        if (!empty($aSearch['status'])) {
            $bIsSearch = true;
            $sCond .= ' AND n.status = \'' . $aSearch['status'] . '\'';
        }
        if (!empty($aSearch['audience_type'])) {
            $bIsSearch = true;
            $sCond .= ' AND n.audience_type = \'' . $aSearch['audience_type'] . '\'';
        }
        if (isset($aSearch['sent_from_month'], $aSearch['sent_from_day'], $aSearch['sent_from_year'])) {
            $bIsSearch = true;
            $iStart = Phpfox::getLib('date')->mktime(0, 0, 0, $aSearch['sent_from_month'], $aSearch['sent_from_day'],
                $aSearch['sent_from_year']);
            $sCond .= ' AND n.schedule_time >= ' . Phpfox::getLib('date')->convertToGmt($iStart);
        }
        if (isset($aSearch['sent_to_month'], $aSearch['sent_to_day'], $aSearch['sent_to_year'])) {
            $bIsSearch = true;
            $iEnd = Phpfox::getLib('date')->mktime(23, 23, 59, $aSearch['sent_to_month'], $aSearch['sent_to_day'],
                $aSearch['sent_to_year']);
            $sCond .= ' AND n.schedule_time <= ' . Phpfox::getLib('date')->convertToGmt($iEnd);
        }
        $aNotifications = Phpfox::getService('yncwebpush.notification')->getForManage($sCond, $iPage, $iLimit, $iCount);
        Phpfox::getLib('pager')->set([
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $iCount,
        ]);
        if (count($aSearch)) {
            $bIsSearch = true;
            $this->template()->assign([
                'aForms' => $aSearch
            ]);
        }
        if ($bIsSearch || $iPage > 1) {
            $this->template()->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('Web Push Notification'),
                    $this->url()->makeUrl('admincp.yncwebpush.manage-notifications'));
        }
        $this->template()->setTitle(_p('manage_notifications'))
            ->setBreadCrumb(_p('manage_notifications'), $this->url()->makeUrl('admincp.app', ['id' => 'YNC_WebPush']))
            ->assign([
                'aNotifications' => $aNotifications,
                'bIsSearch' => $bIsSearch,
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncwebpush.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}