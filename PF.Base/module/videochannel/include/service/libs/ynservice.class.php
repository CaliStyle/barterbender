<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
class Younet_Service extends Phpfox_Service
{
	protected function cache()
    {
    	return $oCache = new YouNet_Cache();		
    }
}
?>