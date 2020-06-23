<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: paypal.class.php 7204 2014-03-18 18:56:27Z Fern $
 */
class Phpfox_Gateway_Api_GoPay implements Phpfox_Gateway_Interface
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
	private $_aCurrency = array('USD', 'CAD', 'GBP', 'EUR', 'AUD', 'NZD','CZK');
	
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
			'client_id' => array(
				'phrase' => Phpfox::getPhrase('ynadvancedpayment.client_id'),
				'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.client_id'),
				'value' => (isset($this->_aParam['setting']['client_id']) ? $this->_aParam['setting']['client_id'] : '')
			),
			'secure_key' => array(
				'phrase' => Phpfox::getPhrase('ynadvancedpayment.secure_key'),
				'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.secure_key'),
				'value' => (isset($this->_aParam['setting']['secure_key']) ? $this->_aParam['setting']['secure_key'] : '')
			),
			'goid' => array(
				'phrase' => Phpfox::getPhrase('ynadvancedpayment.go_pay_id'),
				'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.go_pay_id'),
				'value' => (isset($this->_aParam['setting']['goid']) ? $this->_aParam['setting']['goid'] : '')
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
			'url' => ($this->_aParam['is_test'] ?  Phpfox::getLib('url')->makeUrl('ynadvancedpayment.gopay', array('mode' => 0)) : Phpfox::getLib('url')->makeUrl('ynadvancedpayment.gopay', array('mode' => 1))),
			'param' => array(
				'goid' => isset($this->_aParam['setting']['goid'])?$this->_aParam['setting']['goid']:'',
				'client_id' => isset($this->_aParam['setting']['client_id'])?$this->_aParam['setting']['client_id']:'',
				'secure_key' => isset($this->_aParam['setting']['secure_key'])?$this->_aParam['setting']['secure_key']:'',
				'is_test' => $this->_aParam['is_test'] ? 1 : 0,
				'item_name' => $this->_aParam['item_name'],
				'item_number' => $this->_aParam['item_number'],
				'currency_code' => $this->_aParam['currency_code'],
				'notify_url' => Phpfox::getLib('gateway')->url('gopay'),
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
		$aParams = $_GET + $_POST;
		Phpfox::log('Starting GoPay callback');
        // Loop through each of the variables posted by GoPay
		if(!isset($aParams['id'])){
			return;
		}
        
        Phpfox::log('Attempting callback');   
        // Post back to GoPay system to validate
        // get token
        $aCallBack = Phpfox::getService('ynadvancedpayment.gopayaim')->process_callback($aParams['id']); 

        $aCallBack = json_decode($aCallBack,true);

		Phpfox::log('Callback OK');
		
		if(!empty($aCallBack)){

			if(isset($aCallBack['order_number'])){
				
				$aParts = explode('|', $aCallBack['order_number']);				

				if (Phpfox::isModule($aParts[0]))
				{
					Phpfox::log('Module is valid.');
					Phpfox::log('Checking module callback for method: paymentApiCallback');
					if (Phpfox::hasCallback($aParts[0], 'paymentApiCallback'))
					{

						Phpfox::log('Module callback is valid.');

						$sStatus = null;				
						if (isset($aCallBack['state']))
						{
							switch ($aCallBack['state'])
							{
								case 'PAID':
									$sStatus = 'completed';
									break;
								default:
									$sStatus = 'cancel';
									break;
							}
						}

						Phpfox::log('Status built: ' . $sStatus);

						if($sStatus !== null)
						{
							Phpfox::log('Executing module callback');
							Phpfox::callback($aParts[0] . '.paymentApiCallback', array(
									'gateway' => 'gopay',
									'ref' => $aCallBack['order_number'],						
									'status' => $sStatus,
									'item_number' => $aParts[1],
									'total_paid' => (isset($aCallBack['amount']) ? ($aCallBack['amount']/100) : null)
								)
							);
							
							header('HTTP/1.1 200 OK');				
						}
						else 
						{
							Phpfox::log('Status is NULL. Nothing to do');
						}


					}
					else{
						Phpfox::log('Module callback is not valid.');
					}
				

				}
				else 
				{
					Phpfox::log('Module is not valid.');
				}	
			}
		}
	
	}
}

?>
