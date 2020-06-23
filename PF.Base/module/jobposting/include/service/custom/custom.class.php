<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright        [YOUNET_COPPYRIGHT]
 * @author          VuDP, AnNT
 * @package          Module_jobposting
 */

class JobPosting_Service_Custom_Custom extends Phpfox_service
{
    private $_aHasOption = array('select', 'radio', 'multiselect', 'checkbox');
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('jobposting_custom_field');
        $this->_sTableOption = Phpfox::getT('jobposting_custom_option');
        $this->_sTableValue = Phpfox::getT('jobposting_custom_value');
    }

    public function buildHtmlForReview($aFields = array())
    {
        $sHtml = '';
        
        $aType = array(
            'textarea' => _p('large_text_area'),
            'text' => _p('small_text_area_255_characters_max'),
            'select' => _p('selection'),
            'multiselect' => _p('core.multiple_selection'),
            'radio' => _p('core.radio'),
            'checkbox' => _p('core.checkbox'),
        );

        $sImageDelete = Phpfox::getLib('image.helper')->display(array(
                'theme' => 'misc/delete.png',
                'return_url' => true,
            )
        );
        $sImageEdit = Phpfox::getLib('image.helper')->display(array(
                'theme' => 'misc/page_edit.png',
                'return_url' => true,
            )
        );

        if(is_array($aFields) && count($aFields))
        {
            foreach($aFields as $k=>$aField)
            {
                $sHtml .= '<div class="ynjp_customField_holder" id="js_custom_field_'.$aField['field_id'].'"><div class="ynjp_customField_left"><div class="ynjp_customField_title">';
                if($aField['is_required'])
                {
                    $sHtml .= '<span class="required">*</span>';
                }
                $sHtml .= '<span>'._p($aField['phrase_var_name']).'</span></div>';
                $sHtml .= '<div class="ynjp_customField_control_holder"><a href="#" onclick="tb_show(\''._p('edit_field_question').'\', $.ajaxBox(\'jobposting.controllerAddField\', \'height=300&width=300&action=edit&id='.$aField['field_id'].'\')); return false;" class="ynjp_customField_control_edit" title="'._p('edit').'">'.$sImageEdit.'</a>';
                $sHtml .= '<a href="#" onclick="if(confirm(\''._p('core.are_you_sure').'\')) $.ajaxCall(\'jobposting.deleteField\', \'id='.$aField['field_id'].'\'); return false;" class="ynjp_customField_control_delete" title="'._p('delete').'">'.$sImageDelete.'</a></div>';
                $sHtml .= '</div>';
				$sHtml .= '<p class="extra_info">'._p('type').': '.$aType[$aField['var_type']].'</p>';
                if(in_array($aField['var_type'], $this->_aHasOption))
                {
                    $sHtml .= '<div class="ynjp_customField_right">';
                    if(!empty($aField['option']))
                    {
                        $iNo = 0;
                        foreach($aField['option'] as $k2=>$sOption)
                        {
                            $sHtml .= _p('option').' '.(++$iNo).': '._p($sOption).'<br />';
                        }
                    }
                    else
                    {
                        $sHtml .= _p('this_field_will_be_hidden_until_it_has_at_least_one_option');
                    }
                    $sHtml .= '</div>';
                }
                $sHtml .= '</div>';
            }
        }
                
        return $sHtml;
    }
    
    /**
     * @param int $iCompanyId
     * @return array
     */
    public function getByCompanyId($iCompanyId, $iObjType = null)
    {
    	$where = 'company_id = '.(int)$iCompanyId; 
		
		if(!empty($iObjType))
			$where .= ' AND type = '.(int)$iObjType;
		
		 $aFields = $this->database()->select('*')
            ->from($this->_sTable)
            ->where($where)
            ->order('ordering ASC')
			->execute('getSlaveRows');
        
        if(is_array($aFields) && count($aFields))
        {
            foreach($aFields as $k=>$aField)
            {
                if(in_array($aField['var_type'], $this->_aHasOption))
                {
					$aOptions = $this->database()->select('*')->from($this->_sTableOption)->where('field_id = '.$aField['field_id'])->order('option_id ASC')->execute('getSlaveRows');
                    if(is_array($aOptions) && count($aOptions))
                    {
                        foreach($aOptions as $k2=>$aOption)
                        {
                            $aFields[$k]['option'][$aOption['option_id']] = $aOption['phrase_var_name'];
                        }
                    }
                }
				
            }
        }
        return $aFields;
    }
    
    public function getByApplicationId($iApplicationId, $type = NULL)
    {
    	if(is_null($type))
		{
			$where = 'cf.type IS NULL AND cv.application_id = '.$iApplicationId;
		}
		else{
			$where = 'cf.type = '.$type.' AND cv.application_id = '.$iApplicationId;
		}
		
        $aFields = $this->database()->select('cf.*, cv.value')
            ->from($this->_sTable, 'cf')
            ->leftjoin($this->_sTableValue, 'cv', 'cv.field_id = cf.field_id')
            ->where($where)
            ->group('cf.field_id')
            ->order('cf.field_id ASC')
            ->execute('getSlaveRows');
        
        if(is_array($aFields) && count($aFields))
        {
            foreach($aFields as $k=>$aField)
            {
                if(in_array($aField['var_type'], $this->_aHasOption))
                {
                    $aOptions = $this->database()->select('co.*')
                        ->from($this->_sTableOption, 'co')
                        ->leftjoin($this->_sTableValue, 'cv', 'cv.option_id = co.option_id')
                        ->where('co.field_id = '.$aField['field_id'].' AND cv.application_id = '.$iApplicationId)
                        ->order('co.option_id ASC')
                        ->execute('getSlaveRows');
                    
                    if(is_array($aOptions) && count($aOptions))
                    {
                        foreach($aOptions as $k2=>$aOption)
                        {
                            $aFields[$k]['value'][$aOption['option_id']] = $aOption['phrase_var_name'];
                        }
                    }
                }
            }
        }
        
        return $aFields;
    }

	public function getByObjectId($iObject, $type = NULL, $isJob = false)
    {
    	
        $aFields = $this->database()->select('cf.*, cv.value')
            ->from($this->_sTable, 'cf')
            ->leftjoin($this->_sTableValue, 'cv', 'cv.field_id = cf.field_id AND cv.application_id = '.$iObject. ($isJob ? ' AND cv.type = 2' : ''))
            ->where('cf.type = '.$type. ($isJob ? ' AND cf.company_id = 0' : ''))
            ->group('cf.field_id')
            ->order('cf.ordering ASC')
            ->execute('getSlaveRows');
        
        if(is_array($aFields) && count($aFields))
        {
            foreach($aFields as $k=>$aField)
            {
                if(in_array($aField['var_type'], $this->_aHasOption))
                {
                    //get all option of specific field.
                    $aOptions = $this->database()->select('*')->from($this->_sTableOption)->where('field_id = '.$aField['field_id'])->order('option_id ASC')->execute('getSlaveRows');
                    if(is_array($aOptions) && count($aOptions))
                    {
                        foreach($aOptions as $k2=>$aOption)
                        {
                            $aFields[$k]['option'][$aOption['option_id']] = $aOption['phrase_var_name'];
                        }
                    }

                    //get default option of specifice field belong to specific.
                    $aOptions = $this->database()->select('co.*')
                        ->from($this->_sTableOption, 'co')
                        ->leftJoin($this->_sTableValue, 'cv', 'cv.option_id = co.option_id')
                        ->where('co.field_id = '.$aField['field_id'].' AND cv.application_id = '.$iObject)
                        ->order('co.option_id ASC')
                        ->execute('getSlaveRows');
                    
                    if(is_array($aOptions) && count($aOptions))
                    {
                        foreach($aOptions as $k2=>$aOption)
                        {
                            $aFields[$k]['value'][$aOption['option_id']] = $aOption['phrase_var_name'];
                        }
                    }
                }
            }
        }
        
        return $aFields;
    }
	
            
    public function getForCustomEdit($iId)
    {
        $aField = $this->database()->select('*')->from($this->_sTable)->where('field_id = '.(int)$iId)->execute('getRow');
        
        list($sModule, $sVarName) = explode('.', $aField['phrase_var_name']);
        
        // Get the name of the field in every language
        $aPhrases = $this->database()->select('language_id, text')
            ->from(Phpfox::getT('language_phrase'))
            ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
            ->execute('getSlaveRows');
        
        foreach ($aPhrases as $aPhrase)
        {
            $aField['name'][$aField['phrase_var_name']][$aPhrase['language_id']] = $aPhrase['text'];
        }
        
        if ($aField['var_type'] == 'select' || $aField['var_type'] == 'multiselect' || $aField['var_type'] == 'radio' || $aField['var_type'] == 'checkbox')
        {
            $aOptions = $this->database()->select('option_id, field_id, phrase_var_name')
                ->from($this->_sTableOption)
                ->where('field_id = ' . $aField['field_id'])
                ->order('option_id ASC')
                ->execute('getSlaveRows');
            
            foreach ($aOptions as $iKey => $aOption)
            {
                list($sModule, $sVarName) = explode('.', $aOption['phrase_var_name']);

                $aPhrases = $this->database()->select('language_id, text, var_name')
                    ->from(Phpfox::getT('language_phrase'))
                    ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
                    ->execute('getSlaveRows');
                
                foreach ($aPhrases as $aPhrase)
                {
                    if (!isset($aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']]))
                    {
                        $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']] = array();
                    }
                    $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']]['text'] = $aPhrase['text'];
                }
            }
        }
        return $aField;
    }

	public function updateOrder($aVals)
	{
		foreach ($aVals as $iId => $iOrder)
		{
			$this->database()->update($this->_sTable, array('ordering' => $iOrder), 'field_id = ' . (int) $iId);
		}
		
		$this->cache()->remove('jobposting', 'substr');
		
		return true;
	}

}