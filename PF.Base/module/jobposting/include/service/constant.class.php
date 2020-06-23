<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [YOUNET_COPPYRIGHT]
 * @author          VuDP, AnNT
 * @package         Module_jobposting
 */

class JobPosting_Service_Constant extends Phpfox_service
{
    private $_aTransactionStatus = array(
        1 => 'initialized',
        2 => 'pending',
        3 => 'success'
    );
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        
    }
    
    public function getAllTransactionStatuses()
    {
        return $this->_aTransactionStatus;
    }
    
    public function getTransactionStatusIdByName($sStatusName)
    {
        return array_search($sStatusName, $this->_aTransactionStatus);
    }
    
    public function getTransactionStatusNameById($iStatusId)
    {
        if(isset($this->_aTransactionStatus[$iStatusId]))
        {
            return $this->_aTransactionStatus[$iStatusId];
        }
        return false;
    }

}
