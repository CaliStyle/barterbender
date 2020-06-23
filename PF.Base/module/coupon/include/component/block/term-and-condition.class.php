<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
class Coupon_Component_Block_Term_And_Condition extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		// Check User login requirement	
		Phpfox::isUser(true);
		
		$iCouponId = $this->getParam('iId');
		
		$sTermCondition = Phpfox::getLib('database')
							->select('term_condition_parsed as term_condition')
							->from(Phpfox::getT('coupon_text'))
							->where("coupon_id = {$iCouponId}")
							->execute("getSlaveField");
		
		$this->template()->assign(array(
				'iCouponId' 	  => $iCouponId,
				'sTermCondition'  => $sTermCondition
			)
		);
		
		return 'block';
	}
}