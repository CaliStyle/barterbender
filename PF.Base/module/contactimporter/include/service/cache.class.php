<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Younetcore_Service_Cache extends Phpfox_Service
{
	protected function cache()
	{
		return $oCache = new YouNet_Cache();
	}

	public function getMemcache()
	{
		return $this -> cache() -> getMemcache();
	}

	public function set($sName, $sGroup = '')
	{
		return $this -> cache() -> set($sName, $sGroup);
	}

	public function get($sId, $iTime = 0)
	{
		return $this -> cache() -> set($sId, $iTime);
	}

	public function save($sId, $mContent)
	{
		return $this -> cache() -> set($sId, $mContent);
	}

	public function remove($sName = null, $sType = '')
	{
		return $this -> cache() -> remove($sName, $sType);
	}

}