<?php
/**
 * [PHPFOX_HEADER]
 Using: Phpfox::getService('socialmediaimporter.cache')
 */

defined('PHPFOX') or exit('NO DICE!');
require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'videochannel' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs.class.php');
class Videochannel_Service_Cache extends Phpfox_Service 
{
	protected function cache()
    {
    	return $oCache = new YouNet_Cache();		
    }	
	
	public function getMemcache()
	{
		return $this->cache()->getMemcache();	
	}
	
	public function set($sName, $sGroup = '')	
	{
		return $this->cache()->set($sName, $sGroup);	
	}
	
	public function get($sId, $iTime = 0)	
	{
		return $this->cache()->set($sId, $iTime);	
	}
	
	public function save($sId, $mContent)
	{
		return $this->cache()->set($sId, $mContent);	
	}
	
	public function remove($sName = null, $sType = '')
	{
		return $this->cache()->remove($sName, $sType);	
	}
}
?>