<?php

namespace Apps\YNC_WebPush\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;

class SubscribersNotificationBlock extends Phpfox_Component
{
    public function process()
    {
        if (Phpfox::isAdminPanel()) {
            return false;
        }
        $iId = $this->getParam('id', 0);
        if (!$iId) {
            return false;
        }
        $aUsers = Phpfox::getService('yncwebpush.notification')->getSubscribersOfNotification($iId);
        if (!$aUsers) {
            Phpfox_Error::set(_p('no_subscribers_found'));
        }
        $this->template()->assign([
            'aUsers' => $aUsers
        ]);

        return 'block';
    }
}