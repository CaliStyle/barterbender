<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Coupon_Service_Transaction_Process extends Phpfox_Service
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
        $this->_sTable = Phpfox::getT('coupon_transaction');
    }

	public function add($aVals)
	{
		$iTransactionId= $this->database()->insert($this->_sTable, $aVals);
		return $iTransactionId;
	}

    /**
     * @by : datlv
     * @param $iTransactionId
     * @param array $aParam
     * @param int $iDonorId
     */
    public function updatePaypalTransaction($iTransactionId, $aParam = array())
	{
        $aUpdate = array();
		if($aParam['status'] == Phpfox::getService('coupon.transaction')->getPaypalStatusCode('completed'))
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => '',
				'status' => Phpfox::getService('coupon.transaction')->getStatusCode('success'),
				'amount' => $aParam['total_paid'],
				'paypal_account' =>'',
				'paypal_transaction_id' => ''
			);

		}
		elseif($aParam['status'] == Phpfox::getService('coupon.transaction')->getPaypalStatusCode('pending'))
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => '',
				'status' => Phpfox::getService('coupon.transaction')->getStatusCode('pending'),
				'amount' => $aParam['total_paid'],
				'paypal_account' =>'',
				'paypal_transaction_id' => ''
				
			);

		}
		elseif(($aParam['status'] == Phpfox::getService('coupon.transaction')->getPaypalStatusCode('denied')))
		{
			$aUpdate = array(
				'time_stamp' => PHPFOX_TIME,
				'transaction_log' => '',
				'status' => Phpfox::getService('coupon.transaction')->getStatusCode('denied'),
				'amount' => $aParam['total_paid'],
				'paypal_account' =>'',
				'paypal_transaction_id' => ''
				
			);
			
		}
		
		$this->database()->update($this->_sTable, $aUpdate, 'transaction_id = ' . $iTransactionId);
	}

}

?>