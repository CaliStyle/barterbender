<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 */
class Coupon_Service_Transaction_Transaction extends Phpfox_Service
{
    private $_aPaypalStatus = array(
        'completed' => 'completed',
        'pending' => 'pending',
        'denied' => 'denied'
    );

    /**
     * get status number based on the name of status
     * @by minhta
     * @param string $sStatus name of status we want to retrieve
     * @return
     */
    public function getPaypalStatusCode($sStatus) {
        if (isset($this->_aPaypalStatus[$sStatus])) {
            return $this->_aPaypalStatus[$sStatus];
        } else {
            return false;
        }
    }

    public function getAllPaypalStatus() {
        return $this->_aPaypalStatus;
    }

    public function getStatusPhraseFromCode($iCode)
    {
        switch($iCode)
        {
            case $this->_status['initialized']:
                return _p('initialized_upper');
                break;
            case $this->_status['success']:
                return _p('successed_upper');
                break;
            case $this->_status['pending']:
                return _p('pending_upper');
                break;
            case $this->_status['denied']:
                return _p('denied_upper');
                break;
            default:
                return _p('initialized_upper');
                break;
        }
    }

    private $_status = array(
        'initialized' => 1,
        'success' => 2,
        'pending' => 3,
        'denied' => 4
    );

    /**
     * @by : datlv
     * @param $sStatus
     * @return bool
     */
    public function getStatusCode($sStatus) {
        if (isset($this->_status[$sStatus])) {
            return $this->_status[$sStatus];
        } else {
            return false;
        }
    }

    public function getAllStatus() {
        return $this->_status;
    }

    public function getReverseStatus() {
        return array_keys($this->_status);
    }
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('coupon_transaction');
	}

    /**
     * @by datlv
     * @param $iTransactionId
     * @return mixed
     */
    public function getTransactionById($iTransactionId)
    {
        $aTransaction = $this->database()->select('*')->from($this->_sTable)->where('transaction_id = ' . $iTransactionId)->execute('getRow');
        return $aTransaction;
    }

    public function getTransactionForCallback($iTransactionId)
    {
        $aTransaction = $this->database()->select('coupon_id, payment_type')->from($this->_sTable)->where('transaction_id = ' . $iTransactionId)->execute('getRow');
        return $aTransaction;
    }

	/** Get total item count from query
	 * @author TienNPL
	 * @param array $aConds is input filter conditions
	 * @return number of item gotten
	 */
	public function getItemCountForManage($aConds)
	{		
		// Generate query object	
		$oQuery = $this -> database()
						-> select('count(*)')
						-> from($this->_sTable, 'ct')
						-> join(Phpfox::getT('coupon'),'c','c.coupon_id = ct.coupon_id')
						-> join(Phpfox::getT('user'),'u','u.user_id = ct.user_id');
						
		// Filfer conditions
		if($aConds)
		{
			$oQuery-> where($aConds);
		}
								
		return $oQuery->execute('getSlaveField');
	}
	
	/**
	 * Get Coupon items according to the data input (this only use for back-end browsing)
	 * @author TienNPL
	 * @param array $aConditions is the array of filter conditions 
	 * @param string $sOrder is the listing order 
	 * @param int $iLimit is the limit of row's number output
	 * @return array of resume items data
	 */
	public function getTransactionsForManage($aConds, $sOrder, $iPage = 0, $iLimit = 0, $iCount = 0)
	{
		// Generate query object						
		$oSelect = $this -> database() 
						 -> select('ct.*, u.user_name, u.full_name, c.title as coupon_name')
						 -> from($this->_sTable, 'ct')
						 ->	join(Phpfox::getT('coupon'),'c','c.coupon_id = ct.coupon_id')
						 -> join(Phpfox::getT('user'),'u','u.user_id = ct.user_id');
		
		// Filter select condition
		if($aConds)
		{
			$oSelect->where($aConds);
		}
		
		// Setup select ordering		
		if($sOrder)
		{
			$oSelect->order($sOrder);
		}
		
		// Setup limit items getting
		$oSelect->limit($iPage, $iLimit, $iCount);

		$aCoupons = $oSelect->execute('getRows');
		
	 	return $aCoupons;
	}

    /**
     * by : datlv
     * @param $iCouponId
     * @return mixed
     */
    public function getTransactionIdByCouponId($iCouponId)
    {
        return $this->database()->select('transaction_id')
            ->from($this->_sTable)
            ->where('coupon_id = ' . $iCouponId)
            ->execute('getField');
    }

    /**
     * @param $iCouponId
     * @return mixed
     */
    public function countTransactionByCouponId($iCouponId)
    {
        return $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('coupon_id = ' . $iCouponId)
            ->execute('getField');
    }
}

?>	