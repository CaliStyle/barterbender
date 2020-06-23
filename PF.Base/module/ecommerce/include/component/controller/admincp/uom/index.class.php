<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Admincp_Uom_Index extends Phpfox_Component
{

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('ecommerce.uom.process')->delete($iDelete)) {
                $this->url()->send('admincp.ecommerce.uom', null, _p('uom_successfully_deleted'));
            }
        }

        $this->template()->setTitle(_p('admincp_manage_uom'))->setBreadCrumb(_p("Apps"),
                $this->url()->makeUrl('admincp.apps'))->setBreadCrumb(_p('module_ecommerce'),
                $this->url()->makeUrl('admincp.app') . '?id=__module_ecommerce')->setBreadcrumb(_p('admincp_manage_uom'),
                $this->url()->makeUrl('admincp.ecommerce.uom'))->setPhrase(array(
                'ecommerce.are_you_sure_this_will_delete_all_products_that_belong_to_this_uom_and_cannot_be_undone',
                'ecommerce.are_you_sure',
                'ecommerce.yes',
                'ecommerce.no',
            ))->setHeader(array(
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'ecommerce.uomOrdering\'}); }</script>',
                    'magnific-popup.css' => 'module_ecommerce',
                ))->assign(array(
                    'sCategories' => Phpfox::getService('ecommerce.uom')->get(),
                    'aCategories' => Phpfox::getService('ecommerce.uom')->getAll()
                ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

    }

}
