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
 
class Coupon_Component_Block_Latest_Of_This_Member extends Phpfox_Component 
{
 	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() 
	{
		$aCoupon = $this->getParam('aCoupon');
		
		if(!$aCoupon)
		{
			return FALSE;
		}
		
		// Setup variables
		$iLimit  = Phpfox::getParam('coupon.number_of_latest_coupon_of_member');
		if(!$iLimit)
		{
			$iLimit = 5;
		}
		$aConds  = array();
		$oCoupon = Phpfox::getService('coupon');
		
		// Set conditions
		$aConds[] = "c.user_id = {$aCoupon['user_id']} AND c.coupon_id <> {$aCoupon['coupon_id']}";
		$aConds[] = "AND is_draft = 0 AND is_approved = 1 AND (c.status = 1 OR c.status = 5)";
		
		$aCoupons = $oCoupon->getCouponsForManage($aConds, "expire_time DESC", $iLimit);
		
		if(count($aCoupons) == 0)
		{
			return FALSE;
		}
		$this->template()->assign(array(
			'sHeader'  => _p('others_of_this_member'),
			'aCoupons' => $aCoupons,
			'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png"
		));
		
		return 'block';
	}	
}
 
?>