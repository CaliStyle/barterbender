<?php

defined('PHPFOX') or exit('NO DICE!');

require_once(__DIR__ . PHPFOX_DS . 'libs' . PHPFOX_DS . 'facebook.php');
require_once(__DIR__ . PHPFOX_DS . 'libs' . PHPFOX_DS . 'twitter.php');
require_once(__DIR__ . PHPFOX_DS . 'libs' . PHPFOX_DS . 'linkedin.php');

class SocialBridge_Service_Agents extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = phpfox::getT('socialbridge_agents');
    }

    //Add user agents & token
    public function addToken($iUserId = null, $sService = 'facebook', $aParams)
    {
        if ($iUserId == null || !$sService || count($aParams) <= 0) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('socialbridge.service_agents_addtoken_start')) ? eval($sPlugin) : false);

        if (Phpfox::isModule('socialstream')) {
            Phpfox::getService('socialbridge.providers')->updateProviderSetting($sService, $iUserId, 'socialstream', 1);
        }

        (($sPlugin = Phpfox_Plugin::get('socialbridge.service_agents_addtoken_end')) ? eval($sPlugin) : false);

        return true;
    }

    //Get user agent
    public function getToken($iUserId = null, $sService = 'facebook')
    {
        if (!$iUserId || !$sService) {
            return false;
        }
        $aProvider = phpfox::getService('socialbridge.providers')->getProvider($sService, false);

        $aAgent = $this->database()->select('*')->from($this->_sTable)->where('user_id = ' . (int)$iUserId . ' AND service_id= ' . $aProvider['service_id'])->execute('getRow');

        return $aAgent;
    }

    //Get connected agents
    public function getUserConnected($iUserid = null, $iServiceId = null)
    {
        if ($iUserid == null || $iServiceId == null) {
            return false;
        }
        $aAgents = $this->database()->select('*')->from($this->_sTable)->where('service_id = ' . (int)$iServiceId . ' AND user_id = ' . $iUserid)->execute('getRow');
        if (count($aAgents) <= 0) {
            return false;
        }
        if ($aAgents['params'] && phpfox::getLib('parse.format')->isSerialized($aAgents['params'])) {
            $aAgents['params'] = unserialize($aAgents['params']);
        } else {
            $aProvider['params'] = null;
        }
        if ($aAgents['token'] && !empty($aAgents['token'])) {
            $aAgents['token'] = base64_decode($aAgents['token']);
        }

        return $aAgents;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('socialbridge.service_agents__call')) {
            eval($sPlugin);
            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
