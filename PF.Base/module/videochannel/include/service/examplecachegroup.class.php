<?php
defined('PHPFOX') or exit('NO DICE!');
require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'socialmediaimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs.class.php');
class Socialmediaimporter_Service_Example extends Younet_Service
{    
    public function __construct()
	{		
			
	}
	
	public function saveCacheGroup()
	{		
		$sPrefix = 'socialmediaimporter';
		$sIdentity = Phpfox::getUserId();
		$iTime = 30*60;
		
		$sCacheId = $this->cache()->set(array($sPrefix, $sIdentity));
        if (!$aRows = $this->cache()->get($sCacheId, $iTime))	
		{
			$aRows = array('Test');
			$this->cache()->save($sCacheId, $aRows);
		}
	}	
	
	public function removeCacheGroup()
	{
		$sPrefix = 'socialmediaimporter';
		$this->cache()->remove(array($sPrefix, ''));
	}
}
?>