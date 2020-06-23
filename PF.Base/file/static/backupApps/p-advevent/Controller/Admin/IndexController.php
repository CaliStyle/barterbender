<?php
namespace Apps\P_AdvEvent\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class IndexController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bSubCategory = false;
        $aParent = [];
        if (($iId = $this->request()->getInt('sub')))
        {
            $bSubCategory = true;
            $aParent = Phpfox::getService('fevent.category')->getForEdit($iId);
        }

        if ($iDelete = $this->request()->getInt('delete'))
        {

            if (Phpfox::getService('fevent.category.process')->delete($iDelete))
            {
                $this->url()->send('admincp.fevent', null, _p('category_successfully_deleted'));
            }

        }

        $aCategories = ($bSubCategory ? Phpfox::getService('fevent.category')->getForAdmin($iId) : Phpfox::getService('fevent.category')->getForAdmin());

        $this->template()->setTitle(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(_p('manage_categories') . (($bSubCategory && isset($aParent['name'])) ? ': '. _p($aParent['name']) : ''))
            ->setHeader(array(
                    'jscript/admin.js' => 'app_p-advevent',
                    'drag.js' => 'static_script',
                )
            )
            ->assign(array(
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => $aCategories
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}