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

class Coupon_Component_Controller_Admincp_Template_Add extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;
        
        if ($iEditId = $this->request()->get('id'))
        {
            if ($aTemplate = Phpfox::getService('coupon.template')->get($iEditId))
            {
                $bIsEdit = true;
                $this->template()->assign('aForms', $aTemplate);
                $aParams = unserialize($aTemplate['params']);
                Phpfox::getService('coupon.template')->convertToPosition($aParams);
            }
        }
        
        if ($aVals = $this->request()->get('val'))
        {
            if ($bIsEdit)
            {
                if (Phpfox::getService('coupon.template.process')->update($iEditId, $aVals))
                {
                    $this->url()->send('admincp.coupon.template.add', array('id' => $iEditId), _p('print_template_updated_successfully'));
                }
            }
            else
            {
                if ($iId = Phpfox::getService('coupon.template.process')->add($aVals))
                {
                    $this->url()->send('admincp.coupon.template.add', null, _p('print_template_added_successfully'));
                }
            }
            
            $this->template()->assign('aForms', $aVals);
        }
        
        $this->template()->setTitle(_p('add_print_template'))
        ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
        ->setBreadCrumb(_p('module_coupon'), $this->url()->makeUrl('admincp.app').'?id=__module_coupon')
        ->setBreadCrumb(_p('add_print_template'), $this->url()->makeUrl('admincp.coupon.template.add'))
        ->setHeader('cache', array(
            'jquery.minicolors.css' => 'module_coupon',
            'jquery.minicolors.js' => 'module_coupon',
            'detail.css' => 'module_coupon',
        ));
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_admincp_template_add_clean')) ? eval($sPlugin) : false);
    }
}

?>