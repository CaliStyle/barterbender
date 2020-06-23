<?php

defined('PHPFOX') or exit('NO DICE!');

require_once dirname(dirname(__file__)) . '/service/paymentgateway.class.php';

class Ynadvancedpayment_Service_Authorizenetarb extends Ynadvancedpayment_Service_Paymentgateway
{
    protected $_arb_host = '';
    protected $_api_login = '';
    protected $_transaction_key = '';

    public function initialize($gateway, $params) 
    {
    	$this->_order = $params;
        $this->_gatewaySettings = (array) $gateway['config'];
		$this->_gatewaySettings['test_mode'] = $gateway['test_mode'];
        $this->_api_login = $this->plugin_settings('api_login');
        $this->_transaction_key = $this->plugin_settings('transaction_key');
        $subdomain = ($this->plugin_settings('test_mode')) ? 'apitest' : 'api'; 
		$this->_arb_host = "https://" . $subdomain . ".authorize.net/xml/v1/request.api"; 
    }
	public function create_subscription($gateway, $params, $package)
	{
		$this->initialize($gateway, $params); 

		$amount = number_format($this->order('total'), 2, '.', '');
		$unit = '';
		$length = 0;
		$recurrence = $package['recurrence'];
		switch ($package['recurrence_type']) 
		{
			case 'day':
				$length = $recurrence;
				$unit = 'days';
				break;
			case 'week':
				$length = $recurrence * 7;
				$unit = 'days';
				break;
			case 'month':
				$length = $recurrence;
				$unit = 'months';
				break;
			case 'year':
				$length = $recurrence * 12;
				$unit = 'months';
				break;
		}
		$startDate = date("Y-m-d", strtotime("+ ".$length ." ".str_replace('s', '', $unit)));
		$totalOccurrences = 9999;
		$trialOccurrences = 0;
		$trialAmount = 0.00;
		$cardNumber = $this -> order('credit_card_number');
		$firstName = $this->order('first_name');
		$lastName = $this->order('last_name');
		$expirationDate = str_pad($this->order('expiration_year'), 4, '0', STR_PAD_LEFT) ."-".str_pad($this->order('expiration_month'), 2, '0', STR_PAD_LEFT);
		$content =
	        "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
	        "<ARBCreateSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
	        "<merchantAuthentication>".
	        "<name>" . $this ->_api_login . "</name>".
	        "<transactionKey>" . $this -> _transaction_key . "</transactionKey>".
	        "</merchantAuthentication>".
	        "<subscription>".
	        "<paymentSchedule>".
	        "<interval>".
	        "<length>". $length ."</length>".
	        "<unit>". $unit ."</unit>".
	        "</interval>".
	        "<startDate>" . $startDate . "</startDate>".
	        "<totalOccurrences>". $totalOccurrences . "</totalOccurrences>".
	        "<trialOccurrences>". $trialOccurrences . "</trialOccurrences>".
	        "</paymentSchedule>".
	        "<amount>". $amount ."</amount>".
	        "<trialAmount>" . $trialAmount . "</trialAmount>".
	        "<payment>".
	        "<creditCard>".
	        "<cardNumber>" . $cardNumber . "</cardNumber>".
	        "<expirationDate>" . $expirationDate . "</expirationDate>".
	        "</creditCard>".
	        "</payment>".
	        "<billTo>".
	        "<firstName>". $firstName . "</firstName>".
	        "<lastName>" . $lastName . "</lastName>".
	        "</billTo>".
	        "</subscription>".
	        "</ARBCreateSubscriptionRequest>";   
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this -> _arb_host);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		list ($resultCode, $code, $text, $subscriptionId) = $this -> _parse_return($response);
		if($resultCode == 'Ok')
		{
			return $subscriptionId;
		}
		else {
			return NULL;
		}
	}
    
	//function to parse Authorize.net response
	protected function _parse_return($content)
	{
		$resultCode = $this-> _substring_between($content,'<resultCode>','</resultCode>');
		$code = $this-> _substring_between($content,'<code>','</code>');
		$text = $this-> _substring_between($content,'<text>','</text>');
		$subscriptionId = $this-> _substring_between($content,'<subscriptionId>','</subscriptionId>');
		return array ($resultCode, $code, $text, $subscriptionId);
	}
	
	//helper function for parsing response
	protected function _substring_between($haystack,$start,$end) 
	{
		if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) 
		{
			return false;
		} 
		else 
		{
			$start_position = strpos($haystack,$start)+strlen($start);
			$end_position = strpos($haystack,$end);
			return substr($haystack,$start_position,$end_position-$start_position);
		}
	}
}
