<?php
Class Ynadvancedpayment_Service_ITransact_XMLResponse
{
	public $responseXML;
	private $xpath;

	public function __construct($xml) 
	{
		$this->responseXML = $xml;
		$xmldom = new DOMDocument();
		$xmldom->loadXML($xml);
		$this->xpath = new DOMXpath($xmldom);
	}	

	public function authCode() {
		return $this->simpleNodeValue("*/TransactionResult/AuthCode");
	}

	public function avsCategory() {
		return $this->simpleNodeValue("*/TransactionResult/AVSCategory");
	}

	public function avsResponse() {
		return $this->simpleNodeValue("*/TransactionResult/AVSResponse");
	}

	public function cvv2Response() {
		return $this->simpleNodeValue("*/TransactionResult/CVV2Response");
	}

	public function errorCategory() {
		return $this->simpleNodeValue("*/TransactionResult/ErrorCategory");
	}

	public function errorMessage() {
		return $this->simpleNodeValue("*/TransactionResult/ErrorMessage");
	}

	public function status() {
		return $this->simpleNodeValue("*/TransactionResult/Status");
	}
	
	public function amount() {
		return $this->simpleNodeValue("*/TransactionResult/Total");
	}

	public function xid() {
		return $this->simpleNodeValue("*/TransactionResult/XID");
	}

	private function simpleNodeValue($query) 
	{
		$node = $this->xpath->query($query);
		return $node->item(0)->nodeValue;
	}
}
?>