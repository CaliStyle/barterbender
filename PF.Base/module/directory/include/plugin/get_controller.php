<?php 
;

if(Phpfox::isModule('directory')){
	$oRequest = Phpfox::getLib('request');
	$payment = $oRequest->get('businesspayment', false); 
	if($payment !== false && $payment == 'done'){
		define('PHPFOX_NO_CSRF', true);
	}
}
;
?>