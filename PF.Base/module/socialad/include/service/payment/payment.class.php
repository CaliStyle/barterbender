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

class Socialad_Service_Payment_Payment extends Phpfox_Service
{

	private $_aTransactionStatus ;
	public function __construct() {
		$this->_aTransactionStatus = array( 
			"initialized" => array(
				"id" => 1, 
				"phrase" => _p("initialized"),
			),
			"expired" => array(
				"id" => 2, 
				"phrase" => _p("expired"),
			),
			"pending" => array(
				"id" => 3, 
				"phrase" => _p("pending"),
			),
			"completed" => array(
				"id" => 4, 
				"phrase" => _p("completed"),
			),
			"canceled" => array(
				"id" => 5, 
				"phrase" => _p("canceled"),
			),
		);

		$this->_aTransactionMethods = array( 
			"paypal" => array(
				"id" => 1, 
				"phrase" => _p("paypal"),
			),
			"2checkout" => array(
				"id" => 2, 
				"phrase" => _p("2checkout"),
			),
			"paylater" => array(
				"id" => 3, 
				"phrase" => _p("pay_later"),
			),
			"paybycredit" => array(
				"id" => 4, 
				"phrase" => _p('pay_credit'),
			),
			"activitypoints" => array(
				"id" => 5, 
				"phrase" => _p('pay_activity_points'),
			),
            "braintree" => array(
                "id" => 6,
                "phrase" => _p("Braintree")
            ),
            "ccbill" => array(
                "id" => 7,
                "phrase" => _p("CCBill")
            ),
            "gopay" => array(
                "id" => 8,
                "phrase" => _p("Gopay")
            )
		);
		$this->_sTransactionTable = Phpfox::getT("socialad_transaction");
		$this->_sTransactionTableAlias = "at";
	}

	/**
	 * we know that younet payment gateway module only returns pending or completed status
	 * @params $aParam array 
	 */
	public function handlePaymentCallback($aParam) {

        $aGateway = $this->getTransactionMethods($aParam['gateway']);
        $iMethodId = null;
        if ($aGateway != null) {
            $iMethodId = $aGateway['id'];
        }
		if($aParam['gateway'] == 'activitypoints'){

				$iTransactionId = isset($aParam['transaction_id']) ? $aParam['transaction_id'] : (isset($aParam['item_number'])) ? $aParam['item_number'] : 0;
				$sStatus = $aParam['status'];
				if('completed' == $sStatus){
					Phpfox::getService('socialad.payment.process')->completeTransaction($iTransactionId, $aParam, $iMethodId);
				}
				return;
		}
		$iTransactionId = $aParam['item_number'];
		$sStatus = $aParam['status'];

		$aVals = array( 
			'transaction_id' => $iTransactionId,
		);
		switch($sStatus) {
			case 'completed':
					Phpfox::getService('socialad.payment.process')->completeTransaction($iTransactionId, $aParam, $iMethodId);
				break;
			case 'pending':
					Phpfox::getService('socialad.payment.process')->pendTransaction($iTransactionId);
				break;
			case 'default':
				break;
		}

		if($aParam['gateway'] == '2checkout') {
			$aTransaction = Phpfox::getService('socialad.payment')->getTransactionById($iTransactionId);
			Phpfox::getLib('url')->send('socialad.ad.detail', array('id' => $aTransaction['transaction_ad_id']));
		}
	}

	public function checkPaymentExpired($transaction) {

		if(is_array($transaction)) {
			$aTransaction = $transaction;
		} else {
			$aTransaction =Phpfox::getService('socialad.payment')->getTransactionById($transaction);
		}

		if(!$aTransaction) {
			return false;
		}

		if($aTransaction['transaction_status_id'] != Phpfox::getService('socialad.helper')->getConst('transaction.status.initialized')) {
			return false;
		}

		$iExpireTime = Phpfox::getParam('socialad.pay_later_request_expired_time') * 24; // days x 24 => hours

		$iLastedTime = floor((PHPFOX_TIME - $aTransaction['transaction_start_date'])/(60 *60));

		if($iLastedTime >= $iExpireTime) {
			Phpfox::getService('socialad.payment.process')->expireTransaction($aTransaction['transaction_id']);
		}


	}

