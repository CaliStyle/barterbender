<?php

defined('PHPFOX') or exit('NO DICE!');

class SocialBridge_Component_Controller_Setting extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $oRequest = $this->request();
        $iUserId = Phpfox::getUserId();
        $oService = Phpfox::getService('socialbridge');
        $sTab = $oRequest->get('tab', '');

        if ($sDisconnectService = $oRequest->get('disconnect')) {
            if (Phpfox::isModule('socialstream')) {
                if (Phpfox::getLib('database')->tableExists(Phpfox::getT('socialstream_setting'))) {
                    Phpfox::getLib('database')->delete(Phpfox::getT('socialstream_setting'),
                        'user_id = ' . (int)$iUserId . ' AND service = \'' . $sDisconnectService . '\'');
                }
            }
            $oService->removeTokenData($sDisconnectService, $iUserId);
            $this->url()->send('socialbridge.setting', null);
        }

        $aProviders = $oService->getAllProviderData($iUserId);
        (($sPlugin = Phpfox_Plugin::get('socialbridge.component_controller_setting_process_supported_modules')) ? eval($sPlugin) : false);

        if (count($aProviders)) {
            $aMenus = array('connections' => _p('socialbridge.connections'));
            (($sPlugin = Phpfox_Plugin::get('socialbridge.component_controller_setting_process')) ? eval($sPlugin) : false);

            $this->template()->buildPageMenu('js_setting_block', $aMenus, array(
                'no_header_border' => true,
                'link' => '#',
                'phrase' => _p('socialbridge.view_your_settings')
            ));
        }

        $this->template()->setTitle(_p('socialbridge.manage_social_accounts'))
            ->setBreadcrumb(_p('socialbridge.manage_social_accounts'))
            ->assign(array(
                'aProviders' => $aProviders,
                'sCoreUrl' => Phpfox::getService('socialbridge')->getStaticPath(),
                'sTab' => $sTab,
            ));
        $this->template()->setHeader('cache', array(
            'socialbridge.js' => 'module_socialbridge'
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('socialbridge.component_controller_setting_clean')) ? eval($sPlugin) : false);
    }
}
