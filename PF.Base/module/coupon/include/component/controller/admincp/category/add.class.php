<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 */
class Coupon_Component_Controller_Admincp_Category_Add extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll();

        if ($iEditId = $this->request()->getInt('id')) {
            if ($aCategory = Phpfox::getService('coupon.category')->getForEdit($iEditId)) {
                $bIsEdit = true;

                $this->template()->setHeader('<script type="text/javascript"> $Behavior.ynjpEditIndustry = function(){ $(function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);}); };</script>')->assign('aForms', $aCategory);
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($bIsEdit) {
                if (Phpfox::getService('coupon.category.process')->update($aCategory['category_id'], $aVals)) {
                    $this->url()->send('admincp.coupon.category.add', array('id' => $aCategory['category_id']), _p('category_successfully_updated'));
                }
            } else {
                if (Phpfox::getService('coupon.category.process')->add($aVals)) {
                    $this->url()->send('admincp.coupon.category.add', null, _p('category_successfully_added'));
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_coupon'), $this->url()->makeUrl('admincp.app').'?id=__module_coupon')
            ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.coupon.category.add'))
            ->assign(array(
                    'sOptions' => Phpfox::getService('coupon.category')->display('option')->get($iEditId),
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
        (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_admincp_category_add_clean')) ? eval($sPlugin) : false);
    }

}

?>