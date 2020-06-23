<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class CustomFieldController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if ($aOrder = $this->request()->getArray('order')) {
            if (Phpfox::getService('advancedmarketplace.category.process')->updateOrder($aOrder)) {
                $this->url()->send('admincp.advancedmarketplace', null,
                    _p('advancedmarketplace.category_order_successfully_updated'));
            }
        }

        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('advancedmarketplace.category.process')->delete($iDelete)) {
                $this->url()->send('admincp.advancedmarketplace', null,
                    _p('advancedmarketplace.category_successfully_deleted'));
            }
        }

        $this->template()->setTitle(_p('advancedmarketplace.admin_menu_manage_custom_fields'))
            ->setBreadcrumb(_p('advancedmarketplace.admin_menu_manage_custom_fields'),
                $this->url()->makeUrl('admincp.advancedmarketplace.customfield'))
            ->setPhrase(array(
                    'advancedmarketplace.are_you_sure_this_will_delete_all_listings_that_belong_to_this_category_and_cannot_be_undone'
                )
            )
            ->setHeader(array(
                    'jquery/ui.js' => 'static_script',
                    'jscript/admin.js' => 'app_p-advmarketplace',
                    '<script type="text/javascript">$Core.advancedmarketplace.url(\'' . $this->url()->makeUrl('admincp.advancedmarketplace') . '\');</script>'
                )
            )
            ->assign(array(
                    'sCategories' => Phpfox::getService('advancedmarketplace.category')->display('admincp')->get()
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
