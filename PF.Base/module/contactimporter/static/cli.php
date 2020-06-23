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
// Require phpFox Init

define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';

$sParentProtocol = Phpfox::getCookie('yn_parent_protocol');	
	
if(!empty($sParentProtocol))
{
    Phpfox::setCookie('yn_parent_protocol', '', -1);
    $sCurrentProtocol = "http";
    
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "on")
    {
        $sCurrentProtocol = 'https';
    }
    
    if($sParentProtocol != $sCurrentProtocol)
    {		
        $domain = $_SERVER['HTTP_HOST'];		
        $path = $_SERVER['SCRIPT_NAME'];	
        $queryString = $_SERVER['QUERY_STRING'];		
        $url = $sParentProtocol."://" . $domain . $path . "?" . $queryString;		
        Header( "HTTP/1.1 301 Moved Permanently" );
        Header( "Location: ".$url);
        exit;
    }
}