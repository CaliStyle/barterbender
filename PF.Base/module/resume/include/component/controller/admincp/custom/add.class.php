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
class Resume_Component_Controller_Admincp_Custom_Add extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {        
        $bHideOptions = true;
        $iDefaultSelect = 4;
        $bIsEdit = false;
        $this->template()->assign(array('aForms' => array()));
            
        $aFieldValidation = array(
            'var_type' => _p('resume.select_what_type_of_custom_field_this_is')
        );
        
        $oCustomValidator = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'js_custom_field', 
                'aParams' => $aFieldValidation,
                'bParent' => true
            )
        );        
        
        $this->template()->assign(array(
                'sCustomCreateJs' => $oCustomValidator->createJS(),
                'sCustomGetJsForm' => $oCustomValidator->getJsForm()    
            )
        );
        /*prepare info for edit*/
        if($iId = $this->request()->get('id')){
            $aField = Phpfox::getService('resume.custom')->getForCustomEdit($iId);
            if (isset($aField['field_id']))
            {
                $bIsEdit = true;
                $aForms = $aField;

                if (isset($aField['option']) && $aField['var_type'] == 'select')
                {
                    $bHideOptions = false;
                }
                $aPhraseVarName = explode('.', $aField['phrase_var_name']);
                $this->template()->assign([
                    'sPhraseTitle' => (!empty($aPhraseVarName) && $aPhraseVarName[0] == 'core' && !empty($aPhraseVarName[1]) ? $aPhraseVarName[1] : '')
                ]);
            }
        }
        if (($aVals = $this->request()->getArray('val')))
        {
            if ($oCustomValidator->isValid($aVals))
            {
                if(isset($aVals['field_id'])) {
                    if (Phpfox::getService('resume.custom.process')->update($aVals['field_id'], $aVals)) {
                        $this->url()->send('admincp.resume.custom', null, _p('field_successfully_updated'));
                    }
                }
                else {
                    if (Phpfox::getService('resume.custom.process')->add($aVals)) {
                        $this->url()->send('admincp.resume.custom.add', null, _p('field_successfully_added'));
                    }
                }
            }
            
            if (isset($aVals['var_type']) && $aVals['var_type'] == 'select')
            {
                $bHideOptions = false;
                $iCnt = 0;
                $sOptionPostJs = '';
                foreach ($aVals['option'] as $iKey => $aOptions)
                {
                    if (!$iKey)
                    {
                        continue;
                    }
                    
                    $aValues = array_values($aOptions);
                    if (!empty($aValues[0]))
                    {
                        $iCnt++;
                    }
                    
                    foreach ($aOptions as $sLang => $mValue)
                    {
                        $sOptionPostJs .= 'option_' . $iKey . '_' . $sLang . ': \'' . str_replace("'", "\'", $mValue) . '\',';    
                    }
                }
                $sOptionPostJs = rtrim($sOptionPostJs, ',');
                $iDefaultSelect = $iCnt;        
            }
        }
        if($bIsEdit){
            $sTitle = _p('update_a_custom_field');
            $sAction =   $this->url()->makeUrl('admincp.resume.custom.add',array('id' => $iId));
        }
        else{
            $sTitle = _p('add_a_new_custom_field');
            $sAction =   $this->url()->makeUrl('admincp.resume.custom.add');
        }
        $this->template()->setTitle(_p('resume.add_a_new_custom_field'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
            ->setBreadcrumb($sTitle, $sAction)
            ->setPhrase(array(
                    'resume.are_you_sure_you_want_to_delete_this_custom_option'
                )
            )
            ->setHeader(array(
                    '<script type="text/javascript"> var bIsEdit = false; </script>',
                    'admin.js' => 'module_custom',
                    'custom.js' => 'module_resume',
                    '<script type="text/javascript">$Behavior.resumeAdminCustomAdd = function(){$Core.custom.init(' . $iDefaultSelect . '' . (isset($sOptionPostJs) ? ', {' . $sOptionPostJs . '}' : '') . ');}</script>'
                )
            )
            ->assign(array(
                    'aLanguages' => Phpfox::getService('language')->getAll(),
                    'bHideOptions' => $bHideOptions,
                    'bIsEdit' => $bIsEdit,
                    'iId' => isset($iId) ? $iId : '',
                )
            );
        if($bIsEdit){
            $sTypeCustomFieldText = _p('custom.large_text_area');
            $sTypeCustomField = 'textarea';

            switch ($aForms['var_type'])
            {
                case 'select':
                    $sTypeCustomFieldText = _p('custom.selection');
                    $sTypeCustomField = 'select';
                    break;
                case 'radio':
                    $sTypeCustomFieldText = _p('core.radio');
                    $sTypeCustomField = 'radio';
                    break;
                case 'multiselect':
                    $sTypeCustomFieldText = _p('core.multiple_selection');
                    $sTypeCustomField = 'multiselect';
                    break;
                case 'checkbox':
                    $sTypeCustomFieldText = _p('core.checkbox');
                    $sTypeCustomField = 'checkbox';
                    break;
                case 'text':
                    $sTypeCustomFieldText = _p('custom.small_text_area_255_characters_max');
                    $sTypeCustomField = 'text';
                    break;
                case 'textarea':
                    $sTypeCustomFieldText = _p('custom.large_text_area');
                    $sTypeCustomField = 'textarea';
                    break;
            }
            $this->template()->assign(array(
                'aForms' => $aForms,
                'sTypeCustomFieldText' =>$sTypeCustomFieldText,
                'sTypeCustomField' =>$sTypeCustomField
            ));
        }
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('resume.component_controller_admincp_custom_add_clean')) ? eval($sPlugin) : false);
    }
}

?>