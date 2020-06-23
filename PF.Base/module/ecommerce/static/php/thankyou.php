<?php

include 'cli.php';

$aParams = $_GET + $_POST;

if(isset($aParams['sLocation'])){
	$sLocation = $_GET['sLocation'];
	$sUrl = urldecode($sLocation);
	header('Location: ' . $sUrl);
}

print_r($aParams); die;

$aParamAdaptivePayment = Phpfox::getService('ecommerce.adaptivepayment')->checkStatusAdaptivePaypal($aParams['pay_key']);
if(isset($aParams['status']) && $aParams['status']=='COMPLETED'){

	if(isset($aParamAdaptivePayment['paymentInfoList']) && count($aParamAdaptivePayment['paymentInfoList'])){
		Phpfox::getService('ecommerce.callback')->paymentApiCallbackAdaptivePayment($aParamAdaptivePayment);
	}
}

?>