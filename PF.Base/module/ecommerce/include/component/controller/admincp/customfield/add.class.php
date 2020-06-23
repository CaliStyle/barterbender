<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Admincp_Customfield_Add extends Phpfox_Component {

    public function process()
    {
        $bIsEditField = false;
        $bIsEditGroup = false;
        $bAddInput = false;

        $aValidation = array(
            'group_name' => array(
                'def' => 'required',
                'title' => _p('group_name_cannot_be_empty')
            ),
        );

        $oValid = Phpfox::getLib('validator')->set(array(
            'sFormName' => 'js_add_group_name_form',
            'aParams' => $aValidation
        ));

        if ($iEditGroupId = $this->request()->getInt('id'))
        {
            if ($aGroup = Phpfox::getService('ecommerce.custom.group')->getGroupForEdit($iEditGroupId))
            {
                $bAddInput = true;
                $bIsEditGroup = true;
                $aCategories = Phpfox::getService('ecommerce.category')->getParentCategory();

                $this->template()->assign(array(
                    'aGroup' => $aGroup,
                    'aCategories' => $aCategories,
                    'bIsEditGroup' => $bIsEditGroup
                ));
            }
        }

        if ($aVals = $this->request()->getArray('val'))
        {
            if ($this->__isValid($aVals))
            {
                if ($bIsEditGroup)
                {
                    if (Phpfox::getService('ecommerce.custom.group')->updateGroup($aGroup['group_id'], $aVals))
                    {
                        $this->url()->send('admincp.ecommerce.customfield.add', array('id' => $aGroup['group_id']), _p('custom_field_groups_successfully_updated'));
                    }
                }
                else
                {
                    if ($iGroupId = Phpfox::getService('ecommerce.custom.group')->addGroup($aVals))
                    {
                        $this->url()->send('admincp.ecommerce.customfield.add', array('id' => $iGroupId), _p('custom_field_groups_successfully_added'));
                    }
                }
            }
        }

        if ($iCustomFieldDelete = $this->request()->getInt('delete'))
        {
            if (Phpfox::getService('ecommerce.custom.process')->delete($iCustomFieldDelete))
            {
                if ($bIsEditGroup)
                {
                    $this->url()->send('admincp.ecommerce.customfield.add', array('id' => $iEditGroupId), _p('custom_field_successfully_deleted'));
                }
            }
        }

        $aLanguages = Phpfox::getService('language')->get();
        if ($bAddInput)
        {
            $aKeys = array_keys($aGroup['group_name']);
            $aVals2 = array_values($aGroup['group_name']);
        }
        foreach ($aLanguages as $iKey => $aLanguage)
        {
            if ($bAddInput && isset($aKeys[0]))
            {
                $aLanguages[$iKey]['phrase_var_name'] = $aKeys[0];
            }
            $mPost = '';
            if ($bAddInput && isset($aVals2[0]))
            {
                foreach ($aVals2[0] as $keyaVals2 => $valueaVals2)
                {
                    if ($aLanguages[$iKey]['language_id'] == $keyaVals2)
                    {
                        $mPost = $valueaVals2;
                    }
                }
            }
            $aLanguages[$iKey]['post_value'] = $mPost;
        }

        $this->template()->setTitle($bIsEditGroup ? _p('edit_custom_field_groups') : _p('add_custom_field_groups'))
                ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('module_ecommerce'), $this->url()->makeUrl('admincp.app').'?id=__module_ecommerce')
                ->setBreadcrumb($bIsEditGroup ? _p('edit_custom_field_groups') : _p('add_custom_field_groups'))
                ->assign(array(
                    'sCorePath' => Phpfox::getParam('core.path'),
                    'aLanguages' => $aLanguages,
                    'sUrl' => $this->url()->makeUrl('admincp.ecommerce.customfield.add'),
                        // 'sCreateJs'   => $oValid -> createJS(), 
                        )
                )
                ->setPhrase(
                        array(
                            'ecommerce.edit_custom_field',
                            'ecommerce.are_you_sure',
                            'ecommerce.set_to_active',
                            'ecommerce.yes',
                            'ecommerce.no'
                ))
                ->setHeader(array(
                    'jquery/ui.js' => 'static_script',
                    'admin.js' => 'module_ecommerce',
                    'ynecommerce_custom_field_admin.js' => 'module_ecommerce',
                    '<script type="text/javascript">$Behavior.setURLEcommerce = function() { $Core.ecommerceCustomFieldAdmin.url(\'' . $this->url()->makeUrl('admincp.ecommerce.customfield.add') . '\'); } </script>'
        ));
    }

    private function __isValid($aVals)
    {
        $emptyGroupName = false;
        $group_name = $aVals['group_name'];
        foreach ($group_name as $keygroup_name => $valuegroup_name)
        {
            if (is_array($valuegroup_name))
            {
                foreach ($valuegroup_name as $keyvaluegroup_name => $valuevaluegroup_name)
                {
                    if (strlen(trim($valuevaluegroup_name)) == 0)
                    {
                        $emptyGroupName = true;
                        break;
                    }
                }
            }
            else
            {
                if (strlen(trim($valuegroup_name)) == 0)
                {
                    $emptyGroupName = true;
                    break;
                }
            }
        }
        if ($emptyGroupName)
        {
            Phpfox_Error::set(_p('group_name_cannot_be_empty'));
            return false;
        }

        return true;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        
    }

}

?>