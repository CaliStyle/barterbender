<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         AnNT
 * @package        Module_Coupon
 * @version        3.02
 */

class Coupon_Component_Block_Themes extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
    	$aCoupon = Phpfox::getService('coupon')->getSampleCoupon();
        
        $aTemplates = Phpfox::getService('coupon.template')->getForManage();
        foreach ($aTemplates as $k => $aTemplate)
        {
            $aParams = unserialize($aTemplate['params']);
            $aTemplates[$k]['html'] = Phpfox::getService('coupon.template')->buildHtml($aParams, $aCoupon, false, true);
        }
        
        $this->template()->assign(array(
        	'aCoupon' => $aCoupon,
            'aTemplates' => $aTemplates
		));
    }
}

?>