<?php

require_once dirname(dirname(__file__)) . '/ITransact/xmlresponse.class.php';
class Ynadvancedpayment_Service_ITransact_XMLRequest
{
	public $api_key;
	public $username;
	public $target_gateway;

	public function genAPICredentials($paylaod) 
	{
		$writer = new XMLWriter();
		$writer->openMemory();	
		$writer->startElement("APICredentials");
			$writer->writeElement("Username", $this->username);
			$writer->writeElement("TargetGateway", $this->target_gateway);
			$writer->writeElement("PayloadSignature", $this->payloadSignature($paylaod));
		$writer->endElement();
		return $writer->outputMemory();		
	}

	public function submit($url, $postData) 
	{
		$header[] = "Content-type: text/xml";
		$header[] = "Content-length: ".strlen($postData);

		$ch = curl_init(); /// initialize a cURL session 
		curl_setopt ($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);		
		curl_setopt($ch, CURLOPT_POST, 1);  /// tell it to make a POST, not a GET
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 		
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); /// This allows the output to be set into a variable $datastream
		curl_setopt ($ch, CURLOPT_TIMEOUT, 360);
		curl_setopt ($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);		

		$datastream = curl_exec ($ch); /// execute the curl session and return the output to a variable $datastream
		$datastream = str_replace(" standalone=\"yes\"","",$datastream);
		curl_close ($ch); /// close the curl session ^M
		return new Ynadvancedpayment_Service_ITransact_XMLResponse($datastream);
	}

	private function payloadSignature($payload) 
	{
		$hash = hash_hmac('sha1', $payload, $this->api_key, true);		
		$sig = base64_encode($hash);
  		return 	$sig;
	}
}
?>