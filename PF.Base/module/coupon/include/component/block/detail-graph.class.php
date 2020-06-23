<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Block_Detail_Graph extends Phpfox_Component {

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
		
		// Claim Percent
		$iPercent = 100;
		if($aCoupon['quantity'] > 0)
		{
			$iPercent = $aCoupon['total_claim']/$aCoupon['quantity']*100;	
		}
		
		// Claims Remain
		$sRemain = _p("unlimited_remain");
		if($aCoupon['quantity'] > 0)
		{
			$sRemain = $aCoupon['quantity'] - $aCoupon['total_claim'];
			if($sRemain < 0)
			{
				$sRemain = 0;
			}
			
			$sRemain = $sRemain . " " . _p("claims_remain");
		}
		
		// Remain Time
		$sRemainTime = Phpfox::getService('coupon')->convertTimeToCountdownString($aCoupon['end_time']);
		
		$this->template()->assign(array(
				'aCoupon'  	   => $aCoupon,
				'iPercent'     => $iPercent,
				'sRemain'  	   => $sRemain,
				'sRemainTime' => $sRemainTime
			)
		);
		return 'block';
	}
}

?>
