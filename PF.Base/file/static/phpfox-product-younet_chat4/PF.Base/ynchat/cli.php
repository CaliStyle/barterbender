<?php
/**
 * Key to include phpFox
 *
 */
define('PHPFOX', true);
if(!defined('YNCHAT_SERVER') || YNCHAT_SERVER == false){
    ob_start();
}

/**
 * Directory Seperator
 *
 */
define('PHPFOX_DS', DIRECTORY_SEPARATOR);

/**
 * phpFox Root Directory
 *
 */
define('PHPFOX_DIR', dirname(dirname(__FILE__)) . PHPFOX_DS);
define('YNCHAT_DIR', dirname(__FILE__) . PHPFOX_DS);
define('YNCHAT_DEBUG', FALSE); // TRUE or FALSE
define('YNCHAT_INCLUDE_JQUERY','1');
define('YNCHAT_GZIP_ENABLED','0');				// Set to 1 if you would like to compress output of JS and CSS

// Require phpFox Init
define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
define('PHPFOX_NO_APPS', true);

if (empty($_SERVER['REQUEST_METHOD'])) {
	$_SERVER['REQUEST_METHOD'] = 'GET';
}

// Require phpFox Init
include PHPFOX_DIR .  'start.php';
include YNCHAT_DIR . 'ynlog.php';
// nothing for some issue.
if (function_exists('ini_set'))
{
	ini_set('display_startup_errors', 0);
	ini_set('display_errors', 0);
	error_reporting(E_ERROR);
}


/**
 * set error handler
 */
set_error_handler(array(
	'Ynlog',
	'handleError'
));

/**
 * set exception handler
 */
set_exception_handler(array(
	'Ynlog',
	'handleException'
));
/**
 * Register the shutdown PHP script function.
 * If there is a fatal error, this function will clear all buffer and return the error json.
 */
register_shutdown_function(array(
	'Ynlog',
	'handeShutdown'
));


// we use ONLY below object to communicate between Chat's package and Platform's package
// when change to other platform, we should update
//      . this below object
//      . cli file
//      . Platform's package
//      . install file
//
// we should follow structure of data/result when calling API/method from $oYNChat object
$oYNChat = Phpfox::getService('ynchat');        // CHANGE IT WHEN USING WITH OTHER PLATFORM

?>