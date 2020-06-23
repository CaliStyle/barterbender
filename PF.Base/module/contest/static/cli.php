<?php
/**
 * Key to include phpFox
 *
 */
define('PHPFOX', true);
define('PHPFOX_NO_SESSION',true);
define('PHPFOX_NO_USER_SESSION',true);
ob_start();
/**
 * Directory Seperator
 *
 */
define('PHPFOX_DS', DIRECTORY_SEPARATOR);

/**
 * phpFox Root Directory
 *
 */
define('PHPFOX_DIR', dirname(dirname(dirname(dirname(__FILE__)))) . PHPFOX_DS);


define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';


