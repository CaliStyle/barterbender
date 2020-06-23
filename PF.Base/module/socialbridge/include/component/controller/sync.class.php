<?php
defined('PHPFOX') or exit('NO DICE!');

class SocialBridge_Component_Controller_Sync extends Phpfox_Component
{
    public function process()
    {
        phpfox::isUser(true);
        $oRequest = $this->request();
        $sService = $oRequest->get('service');
        $sCallbackUrl = phpfox::getLib('url')->makeUrl('socialbridge.setting');
        $sUrl = Phpfox::getService('socialbridge')->getAuthUrl($sService, $sCallbackUrl);
        $this->url()->send($sUrl);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('socialbridge.component_controller_sync_clean')) ? eval($sPlugin) : false);
    }
}
