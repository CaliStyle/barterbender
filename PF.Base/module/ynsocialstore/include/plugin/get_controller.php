<?php 
;

if(Phpfox::isModule('ynsocialstore')){
	$oRequest = Phpfox::getLib('request');
	$payment = $oRequest->get('storepayment', false); 
	if($payment !== false && $payment == 'done'){
		define('PHPFOX_NO_CSRF', true);
	}
}
;
?>