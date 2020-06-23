<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Resume_Service_Custom_Process extends Phpfox_Service 
{    
    private $_aOptions = array();
    private $_sTableOption;
    private $_sTableValue;
    /**
     * Used to control when to add another feed
     * @var array of boolean 
     */
    private $_aFeedsAdded = array();
    /**
     * Class constructor
     */    
    public function __construct()
    {    
        $this->_sTable = Phpfox::getT('resume_custom_field');
        $this->_sTableOption = Phpfox::getT('resume_custom_option');
        $this->_sTableValue = Phpfox::getT('resume_custom_value');
    }
    
    /**
     * Adds a new custom field, the options must come in this structure
     *  array(
     *    option = array(
     *        # => array(
     *        <language_id> => array(
     *            text => option text
     * @param array $aVals
     * @return mixed
     */
    public function add($aVals)
    {
        $this->_aOptions = array();
        
        // Prepare the name of the custom field
        $sVarName = '';
        $sDefaultLanguageCode = Phpfox::getService('language')->getDefaultLanguage();
        if (!isset($aVals['name'][$sDefaultLanguageCode]) || empty($aVals['name'][$sDefaultLanguageCode])) {
            return Phpfox_Error::set(_p('custom.provide_a_name_for_the_custom_field'));
        }

        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage) {
            if (empty($aVals['name'][$aLanguage['language_code']])) {
                $aVals['name'][$aLanguage['language_code']] = $aVals['name'][$sDefaultLanguageCode];
            }
        }

        $sVarName = 'resume_custom_field_' . md5($sDefaultLanguageCode . time());
        \Core\Lib::phrase()->addPhrase($sVarName, $aVals['name']);

        $sFieldName = substr(Phpfox::getService('language.phrase.process')->prepare($aVals['name'][$sDefaultLanguageCode]), 0, 20);

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
            }
            
            if (!$iTotalOptions)
            {
                return Phpfox_Error::set(_p('custom.you_have_selected_that_this_field_is_a_select_custom_field_which_requires_at_least_one_option'));
            }
        }
       
        $iCustomFieldCount = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('resume_custom_field'))
            ->where('phrase_var_name = \'' . $this->database()->escape('core.' . $sVarName) . '\'')
            ->execute('getField');
        
        while(1)
		{
			$tmpsVarName = $sVarName . ($iCustomFieldCount + 1);
			$tmpiCustomFieldCount = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('resume_custom_field'))
            ->where('phrase_var_name = \'' . $this->database()->escape('core.' . $tmpsVarName) . '\'')
            ->execute('getField');
			if($tmpiCustomFieldCount > 0){
				$iCustomFieldCount++;
			}
			else {
				break;
			}
		}
		
        if ($iCustomFieldCount > 0)
        {
            $sVarName = $sVarName . ($iCustomFieldCount + 1);
            $sFieldName = $sFieldName . ($iCustomFieldCount + 1);            
        }
        
        $aSql = array(
            'field_name' => $sFieldName,
            'phrase_var_name' => 'core.' . $sVarName,
            'type_name' => $sTypeName,
            'var_type' => $aVals['var_type'],
            'is_required' => (isset($aVals['is_required']) ? (int) $aVals['is_required'] : 0)
        );
        
        // Insert into DB
        $iFieldId = $this->database()->insert($this->_sTable, $aSql);        
        if ($bAddToOptions && !empty($aVals['option']) && is_array($aVals['option']))
        {
            $this->_addOptions($iFieldId, $aVals);
        }

        \Core\Lib::phrase()->clearCache();

        return array(
            $iFieldId,
            $this->_aOptions
        );
    }
    
    public function toggleActivity($iId)
    {
        
        $aField = $this->database()->select('field_id, is_active')
            ->from($this->_sTable)
            ->where('field_id = ' . (int) $iId)
            ->execute('getSlaveRow');
            
        if (!isset($aField['field_id']))
        {
            return Phpfox_Error::set(_p('custom.unable_to_find_the_custom_field'));
        }
        
        $this->database()->update($this->_sTable, array('is_active' => ($aField['is_active'] ? 0 : 1)), 'field_id = ' . $aField['field_id']);
        
        $this->cache()->remove();
        
        return true;
    }

    /**
     * @param $iId
     * @param $aVals
     * @return bool
     */
    public function update($iId, $aVals)
    {

        $bAddToOptions = false;
        switch ($aVals['var_type'])
        {
            case 'select':
            case 'radio':
            case 'multiselect':
            case 'checkbox':
                $bAddToOptions = true;
                break;
            default:
                break;
        }

        $sDefaultLanguageCode = Phpfox::getService('language')->getDefaultLanguage();
        if (!isset($aVals['name'][$sDefaultLanguageCode]) || empty($aVals['name'][$sDefaultLanguageCode])) {
            return Phpfox_Error::set(_p('provide_a_message_for_the_package'));
        }

        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage) {
            if (empty($aVals['name'][$aLanguage['language_code']])) {
                $aVals['name'][$aLanguage['language_code']] = $aVals['name'][$sDefaultLanguageCode];
            }
        }

        $aField = Phpfox::getService('resume.custom')->getForCustomEdit($iId);
        $aPhraseVarName = explode('.', $aField['phrase_var_name']);
        $sPhraseVarName = !empty($aPhraseVarName) && $aPhraseVarName[0] == 'core' && !empty($aPhraseVarName[1]) ? $aPhraseVarName[1] : '';
        $bAlreadyPhrase = true;

        foreach ($aLanguages as $aLanguage) {
            $iPhraseId = Phpfox::getLib('database')->select('phrase_id')
                ->from(':language_phrase')
                ->where('language_id="' . $aLanguage['language_id'] . '" AND var_name="' . $sPhraseVarName . '"' )
                ->executeField();
            if ($iPhraseId) {
                Phpfox::getService('language.phrase.process')->update($iPhraseId, $aVals['name'][$aLanguage['language_id']]);
            } else {
                $bAlreadyPhrase = false;
                break;
            }
        }
        if (!$bAlreadyPhrase) {
            $sVarName = 'resume_custom_field_' . md5($sDefaultLanguageCode . time());
            \Core\Lib::phrase()->addPhrase($sVarName, $aVals['name']);
        }

        if ($bAddToOptions && !empty($aVals['option']) && is_array($aVals['option']) && !empty($aVals['current']) && is_array($aVals['current']))
        {
            $iTotalOptions = 0;
            $aAllOptions = array_merge($aVals['option'],$aVals['current']);
            foreach ($aAllOptions as $aOption)
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
            }

            if (!$iTotalOptions)
            {
                return Phpfox_Error::set(_p('custom.you_have_selected_that_this_field_is_a_select_custom_field_which_requires_at_least_one_option'));
            }
        }

        $aVals['field_id'] = $iId; // used in addOptions

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
                    else
                    {
                        $aPhraseVarName = explode('.', $sKey);
                        if(!empty($aPhraseVarName[1]))
                        {
                            Phpfox::getService('language.phrase.process')->add(array(
                                'var_name' => $aPhraseVarName[1],
                                'module' => 'resume|resume',
                                'product_id' => 'younet_resume4',
                                'text' => array($sLang => $aValue['text'])
                            ));
                        }
                    }
                }
            }
        }

        if (($aVals['var_type'] == 'select' || $aVals['var_type'] == 'radio' || $aVals['var_type'] == 'checkbox' || $aVals['var_type'] == 'multiselect') && !empty($aVals['option']) && is_array($aVals['option']))
        {
            $this->_addOptions($iId, $aVals);
        }

        $this->database()->update($this->_sTable, array('is_required' => $aVals['is_required']), 'field_id = '.(int)$iId);

        $this->cache()->remove();

        return true;
    }
    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder)
        {
            $this->database()->update($this->_sTable, array('ordering' => (int) $iOrder), 'field_id = ' . (int) $iId);
        }
        return true;
    }
    
    public function delete($iId)
    { 
        
        $aField = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('field_id = ' . (int) $iId)
            ->execute('getRow');
            
        if (!isset($aField['field_id']))
        {
            return Phpfox_Error::set(_p('custom.unable_to_find_the_custom_field_you_want_to_delete'));
        }
        
        list($sModule, $sPhrase) = explode('.', $aField['phrase_var_name']);
        
        $this->database()->delete(Phpfox::getT('language_phrase'), 'module_id = \'' . $sModule . '\' AND var_name = \'' . $sPhrase . '\'');
        
        $aOptions = $this->database()->select('*')
            ->from(Phpfox::getT('resume_custom_option'))
            ->where('field_id = ' . $aField['field_id'])
            ->execute('getRows');        
            
        foreach ($aOptions as $aOption)
        {
            list($sModule, $sPhrase) = explode('.', $aOption['phrase_var_name']);
    
            $this->database()->delete(Phpfox::getT('language_phrase'), 'module_id = \'' . $sModule . '\' AND var_name = \'' . $sPhrase . '\'');
        }
        
        $this->database()->delete(Phpfox::getT('resume_custom_option'), 'field_id = ' . $aField['field_id']);
        
        // Also delete the values associated with deleted field
        $this->database()->delete(Phpfox::getT('resume_custom_value'),'field_id = ' . $aField['field_id']);
        $this->database()->delete($this->_sTable, 'field_id = ' . $aField['field_id']);
        $this->cache()->remove();
        
        return true;    
    }

	public function updateScoreByFieldId($field_name, $score){
		$aRow = $this->database()->select('*')->from($this->_sTable)->where('field_name = "'.$field_name.'"')->execute('getRow');
		if(isset($aRow['field_id']))
		{
			$this->database()->	update($this->_sTable,array('score'=>$score),'field_id = '.$aRow['field_id']);	
		}
		
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
        if ($sPlugin = Phpfox_Plugin::get('resume.service_process__call'))
        {
            return eval($sPlugin);
        }
            
        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
    
    private function _addOptions($iFieldId, &$aVals)
    {
        foreach ($aVals['option'] as $iKey => $aOptions)
        {
            if (isset($aVals['option'][$iKey]['added']) && $aVals['option'][$iKey]['added'] == true)
            {
                continue;
            }
            $aOptionsAdded = array();
            $aExisting = array();
            $iSeqNumber = in_array($iKey, $aExisting) ? (max($aExisting) + 1) : $iKey;
            $aExisting[] = $iSeqNumber;
            foreach ($aOptions as $sLang => $aOption)
            {
                if (empty($aOption['text'])) 
                {
                    continue;
                }
                
                $sPhraseVar = 'cf_option_' . $iFieldId . '_' . $iSeqNumber;
                
                Phpfox::getService('language.phrase.process')->add(array(
                        'var_name' => $sPhraseVar,
                        'module' => 'resume|resume',
                        'product_id' => 'younet_resume4',
                        'text' => array($sLang => $aOption['text'])
                    ));
                
                // Only add one option per language
                if (!in_array($iKey, $aOptionsAdded))
                {
                    $this->_aOptions[$iKey . $sLang] = $this->database()->insert(Phpfox::getT('resume_custom_option'), array(
                        'field_id' => $iFieldId,
                        'phrase_var_name' => 'resume.' .$sPhraseVar
                    )
                    );
                    $aOptionsAdded[] = $iKey;
                }                
            }
            $aVals['option'][$iKey]['added'] = true;
        }
        
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
        }

        Phpfox::getService('language.phrase.process')->delete($aOption['phrase_var_name']);
        $this->database()->delete($this->_sTableOption, 'option_id = ' . $iId);
        $this->database()->delete($this->_sTableValue, 'field_id = ' . $aOption['field_id']);

        return true;
    }
}

?>
