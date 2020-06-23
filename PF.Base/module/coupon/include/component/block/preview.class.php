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

class Coupon_Component_Block_Preview extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
    	$iId = $this->request()->get('id');
		if (!empty($iId))
		{
			$aTemplate = Phpfox::getService('coupon.template')->get($iId);
			$aParams = unserialize($aTemplate['params']);
		}
		else
		{
			$aParams = $this->request()->get('val');
			unset($aParams['name']);
		}
        
    	$aCoupon = Phpfox::getService('coupon')->getSampleCoupon();
        
        $sHtml = Phpfox::getService('coupon.template')->buildHtml($aParams, $aCoupon);
        
        $this->template()->assign(array(
        	'sHtml' => $sHtml
		));
    }
}

?>