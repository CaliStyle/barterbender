<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

require_once(__DIR__ . PHPFOX_DS . 'libs' . PHPFOX_DS . 'facebook.php');

class SocialBridge_Service_Libs extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = phpfox::getT('socialbridge_services');
    }

    public function timeline()
    {
        return Phpfox::getService('socialbridge')->timeline();
    }

    public function getFBAccessToken()
    {
        $aProvider = phpfox::getService('socialbridge.providers')->getProvider('facebook');
        $aConfig = $aProvider['params'];
        $oFacebook = new FacebookSBYN($aConfig);

        return $oFacebook->getUserAccessToken();
    }
}
