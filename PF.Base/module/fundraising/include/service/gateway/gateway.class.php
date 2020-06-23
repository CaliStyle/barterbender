<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Service
 * @version 		$Id: gateway.class.php 1646 2010-06-10 12:40:28Z Raymond_Benc $
 */
class Fundraising_Service_Gateway_Gateway extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('fundraising_gatewayapi');	
	}
	
        public function getActiveGatewaySetting($sGatewayId)
        {
            $aRow = $this->database()->select('*')
                             ->from(Phpfox::getT('fundraising_gatewayapi'))
                             ->where('is_active = 1 AND gateway_id=\'' . $sGatewayId . '\' ')
                             ->execute('getRow');
            return $aRow;
        }
        
	public function getActive()
	{
		$aGateways = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->where('ag.is_active = 1')
			->execute('getRows');	
			
		foreach ($aGateways as $iKey => $aGateway)
		{
			$oGateway = Phpfox::getService('fundraising.gateways')->load($aGateway['gateway_id'], $aGateway);
			
			if ($oGateway === false)
			{
				continue;
			}
			
			$aGateways[$iKey]['custom'] = $oGateway->getEditForm();			
		}
			
		return $aGateways;	
	}
        
        public function getAllGateways()
	{
		$aGateways = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->execute('getRows');	
			
			
		return $aGateways;	
	}
        
        
	
	public function getUserGateways($iUserId)
	{
		$aRows = $this->database()->select('*')
			->from(Phpfox::getT('user_gateway'))
			->where('user_id = ' . (int) $iUserId)
			->execute('getSlaveRows');
			
		$aGateways = array();
		foreach ($aRows as $iKey => $mValue)
		{
			$aCache = unserialize($mValue['gateway_detail']);
			$bSkip = false;
			foreach ($aCache as $sSettingKey => $sSettingValue)
			{
				if (empty($sSettingValue))
				{
					$bSkip = true;
				}
			}
			
			if ($bSkip === true)
			{
				$aGateways[$mValue['gateway_id']]['gateway'] = null;
				
				continue;
			}
			
			// http://www.phpfox.com/tracker/view/15071/
			$aCache['seller_id'] = $mValue['user_id']; 
			$aGateways[$mValue['gateway_id']]['gateway'] = $aCache; //unserialize($mValue['gateway_detail']);
		}		
			
		return $aGateways;
	}
	
	public function get($aGatewayData = array())
	{
		$aGateways = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->where('ag.is_active = 1')
			->execute('getRows');
		
		foreach ($aGateways as $iKey => $aGateway)
		{
			if (isset($aGatewayData['fail_' . $aGateway['gateway_id']]) && $aGatewayData['fail_' . $aGateway['gateway_id']] === true)
			{
				unset($aGateways[$iKey]);
				
				continue;
			}
			
			if (!($oGateway = Phpfox::getService('fundraising.gateways')->load($aGateway['gateway_id'], array_merge($aGateway, $aGatewayData))))
			{				
				unset($aGateways[$iKey]);
				
				continue;
			}
			
			if (($aGateways[$iKey]['form'] = $oGateway->getForm()) === false)
			{				
				unset($aGateways[$iKey]);
			}
		}

		
		return $aGateways;
	}
	
	public function callback($sGateway)
	{
		Phpfox::startLog('Callback started.');
		if (empty($sGateway))
		{
			Phpfox::getService('fundraising.gateway.process')->addLog(null, Phpfox::endLog());
			
			return false;
		}
		
		$aGateway = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->where('ag.gateway_id = \'' . $this->database()->escape($sGateway) . '\' AND ag.is_active = 1')
			->execute('getRow');
			
		if (!isset($aGateway['gateway_id']))
		{
			Phpfox::getService('fundraising.gateway.process')->addLog(null, Phpfox::endLog());
			
			return false;
		}
		
		if (!($oGateway = Phpfox::getService('fundraising.gateways')->load($aGateway['gateway_id'], $_REQUEST)))
		{
			Phpfox::getService('fundraising.gateway.process')->addLog($aGateway['gateway_id'], Phpfox::endLog());
			
			return false;
		}
		
		$mReturn = $oGateway->callback();
		Phpfox::getService('fundraising.gateway.process')->addLog($aGateway['gateway_id'], Phpfox::endLog());
		if ($mReturn == 'redirect')
		{
			Phpfox::getLib('url')->send('');
		}
	}
	
	public function getPeriodPhrase($sPeriod, $sCost, $sDefaultCost)
	{		
		// recurring price = 0 then, no recurring!
		if(empty($sRecurring))
		{
			return null;
		}

		switch ($sPeriod)
		{
			case '1':
				$sPhrase = ($sCost == $sDefaultCost ? _p('api.cost_per_month', array('cost' => $sCost)) : _p('api.default_cost_and_then_cost_per_month', array('default_cost' => $sDefaultCost, 'cost' => $sCost)));
				break;
			case '2':
				$sPhrase = ($sCost == $sDefaultCost ? _p('api.cost_per_quarter', array('cost' => $sCost)) : _p('api.default_cost_and_then_cost_per_quarter', array('default_cost' => $sDefaultCost, 'cost' => $sCost)));
				break;
			case '3':
				$sPhrase = ($sCost == $sDefaultCost ? _p('api.cost_biannualy', array('cost' => $sCost)) : _p('api.default_cost_and_then_cost_biannualy', array('default_cost' => $sDefaultCost, 'cost' => $sCost)));
				break;
			case '4':
				$sPhrase = ($sCost == $sDefaultCost ? _p('api.cost_annually', array('cost' => $sCost)) : _p('api.default_cost_and_then_cost_annually', array('default_cost' => $sDefaultCost, 'cost' => $sCost)));
				break;				
		}
		
		return $sPhrase;
	}
	
	public function getForAdmin()
	{
		$aGateways = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->order('ag.title ASC')
			->execute('getRows');	
			
		return $aGateways;		
	}	
	
	public function getForEdit($sGateway)
	{
		$aGateway = $this->database()->select('*')
			->from($this->_sTable)
			->where('gateway_id = \'' . $this->database()->escape($sGateway) . '\'')
			->execute('getSlaveRow');
			
		if (!isset($aGateway['gateway_id']))
		{
			return false;
		}
			
		return $aGateway;
	}
        
        public function getForEditCustom($sGateway)
	{
		$aGateway = $this->database()->select('*')
			->from($this->_sTable)
			->where('gateway_id = \'' . $this->database()->escape($sGateway) . '\'')
			->execute('getSlaveRow');
			
		if (!isset($aGateway['gateway_id']))
		{
			return false;
		}
		
		$oGateway = Phpfox::getService('fundraising.gateways')->load($aGateway['gateway_id'], $aGateway);
		
		if ($oGateway === false)
		{
			return false;
		}
		
		$aGateway['custom'] = $oGateway->getEditForm();
			
		return $aGateway;
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('fundraising.service_gateway_gateway__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}

?>