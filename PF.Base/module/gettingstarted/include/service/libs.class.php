<?php
$sLibPath = PHPFOX_DIR . 'module' . PHPFOX_DS . 'gettingstarted' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs' . PHPFOX_DS;
if (!class_exists('Younet_Cache'))
{
	require_once($sLibPath . 'yncache.class.php');
}
if (!class_exists('Younet_Service'))
{
	require_once($sLibPath . 'ynservice.class.php');
}
?>