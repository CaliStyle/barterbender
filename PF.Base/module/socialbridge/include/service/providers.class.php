<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class SocialBridge_Service_Providers extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = phpfox::getT('socialbridge_services');
    }

    public function updateProviderSetting($sService = null, $iUserId, $sModuleId, $iIsActive)
    {
        if (!$iUserId || $sService == null) {
            return array();
        }

        $aRow = $this->database()->select('*')->from(Phpfox::getT('socialbridge_services_setting'),
            'sbss')->join(Phpfox::getT('socialbridge_services'), 'sbs',
            'sbs.service_id = sbss.service_id')->where('sbs.name LIKE "' . $sService . '" AND sbss.user_id = ' . (int)$iUserId . ' AND sbss.module_id = "' . $sModuleId . '"')->execute('getRow');

        if (empty($aRow)) {
            $iServiceId = (int)$this->database()->select('service_id')->from(Phpfox::getT('socialbridge_services'))->where("name = '" . $sService . "'")->execute('getField');

            $aInsert = array(
                'user_id' => (int)$iUserId,
                'service_id' => $iServiceId,
                'module_id' => $sModuleId,
                'is_active' => (int)$iIsActive
            );

            if ($this->database()->insert(Phpfox::getT('socialbridge_services_setting'), $aInsert)) {
                return true;
            }
        } else {
            $iServiceId = (int)$aRow['service_id'];
            if ($this->database()->update(Phpfox::getT('socialbridge_services_setting'),
                array('is_active' => $iIsActive),
                'user_id = ' . (int)$iUserId . ' AND service_id = ' . $iServiceId . ' AND module_id = "' . $sModuleId . '"')) {
                return true;
            }
        }

        return false;
    }

    public function getProvider($sService = "", $bActive = false, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('socialbridge.service_libs_getprovider_start')) ? eval($sPlugin) : false);
        if ($sService == "") {
            return false;
        }
        if ($bActive == true && $iUserId != null) {
            $aProvider = $this->database()->select('sbs.*')->from($this->_sTable,
                'sbs')->join(Phpfox::getT('socialbridge_services_setting'), 'sbss',
                'sbs.service_id = sbss.service_id')->where("sbss.user_id = " . (int)$iUserId . " AND sbss.is_active = 1 AND sbs.name = '" . $this->database()->escape($sService) . "'")->execute('getRow');
        } elseif ($bActive == true) {
            $aProvider = $this->database()->select('sbs.*')->from($this->_sTable,
                'sbs')->where("sbs.is_active = 1 AND sbs.name = '" . $this->database()->escape($sService) . "'")->execute('getRow');
        } else {
            $aProvider = $this->database()->select('sbs.*')->from($this->_sTable,
                'sbs')->where("sbs.name = '" . $this->database()->escape($sService) . "'")->execute('getRow');
        }

        if (isset($aProvider['params']) && phpfox::getLib('parse.format')->isSerialized($aProvider['params'])) {
            $aProvider['params'] = unserialize($aProvider['params']);
        }
        (($sPlugin = Phpfox_Plugin::get('socialbridge.service_libs_getprovider_end')) ? eval($sPlugin) : false);

        return $aProvider;
    }

    //Get providers setting
    public function getProviders($bDisplay = true, $bPopup = false)
    {
        $sQuery = "";
        if ($bDisplay == true) {
            $sQuery = "sb.is_active = 1";
        }

        (($sPlugin = Phpfox_Plugin::get('socialbridge.service_libs_getprovider_start')) ? eval($sPlugin) : false);

        $aProviders = $this->database()->select('*')->from(Phpfox::getT('socialbridge_services'),
            'sb')->where($sQuery)->order('ordering ASC')->execute('getRows');

        foreach ($aProviders as $iKey => $aProvider) {
            if ($aProviders[$iKey]['params'] != "" && phpfox::getLib('parse.format')->isSerialized($aProviders[$iKey]['params'])) {
                $aParams = unserialize($aProviders[$iKey]['params']);
            } else {
                $aProviders[$iKey]['params'] = $aParams = null;
            }

            $aProviders[$iKey]['params'] = $aParams;
            if ($bDisplay == true) {
                $aAgent = phpfox::getService('socialbridge.agents')->getUserConnected(Phpfox::getUserId(),
                    $aProvider['service_id']);
                if ($aAgent) {
                    $aProviders[$iKey]['Agent'] = $aAgent;
                }
            }
        }

        (($sPlugin = Phpfox_Plugin::get('socialbridge.service_libs_getprovider_end')) ? eval($sPlugin) : false);

        return $aProviders;
    }

    public function addSetting($sService = "", $sParams = "", $bUpdateStatus = false, $iStatus = 0)
    {
        if ($sService == "") {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('socialbridge.service_libs_addsetting_start')) ? eval($sPlugin) : false);

        $aUpdate = [
            'params' => $sParams,
        ];

        if($bUpdateStatus)
        {
            $aUpdate['is_active'] = $iStatus;
        }

        $this->database()->update($this->_sTable, $aUpdate, 'name ="' . $sService . '"');
    }

    public function activeProvider($iServiceId, $iStatus)
    {
        $this->database()->update($this->_sTable, array(
            'is_active' => $iStatus
        ), 'service_id ="' . $iServiceId . '"');
    }
}
