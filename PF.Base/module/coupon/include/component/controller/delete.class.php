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

class Coupon_Component_Controller_Delete extends Phpfox_Component 
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() 
    {
    	// User Login Requirement
    	Phpfox::isUser(TRUE);	
		$iViewerId = Phpfox::getUserId();
		
		// Check Coupon Id
		if ($iId = $this->request()->getInt('req3'))
		{
			// Get Related Coupon
			$aCoupon = Phpfox::getService("coupon")->quickGetCouponById($iId);
			
			if(!$aCoupon)
			{
				return Phpfox_Error::set(_p('coupom.can_not_find_selected_coupon_to_delete'));
			}
			
			// Checking Delete Permission
			$bCanDelete = FALSE;
			if($iViewerId == $aCoupon['user_id'])
			{
				$bCanDelete = Phpfox::getUserParam('coupon.can_delete_own_coupon');
			}
			else 
			{
				$bCanDelete = Phpfox::getUserParam('coupon.can_delete_other_user_coupon');
			}

			if(!$bCanDelete)
			{
				$this->url()->send('subscribe');
			}
			
			// Process
			Phpfox::getService('coupon.process')->delete($aCoupon['coupon_id']);
			
			if($iViewerId == $aCoupon['user_id'])
			{
				$this->url()->send('coupon.view_my', array(),_p('coupon_deleted_successfully'));
			}
			else 
			{
				$this->url()->send('coupon', array(),_p('coupon_deleted_successfully'));
			}
		}
		else 
		{
			return Phpfox_Error::set(_p('can_not_find_selected_coupon_to_delete'));
		}
	}
}

?>