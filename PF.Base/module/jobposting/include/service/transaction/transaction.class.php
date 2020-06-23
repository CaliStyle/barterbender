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

class JobPosting_Service_Transaction_Transaction extends Phpfox_service
{
    private $_aStatus = array(
        1 => 'initialized',
        2 => 'pending',
        3 => 'completed',
        4 => 'denied'
    );
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('jobposting_transaction');
    }
    
    public function get($iId)
    {
       	$aRow = $this->database()->select('*')->from($this->_sTable)->where('transaction_id = '.(int)$iId)->execute('getSlaveRow');
		if($aRow)
		{
			$aRow['invoice'] = unserialize($aRow['invoice']);
		}
		return $aRow;
    }
    
    public function getAllStatuses()
    {
        return $this->_aStatus;
    }
    
    public function getStatusIdByName($sStatusName)
    {
        return array_search($sStatusName, $this->_aStatus);
    }
    
    public function getStatusNameById($iStatusId)
    {
        if(isset($this->_aStatus[$iStatusId]))
        {
            return $this->_aStatus[$iStatusId];
        }
        return false;
    }
	
	public function getTransaction($aConds, $sOrder, $iPage = 0, $iLimit = 0, $iCount = 0)
	{
		// Generate query object	
						
		$oSelect = $this -> database() 
						 -> select('tr.*,u.*,ca.name, ca.company_id,tr.status as status_pay')
						 -> from($this->_sTable, 'tr')
						 -> Join(PHpfox::getT('jobposting_company'),'ca','ca.company_id = tr.item_id')
						 -> join(Phpfox::getT('user'),'u','u.user_id=tr.user_id');
		
		// Get query table join			 
		
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

		
		$aTransactions = $oSelect->execute('getRows'); 
	 	return $aTransactions;
	}
	
	public function getItemCount($aConds = array())
	{
		$oQuery = $this -> database()
				-> select('count(*) as count')
				-> from($this->_sTable,'tr')
				-> Join(PHpfox::getT('jobposting_company'),'ca','ca.company_id = tr.item_id');
		
		if($aConds)
		{
			$oQuery->where($aConds);
		}
		
		$iCnt = (int)$oQuery-> execute('getSlaveField');
		return $iCnt;
	}
    
    public function isPaidToSponsor($iCompanyId)
    {
        $aRow = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('item_id = '.(int)$iCompanyId.' AND payment_type = 1 AND status = '.$this->getStatusIdByName('completed'))
            ->execute('getSlaveRow');
        
        if (!empty($aRow))
        {
            return true;
        }
        
        return false;
    }
}