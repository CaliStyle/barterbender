<?php

namespace Apps\YNC_WebPush\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class MarkReadNotificationController extends Phpfox_Component
{
    public function process()
    {
        $iId = $this->request()->getInt('notification_id');
        if (!$iId) {
            return false;
        }
        Phpfox::getService('notification.process')->markAsRead($iId);
        Phpfox::getService('yncwebpush.notification.process')->updateSeenNotification($iId);
        echo json_encode([
            'result' => 1
        ]);
        exit;
    }
}