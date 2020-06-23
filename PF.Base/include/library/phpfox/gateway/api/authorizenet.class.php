<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Authorize.Net Payment Gateway API
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: paypal.class.php 7204 2014-03-18 18:56:27Z Fern $
 */
class Phpfox_Gateway_Api_Authorizenet implements Phpfox_Gateway_Interface
{
	/**
	 * Holds an ARRAY of settings to pass to the form
	 *
	 * @var array
	 */
	private $_aParam = array();
	
	/**
	 * Holds an ARRAY of supported currencies for this payment gateway
	 *
	 * http://support.authorize.net/authkb/index?page=content&id=A414&actp=RSS
	 * http://community.developer.authorize.net/t5/The-Authorize-Net-Developer-Blog/Authorize-Net-UK-Europe-Update/ba-p/35957
	 * @var array
	 */
	private $_aCurrency = array('USD', 'CAD', 'GBP', 'EUR', 'AUD', 'NZD');
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		
	}	
	
	/**
	 * Set the settings to be used with this class and prepare them so they are in an array
	 *
	 * @param array $aSetting ARRAY of settings to prepare
	 */
	public function set($aSetting)
	{
		$this->_aParam = $aSetting;
		
		if (Phpfox::getLib('parse.format')->isSerialized($aSetting['setting']))
		{
			$this->_aParam['setting'] = unserialize($aSetting['setting']);
		}
	}
	
	/**
	 * Each gateway has a unique list of params that must be passed with the HTML form when posting it
	 * to their site. This method creates that set of custom fields.
	 *
	 * @return array ARRAY of all the custom params
	 */
	public function getEditForm()
	{		
		return array(
			'api_login' => array(
				'phrase' => Phpfox::getPhrase('ynadvancedpayment.api_login_id'),
				'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.the_merchant_api_login_id_is_provided_in_the_merchant_interface_and_must_be_stored_securely'),
				'value' => (isset($this->_aParam['setting']['api_login']) ? $this->_aParam['setting']['api_login'] : '')
			), 
			'transaction_key' => array(
				'phrase' => Phpfox::getPhrase('ynadvancedpayment.transaction_key'),
				'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.the_merchant_transaction_key_is_provided_in_the_merchant_interface_and_must_be_stored_securely'),
				'value' => (isset($this->_aParam['setting']['transaction_key']) ? $this->_aParam['setting']['transaction_key'] : '')
			), 
			'transaction_settings' => array(
				'phrase' => Phpfox::getPhrase('ynadvancedpayment.credit_card_transaction_types'),
				'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.credit_card_transaction_types_hint_2'),
				'value' => (isset($this->_aParam['setting']['transaction_settings']) ? $this->_aParam['setting']['transaction_settings'] : 'AUTH_CAPTURE')
			), 
		);
	}
	
	/**
	 * Returns the actual HTML <form> used to post information to the 3rd party gateway when purchasing
	 * an item using this specific payment gateway
	 *
	 * @return bool FALSE if we can't use this payment gateway to purchase this item or ARRAY if we have successfully created a form
	 */
	public function getForm()
	{		
		$bCurrencySupported = true;
				
		if (!in_array($this->_aParam['currency_code'], $this->_aCurrency))
		{
			if (!empty($this->_aParam['alternative_cost']))
			{
				$aCosts = unserialize($this->_aParam['alternative_cost']);
				foreach ($aCosts as $aCost)
				{
					$sCode = key($aCost);
					$iPrice = $aCost[key($aCost)];
					
					if (in_array($sCode, $this->_aCurrency))
					{
						$this->_aParam['amount'] = $iPrice;
						$this->_aParam['currency_code'] = $sCode;
						$bCurrencySupported = false;
						break;
					}
				}
			   
				if ($bCurrencySupported === true)
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		
		$aForm = array(
			'url' => ($this->_aParam['is_test'] ?  Phpfox::getLib('url')->makeUrl('ynadvancedpayment.authorizenet', array('mode' => 0)) : Phpfox::getLib('url')->makeUrl('ynadvancedpayment.authorizenet', array('mode' => 1))),
			'param' => array(
				'api_login' => $this->_aParam['setting']['api_login'],
				'transaction_key' => $this->_aParam['setting']['transaction_key'],
				'transaction_settings' => $this->_aParam['setting']['transaction_settings'],
				'is_test' => $this->_aParam['is_test'] ? 1 : 0,
				'item_name' => $this->_aParam['item_name'],
				'item_number' => $this->_aParam['item_number'],
				'currency_code' => $this->_aParam['currency_code'],
				'notify_url' => Phpfox::getLib('gateway')->url('authorizenet'),
				'return' => $this->_aParam['return'],
			)
		);
		
		if ($this->_aParam['recurring'] > 0)
		{
	        switch ($this->_aParam['recurring'])
	        {
				case '1':
					$sPeriod = 'month';
					$iEach = 1;
					break;
				case '2':
					$sPeriod = 'month';
					$iEach = 3;
					break;
				case '3':
					$sPeriod = 'month';
					$iEach = 6;
					break;
				case '4':
					$sPeriod = 'year';
					$iEach = 1;
					break;              
	        }			
			
			if ((!isset($this->_aParam['recurring_cost']) || empty($this->_aParam['recurring_cost'])) 
				&& !empty($this->_aParam['alternative_recurring_cost']))
			{
				$aCosts = unserialize($this->_aParam['alternative_recurring_cost']);
				$bPassed = false;
				foreach ($aCosts as $aCost)
				{
					foreach($aCost as $sKey => $iCost)
					{
						if (in_array($sKey, $this->_aCurrency))
						{
							// Make all in the same currency
							$this->_aParam['currency_code'] = $sKey;
							$this->_aParam['amount'] = unserialize($this->_aParam['alternative_cost']);
							$this->_aParam['amount'] = $this->_aParam['amount'][0][$sKey];
							
							$this->_aParam['recurring_cost'] = $iCost;
							if (is_array($this->_aParam['recurring_cost']))
							{
								$aRec = array_values($this->_aParam['recurring_cost']);
								$this->_aParam['recurring_cost'] = array_shift($aRec);
							}
							$bPassed = true;
							break;
						}
					}
					
					if($bPassed)
					{
						break;
					}
				}
			   
				if ($bPassed === false)
				{
					return false;
				}
			}
			
			// If recurring is not zero, set the recurring settings
			if($this->_aParam['recurring_cost'] > 0)
			{
				$aForm['param']['cmd'] = 'recurring';
				$aForm['param']['amount'] = $this->_aParam['amount'];
				$aForm['param']['recurrence_type'] = $sPeriod;
				$aForm['param']['recurrence'] = $iEach;
				$aForm['param']['recurring_cost'] = $this->_aParam['recurring_cost']; // $aCosts[$this->_aParam['currency_code']]; change made for 3.7.1
			}
			// if zero, why to set recurring?
			else
			{
				$aForm['param']['cmd'] = 'not_recurring';
				$aForm['param']['amount'] = $this->_aParam['amount'];
			}
		}
		else 
		{
			$aForm['param']['cmd'] = 'not_recurring';
			$aForm['param']['amount'] = $this->_aParam['amount'];
		}
		
		return $aForm;
	}
	
	/**
	 * Performs the callback routine when the 3rd party payment gateway sends back a request to the server,
	 * which we must then back and verify that it is a valid request. This then connects to a specific module
	 * based on the information passed when posting the form to the server.
	 *
	 */
	public function callback()
	{
		return false;				
	}
}

?>
