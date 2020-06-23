<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.php 3829 2011-12-19 09:33:03Z Raymond_Benc $
 */

// Make sure we are running PHP5.
if (version_compare(phpversion(), '5', '<') === true)
{
	exit('phpFox 2 or higher requires PHP 5 or newer.');
}
ob_start();

/**
 * Key to include phpFox
 *
 */
define('PHPFOX', true);

/**
 * Directory Seperator
 *
 */
define('PHPFOX_DS', DIRECTORY_SEPARATOR);

/**
 * phpFox Root Directory
 *
 */
define('PHPFOX_DIR', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . PHPFOX_DS . 'PF.Base' . PHPFOX_DS);

define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
require(PHPFOX_DIR . 'vendor' . PHPFOX_DS . 'autoload.php');
define('PHPFOX_NO_RUN', true);

// Require phpFox Init
include PHPFOX_DIR . 'start.php';

?>