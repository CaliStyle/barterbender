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
 
class Coupon_Component_Block_Photo_And_Menu extends Phpfox_Component 
{
 	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() 
	{
		$aCoupon = $this->getParam('aCoupon');
		$oCoupon = Phpfox::getService('coupon');
		if(!$aCoupon)
		{
			return FALSE;
		}
		
		$iViewerId = Phpfox::getUserId();
		
		$bIsFavorited = $oCoupon->isFavorited($aCoupon['coupon_id']);
		
		$bCanFollow = TRUE;
		if($aCoupon['user_id'] == $iViewerId)
		{
			$bCanFollow = FALSE;
		}
		/**
		 * Add rating method 
		 */
		$this->setParam('aRatingCallback', array(
				'type' => 'coupon',
				'total_rating' => _p('total_rating_ratings', array('total_rating' => $aCoupon['total_rating'])),
				'default_rating' => $aCoupon['total_score'],
				'item_id' => $aCoupon['coupon_id'],
				'stars' => array(
					'2' => _p('poor'),
					'4' => _p('nothing_special'),
					'6' => _p('good'),
					'8' => _p('pretty_cool'),
					'10' => _p('awesome')
				)
			)
		);	
									 
		$this->template()->assign(array(
			'aCoupon' => $aCoupon,
			'bIsFavorited' => $bIsFavorited,
			'bCanFollow' => $bCanFollow,
			'sCorePath' => Phpfox::getParam('core.path')
		));
		
		return 'block';
	}	
}
 
?>