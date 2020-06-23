<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
class Document_Component_Controller_Admincp_Add extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {

        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll();

        if ($iEditId = $this->request()->getInt('id')) {
            if ($aCategory = Phpfox::getService('document.category')->getForEdit($iEditId)) {
                $bIsEdit = true;

                $this->template()->setHeader('<script type="text/javascript"> $Behavior.ynjpEditIndustry = function(){ $(function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);}); };</script>')->assign('aForms', $aCategory);
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('document.category.process')->update($aCategory['category_id'], $aVals)) {
                        $this->url()->send('admincp.document.add', array('id' => $aCategory['category_id']), _p('category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('document.category.process')->add($aVals)) {
                        $this->url()->send('admincp.document.add', null, _p('category_successfully_added'));
                    }
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.document.add'))
            ->assign(array(
                    'sOptions' => Phpfox::getService('document.category')->display('option')->get(),
                    'bIsEdit' => $bIsEdit,
                    'aLanguages' => $aLanguages
                )
            );
    }

    /**
     * validate input value
     * @param $aVals
     *
     * @return bool
     */
    private function _validate($aVals)
    {
        return Phpfox::getService('language')->validateInput($aVals, 'name', false);
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}

