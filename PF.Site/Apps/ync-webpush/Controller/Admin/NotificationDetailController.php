<?php

namespace Apps\YNC_WebPush\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class NotificationDetailController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);
        $iId = $this->request()->getInt('id');
        if (!$iId) {
            $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'],
                _p('notification_you_are_looking_for_does_not_exists_or_has_been_removed'));
        }
        $aNotification = Phpfox::getService('yncwebpush.notification')->getNotification($iId, true);
        if (!$aNotification) {
            $this->url()->send('admincp.app', ['id' => 'YNC_WebPush'],
                _p('notification_you_are_looking_for_does_not_exists_or_has_been_removed'));
        }
        $this->template()->setTitle(_p('notification_details'))
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Web Push Notification'), $this->url()->makeUrl('admincp.app', ['id' => 'YNC_WebPush']))
            ->setBreadCrumb(_p('notification_details'), $this->url()->makeUrl('admincp.yncwebpush.notification-detail'))
            ->setHeader([
                'jscript/admin.js' => 'app_ync-webpush',
                'css/admin.css' => 'app_ync-webpush',
            ])
            ->assign([
                'aItem' => $aNotification,
                'sHostName' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'
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