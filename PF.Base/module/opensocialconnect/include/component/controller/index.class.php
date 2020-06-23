<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class OpenSocialConnect_Component_Controller_Index extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $sService = $this->request()->get('service');
        if (empty($sService)) {
            $this->url()->send(Phpfox::getParam('core.path'));
        }
        if ($sService == 'facebook') {
            Phpfox::getService('socialbridge.provider.facebook')->removeTokenData();
        }
        $sUrl = Phpfox::getService('opensocialconnect')->getReturnUrl($sService);
        $this->url()->send($sUrl);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('opensocialconnect.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }

}

?>