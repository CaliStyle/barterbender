<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

Phpfox::getService('donation.classhelper')->getAPIClass('donation.include.service.gatewayinterface');

class Donation_Service_Gateways extends Phpfox_Service
{
    private $_aObject = array();
    private $_aAPIs = array();

    /**
     * Loads a specific payment gateway API class
     *
     * @param string $sGateway Gateway API ID
     * @param array $aSettings ARRAY of custom settings to pass along to the gateway class
     * @return object Returns the object of the API gateway class
     */
    public function load($sGateway, $aSettings = null)
    {
        if (!isset($this->_aObject[$sGateway])) {
            $sFilePath = PHPFOX_DIR_MODULE . 'donation' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'api' . PHPFOX_DS . $sGateway . '.class.php';
            $aGatewaySetting = Phpfox::getService('donation.gateway')->getActiveGatewaySetting($sGateway);
            if ($aGatewaySetting) {
                $this->_aObject[$sGateway] = (file_exists($sFilePath) ? Phpfox::getService('donation.classhelper')->getAPI('donation.include.service.api.' . $sGateway) : false);

                if ($aSettings !== null && $this->_aObject[$sGateway] !== false) {
                    $this->_aObject[$sGateway]->set(array_merge($aSettings, $aGatewaySetting));
                }
            } else {
                return false;
            }
        } else if (isset($aSettings['currency_code'])) {
            $this->_aObject[$sGateway]->set($aSettings);
        }

        return $this->_aObject[$sGateway];
    }

    /**
     * Creates the API callback URL for a specific gateway.
     *
     * @param string $sGateway Gateway ID
     * @return string Full path to the callback location for this specific gateway
     */
    public function url($sGateway)
    {
        // Make URL, no matter whether short URL is enabled or not
        $sUrl = Phpfox::getLib('phpfox.url')->makeUrl('donation.gateway.callback', array($sGateway));
        // Disable use of short URLs if enabled
        if (Phpfox::getParam('core.url_rewrite') == 1) {
            Phpfox::getLib('setting')->setParam('core.url_rewrite', 2);
            $sUrl = Phpfox::getLib('phpfox.url')->makeUrl('donation.gateway.callback', array($sGateway));
            Phpfox::getLib('setting')->setParam('core.url_rewrite', 1);
        }

        return $sUrl;

    }
}

?>
