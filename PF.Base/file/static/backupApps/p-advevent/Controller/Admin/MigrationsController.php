<?php
namespace Apps\P_AdvEvent\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class MigrationsController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_fevent"), $this->url()->makeUrl('admincp.app',['id' => '__module_fevent']))
            ->setBreadcrumb("Migration Events");
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('musicstore.component_controller_admincp_singer_clean')) ? eval($sPlugin) : false);
    }
}