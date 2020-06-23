<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */

class Coupon_Component_Controller_Print extends Phpfox_Component 
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() 
    {
        Phpfox::isUser('true');
        
    	// Get coupon id that prepare to print 
    	if (!($iCouponId = $this->request()->get('req3'))) {
            $this->url()->send('coupon');
        }
		
		if (!($aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId))) {
            return Phpfox_Error::display(_p('the_coupon_you_are_looking_for_either_does_not_exist_or_has_been_removed'));
        }
		
        if (strstr($aCoupon['print_option']['style'], 'custom_') !== false)
        {
            $iTemplate = (int)str_replace('custom_', '', $aCoupon['print_option']['style']);
            $aTemplate = Phpfox::getService('coupon.template')->get($iTemplate);
            if(!empty($aTemplate))
            {
                $aParams = unserialize($aTemplate['params']);
                $sHtml = Phpfox::getService('coupon.template')->buildHtml($aParams, $aCoupon, true);
                $this->template()->assign('sHtml', $sHtml);
            }
            else
            {
                $ran = array(1,2,3,4,5);
                $k = array_rand($ran);
                $v = $ran[$k];
                $aCoupon['print_option']['style'] = $v;
            }
        }
        
		$this->template()->setHeader(array(
			'detail.css'=> 'module_coupon',
			'jquery.carouFredSel-6.2.1-packed.js' => 'module_coupon'
		))
        ->assign(array(
			'aCoupon' => $aCoupon
		));
	}
}