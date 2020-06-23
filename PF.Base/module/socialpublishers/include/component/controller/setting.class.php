<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Component_Controller_setting extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if(Phpfox::isModule('socialbridge'))
        {
            $this->url()->send('socialbridge.setting', null);
        }        
        $this->url()->send('', null);        
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('socialpublishers.component_controller_setting_clean')) ? eval($sPlugin) : false);
    }

}
?>