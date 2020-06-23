<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynadvancedpayment_Service_Paymentgateway extends Phpfox_Service
{
	protected $_gatewaySettings = array();
	protected $_order = array();
	//the core library for payments,
	protected $_core;

	public function __call($method, $args)
	{
		try
		{
			if ( ! method_exists($this->_core, $method))
			{
				throw new Exception('Call to undefined method %s::%s() in %s on line %s');
			}
			else if ( ! is_callable(array($this->_core, $method)))
			{
				throw new Exception('Call to private method %s::%s() in %s on line %s');
			}
		}
		catch(Exception $e)
		{
			$backtrace = $e->getTrace();
			$backtrace = $backtrace[1];
			return trigger_error(sprintf($e->getMessage(), $backtrace['class'], $backtrace['function'], $backtrace['file'], $backtrace['line']));
		}
		
		return call_user_func_array(array($this->_core, $method), $args);
	}

	public function plugin_settings($key, $default = FALSE)
	{
		$settings = $this->_gatewaySettings;
		
		if ($key === FALSE)
		{
			return ($settings) ? $settings : $default;
		}
		
		return (isset($settings[$key])) ? $settings[$key] : $default;
	}

	public function order($key = FALSE)
	{
		if ($key !== FALSE)
		{
			return (isset($this->_order[$key])) ? $this->_order[$key] : FALSE;
		}
		
		return $this->_order;
	}

	public function year_2($year)
	{
		if (strlen($year > 2))
		{
			return substr($year, -2);
		}
		return str_pad($year, 2, '0', STR_PAD_LEFT);
	}

	public function strip_punctuation($text) 
    {
        return preg_replace('/[^a-zA-Z0-9\s-_]/', ' ', $text);
    }

	public function getGatewayById($sGateway, $returnObject = true)
	{
		$aGateway = $this->database()->select('*')
			->from(Phpfox::getT('api_gateway'))
			->where('gateway_id = \'' . $this->database()->escape($sGateway) . '\'')
			->execute('getSlaveRow');
			
		if (!isset($aGateway['gateway_id']))
		{
			return false;
		}

		if($returnObject === false){
			if('gopay' == $sGateway){
				$aSetting =  unserialize($aGateway['setting']);
				$aGateway = array_merge($aGateway,$aSetting);
			}
			return $aGateway;
		}
		
		$oGateway = Phpfox::getLib('gateway')->load($aGateway['gateway_id'], $aGateway);
		
		if ($oGateway === false)
		{
			return false;
		}
		
		$aGateway['custom'] = $oGateway->getEditForm();
			
		return $aGateway;
	}    	

	public function getYNSubscriptionByGatewayIdAndGetawaySubscriptionId($gateway_id, $getaway_subscription_id){
		return $this->database()
			->select('*')
			->from(Phpfox::getT('ynadvancedpayment_subscriptions'),'c')
			->where("gateway_id = '" . $gateway_id . "' AND getaway_subscription_id = " . (int)$getaway_subscription_id)
			->execute('getSlaveRow');
	}
	public function getPackageId($iPurchaseId)
    {
        return $this->database()->select('sp.package_id')
                ->from(Phpfox::getT('subscribe_purchase'), 'sp')
                ->where('sp.purchase_id = ' . (int)$iPurchaseId)
                ->execute('getSlaveField');
    }
}
