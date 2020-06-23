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
class Fevent_Component_Controller_Admincp_Custom_Add extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {        
        $bHideOptions = true;
        $iDefaultSelect = 4;
        $bIsEdit = false;
        
        Phpfox::getUserParam('fevent.can_add_custom_fields', true);
        $this->template()->assign(array('aForms' => array()));
            
        $aFieldValidation = array(
            'category_id' => _p('select_a_category_this_custom_field_will_belong_to'),
            'var_type' => _p('select_what_type_of_custom_field_this_is')
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
            $aField = Phpfox::getService('fevent.custom')->getForCustomEdit($iId);
            if (isset($aField['field_id']))
            {                
                $bIsEdit = true;
                $aForms = $aField;
                
                if (isset($aField['option']) && $aField['var_type'] == 'select')
                {
                    $bHideOptions = false;                
                }
            }
        }

        if (($aVals = $this->request()->getArray('val')))
        {
            if ($oCustomValidator->isValid($aVals))
            {

                if(isset($aVals['field_id'])){  
                    if (Phpfox::getService('fevent.custom.process')->update($aVals['field_id'],$aVals))
                    {
                        $this->url()->send('admincp.fevent.custom', null, _p('field_successfully_updated'));
                    }
                }
                else{
                    if (Phpfox::getService('fevent.custom.process')->add($aVals))
                    {
                        $this->url()->send('admincp.fevent.custom.add', null, _p('field_successfully_added'));
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
                        $sLang = str_replace("-", "_", $sLang);
                        $sOptionPostJs .= 'option_' . $iKey . '_' . $sLang . ': \'' . str_replace("'", "\'", $mValue) . '\',';    
                    }
                }
                $sOptionPostJs = rtrim($sOptionPostJs, ',');
                $iDefaultSelect = $iCnt;        
            }
        }
        
        $oMulticat = Phpfox::getService('fevent.multicat');
        $css = array('id'=>'', 'name'=>'val[category_id]', 'class'=>'category_id');
        
        if($aVals = $this->request()->getArray('val')) {
            $sOptions = $oMulticat->getSelectBox($css, $aVals['category_id'], null, null);
        } else {
            $sOptions = $oMulticat->getSelectBox($css, null, null, null);
        }

        if($bIsEdit){
/*            print_r($aForms);
            die;*/
            $sOptions = $oMulticat->getSelectBox($css, $aForms['category_id'], null, null);
        }
        
        if($bIsEdit){
            $sTitle = _p('update_a_custom_field');
            $sAction =   $this->url()->makeUrl('admincp.fevent.custom.edit',array('id' => $iId));     
        }
        else{
            $sTitle = _p('add_a_new_custom_field');
            $sAction =   $this->url()->makeUrl('admincp.fevent.custom.add');     
        }

        $this->template()->setTitle($sTitle)
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_fevent"), $this->url()->makeUrl('admincp.app',['id' => '__module_fevent']))
            ->setBreadcrumb($sTitle, $sAction)
            ->setPhrase(array(
                    'are_you_sure_you_want_to_delete_this_custom_option'
                )
            )
            ->setHeader(array(
                    '<script type="text/javascript"> var bIsEdit = false; </script>',
                    'admin.js' => 'module_custom',
                    '<script type="text/javascript">$Behavior.feventAdminCustomAdd = function(){$Core.custom.init(' . $iDefaultSelect . '' . (isset($sOptionPostJs) ? ', {' . $sOptionPostJs . '}' : '') . ');}</script>'
                )
            )
            ->assign(array(
                    'aLanguages' => Phpfox::getService('language')->getAll(),
                    'bHideOptions' => $bHideOptions,
                    'sOptions' => $sOptions,
                    'iDefaultSelect' => $iDefaultSelect,
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
/*echo '<pre>';
print_r($aForms);
die;*/
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
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_admincp_custom_add_clean')) ? eval($sPlugin) : false);
    }
}

?>