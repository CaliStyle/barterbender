<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

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
        if (($iId = $this->request()->getInt('sub'))) {
            $bSubCategory = true;
        }
        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('advancedmarketplace.category.process')->delete($iDelete)) {
                $this->url()->send('admincp.advancedmarketplace', null, _p('category_successfully_deleted'));
            }
        }
        $aCategories = ($bSubCategory ? Phpfox::getService('advancedmarketplace.category')->getForAdmin($iId) : Phpfox::getService('advancedmarketplace.category')->getForAdmin());
        $this->template()->setTitle(_p('advancedmarketplace.manage_categories'))
            ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('advancedmarketplace'),
                $this->url()->makeUrl('admincp.app', ['id' => '__module_advancedmarketplace']))
            ->setBreadCrumb(_p('advancedmarketplace.manage_categories'),
                $this->url()->makeUrl('admincp.advancedmarketplace'))
            ->setHeader('cache', array(
                    'jquery/ui.js' => 'static_script',
                    'jscript/admin.js' => 'app_p-advmarketplace',
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'advancedmarketplace.categoryOrdering\'}); }</script>'
                )
            )
            ->setPhrase(array(
                    'advancedmarketplace.admin_menu_manage_custom_fields'
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
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}
