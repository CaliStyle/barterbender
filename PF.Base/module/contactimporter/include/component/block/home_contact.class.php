<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Block_Home_contact extends Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::isModule('socialbridge')) {
            return false;
        }
        $top_5_email = phpfox::getService('contactimporter')->getTopProviders();
        $settings = phpfox::getService('socialbridge.providers')->getProvider('facebook');
        $this->template()->assign(array(
            'user_id' => Phpfox::getUserId(),
            'fbAIP' => !empty($settings['params']['app_id']) ? $settings['params']['app_id'] : "",
            'icon_size' => phpfox::getService('contactimporter')->getIconSize(),
            'top_5_email' => $top_5_email,
            'more_path' => Phpfox::getLib('url')->makeUrl('contactimporter'),
            'core_url' => Phpfox::getParam('core.path'),
            'sHeader' => _p('homepage_contact'),
            'sDeleteBlock' => 'dashboard',
            'contactimporter.js' => 'module_contactimporter',
            'facebookInviteLink' => Phpfox::getLib('url')->makeUrl('contactimporter.inviteuser', array('id' => Phpfox::getUserId())),
        ));

        return 'block';
    }
}
