<?php

class Ynadvancedpayment_Service_Braintree_Braintree extends Phpfox_Service
{
	function includeFiles(){
		require(dirname(__file__) . '/lib/Braintree.php');
	}
}