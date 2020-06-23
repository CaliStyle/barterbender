<?php

namespace Apps\YNC_WebPush\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class SettingController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        if (($aVals = $this->request()->getArray('val'))) {
            if (Phpfox::getService('yncwebpush.setting.process')->update($aVals)) {
                $this->url()->send('push-notification',
                    ['tab' => empty($aVals['current_tab']) ? '' : $aVals['current_tab']],
                    _p('push_notification_settings_successfully_updated'));
            }
        }
        $aSettings = Phpfox::getService('yncwebpush.setting')->get();
        $aMenus = array(
            'notifications' => _p('push_notifications'),
            'subscribe' => _p('subscription_settings')
        );

        $this->template()->buildPageMenu('js_push_notification_block',
            $aMenus,
            array(
                'no_header_border' => true,
                'link' => $this->url()->makeUrl(Phpfox::getUserBy('user_name')),
                'phrase' => _p('view_your_profile')
            )
        );
        $this->template()->setTitle(_p('push_notification_settings'))
            ->setBreadCrumb(_p('account'), $this->url()->makeUrl('profile'), true)
            ->setBreadCrumb(_p('push_notification_settings'), $this->url()->makeUrl('push-notification'), true)
            ->assign(array(
                'aForms' => $aSettings,
                'aPrivacyNotifications' => $aSettings['notification_setting'],
            ));
    }
}