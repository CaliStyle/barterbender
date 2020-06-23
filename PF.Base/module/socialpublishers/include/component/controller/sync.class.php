<?php

defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Component_Controller_Sync extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        phpfox::isUser(true);
		
        $bRedirect = $this->request()->get('redirect');
		
        $sService = $this->request()->get('service');
		
        $sUrlRedirect = phpfox::getLib('url')->makeUrl('socialpublishers.setting');
		
        $sUrl = phpfox::getService('socialbridge')->getAuthUrl($sService, $sUrlRedirect, $bRedirect);

		$this->url()->send($sUrl);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('socialpublishers.component_controller_sync_clean')) ? eval($sPlugin) : false);
    }

}