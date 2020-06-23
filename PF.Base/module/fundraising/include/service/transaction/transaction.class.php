<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Transaction_Transaction extends Phpfox_Service {

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
	 * get status number based on the name of status
	 * @by minhta
	 * @param string $sStatus name of status we want to retrieve
	 * @return
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
        $this->_sTable = Phpfox::getT('fundraising_transaction');
    }


	public function getTransactionLog($iTransactionId)
	{
		$aRow = $this->database()->select('transaction_log')
				->from($this->_sTable)
				->where('transaction_id = ' . $iTransactionId )
				->execute('getSlaveRow');

		if(isset($aRow['transaction_log']))
		{
			return $aRow['transaction_log'];
		}
		else
		{
			return '';
		}
	}

    private function getDonorField() {
        return 'fd.full_name as guest_full_name, fd.email_address as guest_email_address, fd.is_guest, ';
    }

	public function getTransactionById($iTransactionId)
	{
	
		$aRow = $this->database()->select('*')
			->from($this->_sTable)
			->where('transaction_id = ' . $iTransactionId )
			->execute('getSlaveRow');

		if(isset($aRow['transaction_id']))
		{
			return $aRow;
		}
		else
		{
			return false;
		}
	}
    /**
     * get transaction by campaign ID
     * @by datlv
     * @param $iCampaignId
     * @param null $aVals
     * @param int $iPage
     * @param $iLimit
     * @return array($iTotal , $aTransactions)
     */

    public function getTransactionByCampaignId($sCondition = '', $iPage = 1, $iLimit)
    {
        $iTotal = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'ft')
            ->leftjoin(Phpfox::getT('fundraising_donor'), 'fd', 'fd.donor_id = ft.donor_id')
            ->leftjoin(Phpfox::getT('fundraising_campaign'), 'fc', 'fc.campaign_id = ft.campaign_id')
            ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fd.user_id')
            ->where($sCondition)
            ->execute('getSlaveField');

        $aTransactions = $this->database()->select('ft.* , fd.full_name as guest_full_name, fd.email_address as guest_email_address, fd.is_guest, u.full_name, u.email , fc.title')
            ->from($this->_sTable, 'ft')
            ->leftjoin(Phpfox::getT('fundraising_donor'), 'fd', 'fd.donor_id = ft.donor_id')
            ->leftjoin(Phpfox::getT('fundraising_campaign'), 'fc', 'fc.campaign_id = ft.campaign_id')
            ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fd.user_id')
            ->where($sCondition)
			->order('ft.time_stamp DESC')
            ->limit($iPage, $iLimit, $iTotal)
            ->execute('getSlaveRows');

        if($aTransactions)
        {
			
            foreach($aTransactions as &$aTransaction) {
               $aTransaction['status'] = Phpfox::getService('fundraising.transaction')->getStatusPhraseFromCode($aTransaction['status']); 
            }
            return array($iTotal,$aTransactions);
        }
        else
        {
            return array($iTotal, null);
        }
    }

    /**
     * get all transaction of all campaign
     * @by datlv
     * @param null $aVals
     * @param int $iPage
     * @param $iLimit
     * @return array($iTotal, $aCampaignStats)
     */

    public function getTransactionForAllCampaign($sCondition = '', $iPage = 1, $iLimit)
    {
        $iTotal = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'ft')
            ->leftjoin(Phpfox::getT('fundraising_campaign'), 'fc', 'fc.campaign_id = ft.campaign_id')
            ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where($sCondition)
            ->execute('getSlaveField');

        $aCampaignStats = $this->database()->select('ft.*, fc.title, u.full_name as owner')
            ->from($this->_sTable , 'ft')
            ->leftjoin(Phpfox::getT('fundraising_campaign'), 'fc', 'fc.campaign_id = ft.campaign_id')
            ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where($sCondition)
			->order('ft.time_stamp DESC')
            ->limit($iPage, $iLimit, $iTotal)
            ->execute('getSlaveRows');

        if($aCampaignStats)
        {
            foreach($aCampaignStats as $iKey => $aCampaignStat) {
                $aCampaignStats[$iKey]['status'] = Phpfox::getService('fundraising.transaction')->getStatusPhraseFromCode($aCampaignStats[$iKey]['status']); 
                $aDonor = Phpfox::getService('fundraising.user')->getDonorNameById($aCampaignStat['donor_id']);
                if($aDonor)
                {
                    $aCampaignStats[$iKey]['donor'] = $aDonor['is_guest'] ? $aDonor['guest_full_name'] : ($aDonor['full_name'] ? $aDonor['full_name']  : '');
                }
                else
                {
                    $aCampaignStats[$iKey]['donor'] = '';
                }
            }

            return array($iTotal, $aCampaignStats);
        }
        else
        {
            return array($iTotal, null);
        }
    }
}

?>
