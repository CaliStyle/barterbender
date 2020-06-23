<?php
/**
 * This cron job should be confiugure to run every hour
 */

ob_start();

define('PHPFOX', TRUE);
define('PHPFOX_NO_SESSION', TRUE);
define('PHPFOX_NO_USER_SESSION', TRUE);
define('PHPFOX_DS', DIRECTORY_SEPARATOR);

define('PHPFOX_DIR', dirname(dirname(dirname(dirname(__FILE__)))) . PHPFOX_DS);

include_once PHPFOX_DIR . 'vendor/autoload.php';
define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';

set_time_limit(15 * 60 * 60);

if (Phpfox::isModule('contactimporter'))
{
	Phpfox::getService('contactimporter.process') -> sendInviteInQueue();
}
?>