<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: ajax.php 2771 2011-07-30 19:34:11Z Raymond_Benc $
 */
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
define('PHPFOX_DIR', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . PHPFOX_DS);
define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';
