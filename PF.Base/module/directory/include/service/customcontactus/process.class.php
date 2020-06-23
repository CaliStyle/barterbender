<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Service_CustomContactUs_Process extends Phpfox_service
{
    private $_aOptions = array();
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('directory_contactuscustomfield');
        $this->_sTableOption = Phpfox::getT('directory_contactuscustomoption');
    }
    
    /**
     * Adds a new custom field, the options must come in this structure
     *  array(
     *    option = array(
     *        # => array(
     *        <language_id> => array(
     *            text => option text
     * @param $aVals
     * @return array
     */
    public function add($aVals,$iContactUsId)
    {
        $this->_aOptions = array();
        $sModuleId = 'core';
        $sProductId = 'phpfox';
        
        // Prepare the name of the custom field
        $sVarName = '';
        foreach ($aVals['name'] as $iId => $aText)
        {
            if (empty($aText['text']))
            {
                continue;
            }
            
            $sVarName = Phpfox::getService('language.phrase.process')->prepare($aText['text']);
            
            break;
        }    
        
        if (empty($sVarName))
        {
            return Phpfox_Error::set(_p('custom.provide_a_name_for_the_custom_field'));
        }
        
        $sFieldName = substr($sVarName, 0, 20);
        $sVarName = 'directory_' . $sVarName;
        
        $bAddToOptions = false;
        switch ($aVals['var_type'])
        {
            case 'select':
            case 'radio':
                $sTypeName = 'VARCHAR(150)';
                $sValueTypeName = 'SMALLINT(5)';
                $bAddToOptions = true;
                break;
            case 'multiselect':
            case 'checkbox':
                $sTypeName = 'MEDIUMTEXT';
                $sValueTypeName = 'MEDIUMTEXT';
                $bAddToOptions = true;
                break;
            case 'text':
                $sTypeName = 'VARCHAR(255)';
                $sValueTypeName = 'VARCHAR(255)';        
                break;
            case 'textarea':
                $sTypeName = 'MEDIUMTEXT';
                $sValueTypeName = 'MEDIUMTEXT';
                break;
            default:
                return Phpfox_Error::set(_p('custom.not_a_valid_type_of_custom_field'));
                break;
        }        
        
        if ($bAddToOptions && !empty($aVals['option']) && is_array($aVals['option']))
        {
            $iTotalOptions = 0;
            foreach ($aVals['option'] as $aOption)
            {
                foreach ($aOption as $aLanguage)
                {                    
                    if (isset($aLanguage['text']) && !empty($aLanguage['text']))
                    {
                        $iTotalOptions++;
                        // there may be more languages, counting them would give an incorrect number of options
                        break; 
                    }
                }
                $aValues = array_values($aOption);
            }
            
            if (!$iTotalOptions)
            {
                return Phpfox_Error::set(_p('custom.you_have_selected_that_this_field_is_a_select_custom_field_which_requires_at_least_one_option'));
            }
        }
        
        $iCustomFieldCount = $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('phrase_var_name = \'' . $this->database()->escape($sModuleId . '.' . $sVarName) . '\'')
            ->execute('getField');
        
        if ($iCustomFieldCount > 0)
        {
            $sVarName = $sVarName . ($iCustomFieldCount + 1);
            $sFieldName = $sFieldName . ($iCustomFieldCount + 1);            
        }
        
        $aSql = array(
            'contactus_id'  => $iContactUsId,
            'field_name' => $sFieldName,
            'phrase_var_name' => $sModuleId . '.' . $sVarName,
            'type_name' => $sTypeName,
            'var_type' => $aVals['var_type'],
            'is_active' => 1,
            'is_required' => (isset($aVals['is_required']) ? 1 : 0),
        );
        
        // Insert into DB
        $iFieldId = $this->database()->insert($this->_sTable, $aSql);        
        if ($bAddToOptions && !empty($aVals['option']) && is_array($aVals['option']))
        {
            $this->_addOptions($iFieldId, $aVals);
        }
            
        // Add the new phrase
        if (!Phpfox::getService('language.phrase')->isValid($sModuleId . '.' . $sVarName))
        {
            foreach ($aVals['name'] as $sLang => $aName)
            {
               Phpfox::getService('language.phrase.process')->add(array(
                    'var_name' => '.'.$sVarName,
                    'module' => $sModuleId . '|' . $sModuleId,
                    'product_id' => $sProductId,
                    'text' => array($sLang => $aName['text'])
                ), true
                ); 
            }
            
        }
        
        $this->cache()->remove();
        
        return array(
            $iFieldId,
            $this->_aOptions
        );
    }
    
    public function updateActiveCustomField($iId, $aVals){

        $aFields = $this->database()->select('cf.*')
            ->from($this->_sTable, 'cf')
            ->where('cf.contactus_id = '.(int)$iId)
            ->group('cf.field_id')
            ->order('cf.ordering ASC')
            ->execute('getSlaveRows');

/*        echo '<pre>';
        print_r($aFields);
        echo '<pre>';
        print_r($aVals);
        die;*/
        if(is_array($aFields) && count($aFields))
        {
            foreach($aFields as $k=>$aField)
            {
                if(in_array($aField['field_id'], array_keys($aVals))){
                    $this->database()->update(
                                               $this->_sTable,
                                               array('is_active' => (int)1),
                                              'field_id = ' . $aField['field_id']
                                             );      
    
                }
                else{
                    $this->database()->update(
                           $this->_sTable,
                           array('is_active' => (int)0),
                          'field_id = ' . $aField['field_id']
                         );   
                }
            }
        }

    }
    /**/
    public function update($iId, $aVals)
    {        
        $aVals['field_id'] = $iId; // used in addOptions
        
        // $sKey == the language phrase
        foreach ($aVals['name'] as $sKey => $aPhrases)
        {
            foreach ($aPhrases as $sLang => $aValue)
            {
                if($aValue['text'] == ''){
                    return false;
                }
                
                if (Phpfox::getService('language.phrase')->isValid($sKey, $sLang))
                {
                    Phpfox::getService('language.phrase.process')->updateVarName($sLang, $sKey, $aValue['text']);                    
                }
            }
        }
        
        if (isset($aVals['current']))
        {
            // $sKey == the language phrase
            foreach ($aVals['current'] as $sKey => $aPhrases)
            {
                if (strpos($sKey,'.') === false)
                {
                    continue;
                }
                foreach ($aPhrases as $sLang => $aValue)
                {
                    if (Phpfox::getService('language.phrase')->isValid($sKey, $sLang))
                    {
                        Phpfox::getService('language.phrase.process')->updateVarName($sLang, $sKey, $aValue['text']);
                    }
                }
            }            
        }
        
        if (($aVals['var_type'] == 'select' || $aVals['var_type'] == 'radio' || $aVals['var_type'] == 'checkbox' || $aVals['var_type'] == 'multiselect') && !empty($aVals['option']) && is_array($aVals['option']))
        {
            $this->_addOptions($iId, $aVals);
        }
        
        $this->database()->update($this->_sTable, array('is_required' => isset($aVals['is_required']) ? 1 : 0), 'field_id = '.(int)$iId);
        
        $this->cache()->remove();
        
        return true;
    }
    
    /**
     * Delete custom field
     * @param int $iId
     */
    public function delete($iId)
    {
        $aField = $this->database()->select('*')->from($this->_sTable)->where('field_id = '.(int)$iId)->execute('getRow');
        if (!isset($aField['field_id']))
        {
            return Phpfox_Error::set(_p('directory.unable_to_find_the_custom_field_you_want_to_delete'));
        }
        
        list($sModule, $sPhrase) = explode('.', $aField['phrase_var_name']);
        $this->database()->delete(Phpfox::getT('language_phrase'), 'module_id = \'' . $sModule . '\' AND var_name = \'' . $sPhrase . '\'');        
        
        $aOptions = $this->database()->select('*')->from($this->_sTableOption)->where('field_id = '.$iId)->execute('getRows');        
        foreach ($aOptions as $aOption)
        {
            list($sModule, $sPhrase) = explode('.', $aOption['phrase_var_name']);
            $this->database()->delete(Phpfox::getT('language_phrase'), 'module_id = \'' . $sModule . '\' AND var_name = \'' . $sPhrase . '\'');
        }
        
        $this->database()->delete($this->_sTableOption, 'field_id = ' . $iId);
        $this->database()->delete($this->_sTable, 'field_id = ' . $iId);
        
        $this->cache()->remove();
        
        return true;    
    }
    
    /**
     * Delete custom option
     * @param int $iId
     */
    public function deleteOption($iId)
    {
        $aOption = $this->database()->select('co.*, cf.field_name, cf.var_type, cf.field_id')
            ->from($this->_sTableOption, 'co')
            ->join($this->_sTable, 'cf', 'cf.field_id = co.field_id')
            ->where('co.option_id = '.(int)$iId)
            ->execute('getRow');    
                
        if (!isset($aOption['option_id']))
        {
            return Phpfox_Error::set(_p('directory.unable_to_find_the_custom_option_you_plan_on_deleting'));
        }
        
        Phpfox::getService('language.phrase.process')->delete($aOption['phrase_var_name']);
        $this->database()->delete($this->_sTableOption, 'option_id = ' . $iId);
        
        return true;
    }
    
 
    private function _addOptions($iFieldId, &$aVals)
    {
        $sModuleId = 'core';
        $sProductId = 'phpfox';
        
        // it adds a new language phrase and the var_name is in the form "cf_option_" + <field_id> + <seq_number>
        // but the sequence number may overlap an existing option, so we need to make sure this value is unique
        $aExisting = array();
        if (isset($aVals['current']))
        {
            foreach ($aVals['current'] as $sVarName => $aVal)
            $aExisting[] = str_replace('core.directory_cf_option_' . $aVals['field_id'] . '_','',$sVarName);
        }
        
        foreach ($aVals['option'] as $iKey => $aOptions)
        {
            if (isset($aVals['option'][$iKey]['added']) && $aVals['option'][$iKey]['added'] == true)
            {
                continue;
            }
            $aOptionsAdded = array();
            $iSeqNumber = in_array($iKey, $aExisting) ? (max($aExisting) + 1) : $iKey;
            $aExisting[] = $iSeqNumber;
            foreach ($aOptions as $sLang => $aOption)
            {
                if (empty($aOption['text'])) 
                {
                    continue;
                }
                
                $sPhraseVar = 'directory_cf_option_' . $iFieldId . '_' . $iSeqNumber;
                
                Phpfox::getService('language.phrase.process')->add(array(
                        'var_name' => $sPhraseVar,//'cf_option_' . Phpfox::getService('language.phrase.process')->prepare($aOption['text']),//$sOptionVarName . '_feed',                    
                        'module' => $sModuleId .'|'. $sModuleId,
                        'product_id' => $sProductId,
                        'text' => array($sLang => $aOption['text'])
                    ));
                
                // Only add one option per language
                if (!in_array($iKey, $aOptionsAdded))
                {
                    $this->_aOptions[$iKey . $sLang] = $this->database()->insert($this->_sTableOption, array(
                        'field_id' => $iFieldId,
                        'phrase_var_name' => $sModuleId . '.' .$sPhraseVar
                    )
                    );
                    $aOptionsAdded[] = $iKey;
                }
            }
            $aVals['option'][$iKey]['added'] = true;
        }
        
        return true;                        
    }

    public function updateOrder($aVals)
    {
        $aFields = $this->database()->select('field_id, field_name')
            ->from($this->_sTable)
            ->where('field_id IN ('. implode(',',array_keys($aVals)) .')')
            ->execute('getSlaveRows');

        $this->database()->update(Phpfox::getT('block'), array('ordering' => 1),    'component = "info" AND m_connection="profile.info" AND module_id = "profile"');
        foreach ($aVals as $iId => $iOrder)
        {
            $this->database()->update($this->_sTable, array('ordering' => (int) $iOrder), 'field_id = ' . (int) $iId);
        }
        
        $this->cache()->remove('custom_field', 'substr');
        
        return true;
    }
    

}
