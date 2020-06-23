<?php
namespace Apps\YNC_WebPush\Service;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;

class Callback extends Phpfox_Service
{
    public function getNotificationAdmin_Push($aRow)
    {
        $aNotification = Phpfox::getService('yncwebpush.notification')->getNotification($aRow['item_id']);
        if (!$aNotification) {
            return false;
        }
        return array(
            'message' => $aNotification['title'],
            'link' => $aNotification['redirect_url'],
        );
    }
}