<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

class JobPosting_Service_Transaction_Process extends Phpfox_service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('jobposting_transaction');
    }
    
    /**
     * @param array $aVals
     * @return int
     */
    public function add($aVals)
    {
        return $this->database()->insert($this->_sTable, $aVals);
    }
    
    /**
     * @param int $iId
     * @param array $aParam
     */
    public function update($iId, $aParam)
    {
        $aUpdate = array(
            'time_stamp' => PHPFOX_TIME,
            'transaction_log' => isset($aParam['aTransactionDetail']) ? serialize($aParam['aTransactionDetail']) : NULL,
            'amount' => $aParam['total_paid'],
            'status' => Phpfox::getService('jobposting.transaction')->getStatusIdByName($aParam['status']),
            'paypal_account' =>'',
            'paypal_transaction_id' => ''
        );
        
        return $this->database()->update($this->_sTable, $aUpdate, 'transaction_id = '.(int)$iId);
    }
}
