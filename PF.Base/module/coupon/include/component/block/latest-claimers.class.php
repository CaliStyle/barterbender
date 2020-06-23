<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Block_Latest_Claimers extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

		$aCoupon = $this->getParam('aCoupon');
		
		if(!$aCoupon)
		{
			return FALSE;	
		}
		
		$iLimit = Phpfox::getParam('coupon.number_of_claimers');
		if(!$iLimit)
		{
			$iLimit = 8;
		}
		$aClaimers = Phpfox::getService('coupon')->getLatestClaimers($aCoupon['coupon_id'], $iLimit);

		if(count($aClaimers) == 0 || defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) 
		{
			return FALSE;
		}

		$this->template()->assign(array(
				'aClaimers' => $aClaimers,
				'sHeader' => _p('latest_claimers')
			)
		);
		return 'block';
	}

}

?>
