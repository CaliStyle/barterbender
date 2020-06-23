<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynadvancedpayment_Service_Process extends Phpfox_Service
{
	public function addSubscriptions($aVals)
	{
        $aInsert = array(
            'user_id' => (int)$aVals['user_id'],
            'getaway_subscription_id' => (int)$aVals['getaway_subscription_id'],
            'creation_date' => PHPFOX_TIME ,
            'modified_date' => PHPFOX_TIME,
            'gateway_id' => $aVals['gateway_id'],
            'package_id' => (int)$aVals['package_id'],
            'purchase_id' => (int)$aVals['purchase_id'],
        );

		$id = $this->database()->insert(Phpfox::getT('ynadvancedpayment_subscriptions'), $aInsert);

		return $id;
	}

	public function addSubscribePurchase($aVals){
        $aInsert = array(
            'package_id' => (int)$aVals['package_id'],
            'user_id' => (int)$aVals['user_id'],
            'currency_id' => $aVals['currency_id'] ,
            'price' => (float)$aVals['price'],
            'status' => $aVals['status'],
            'time_stamp' => PHPFOX_TIME,
        );

		$id = $this->database()->insert(Phpfox::getT('subscribe_purchase'), $aInsert);

		return $id;
	}
}
