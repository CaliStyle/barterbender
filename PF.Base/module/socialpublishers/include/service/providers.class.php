<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Service_Providers extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = phpfox::getT('socialbridge_services');
    }

	/**
	 * @param string $sService available options: facebook, twitter, linkedin
	 * @return Socialbriget_Service_Provider_Abstract
	 */
    public function getProvider($sService)
    {
        return Phpfox::getService('socialbridge')->getProvider($sService);
    }
	
	/**
	 * @param bool $bDisplay optional default TRUE
	 * @param bool $bPopup optional default FALSE
	 * @return Socialbriget_Service_Provider_Abstract
	 */
    public function getProviders($bDisplay = true, $bPopup = false)
    {
        return Phpfox::getService('socialbridge.providers')->getProviders($bDisplay, $bPopup);
    }
	
	/**
	 * get default provider
	 * @return Socialbriget_Service_Provider_Abstract
	 */
    public function getFromDefaultPhpFox($aDefaultParams = array(), $sProvider = 'facebook')
    {
        switch ($sProvider)
        {
            case 'twitter':
                if (isset($aDefaultParams['consumer_key']) && isset($aDefaultParams['consumer_secret']) && !empty($aDefaultParams['consumer_secret']) && !empty($aDefaultParams['consumer_key']))
                {
                    return $aDefaultParams;
                }
                $sTwitterkAppID = phpfox::getParam('share.twitter_consumer_key');
                $sTwitterSecret = phpfox::getParam('share.twitter_consumer_secret');
                $aParams = array(
                    'consumer_key' => $sTwitterkAppID,
                    'consumer_secret' => $sTwitterSecret,
                );
                return $aParams;
            case 'facebook':
                if (isset($aDefaultParams['app_id']) && isset($aDefaultParams['secret']) && !empty($aDefaultParams['secret']) && !empty($aDefaultParams['app_id']))
                {
                    return $aDefaultParams;
                }
                $sFacebookAppID = phpfox::getParam('facebook.facebook_app_id');
                $sFacebookSecret = phpfox::getParam('facebook.facebook_secret');
                $aParams = array(
                    'app_id' => $sFacebookAppID,
                    'secret' => $sFacebookSecret,
                );
                return $aParams;
            default:
                return $aDefaultParams;
        }
    }
	
	/**
	 * add setting
	 */
    public function addSetting($sService = "", $sParams = "", $iStatus = 0)
    {
        //USING SOCIAL BRIDGE TO ADD & UPDATE PROVIDER SETTINGS
        Phpfox::getService('socialbridge.providers')->addSetting($sService, $sParams, $iStatus);
    }
}