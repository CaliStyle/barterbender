<?php
namespace Apps\YNC_Feed\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;

class ManageFilterController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $aFilters = Phpfox::getService('ynfeed.filter')->getForAdmin();
        $this->template()
            ->setBreadCrumb(_p('Manage Filters'), $this->url()->makeUrl('admincp.ynfeed.manage-filter'))
            ->assign([
                'sAssetsDir' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-feed/assets/',
                'aFilters' => $aFilters
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynfeed.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}