	public function getRemainDayForTransaction($iTransactionId) {
		$aTransaction =Phpfox::getService('socialad.payment')->getTransactionById($iTransactionId);
		$iExpireTime = Phpfox::getParam('socialad.pay_later_request_expired_time');

		$iLastedTime = floor((PHPFOX_TIME - intval($aTransaction['transaction_start_date']))/(60 *60 * 24));

		return $iExpireTime - $iLastedTime;

	}

	public function cronUpdate() {
		$aConds = array(
			'sat.transaction_status_id = ' . Phpfox::getService('socialad.helper')->getConst('transaction.status.initialized'),
			'sat.transaction_method_id = '. Phpfox::getService('socialad.helper')->getConst('transaction.method.paylater')
		);

		$sConds = implode(" AND ", $aConds);
			
		$aRows = $this->database()->select("sat.*")
				->from($this->_sTransactionTable, "sat")
				->where($sConds)
				->limit($iPage = 0, $iLimit = 20)
				->order('sat.transaction_start_date ASC')
				->execute('getRows');

		foreach($aRows as &$aRow) {
			$this->checkPaymentExpired($aRow);
		}
	
	}

	public function retrivePermissionOnTransaction($aTransaction) {
		$aTransaction['can_confirm_pay_later_transaction'] = Phpfox::getService("socialad.permission")->canConfirmPayLaterTransaction($aTransaction['transaction_id']);

		$aTransaction['can_cancel_pay_later_request'] = Phpfox::getService("socialad.permission")->canCancelPayLaterRequest($aTransaction['transaction_id']);

		return $aTransaction;
	}

	public function getPayLaterTransactions() {
		$aConds = array(
			"sat.transaction_method_id = " . Phpfox::getService("socialad.helper")->getConst("transaction.method.paylater"),
			"sat.transaction_status_id = " . Phpfox::getService('socialad.helper')->getConst('transaction.status.initialized')
		);

		return $this->getWithPermission($aConds);

	}

	public function getTransactionsOfUser($iUserId) {
		$aConds = array(
			"sat.transaction_user_id = " . $iUserId
		);

		return $this->get($aConds);

	}

	public function getTransactionById($iTransactionId) {
		$aConds = array(
			"sat.transaction_id = " . $iTransactionId,
		);

		$aRows = $this->get($aConds);

		return $aRows ? $aRows[0] : false;

	}

	public function getTransactionByAdId($iAdId) {
		$aConds = array(
			"sat.transaction_ad_id = " . (int)$iAdId,
		);

		$aRows = $this->get($aConds);

		return $aRows ? $aRows[0] : false;
	}

	public function count($aConds) {
		$sConds = implode(" AND ", $aConds);

		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTransactionTable, 'sat')
			->where($sConds)
			->execute('getSlaveField');	

