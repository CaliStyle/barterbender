<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class AddController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;
        $bSubCategory = false;
        $aLanguages = Phpfox::getService('language')->getAll();
        if ($iEditId = $this->request()->getInt('id')) {
            if ($aCategory = Phpfox::getService('advancedmarketplace.category')->getForEdit($iEditId)) {
                $bIsEdit = true;
                if($aCategory['parent_id'] != 0){
                    $bSubCategory = true;
                }
                $this->template()->setHeader('<script type="text/javascript">$Behavior.initAdd = function() { $(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true); }</script>')->assign('aForms',
                    $aCategory);
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('advancedmarketplace.category.process')->update($aCategory['category_id'],
                        $aVals)) {
                        $this->url()->send('admincp.advancedmarketplace', array('id' => $aCategory['category_id']),
                            _p('advancedmarketplace.category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('advancedmarketplace.category.process')->add($aVals)) {
                        $this->url()->send('admincp.advancedmarketplace', null,
                            _p('advancedmarketplace.category_successfully_added'));
                    }
                }
            }
        }
        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('advancedmarketplace'),
                $this->url()->makeUrl('admincp.app', ['id' => '__module_advancedmarketplace']))
            ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')),
                $this->url()->makeUrl('admincp.advancedmarketplace'))
            ->assign(array(
                    'sOptions' => Phpfox::getService('advancedmarketplace.category')->display('option')->get(),
                    'bIsEdit' => $bIsEdit,
                    'bSubCategory' => $bSubCategory,
                    'aLanguages' => $aLanguages
                )
            );
    }

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
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}
