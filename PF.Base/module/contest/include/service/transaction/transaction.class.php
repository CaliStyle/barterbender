<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Transaction_Transaction extends Phpfox_service {

	public function __construct()
	{
		$this->_sTable = Phpfox::getT('contest_transaction');
	}

	public function searchTransactions($aConds, $sSort = 'transaction.time_stamp DESC ', $iPage = '', $iLimit = '')
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'transaction')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = transaction.user_id')
			->leftJoin(Phpfox::getT('contest'), 'contest', 'contest.contest_id = transaction.contest_id')
			->where($aConds)
			->order($sSort)
			->execute('getSlaveField');

		$aItems = array();
		if ($iCnt)
		{		
			$aItems = $this->database()->select('transaction.*, contest.*, ' . Phpfox::getUserField())
				->from($this->_sTable, 'transaction')
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = transaction.user_id')
				->leftJoin(Phpfox::getT('contest'), 'contest', 'contest.contest_id = transaction.contest_id')
				->where($aConds)
				->order($sSort)
				->limit($iPage, $iLimit, $iCnt)
				->execute('getSlaveRows');
				
			foreach ($aItems as $iKey => $aItem)
			{
				$aInvoice = unserialize($aItem['invoice']);
				$sServiceType = Phpfox::getService('contest.transaction')->getContestServiceTypeStringFromInvoice($aInvoice);;
				
				$aItems[$iKey]['service_type_string'] = $sServiceType;
				$aItems[$iKey]['link'] = ($aItem['user_id'] ? Phpfox::getLib('url')->permalink($aItem['user_name'] . '.contest', $aItem['contest_id'], $aItem['contest_name']) : Phpfox::getLib('url')->permalink('contest', $aItem['contest_id'], $aItem['contest_name']));

				$aItems[$iKey]['status_text'] = _p('contest.' . Phpfox::getService('contest.constant')->getTransactionStatusNameByStatusId($aItem['status']));

				$aItems[$iKey]['fee_text'] = Phpfox::getService('contest.helper')->getMoneyText($aItem['amount'], $aItem['currency']);
			}
		}


		return array($iCnt, $aItems);
	}


	public function getContestServiceTypeStringFromInvoice($aInvoice)
	{
		$aServiceType = array();

		if($aInvoice['is_publish'])
		{
			$aServiceType[] = _p('contest.publish');
		}
		if($aInvoice['is_premium'])
		{
			$aServiceType[] = _p('contest.premium');
		}
		if($aInvoice['is_feature'])
		{
			$aServiceType[] = _p('contest.feature');
		}

		if($aInvoice['is_ending_soon'])
		{
			$aServiceType[] = _p('contest.ending_soon');
		}

		return implode(', ', $aServiceType);
	}

	/**
	 * return array contents service requested by user corresponding to transaction id
	 * @return mixed array
	 */
	public function getUserRequestsFromTransaction($iTransactionId)
	{
		$aTransaction = $this->database()->select('transaction.*')
				->from($this->_sTable, 'transaction')
				->where('transaction_id = ' . $iTransactionId)
				->execute('getSlaveRow');

		if(!$aTransaction)
		{
			//defensive programming
			return false;
		}

		$iContestId = $aTransaction['contest_id'];

		$aInvoice = unserialize($aTransaction['invoice']);

		$aService = array();

		if($aInvoice['is_publish'])
		{
			$aService[] = 'publish';
		}

		if($aInvoice['is_premium'])
		{
			$aService[] = 'premium';
		}

		if($aInvoice['is_feature'])
		{
			$aService[] = 'feature';
		}

		if($aInvoice['is_ending_soon'])
		{
			$aService[] = 'ending_soon';
		}


		return array(
			'iContestId' => $iContestId,
			'aService' => $aService
			);


	}

	

}