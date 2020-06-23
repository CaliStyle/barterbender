<?php

defined('PHPFOX') or exit('NO DICE!');

function ynap_uninstall301p1()
{
	$oDatabase = Phpfox::getLib('database');
	$oDatabase -> delete(Phpfox::getT('api_gateway'), "gateway_id = 'gopay'");
}

ynap_uninstall301p1();
?>