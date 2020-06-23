<?php
/**
 * Key to include phpFox
 *
 */
// get error when PHPFOX_DEBUG is on
defined('PHPFOX') or define('PHPFOX', true);
defined('PHPFOX_DEBUG') or define('PHPFOX_DEBUG', false);
defined('PHPFOX_NO_SESSION') or define('PHPFOX_NO_SESSION',true);
defined('PHPFOX_NO_USER_SESSION') or define('PHPFOX_NO_USER_SESSION',true);
ob_start();
/**
 * Directory Seperator
 *
 */
defined('PHPFOX_DS') or define('PHPFOX_DS', DIRECTORY_SEPARATOR);
defined('PHPFOX_DEBUG') or define('PHPFOX_DEBUG', false);
/**
 * phpFox Root Directory
 *
 */
defined('PHPFOX_DIR') or define('PHPFOX_DIR', dirname(dirname(dirname(dirname(__FILE__)))) . PHPFOX_DS.'PF.Base'. PHPFOX_DS);
defined('PHPFOX_NO_RUN') or define('PHPFOX_NO_RUN', true);
defined('PHPFOX_START_TIME') or define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';