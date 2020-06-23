<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         AnNT
 * @package        Module_Coupon
 * @version        3.02
 * 
 */

class Coupon_Component_Controller_Admincp_Template_Index extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if ($this->request()->get('req4')=='delete')
        {
            $iDeleteId = $this->request()->get('id');
            if (Phpfox::getService('coupon.template.process')->delete($iDeleteId))
            {
                $this->url()->send('admincp.coupon.template', null, _p('print_template_has_been_deleted'));
            }
        }
        
        $aTemplates = Phpfox::getService('coupon.template')->getForManage();
        
        $this->template()->setTitle(_p('manage_print_templates'))
        ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
        ->setBreadCrumb(_p('module_coupon'), $this->url()->makeUrl('admincp.app').'?id=__module_coupon')
        ->setBreadCrumb(_p('manage_print_templates'), $this->url()->makeUrl('admincp.coupon.template'))
        ->setHeader('cache', array(
            'detail.css' => 'module_coupon',
        ))
        ->assign(array(
            'aTemplates' => $aTemplates
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_admincp_template_index_clean')) ? eval($sPlugin) : false);
    }
}

?>