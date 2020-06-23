<?php

defined('PHPFOX') or exit('NO DICE!');

function ynap_uninstall301()
{
	$oDatabase = Phpfox::getLib('database');
	$oDatabase -> delete(Phpfox::getT('api_gateway'), "gateway_id = 'authorizenet'");
	$oDatabase -> delete(Phpfox::getT('api_gateway'), "gateway_id = 'ccbill'");
}

ynap_uninstall301();
?>