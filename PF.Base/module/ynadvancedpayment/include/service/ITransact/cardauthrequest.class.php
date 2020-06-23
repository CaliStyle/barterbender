<?php
/**
 * 
 * @author MinhNC
 */
require_once dirname(dirname(__file__)) . '/ITransact/xmlrequest.class.php';

class Ynadvancedpayment_Service_ITransact_CardAuthRequest extends Ynadvancedpayment_Service_ITransact_XMLRequest
{
	public $account_number;
	public $expiration_month;
	public $expiration_year;
	public $cvv_number;

	public $email;
	public $bill_address1;
	public $bill_city;
	public $bill_country;
	public $bill_first_name;
	public $bill_last_name;
	public $bill_phone;
	public $bill_state;
	public $bill_zip;

	public $recur_recipe;
	public $recur_total;
	public $recur_description;
	public $recur_reps;

	public $ship_address1;
	public $ship_city;
	public $ship_country;
	public $ship_first_name;
	public $ship_last_name;
	public $ship_phone;
	public $ship_state;
	public $ship_zip;

	public $order_items;
	public $description;
	public $total;

	public $email_text;
	public $send_customer_email;
	public $send_merchant_email;
	public $test_mode;

	public function __construct($fields) 
	{
		while (list($key, $val) = array_shift($fields)) 
		{
			if (property_exists('Ynadvancedpayment_Service_ITransact_CardAuthRequest', $key))
			{
				$this->$key = $val;		
			}
		}
	}

	public function toXML() {

		$beginning = "<?xml version=\"1.0\"?><GatewayInterface>";
		$ending = "</GatewayInterface>";		
		$payload = $this->genPayload();
		$credentials = $this->genAPICredentials($payload);
		return $beginning . $credentials . $payload . $ending;			
	}

	public function genPayload() 
	{
		$writer = new XMLWriter();
		$writer->openMemory();	
		$writer->startElement("AuthTransaction");
			$writer->startElement("AccountInfo");		
				$writer->startElement("CardAccount");
					$writer->writeElement("AccountNumber", $this->account_number);
					$writer->writeElement("ExpirationMonth", $this->expiration_month);
					$writer->writeElement("ExpirationYear", $this->expiration_year);
					$writer->writeElement("CVVNumber", $this->cvv_number);
				$writer->endElement();
			$writer->endElement();
			$writer->startElement("CustomerData");
				$writer->writeElement("Email", $this->email);
				$writer->startElement("BillingAddress");
					$writer->writeElement("Address1", $this->bill_address1);
					$writer->writeElement("City", $this->bill_city);
					$writer->writeElement("Country", $this->bill_country);
					$writer->writeElement("FirstName", $this->bill_first_name);
					$writer->writeElement("LastName", $this->bill_last_name);
					$writer->writeElement("Phone", $this->bill_phone);
					$writer->writeElement("State", $this->bill_state);
					$writer->writeElement("Zip", $this->bill_zip);
				$writer->endElement();
		if($this->ship_address1) 
		{
				$writer->startElement("ShippingAddress");
					$writer->writeElement("Address1", $this->ship_address1);
					$writer->writeElement("Address2", $this->ship_address1);
					$writer->writeElement("City", $this->ship_city);
					$writer->writeElement("Country", $this->ship_country);
					$writer->writeElement("FirstName", $this->ship_first_name);
					$writer->writeElement("LastName", $this->ship_last_name);
					$writer->writeElement("Phone", $this->ship_phone);
					$writer->writeElement("State", $this->ship_state);
					$writer->writeElement("Zip", $this->ship_zip);
				$writer->endElement();
		}
			$writer->endElement();		
		if($this->recur_recipe) 
		{
			$writer->startElement("RecurringData");
				$writer->writeElement("Recipe", $this->recur_recipe);
				$writer->writeElement("RemReps", $this->recur_reps);
				$writer->writeElement("Total", $this->recur_total);
				$writer->writeElement("Description", $this->recur_description);
			$writer->endElement();
		}
		if($this->order_items) 
		{
			$writer->startElement("OrderItems");
			while ($item = array_shift($this->order_items)) 
			{
					$writer->startElement("Item");
					$writer->writeElement("Description", $item[1]['description']);
					$writer->writeElement("Cost", $item[1]['cost']);
					$writer->writeElement("Qty", $item[1]['qty']);
					$writer->endElement();					
			}
			$writer->endElement();
		} 
		else 
		{
			$writer->writeElement("Description", $this->description);
			$writer->writeElement("Total", $this->total);
		}
		if($this->email_text || $this->send_customer_email || $this->send_merchant_email || $this->test_mode)  {
			$writer->startElement("TransactionControl");
			if($this->email_text) {
				$writer->startElement("EmailText");
				while($item = array_shift($this->email_text)) {
					$writer->writeElement("EmailTextItem", $item[1]);
				}
				$writer->endElement();
			}
			if($this->send_customer_email) {
				$writer->writeElement("SendCustomerEmail",$this->send_customer_email);
			}
			if($this->send_merchant_email) {
				$writer->writeElement("SendMerchantEmail",$this->send_merchant_email);
			}
			if($this->test_mode) {
				$writer->writeElement("TestMode",$this->test_mode);
			}			
			$writer->endElement();			
		}
			$writer->endElement();
		$writer->endElement();
		return $writer->outputMemory();
	}
}
?>