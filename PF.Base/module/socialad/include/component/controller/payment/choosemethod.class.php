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

// Add and edit request both go here 
class Socialad_Component_Controller_Payment_Choosemethod extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iAdId = $this->request()->get('id');
		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
		$aPackage = Phpfox::getService('socialad.package')->getPackageById($aAd['ad_package_id']);
        if((int)$aAd['package_price'] == 0)
        { 
           if(Phpfox::getUserParam('socialad.approve_ad')) {
                $iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.pending');
                Phpfox::getService('socialad.ad.process')->updateStatus($aAd['ad_id'], $iNextStatus);
                return Phpfox::getLib('module')->setController('socialad.ad.detail', array('id' => $aAd['ad_id']));
            } else {
                $iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.running');
                Phpfox::getService('socialad.ad.process')->updateStatus($aAd['ad_id'], $iNextStatus);
                return Phpfox::getLib('module')->setController('socialad.ad.detail', array('id' => $aAd['ad_id']));
            }
        }
		$creditMoney = Phpfox::getService('socialad.ad')->getCreditMoneyByUserId(Phpfox::getUserId());
		$bIsHavePayByCredit = false;
		if(isset($creditMoney['creditmoney_id']) 
			&& doubleval($creditMoney['creditmoney_remain_amount']) > 0
			&& doubleval($creditMoney['creditmoney_remain_amount']) >= doubleval($aAd['ad_total_price'])
		){
			$bIsHavePayByCredit = true;
			$this->template()->assign(array(
				'bIsHavePayByCredit' => $bIsHavePayByCredit
			));
		}
		$bIsHavePayLater = Phpfox::getService('socialad.payment')->checkMethod('paylater');

		$sCheckoutParams = Phpfox::getService('socialad.payment')->startPayment($iAdId);
		
		$this->setParam('gateway_data', $sCheckoutParams);
		$bNoPaymentMethodActive = false;
		
		$gateways = Phpfox::getService('api.gateway')->getActive();

		if(!$bIsHavePayByCredit && !$bIsHavePayLater && !count($gateways)){
			$bNoPaymentMethodActive = true;
		}

		$this->template()
		->setHeader(array(
			'jquery.magnific-popup.js'  => 'module_socialad',
			'magnific-popup.css'  => 'module_socialad',
		))
		->assign(array(
			'aPlaceorderAd' => $aAd,
			'aSaPackage' => $aPackage,
			'iSaAffectedAudience' => Phpfox::getService('socialad.ad.audience')->getAffectedAudience($aAd) ,
			'bIsHavePayLater' => $bIsHavePayLater,
			'bNoPaymentMethodActive'	=> $bNoPaymentMethodActive,
		))
		->setPhrase(array(
			'socialad.you_have_itotalpoints_activity_point_s_this_will_cost_icostpoints_activity_point_s_do_you_want_to_purchase',
			'socialad.purchase',
			'socialad.cancel',
			));

		Phpfox::getService('socialad.helper')->loadSocialAdJsCss();

		$this->template()->setTitle(_p('place_order'))
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb( _p('place_order'), $this->url()->makeUrl('socialad.payment.choosemethod', array('id' => $aAd['ad_id'])), true);
	}


	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

