<?php

defined('PHPFOX') or exit('NO DICE!');


class Ecommerce_Service_Adaptivepayment extends Phpfox_Service
{
	public function getApiKeyAdaptivePayMent(){
        $aGlobalSetting = Phpfox::getService('ecommerce')->getGlobalSetting();

		return array(
			'UserName' => $aGlobalSetting['actual_setting']['username_paypal'],
			'Password' => $aGlobalSetting['actual_setting']['password_paypal'],
			'Signature' => $aGlobalSetting['actual_setting']['signature_paypal'],
			'AppId' 	=> 	$aGlobalSetting['actual_setting']['application_id_paypal'],
		);
	}

	public function checkStatusAdaptivePaypal($iApiKeyPaypal){
		$aApiCredentials = $this->getApiKeyAdaptivePayMent();
        $aGatewaySettingPaypal = Phpfox::getService('ecommerce.helper')->getGatewaySetting('paypal');

        $host = '';
		if($aGatewaySettingPaypal['is_test']){
			$host = 'https://svcs.sandbox.paypal.com/AdaptivePayments/PaymentDetails ';
		}
		else
		{
			$host = 'https://svcs.paypal.com/AdaptivePayments/PaymentDetails '; 
		}
        $ch = curl_init($host);

        $data = '';
        $post_array = array(
            "payKey" => $iApiKeyPaypal,
            "requestEnvelope.errorLanguage" => "en_US",
        );

        while (list ($key, $val) = each($post_array)) 
        {
            $data .= $key . "=" . urlencode($val) . "&";
        }


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            "X-PAYPAL-SECURITY-USERID: ".$aApiCredentials['UserName'],
            "X-PAYPAL-SECURITY-PASSWORD: ".$aApiCredentials['Password'],
            "X-PAYPAL-SECURITY-SIGNATURE: ".$aApiCredentials['Signature'],
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON",
            "X-PAYPAL-APPLICATION-ID: ".$aApiCredentials['AppId']
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $aStatusReponse = json_decode(curl_exec($ch),true);

        return $aStatusReponse;
	}
}