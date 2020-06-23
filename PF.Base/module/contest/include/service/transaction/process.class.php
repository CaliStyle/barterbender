<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Transaction_Process extends Phpfox_service {

	public function __construct()
    {
        $this->_sTable = Phpfox::getT('contest_transaction');
    }

	public function add($aInsert)
	{
		$iTransactionId= $this->database()->insert($this->_sTable, $aInsert);
		return $iTransactionId;
	}

	public function updatePaypalTransaction($iTransactionId, $aParam)
	{
		if($aParam['status'] == 'completed')
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => serialize($aParam),
				'status' => Phpfox::getService('contest.constant')->getTransactionStatusIdByStatusName('success'),
				'amount' => $aParam['total_paid'],
				'paypal_account' => '',
				'paypal_transaction_id' => ''
			);

		}
		elseif($aParam['status'] == 'pending')
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => serialize($aParam),
				'status' => Phpfox::getService('contest.constant')->getTransactionStatusIdByStatusName('pending'),
				'amount' => $aParam['total_paid'],
				'paypal_account' => '',
				'paypal_transaction_id' => ''
				
			);

		}
		elseif(($aParam['status'] == 'denied'))
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => serialize($aParam),
				'status' => Phpfox::getService('contest.constant')->getTransactionStatusIdByStatusName('denied'),
				'amount' => $aParam['total_paid'],
				'paypal_account' => '',
				'paypal_transaction_id' => ''
				
			);
			
		}
		
		$this->database()->update($this->_sTable, $aUpdate, 'transaction_id = ' . $iTransactionId);
	}
}