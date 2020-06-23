<?php

namespace Apps\YNC_WebPush\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class SendPushNotificationController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);

        $bIsCompose = $bIsEdit = false;
        $iTemplateId = $this->request()->getInt('template', 0);
        $bPopup = false;
        if ($iEditId = $this->request()->get('edit_id')) {
            $bIsCompose = $bIsEdit = true;
            $aEditItem = Phpfox::getService('yncwebpush.notification')->getForEdit($iEditId);
            $this->setParam('aEditItem', $aEditItem);
            $this->template()->assign([
                'aForms' => $aEditItem,
                'iEditId' => $iEditId
            ]);
        }
        if ($sInstanceSent = $this->request()->get('send_to')) {
            $bPopup = true;
            $aInstanceSent = explode(',', $sInstanceSent);
            if (is_array($aInstanceSent) && count($aInstanceSent)) {
                if ($aInstanceSent[0] == 'all') {
                    $bIsCompose = true;
                    $this->template()->assign([
                        'sType' => 'all',
                        'sTypeId' => '',
                    ]);
                } elseif (count(array_filter($aInstanceSent, 'is_numeric')) == count($aInstanceSent)) {
                    $bIsCompose = true;
                    //Send to specific subscribers
                    $this->template()->assign([
                        'sType' => 'subscriber',
                        'sTypeId' => $sInstanceSent,
                    ]);
                }
            }
        }
        if ($aVals = $this->request()->getArray('val')) {
            if (isset($aVals['select_audience'])) {
                //Forward to next step
                $sType = $aVals['audience_type'];
                if ($sType == 'group') {
                    $sTypeId = $aVals['audience_id'];
                } elseif ($sType == 'browser') {
                    $sTypeId = $aVals['audience_title'];
                } else {
                    $sTypeId = '';
                }
                $bIsCompose = true;
                $this->template()->assign([
                    'sType' => $sType,
                    'sTypeId' => $sTypeId,
                ]);
            } else {
                $bIsCompose = true;
                $iTemplateId = $aVals['template_id'];
                $this->template()->assign([
                    'sType' => $aVals['audience_type'],
                    'sTypeId' => $aVals['audience'],
                    'iSchedule' => isset($aVals['is_schedule']) ? $aVals['is_schedule'] : 0
                ]);
                if ($bIsEdit) {
                    $aVals['notification_id'] = $iEditId;
                    list($iId, $bSchedule) = Phpfox::getService('yncwebpush.notification.process')->add($aVals, true);
                    if ($iId) {
                        $this->url()->send('admincp.yncwebpush.notification-detail', ['id' => $iEditId],
                            $bSchedule ? _p('notification_has_been_scheduled_successfully') : _p('notification_has_been_sent_successfully'));
                    }
                } else {
                    list($iId, $bSchedule) = Phpfox::getService('yncwebpush.notification.process')->add($aVals);
                    if ($iId) {
                        $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'],
                            $bSchedule ? _p('notification_has_been_scheduled_successfully') : _p('notification_has_been_sent_successfully'));
                    }
                }
            }
        }
        $this->template()->setTitle($bIsCompose ? ($bIsEdit ? _p('edit_push_notification') : _p('send_push_notification')) : _p('select_audience'))
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Web Push Notification'), $this->url()->makeUrl('admincp.app', ['id' => 'YNC_WebPush']))
            ->setBreadCrumb($bIsEdit ? _p('edit_push_notification') : _p('send_push_notification'),
                $this->url()->makeUrl('admincp.yncwebpush.send-push-notification'))
            ->setHeader([
                'jscript/admin.js' => 'app_ync-webpush',
                'css/admin.css' => 'app_ync-webpush',
            ]);

        $this->template()->assign([
            'aUserGroups' => Phpfox::getService('user.group')->get(),
            'bIsCompose' => $bIsCompose,
            'iTemplateId' => $iTemplateId,
            'bIsEdit' => $bIsEdit,
            'bPopup' => $bPopup
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncwebpush.component_controller_admincp_send_push_notification_clean')) ? eval($sPlugin) : false);
    }
}