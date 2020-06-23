<?php

if (PHP_SAPI != 'cli')
{
	exit('CLI only!');
}

$_SERVER['REMOTE_ADDR'] = '';
$_SERVER['HTTP_HOST'] = '';
$_SERVER['SERVER_NAME'] = '';

// ignore_user_abort(true);
define('PHPFOX', true);
define('PHPFOX_DS', DIRECTORY_SEPARATOR);
define('PHPFOX_DIR', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . PHPFOX_DS);
define('PHPFOX_NO_SESSION', true);
define('PHPFOX_NO_USER_SESSION', true);
define('PHPFOX_NO_CSRF', true);
define('PHPFOX_NO_PLUGINS', true);
define('PHPFOX_CLI', true);

define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';

if (file_exists(PHPFOX_DIR_CACHE . 'video.lock'))
{
	exit('Video conversion in process.');
}

Phpfox::getService('videochannel.convert')->process();

exit;

?>