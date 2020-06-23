<?php
defined('PHPFOX') or die('NO DICE!');

class Contactimporter_Service_Contact extends Phpfox_Service
{
	var $_iLimit = 100;
	//limit email send every run time

	public function __construct()
	{
		$this -> _sTable = Phpfox::getT('contactimporter_contact');
	}

	public function addTotal($sProvider, $iTotalInvite)
	{
		$iUserId = Phpfox::getUserId();
		$aRow = $this -> database() -> select('*') -> from($this -> _sTable) -> where(sprintf('user_id = %d AND provider = "%s"', $iUserId, $sProvider)) -> execute('getSlaveRow');
		if ($aRow)
		{
			$aUpdate = array('total' => (int)$aRow['total'] + $iTotalInvite, );
			$this -> database() -> update($this -> _sTable, $aUpdate, 'contact_id = ' . (int)$aRow['contact_id']);
		}
		else
		{
			$aSql = array(
				'user_id' => $iUserId,
				'provider' => $sProvider,
				'total' => $iTotalInvite,
			);
			$this -> database() -> insert($this -> _sTable, $aSql);
		}
	}

	public function getStatistic()
	{
		$aProviders = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('contactimporter_providers'), 'cp') -> where('(type = "email" AND enable = 1 AND default_domain !="" ) OR (type = "social" AND enable = 1)') -> order('cp.order_providers ASC') -> execute('getSlaveRows');

		$aProviders = phpfox::getService('contactimporter') -> allowProvider($aProviders);
		if (count($aProviders))
		{
			foreach ($aProviders as $iKey => $aProvider)
			{
				$sName = trim($aProvider['name'], '_');
				$aProviders[$iKey] = array(
					'total' => $this -> getProviderStatistic($sName),
					'title' => $aProvider['title'],
				);
			}
		}

		$iTotal = (int)$this -> database() -> select('SUM(total)') -> from($this -> _sTable) -> where('user_id = ' . Phpfox::getUserId()) -> execute('getSlaveField');
		return array(
			$iTotal,
			$aProviders
		);
	}

	public function getProviderStatistic($sProvider)
	{
		return (int)$this -> database() -> select('total') -> from($this -> _sTable) -> where(sprintf('user_id = %d AND provider = "%s"', Phpfox::getUserId(), $sProvider)) -> execute('getSlaveField');
	}
	public function getProviderTotalInvitations($sProvider)
	{
		$total = (int)$this -> database() -> select('SUM(total) as total') -> from($this -> _sTable) -> where(sprintf('provider = "%s"', $sProvider)) -> execute('getSlaveField');
		return $total;
	}
}
