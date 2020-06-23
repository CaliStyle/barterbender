<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

// Add and edit request both go here 
class Coupon_Component_Controller_Payment extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!($iCouponId = $this->request()->get('req3'))) {
            $this->url()->send('coupon');
        }
		
		if (!($payType = (int) $this->request()->get('req4'))) {
            $payType = 1;
        }

		if (!($aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId))) {
            return Phpfox_Error::display(_p('the_coupon_you_are_looking_for_either_does_not_exist_or_has_been_removed'));
        }

		$aCheckoutParams = Phpfox::getService('coupon.process')->startPayment($iCouponId, $payType);
		
		if ($aCheckoutParams === false) {
			if($payType == 2)
            {
                // publish and feature
                Phpfox::getService('coupon.process')->publishForPaymentIsZero($iCouponId);
                Phpfox::getService('coupon.process')->feature($iCouponId, 1);
            } else 
            {
                // publish 
                Phpfox::getService('coupon.process')->publishForPaymentIsZero($iCouponId);
            }
        
            Phpfox::getLib('url')->send(Phpfox::getLib('url')->permalink('coupon.detail', $iCouponId, $sTitle));
		}
		
		$this->setParam('gateway_data', $aCheckoutParams);
		
		$bNoPaymentMethodActive = false;
		
		$gateways = Phpfox::getService('api.gateway')->getActive();

		if(!count($gateways)){
			$bNoPaymentMethodActive = true;
		}
		
		$this->template()
		->assign(array(
			'aCoupon' => $aCoupon,
			'bNoPaymentMethodActive' => $bNoPaymentMethodActive,
			'aCheckoutParams' => $aCheckoutParams,
            'aCouponStatus' => Phpfox::getService('coupon')->getAllStatus(),
			'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png"
		));
		
		$this->template()->setTitle(_p('place_order'))
			->setBreadcrumb(_p('coupons'), $this->url()->makeUrl('coupon'));
	
		}


	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

