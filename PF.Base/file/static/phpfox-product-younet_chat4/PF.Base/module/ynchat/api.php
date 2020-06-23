<?php

ob_start();

/**
 * Key to include phpFox
 *
 */
define('PHPFOX', true);


header('Access-Control-Allow-Origin: *');


/**
 * Directory Seperator
 *
 */
define('PHPFOX_DS', DIRECTORY_SEPARATOR);

/**
 * phpFox Root Directory
 *
 */
define('PHPFOX_DIR', dirname(dirname(dirname(__FILE__))) . PHPFOX_DS);
// Require phpFox Init

/**
 * skip check post token
 * @see ./include/library/phpfox/phpfox.class.php
 */
define('PHPFOX_NO_CSRF', TRUE);

/**
 * @var bool
 */
define('PHPFOX_IS_AJAX', TRUE);

/**
 * skip save page
 * @see ./include/library/phpfox/phpfox.class.php
 */
define('PHPFOX_DONT_SAVE_PAGE', TRUE);

/**
 * @see ./include/init.inc.php: PHPFOX_NO_PLUGINS
 * skip plugins
 */
define('PHPFOX_NO_PLUGINS', TRUE);

/**
 * @see ./include/init.inc.php: PHPFOX_NO_SESSION
 * skip session init
 */
// define('PHPFOX_NO_SESSION', TRUE);

/**
 * @see ./include/init.inc.php: PHPFOX_NO_USER_SESSION
 *
 */
// define('PHPFOX_NO_USER_SESSION', TRUE);


$sUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

$sUri = trim($sUri, '/');

if ($pos = strpos($sUri, '.php'))
{
	$sUri = substr($sUri, $pos + 5);
}
$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$actionType = 1;
$sMethod =  'get';

/**
 * start init process.
 */
define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR .  'start.php';

// nothing for some issue.
if (function_exists('ini_set'))
{
	ini_set('display_startup_errors', 0);
	ini_set('display_errors', 0);
	error_reporting(E_ERROR);
}

/**
 * generate data
 */
$aData = $_GET + $_POST;
$iId = NULL;
if (preg_match("#^(\w+)\/(\d+)#", $sUri, $matches))
{
	$actionType =  1;
	$sService = $matches[1];
	$sMethod = strtolower($requestMethod) . 'ByIdAction';
	$iId = $matches[2];
}
else
if (preg_match("#^(\w+)\/(\w+)#", $sUri, $matches))
{
	$actionType =  2;
	$sService = $matches[1];
	$sMethod = $matches[2];
	$iId = NULL;
}
else if (preg_match("#^(\w+)#", $sUri, $matches))
{
	$actionType = 3;
	$sService = $matches[1];
	$sMethod = strtolower($requestMethod) . 'Action';
	$iId = NULL;
}
$sService = str_replace('/', '.', 'ynchat/' . $sService);

$isResful = FALSE;

if (!Phpfox::isModule('ynchat'))
{
    echo json_encode(array(
		'error_code' => 1,
		'error_message' => "Module YouNet Chat is not available!"
	));
    die;
}
$api = Phpfox::getService('ynchat.api');

$oService = NULL;

if (!$api -> hasService($sService))
{
	echo json_encode(array(
		'error_code' => 1,
		'error_message' => "Invalid service [{$sService}] request URI [{$sUri}]"
	));
    die;
}
else
{
	// Call the service.
	$oService = Phpfox::getService($sService);
}

$aResult = $oService -> {$sMethod}($aData, $iId);

ob_start();
$content = json_encode($aResult);

while(ob_get_level()){
	ob_get_clean();
}

echo $content;
exit(0);