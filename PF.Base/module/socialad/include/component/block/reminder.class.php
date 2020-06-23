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
class Socialad_Component_Block_Reminder extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iAdId = $this->getParam('iRemindAdId', false);

		$aReminders = array();
		if($iAdId) {
			$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);

			// display ONLY for owner
			if(Phpfox::getUserId() != (int)$aAd['ad_user_id']){
				return false;
			}

			if($aAd['ad_status'] == Phpfox::getService('socialad.helper')->getConst('ad.status.unpaid')) {
				if($iTransactionId = Phpfox::getService('socialad.payment')->checkAdOnprogresPayLater($iAdId)) {
					$sPayLaterRequestPhrase = _p('pay_later_request');
					$aReminders[]  = _p('reminder_pending_pay_later', array(
						'num_day' => Phpfox::getService('socialad.payment')->getRemainDayForTransaction($iTransactionId), 
						'onclick' => "tb_show('{$sPayLaterRequestPhrase}', $.ajaxBox('socialad.showPayLaterPopup', 'height=400&width=700&no_button=true&ad_id={$iAdId}')); return false;"
					));

				} else {
					$aReminders[]  = _p('reminder_not_pay', array(
						'link' => Phpfox::getLib('url')->makeUrl('socialad.payment.choosemethod', array('id' => $iAdId))
					));
				}
			}


			if($aAd['ad_status'] == Phpfox::getService('socialad.helper')->getConst('ad.status.pending')) {
				$aReminders[] = _p('reminder_ad_pending');
			}

			if($aAd['ad_status'] == Phpfox::getService('socialad.helper')->getConst('ad.status.denied')) {
				$aReminders[] = _p('reminder_ad_denied', array(
					'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.action', array(
						'actionname' => 'reedit',
						'id' => $iAdId	
					))	
				) );
			}

		}


		$this->template()->assign(array(
			'aSaReminders' => $aReminders
		));
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

