<?php

namespace Apps\YNC_WebPush\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class RequestBannerBlock extends Phpfox_Component
{
    public function process()
    {
        if (Phpfox::isAdminPanel()) {
            return false;
        }
        $sToken = $this->getParam('token');
        $sBrowser = $this->getParam('browser');
        $sCookie = Phpfox::getCookie('ync_web_push_' . Phpfox::getUserId());
        $iLimitSkip = setting('yncwebpush_skip_times_to_stop_request_banner', 3);
        if (!empty($sCookie)) {
            $aCookie = json_decode($sCookie, true);
            if ($aCookie['skipped'] >= $iLimitSkip) {
                return false;
            }
        }
        if (!empty($sToken)) {
            //Update this token for currently user
            if ($sToken != 'false') {
                Phpfox::getService('yncwebpush.token.process')->addUserToken($sToken, $sBrowser);
            }
            return false;
        }

        if (Phpfox::isUser()) {
            $sMessage = setting('yncwebpush_text_of_banner_for_user');
        } else {
            $sMessage = setting('yncwebpush_text_of_banner_for_guest');
        }
        $this->template()->assign([
            'sMessage' => $sMessage,
            'iUserId' => Phpfox::getUserId()
        ]);
        return 'block';
    }
}