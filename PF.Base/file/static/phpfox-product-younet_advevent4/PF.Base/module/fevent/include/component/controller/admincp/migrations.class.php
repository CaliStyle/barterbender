<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
class Fevent_Component_Controller_Admincp_Migrations extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        
         $this->template()->setHeader(array(
         
       ));
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

?>