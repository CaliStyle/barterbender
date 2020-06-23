<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */


class Socialad_Component_Controller_Payment_Pay extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		$iAdId  = $this->request()->get("id");
		$sMethod  = $this->request()->get("method");

		if('activitypoints' == $sMethod){
			$iMethodId = Phpfox::getService('socialad.helper')->getConst('transaction.method.' . $sMethod, 'id');
			Phpfox::getService('socialad.payment')->startPaymentByActivityPoints((int)$iAdId,$iMethodId);
			Phpfox::getLib('url')->send('socialad.ad.detail', array('id' => $iAdId), _p('pay_by_activity_points_successfully'));
			return true;
		}

		if('paybycredit' == $sMethod){
			Phpfox::getService('socialad.payment')->startPaymentByCredit((int)$iAdId);

			Phpfox::getLib('url')->send('socialad.ad.detail', array('id' => $iAdId), _p('payment_by_credit_is_successfully'));
			return true;
		}
		
		$iMethodId = Phpfox::getService('socialad.helper')->getConst('transaction.method.' . $sMethod, 'id');
		
		
		if(!$iMethodId) { 
			Phpfox::getLib('url')->send('socialad.ad');
		}
		
		if ('paylater' == $sMethod) {
			$iAdId = (int) $iAdId;
			$aAd       =  Phpfox::getService('socialad.ad')->getAdById($iAdId);
			$sUrl      =  urlencode(Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $iAdId)));
			$aPackage  =  Phpfox::getService('socialad.package')->getPackageById($aAd['ad_package_id']);
			$sCurrency =  $aPackage["package_currency"];
			$aExtra  =  array(
			);
			$aVals = array(
				"extra"                 => serialize($aExtra),
				"transaction_ad_id"     => $iAdId,
				"transaction_method_id" => $iMethodId,
				"transaction_amount"    => $aAd['ad_number_of_package'] * $aPackage['package_price'],
				"transaction_currency"  => $aPackage["package_currency"],
				"transaction_user_id"   => $aAd["ad_user_id"]
			);
			$iTransactionId = Phpfox::getService('socialad.payment.process')->addTransaction($aVals);
		}
		
		Phpfox::getLib('url')->send('socialad.ad.detail', array('id' => $iAdId));

	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

