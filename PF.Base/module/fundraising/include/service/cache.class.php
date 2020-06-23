<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Cache extends Phpfox_Service {

	private $_sPrefix = '';
	private $_sDir = 'fundraising';
	private $_bIsDebug = false;

	public function __construct() {
		$this->_sPrefix = 'fundraising_';
		if(defined('PHPFOX_DEBUG') && PHPFOX_DEBUG)
		{
			$this->_bIsDebug = false;
		}
	}

	public function set($sKey, $aValue) {
		
		$sKey = $this->_sPrefix . $sKey;
		$sName = array($this->_sDir, $sKey);
		$sCacheId = $this->cache()->set($sName);
		$iId = $this->cache()->save($sCacheId, $aValue);
		return $iId;
	}


	/*
	 * @params 
	 * 
	 * note: iExpiredTime is in minute
	 */
	public function get($sKey, $sType = 'default') {

		// we skip using cache 
		return false;
		
		$sKey = $this->_sPrefix . $sKey;
		$sName = array($this->_sDir, $sKey);
		$sCacheId = $this->cache()->set($sName);
		switch($sType)
		{
			case 'site_stats';
					$iExpiredTime = 5;
					return $this->cache()->get($sCacheId, $iExpiredTime);	
				break;
			case 'featured':
			case 'latest':
			case 'most-donated':
			case 'most-liked':
					$iExpiredTime = 5;
					return $this->cache()->get($sCacheId, $iExpiredTime);	
				break;
			default :
					$iExpiredTime = 5;
					return $this->cache()->get($sCacheId, $iExpiredTime);
				break;
		}

		return false;
	}

	public function removeAll()
	{
		if (is_dir(PHPFOX_DIR_CACHE . $this->_sDir))
		{
			Phpfox::getLib('file')->delete_directory(PHPFOX_DIR_CACHE . $this->_sDir);
		}
	}

	public function remove($sKey, $sType)
	{
		$sKey = $this->_sPrefix . $sKey;
		$sName = array($this->_sDir, $sKey);
		$this->cache()->remove($sName);
	}

	public function getConst($sKey) {
		

		return 100;
	}

}

?>