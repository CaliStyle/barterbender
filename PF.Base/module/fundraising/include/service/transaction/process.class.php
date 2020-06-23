<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Transaction_Process extends Phpfox_Service 
{

	static $STATUS = array(
		'initialized' =>0,
		'success' => 1
	);
	 /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fundraising_transaction');
    }

	public function add($aVals)
	{
		$aVals['donor_id'] = empty($aVals['donor_id'])?0:$aVals['donor_id'];
		$iTransactionId= $this->database()->insert($this->_sTable, $aVals);
		return $iTransactionId;
	}

	/**
	 * makes sure this function only called when having an IPN message from paypal
	 * @TODO: none 
	 * <pre>
	 * Phpfox::getService('fundraising.name')->function;
	 * </pre>
	 * @by minhta
	 * @param
	 * @return
	 */
	public function updatePaypalTransaction($iTransactionId, $aParam = array(), $iDonorId = 0)
	{
		if($aParam['status'] == Phpfox::getService('fundraising.transaction')->getPaypalStatusCode('completed'))
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => serialize($aParam['aTransactionDetail']),
				'donor_id' => ($iDonorId) ? $iDonorId : 0,
				'status' => Phpfox::getService('fundraising.transaction')->getStatusCode('success'),
				'amount' => $aParam['total_paid'],
				'paypal_account' =>$aParam['payer_email'],
				'paypal_transaction_id' => $aParam['transaction_id']
			);

		}
		elseif($aParam['status'] == Phpfox::getService('fundraising.transaction')->getPaypalStatusCode('pending'))
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => serialize($aParam['aTransactionDetail']),
				'status' => Phpfox::getService('fundraising.transaction')->getStatusCode('pending'),
				'amount' => $aParam['total_paid'],
				'paypal_account' =>$aParam['payer_email'],
				'paypal_transaction_id' => $aParam['transaction_id']
				
			);

		}
		elseif(($aParam['status'] == Phpfox::getService('fundraising.transaction')->getPaypalStatusCode('denied')))
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => serialize($aParam['aTransactionDetail']),
				'status' => Phpfox::getService('fundraising.transaction')->getStatusCode('denied'),
				'amount' => $aParam['total_paid'],
				'paypal_account' =>$aParam['payer_email'],
				'paypal_transaction_id' => $aParam['transaction_id']
				
			);
			
		}
		
		$this->database()->update($this->_sTable, $aUpdate, 'transaction_id = ' . $iTransactionId);
	}

}

?>