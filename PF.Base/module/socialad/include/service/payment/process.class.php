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

class Socialad_Service_Payment_Process extends Phpfox_Service
{

	public function cancelPayLaterRequest($iTransactionId) {
		$aUpdate = array( 
			"transaction_status_id" => Phpfox::getService("socialad.helper")->getConst("transaction.status.canceled")
		);

		$this->database()->update(Phpfox::getT("socialad_transaction"), $aUpdate, "transaction_id = " . $iTransactionId);
	}

	public function completeTransaction($iTransactionId, $aParam = array(), $method = 1) {

		$aUpdate = array(
		    'transaction_method_id' => $method,
			"transaction_status_id" => Phpfox::getService("socialad.helper")->getConst("transaction.status.completed")
		);
		$this->database()->update(Phpfox::getT("socialad_transaction"), $aUpdate, "transaction_id = " . $iTransactionId);
		
		$aTransaction = Phpfox::getService('socialad.payment')->getTransactionById($iTransactionId);

		if('pay_later' == $method){
			// do not check amount and currency
			Phpfox::getService('socialad.ad.process')->completeOrder($aTransaction['transaction_ad_id'], $method);
		} else {
			if(isset($aParam['total_paid'])){
				if($aParam['total_paid'] == $aTransaction['transaction_amount']){
					Phpfox::getService('socialad.ad.process')->completeOrder($aTransaction['transaction_ad_id']);
				}
			}
		}
        (($sPlugin = Phpfox_Plugin::get('socialad.service_payment_process_complete_transaction__end')) ? eval($sPlugin) : false);
	}

	public function completeTransactionByPaymentCredit($iAdId) {
		Phpfox::getService('socialad.ad.process')->completeOrder($iAdId);
	}

	public function expireTransaction($iTransactionId) {

		$aUpdate = array( 
			"transaction_status_id" => Phpfox::getService("socialad.helper")->getConst("transaction.status.expired")
		);

		$this->database()->update(Phpfox::getT("socialad_transaction"), $aUpdate, "transaction_id = " . $iTransactionId);
	}
	
	public function pendTransaction($iTransactionId) {

		$aUpdate = array( 
			"transaction_status_id" => Phpfox::getService("socialad.helper")->getConst("transaction.status.pending")
		);

		$this->database()->update(Phpfox::getT("socialad_transaction"), $aUpdate, "transaction_id = " . $iTransactionId);
	}

	public function removeDuplicateTransaction($iAdId) {
		$this->database()->delete(Phpfox::getT('socialad_transaction'), 'transaction_ad_id = '. $iAdId);
	}

	public function addTransaction($aVals) {

		$this->removeDuplicateTransaction($aVals['transaction_ad_id']); // make sure that one ad has only one transaction

		$aInsert = array( 
			"transaction_amount"      => $aVals["transaction_amount"],
			"transaction_currency"    => $aVals["transaction_currency"],
			"transaction_method_id"    => (isset($aVals["transaction_method_id"])) ? $aVals["transaction_method_id"] : NULL,
			"transaction_user_id"     => $aVals["transaction_user_id"],
			"transaction_ad_id"       => $aVals["transaction_ad_id"],
			"transaction_status_id"   => Phpfox::getService("socialad.helper")->getConst("transaction.status.initialized", "id"),
			"transaction_start_date"  => PHPFOX_TIME,
			"transaction_pay_date"    => NULL,
			"transaction_description" => "",
			"extra"                   => $aVals["extra"]
		);

		$iTransactionId = $this->database()->insert(Phpfox::getT("socialad_transaction"), $aInsert);

		return $iTransactionId;
	}

	public function confirmTransaction($iTransactionId) {
		$this->completeTransaction($iTransactionId, array(), 'pay_later');
		$aTransaction = Phpfox::getService('socialad.payment')->getTransactionById($iTransactionId);
		Phpfox::getService('socialad.mail')->sendMailAndNotificaiton('order_confirm', $iAdId = $aTransaction['transaction_ad_id']);
	}

public function purchaseWithPoints($sModule, $iItem, $iTransactionId, $iTotal, $sCurreny)
	{
		if (!Phpfox::isModule($sModule))
		{
			return Phpfox_Error::set('Not a valid module.');
		}
		
		$iTotalPoints = (int) $this->database()->select('activity_points')
				->from(Phpfox::getT('user_activity'))
				->where('user_id = ' . (int) Phpfox::getUserId())
				->execute('getSlaveField');
				
		$aSetting = Phpfox::getParam('user.points_conversion_rate');
		if (isset($aSetting[$sCurreny]))
		{
			$iConversion = $iTotal / $aSetting[$sCurreny];
			if ($iTotalPoints >= $iConversion)
			{
				$iNewPoints = ($iTotalPoints - $iConversion);				
				
				$bReturn = Phpfox::callback($sModule. '.paymentApiCallback', array(
						'gateway' => 'activitypoints',
						'status' => 'completed',
						'item_number' => $iItem,
						'transaction_id' => $iTransactionId,
						'total_paid' => $iTotal,
						'currency' => $sCurreny
					)
				);
				
				// http://www.phpfox.com/tracker/view/15424/
				Phpfox::getService('api.gateway')->callback('activitypoints');

				if ($bReturn === false)
				{
					return false;
				}
				
				$this->database()->update(Phpfox::getT('user_activity'), array('activity_points' => (int) $iNewPoints), 'user_id = ' . (int) Phpfox::getUserId());
				
				return true;
			}
		}
		
		return Phpfox_Error::set(_p('user.not_enough_points', array('total' => (int) $iTotalPoints)));
	}

}



