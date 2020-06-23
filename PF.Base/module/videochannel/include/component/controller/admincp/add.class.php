<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Admincp_Add extends Admincp_Component_Controller_App_Index
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll();

        if ($iEditId = $this->request()->getInt('id')) {
            if ($aCategory = Phpfox::getService('videochannel.category')->getForEdit($iEditId)) {
                $bIsEdit = true;

                $this->template()->setHeader('<script type="text/javascript"> $Behavior.ynvcEditCategory = function(){ $(function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);}); };</script>')->assign('aForms', $aCategory);
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            if(Phpfox::getService('language')->validateInput($aVals, 'name', false)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('videochannel.category.process')->update($aCategory['category_id'], $aVals)) {
                        $this->url()->send('admincp.videochannel', array('sub' => $aCategory['parent_id']), _p('category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('videochannel.category.process')->add($aVals)) {
                        $this->url()->send('admincp.videochannel', array('sub' => $aVals['parent_id']), _p('category_successfully_added'));
                    }
                }
            }
        }
        $selectBox = Phpfox::getService('videochannel.multicat')->getSelectBox(array('id' => '', 'name' => 'val[parent_id]', 'class' => 'form-control'), $aCategory ? $aCategory['parent_id'] : null, null, null);
        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_videochannel'), $this->url()->makeUrl('admincp.app').'?id=__module_videochannel')
            ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.videochannel.add'))
            ->assign(array(
                    'selectBox' => $selectBox,
                    'bIsEdit' => $bIsEdit,
                    'aLanguages' => $aLanguages
                )
            );
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}

?>