		return $iCnt;
	}

	public function getAllTransactionMethods() {
		return $this->_aTransactionMethods;
	}

	public function getTransactionMethods($sMethod) {
        return isset($this->_aTransactionMethods[$sMethod]) ? $this->_aTransactionMethods[$sMethod] : null;
    }
	public function get($aConds, $aExtra = array()) {
		$sConds = implode(" AND ", $aConds);
			
		if($aExtra && isset($aExtra['limit'])) {
			$this->database()->limit($aExtra['page'], $aExtra['limit']);
		}

		$aRows = $this->database()->select("sat.*, saa.* ")
				->from($this->_sTransactionTable, "sat")
				->join(Phpfox::getT('socialad_ad'), 'saa', 'saa.ad_id = sat.transaction_ad_id')
				->where($sConds)
				->order('sat.transaction_start_date DESC')
				->execute('getRows');

		foreach($aRows as &$aRow) {
			$aRow["transaction_status_phrase"] = Phpfox::getService("socialad.helper")->getPhraseById("transaction.status", $aRow["transaction_status_id"]);
			$aRow["transaction_payment_method_phrase"] = Phpfox::getService("socialad.helper")->getPhraseById("transaction.method", $aRow["transaction_method_id"]);
			$aRow["transaction_amount_text"] = Phpfox::getService("socialad.helper")->getMoneyText($aRow["transaction_amount"], $aRow["transaction_currency"]);
			$aRow['transaction_start_date_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['transaction_start_date']);
			$aRow['transaction_pay_date_phrase'] = Phpfox::getService('socialad.date')->convertTime($aRow['transaction_pay_date']);
		}


		return $aRows;
	}

	public function getWithPermission($aConds, $aExtra = array()) {
		$aRows = $this->get($aConds, $aExtra);
		foreach($aRows as &$aRow) {
			$aRow = $this->retrivePermissionOnTransaction($aRow);
			$aRow['package'] = Phpfox::getService('socialad.package')->getPackageById($aRow['ad_package_id']);			
		}

		return $aRows;

	}


	/**
	 * @param $iAd integer ID of paid ad
	 * @param $sMethod string paypal, 2checkout, paylater
	 * @return array(
	 * 		'result' => boolean, 
	 * 		'checkout_url' => string,
	 * 		'transaction_id' => int
	 *	)
	 */
	public function startPayment($iAdId) {
		$aAd       =  Phpfox::getService('socialad.ad')->getAdById($iAdId);
		$sUrl      =  urlencode(Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $iAdId)));
		$aPackage  =  Phpfox::getService('socialad.package')->getPackageById($aAd['ad_package_id']);
		$sCurrency =  $aPackage["package_currency"];
		$aExtra  =  array(
		);

		$aVals = array(
			"extra"                 => serialize($aExtra),
			"transaction_ad_id"     => $iAdId,
			"transaction_amount"    => $aAd['ad_number_of_package'] * $aPackage['package_price'],
			"transaction_currency"  => $aPackage["package_currency"],
			"transaction_user_id"   => $aAd["ad_user_id"]
		);

		$iTransactionId = Phpfox::getService('socialad.payment.process')->addTransaction($aVals);
		
		$sCheckoutParams = $this->getPaymentParams(array(
			'currency'       => $aVals['transaction_currency'],
			'amount'         => $aVals['transaction_amount'],
			'transaction_id' => $iTransactionId,
			'return_url' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $iAdId))	
		));
		return $sCheckoutParams;
	}

	public function startPaymentByCredit($iAdId) {
		$aAd       =  Phpfox::getService('socialad.ad')->getAdById($iAdId);
		$aPackage  =  Phpfox::getService('socialad.package')->getPackageById($aAd['ad_package_id']);
		$sCurrency =  $aPackage["package_currency"];
		$aExtra  =  array(
		);
		$creditMoney = Phpfox::getService('socialad.ad')->getCreditMoneyByUserId(Phpfox::getUserId());

		$transaction_amount = $aAd['ad_number_of_package'] * $aPackage['package_price'];
		Phpfox::getService('socialad.ad.process')->updateRemainingAmountOfCreditMoneyById($creditMoney['creditmoney_id']
			, ( doubleval($creditMoney['creditmoney_remain_amount']) - doubleval($transaction_amount) )
		);

		// add credit money request of system 
		$aVals = array(
			'creditmoneyrequest_creditmoney_id' => $creditMoney['creditmoney_id'], 
			'creditmoneyrequest_amount' => $transaction_amount, 
			'creditmoneyrequest_reason' => _p('payment_by_credit_for_ad') . ': ' . $aAd['ad_title'],
			'creditmoneyrequest_status' => Phpfox::getService('socialad.helper')->getConst('creditmoneyrequest.status.approved'), 
			'creditmoneyrequest_ad_id' => $iAdId, 
		);

		$id = Phpfox::getService('socialad.ad.process')->addCreditMoneyRequest($aVals);

		// update ad
		Phpfox::getService('socialad.payment.process')->completeTransactionByPaymentCredit($iAdId);

		// create completed transaction 
		$iMethodId = Phpfox::getService('socialad.helper')->getConst('transaction.method.paybycredit', 'id');
		$aTransactionVals = array(
			"extra"                 => serialize($aExtra),
			"transaction_ad_id"     => $iAdId,
			"transaction_method_id" => $iMethodId,
			"transaction_amount"    => $aAd['ad_number_of_package'] * $aPackage['package_price'],
			"transaction_currency"  => $aPackage["package_currency"],
			"transaction_user_id"   => $aAd["ad_user_id"], 
			"transaction_status_id"   => Phpfox::getService("socialad.helper")->getConst("transaction.status.completed", "id"),
			"transaction_start_date"  => PHPFOX_TIME,
			"transaction_pay_date"    => NULL,
			"transaction_description" => "",
		);

		Phpfox::getService('socialad.payment.process')->removeDuplicateTransaction($aTransactionVals['transaction_ad_id']); // make sure that one ad has only one transaction

		$iTransactionId = $this->database()->insert(Phpfox::getT("socialad_transaction"), $aTransactionVals);

		return true;
	}

	public function startPaymentByActivityPoints($iAdId,$iMethodId) {

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
		
		Phpfox::getService('socialad.payment.process')->purchaseWithPoints('socialad', $iAdId,$iTransactionId , $aAd['ad_number_of_package'] * $aPackage['package_price'], $aPackage["package_currency"]);

		return true;
	}



	public function getAllTransactionStatus() {
		return $this->_aTransactionStatus;
	}

	public function getAdminPaypalEmail() {
		return Phpfox::getParam('socialad.paypal_email');
	}
	public function getAdmin2CheckoutId() {
		return Phpfox::getParam('socialad.2checkout_id');
	}
	public function getAdmin2CheckoutSecretWord() {
		return Phpfox::getParam('socialad.socialad2checkout_secret_word');
	}
	/**
	 * @params array(
	 * 		'method_id' => int,
	 * 		'amount' => float,
	 * 		'transaction_id' => int,
	 * 		'currency' => string,
	 * 		'return_url' => string
	 * 	)
	 * @return array('result' -> boolean, 'message' -> string ) if failed
	 * 		   array('result' -> boolea, 'checkout_url' -> string) if succeeded
	 */
	public function getPaymentParams($aVals) {
		$sCorePath = Phpfox::getParam('core.path');
        $sCorePath = str_replace("index.php".PHPFOX_DS, "", $sCorePath);
        $sCorePath .= 'PF.Base'.PHPFOX_DS;
		
		$aParams = array(
			'item_number' 	=> 'socialad|' . $aVals["transaction_id"],
            'currency_code' => $aVals['currency'],
            'amount' 		=> $aVals['amount'],
            'item_name' 	=> 'publish',                                
            'return' 		=> $sCorePath . 'module/socialad/static/thankyou.php?sLocation=' . $aVals['return_url'],
            'recurring' 	=> '',
            'recurring_cost' 	=> '',
            'alternative_cost' 	=> '',
            'alternative_recurring_cost' 	=> ''
		);
		
		return $aParams;
	}

	public function checkAdOnprogresPayLater($iAdId) {
		$aConds = array(
			'ad_id = ' . $iAdId,
			'transaction_method_id = ' . Phpfox::getService('socialad.helper')->getConst('transaction.method.paylater'),
			'transaction_status_id = ' . Phpfox::getService('socialad.helper')->getConst('transaction.status.initialized')
		);

		$aRows = $this->get($aConds);

		return count($aRows) > 0 ? $aRows[0]['transaction_id'] : false;


	}

	public function checkMethod($sMethodName,$aAd = array()) {
		$bResult = false;
		switch($sMethodName) {
			case 'paylater': 
				$bPaylaterActivated = Phpfox::getParam('socialad.activate_pay_later');
				if($bPaylaterActivated) {
					$bResult = true;
				}
				break;
			case 'activitypoints':
				if ( Phpfox::getParam('socialad.activate_activity_points') && Phpfox::getParam('user.can_purchase_with_points') && Phpfox::getUserParam('user.can_purchase_with_points'))
				{
					$iTotalPoints = (int) $this->database()->select('activity_points')
									->from(Phpfox::getT('user_activity'))
									->where('user_id = ' . (int) Phpfox::getUserId())
									->execute('getSlaveField');	

					$sCurreny = $aAd['package_currency'];
					$aSetting = Phpfox::getParam('user.points_conversion_rate');
					if (isset($aSetting[$sCurreny]))
					{
						// Avoid division by zero
						$iConversion = ($aSetting[$sCurreny] != 0 ? ($aAd['package_price'] / $aSetting[$sCurreny]) : 0);
						if ($iTotalPoints >= $iConversion)
						{
							$bResult = true;
						}
					}
				}
				break;
		}


		return $bResult;
	}

	public function getInfoPurchaseActivityPoints($aAd){
		$iTotalPoints = (int) $this->database()->select('activity_points')
									->from(Phpfox::getT('user_activity'))
									->where('user_id = ' . (int) Phpfox::getUserId())
									->execute('getSlaveField');	
		$sCurreny = $aAd['package_currency'];
		$aSetting = Phpfox::getParam('user.points_conversion_rate');
		if (isset($aSetting[$sCurreny]))
		{
			// Avoid division by zero
			$iConversion = ($aSetting[$sCurreny] != 0 ? ($aAd['package_price'] / $aSetting[$sCurreny]) : 0);
			if ($iTotalPoints >= $iConversion)
			{
				return array($iTotalPoints,$iConversion);
			}
		}									
	}
}



