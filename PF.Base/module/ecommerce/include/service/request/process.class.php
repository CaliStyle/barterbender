<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Request_Process extends Phpfox_Service {
	
	public function approve($iRequestId) 
	{
		//udpate request
		$aUpdate = array(
            'creditmoneyrequest_status' => 'approved',
            'creditmoneyrequest_modification_datetime' => PHPFOX_TIME
        );
        $this->database()->update(Phpfox::getT('ecommerce_creditmoneyrequest'), $aUpdate, 'creditmoneyrequest_id = ' . (int) $iRequestId);
        $request = Phpfox::getService('ecommerce.request') -> get($iRequestId);

        // Update user activity

        $aRow = $this->database()->select("ua.activity_total, ua.activity_points")
            ->from(Phpfox::getT('user_activity'), 'ua')
            ->where('ua.user_id = ' . (int) $request['user_id'])
            ->execute('getSlaveRow');

        $aUpdateActivityPoints = array(
            'activity_points' => $request['creditmoneyrequest_amount'] + $aRow['activity_points'],
        );

        $this->database()->update(Phpfox::getT('user_activity'), $aUpdateActivityPoints, 'user_id = '.$request['user_id']);

        if (Phpfox::isModule('notification') && Phpfox::getUserId() != $request['user_id'])
        {
            Phpfox::getService('notification.process')->add('ecommerce_request', $request['creditmoneyrequest_id'], $request['user_id'],$request['user_id']);
        }   
	}
	public function updateRequest($iRequestId,$message) 
    {
        //update response message request
        $aUpdate = array(
            'creditmoneyrequest_response' => $message,
            'creditmoneyrequest_modification_datetime' => PHPFOX_TIME
        );
        $this->database()->update(Phpfox::getT('ecommerce_creditmoneyrequest'), $aUpdate, 'creditmoneyrequest_id = ' . (int) $iRequestId);
    }
	public function deny($iRequestId, $responseMessage) 
	{
		$aUpdate = array(
			'creditmoneyrequest_response' => $responseMessage,
            'creditmoneyrequest_status' => 'rejected',
            'creditmoneyrequest_modification_datetime' => PHPFOX_TIME
        ); 
        $request = Phpfox::getService('ecommerce.request') -> get($iRequestId);       
        if (Phpfox::isModule('notification') && Phpfox::getUserId() != $request['user_id'])
        {
            Phpfox::getService('notification.process')->add('ecommerce_request', $request['creditmoneyrequest_id'], $request['user_id'], $request['user_id']);             
        }  
        $this->database()->update(Phpfox::getT('ecommerce_creditmoneyrequest'), $aUpdate, 'creditmoneyrequest_id = ' . (int) $iRequestId);  
		$request = Phpfox::getService('ecommerce.request') -> get($iRequestId);    
		if($request)
		{
			//get credit
			$credit = Phpfox::getService('ecommerce.creditmoney') -> getCreditMoney($request['user_id']);
			if($credit)
			{
				//refund credit
				Phpfox::getService('ecommerce.creditmoney.process')->updateRemainingAmount($credit['creditmoney_id'], $credit['creditmoney_remain_amount'] + $request['creditmoneyrequest_amount']);
			}
		}
		
	}
	
    public function add($aVals)
    {
        $fAmount = isset($aVals['amount']) ? (float) $aVals['amount'] : 0.0;
        $fRemainAmount = isset($aVals['creditmoney_remain_amount']) ? (float) $aVals['creditmoney_remain_amount'] : 0.0;
        $sReason = isset($aVals['reason']) ? $this->preParse()->clean($aVals['reason']) : '';
        $iCreditMoneyId = isset($aVals['creditmoney_id']) ? (int) $aVals['creditmoney_id'] : 0;
        
        $aInsert = array(
            'creditmoneyrequest_creditmoney_id' => $iCreditMoneyId,
            'user_id' => Phpfox::getUserId(),
            'creditmoneyrequest_amount' => $fAmount,
            'creditmoneyrequest_reason' => $sReason,
            'creditmoneyrequest_creation_datetime' => PHPFOX_TIME,
            'creditmoneyrequest_status' => 'pending',
            'creditmoneyrequest_modification_datetime' => 0,
            'creditmoneyrequest_response' => ''
        );
        
        $iRequestId = $this->database()->insert(Phpfox::getT('ecommerce_creditmoneyrequest'), $aInsert);
        
        Phpfox::getService('ecommerce.creditmoney.process')->updateRemainingAmount($iCreditMoneyId, $fRemainAmount - $fAmount);
        
        return $iRequestId;
    }

    public function updateStatus($iRequestId, $sStatus)
    {
        if (!in_array($sStatus, array('pending','approved','rejected')))
        {
            return false;
        }
        
        $aUpdate = array(
            'creditmoneyrequest_status' => $sStatus,
            'creditmoneyrequest_modification_datetime' => PHPFOX_TIME
        );
        
        return $this->database()->update(Phpfox::getT('ecommerce_creditmoneyrequest'), $aUpdate, 'creditmoneyrequest_id = ' . (int) $iRequestId);
    }
    
    public function delete($iRequestId, $fAmount)
    {
        $aCreditMoney = Phpfox::getService('ecommerce.creditmoney')->getCreditMoney();
        
        Phpfox::getService('ecommerce.creditmoney.process')->updateRemainingAmount($aCreditMoney['creditmoney_id'], $aCreditMoney['creditmoney_remain_amount'] + $fAmount);
        
        return $this->database()->delete(Phpfox::getT('ecommerce_creditmoneyrequest'), 'creditmoneyrequest_id = ' . (int) $iRequestId);
    }
}

?>