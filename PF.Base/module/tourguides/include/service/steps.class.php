<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class TourGuides_Service_Steps extends Phpfox_Service 
{
    /**
     * Class constructor
     */    
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('tourguides_steps');
        $this->_sAllowHTML = "<br><a><h3><h1><h2><h4><p><i><u><link><embed><b><strong><img>";
    }    
    public function init()
    {
        
    }
    public function updateOrder($aVals)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);
        
        if (!isset($aVals['ordering']))
        {
            return Phpfox_Error::set(_p('tourguides.not_a_valid_request'));
        }
        foreach ($aVals['ordering'] as $iId => $iOrder)
        {
            $this->database()->update($this->_sTable, array('orderring' => (int) $iOrder), 'id = ' . (int) $iId);
        }
      
    } 
    public function addStep($aInsertSteps = array())
    {
        $aInsertSteps['description'] = isset($aInsertSteps['description'])?strip_tags($aInsertSteps['description'],$this->_sAllowHTML):"";
        return $this->database()->insert($this->_sTable,$aInsertSteps);
    }
    public function removeStep($iStepId = 0)
    {
        return $this->database()->delete($this->_sTable,'id = '.(int)$iStepId);
    }
    public function updateStep($iStepId, $aParams)
    {
        return $this->database()->update($this->_sTable,$aParams,'id = '.(int)$iStepId);
    }
    public function removeSteps($iTourGuideId = 0)
    {
        return $this->database()->delete($this->_sTable,'tourguide_id = '.(int)$iTourGuideId);
    }
    public function getSteps($iTourGuideId = 0,$bDisplay = false)
    {        
        $sQuery = "";
        if($bDisplay == true)
        {
            $sQuery = " AND is_active = 1 ";
        }
        $aSteps = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('tourguide_id = '.(int)$iTourGuideId. $sQuery)
                    ->order('orderring ASC')
                    ->execute('getRows');
        $sCurrentLangId = Phpfox::getService('language')->getDefaultLanguage();
        
        foreach($aSteps as $iKey => $aStep)
        {
            if(Phpfox::getLib('parse.format')->isSerialized($aStep['description']))
            {
                $aSteps[$iKey]['description'] = unserialize($aStep['description']);                
                $aSteps[$iKey]['default_description'] = $aSteps[$iKey]['description'][$sCurrentLangId];                
            }
            else
            {
                $aSteps[$iKey]['default_description'] = $aStep['description'];            
            }
        }        
        
        return $aSteps;
    }
    public function getStep($iStep = 0)
    {
        $sCurrentLangId = Phpfox::getService('language')->getDefaultLanguage();
        $aStep = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('id = '.(int)$iStep)
                    ->execute('getRow');
        if(Phpfox::getLib('parse.format')->isSerialized($aStep['description']))
        {
            $aStep['description'] = unserialize($aStep['description']);
            $aStep['default_description'] = $aStep['description'][$sCurrentLangId];            
        }
        else
        {
            $aStep['default_description'] = $aStep['description'];
        }
        return $aStep;
    }
    public function deleteStepsAfter($iStep = 0, $iTourId)
    {
        if($iStep == 0)
        {
            $this->removeSteps($iTourId);
            return true;
        }
        
        $aCurrSteps = $this->database()->select('id')
            ->from($this->_sTable)
            ->where('tourguide_id = '.$iTourId)
            ->order('orderring ASC')
            ->limit(0, $iStep)
            ->execute('getSlaveRows');
            
        $sSteps = '';
        foreach($aCurrSteps as $aStep)
        {
            $sSteps .= $aStep['id'].', '; 
        }
        $sSteps = trim($sSteps, ', ');
        
        $this->database()->delete($this->_sTable, 'tourguide_id = '.$iTourId.' AND id NOT IN ('.$sSteps.')');
        return true;
    }
    public function __call($sMethod, $aArguments)
    {
        if ($sPlugin = Phpfox_Plugin::get('tourguides.service_process__call'))
        {
            return eval($sPlugin);
        }
        
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }    
}

?>