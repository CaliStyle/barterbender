<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsocialad401install () {
	$oDatabase = Phpfox::getLib('phpfox.database');
}

if (!defined("YOUNET_IN_UNITTEST")) {
	ynsocialad401install();
}
?>
