<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class OpenSocialConnect_Service_Providers extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('socialconnect_services');
    }

    public function viewLoginHeader()
    {
        Phpfox::getBlock('opensocialconnect.viewloginheader', array());
    }

    public function viewLoginHeaderVisitor()
    {
        Phpfox::getBlock('opensocialconnect.viewloginheadervisitor', array());
    }

    public function getProvider($sService = "")
    {
        if ($sService == "") {
            return false;
        }
        if ($sService == 'flickr2') {
            $sService = 'flickr';
        }

        $aProvider = $this->database()->select('*')->from($this->_sTable)->where("name = '" . $this->database()->escape($sService) . "'")->execute('getRow');

        return $aProvider;
    }

    public function getOpenProviders($iLimitSelected = 20, $bDisplay = true)
    {
        $sCond = ($bDisplay == true) ? 'is_active = 1' : '';

        $aProviders = $this->database()->select('*')->from($this->_sTable)->where($sCond)->order('ordering ASC')->limit($iLimitSelected)->execute('getRows');

        return $aProviders;
    }

    public function updateOrder($aVals)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        if (!isset($aVals['ordering'])) {
            return Phpfox_Error::set(_p('opensocialconnect.not_a_valid_request'));
        }
        foreach ($aVals['ordering'] as $iId => $iOrder) {
            $this->database()->update($this->_sTable, array('ordering' => (int)$iOrder), 'service_id = ' . (int)$iId);
        }
    }

    public function updateActivity($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        $this->database()->update($this->_sTable, array('is_active' => (int)($iType == '1' ? 1 : 0)),
            'service_id = ' . (int)$iId);
    }

    /**
     * @param string $sService , string $sType
     * @author AnNT
     */
    public function updateStatistics($sService, $sType)
    {
        if ($sService == 'flickr2') {
            $sService = 'flickr';
        }

        return $this->database()->update($this->_sTable, array('total_' . $sType => 'total_' . $sType . ' + 1'),
            'name = "' . $sService . '"', false);
    }

    /**
     * @return array
     * @author AnNT
     */
    public function getStatistics()
    {
        return $this->database()->select('service_id, name, title, total_signup, total_sync, total_login')->from($this->_sTable)->order('ordering')->execute('getSlaveRows');
    }

}

?>
