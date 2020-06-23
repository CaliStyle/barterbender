<?php
ob_start();
define('PHPFOX', true);
define('PHPFOX_NO_SESSION',true);
define('PHPFOX_NO_USER_SESSION',true);
define('PHPFOX_DS', DIRECTORY_SEPARATOR);
define('PHPFOX_DIR', dirname(__FILE__) . PHPFOX_DS);
define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';
set_time_limit(30*60*60);

Phpfox::getLib('cache')->remove('younetcore_cache_invalid_yours_name');

/*
echo base64_decode("ICAgICAgICAgJG0gPSAic29jaWFsbWVkaWFpbXBvcnRlciI7CgkkcCA9InNvY2lhbG1lZGlhaW1wb3J0ZXIiOwogICAgaWYocGhwZm94Ojppc01vZHVsZSgkbSkpCiAgICB7CiAgICAgICAgaWYoIXBocGZveDo6aXNNb2R1bGUoJ3lvdW5ldGNvcmUnKSkKICAgICAgICB7CiAgICAgICAgICAgIHBocGZveDo6Z2V0TGliKCdkYXRhYmFzZScpLT51cGRhdGUocGhwZm94OjpnZXRUKCdwcm9kdWN0JyksYXJyYXkoJ2lzX2FjdGl2ZSc9PjApLCdwcm9kdWN0X2lkID0gIicuJHAuJyInKTsKICAgICAgICAgICAgcGhwZm94OjpnZXRMaWIoJ2RhdGFiYXNlJyktPnVwZGF0ZShwaHBmb3g6OmdldFQoJ21vZHVsZScpLGFycmF5KCdpc19hY3RpdmUnPT4wKSwnbW9kdWxlX2lkID0gIicuJG0uJyInKTsKICAgICAgICAgICAgcGhwZm94OjpnZXRMaWIoJ2NhY2hlJyktPnJlbW92ZSgpOwogICAgICAgIH0KICAgIH0KICAgIA=="); 
echo base64_decode("ICAgICAgICAgJG0gPSAic29jaWFsc3RyZWFtIjsKCSRwID0ic29jaWFsc3RyZWFtIjsKICAgIGlmKHBocGZveDo6aXNNb2R1bGUoJG0pKQogICAgewogICAgICAgIGlmKCFwaHBmb3g6OmlzTW9kdWxlKCd5b3VuZXRjb3JlJykpCiAgICAgICAgewogICAgICAgICAgICBwaHBmb3g6OmdldExpYignZGF0YWJhc2UnKS0+dXBkYXRlKHBocGZveDo6Z2V0VCgncHJvZHVjdCcpLGFycmF5KCdpc19hY3RpdmUnPT4wKSwncHJvZHVjdF9pZCA9ICInLiRwLiciJyk7CiAgICAgICAgICAgIHBocGZveDo6Z2V0TGliKCdkYXRhYmFzZScpLT51cGRhdGUocGhwZm94OjpnZXRUKCdtb2R1bGUnKSxhcnJheSgnaXNfYWN0aXZlJz0+MCksJ21vZHVsZV9pZCA9ICInLiRtLiciJyk7CiAgICAgICAgICAgIHBocGZveDo6Z2V0TGliKCdjYWNoZScpLT5yZW1vdmUoKTsKICAgICAgICB9CiAgICB9CiAgICA=");
echo base64_decode("ICAgICAgICAgJG0gPSAic29jaWFsbWVkaWFpbXBvcnRlciI7CgkkcCA9InNvY2lhbG1lZGlhaW1wb3J0ZXIiOwogICAgaWYocGhwZm94Ojppc01vZHVsZSgkbSkpCiAgICB7CiAgICAgICAgaWYoIXBocGZveDo6aXNNb2R1bGUoJ3lvdW5ldGNvcmUnKSkKICAgICAgICB7CiAgICAgICAgICAgIHBocGZveDo6Z2V0TGliKCdkYXRhYmFzZScpLT51cGRhdGUocGhwZm94OjpnZXRUKCdwcm9kdWN0JyksYXJyYXkoJ2lzX2FjdGl2ZSc9PjApLCdwcm9kdWN0X2lkID0gIicuJHAuJyInKTsKICAgICAgICAgICAgcGhwZm94OjpnZXRMaWIoJ2RhdGFiYXNlJyktPnVwZGF0ZShwaHBmb3g6OmdldFQoJ21vZHVsZScpLGFycmF5KCdpc19hY3RpdmUnPT4wKSwnbW9kdWxlX2lkID0gIicuJG0uJyInKTsKICAgICAgICAgICAgcGhwZm94OjpnZXRMaWIoJ2NhY2hlJyktPnJlbW92ZSgpOwogICAgICAgIH0KICAgIH0KICAgIA==");
*/
exit;

$aModule = Phpfox::getLib('module')->loadModules();		
print_r($aModule); exit;

$aRows = Phpfox::getLib('database')->select('m.module_id')
	->from(Phpfox::getT('module'), 'm')
	->join(Phpfox::getT('product'), 'p', 'm.product_id = p.product_id AND p.is_active = 1')
	->where('m.is_active = 1')
	->order('m.module_id')
	->execute('getRows');
	
print_r($aRows);
?>