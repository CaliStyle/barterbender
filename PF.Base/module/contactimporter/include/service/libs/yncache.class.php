<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
class Younet_Cache extends Phpfox_Cache
{
	private $_bFromMemoryYN = array();
	private $_aNameYN = array();

	public function getMemcache()
	{
		$oMemcache = new Memcache;
		$aHosts = Phpfox::getParam('core.memcache_hosts');
		$bPersistent = Phpfox::getParam('core.memcache_persistent');
		foreach ($aHosts as $aHost)
		{
			$oMemcache->addServer($aHost['host'], $aHost['port'], $bPersistent);
		}
		return $oMemcache;
	}

	public function set($sName, $sGroup = '')
	{
        $oCache = parent::getInstance();        
        if (class_exists('Phpfox_Cache_Storage_File') && Phpfox::getParam('core.cache_storage') == 'file')
		{			
            return $oCache->set($sName, $sGroup);
		}
		if (class_exists('Phpfox_Cache_Storage_Memcache') && Phpfox::getParam('core.cache_storage') == 'memcache')
		{   
            if (is_array($sName))
			{
				$sPrefix = isset($sName[0]) ? $sName[0] : '';
				$sIndentity = isset($sName[1]) ? $sName[1] : '';
				if ($sPrefix && $sIndentity)
				{					
                    $aKeys = $this->get($sPrefix);
                    $aKeys = is_array($aKeys) ? $aKeys : array();
					if (!in_array($sIndentity, $aKeys))
					{						
                        $aKeys[] = $sIndentity;
						$this->save($sPrefix, $aKeys, MEMCACHE_COMPRESSED, 0);
					}
				}
				$sName = $sPrefix . $sIndentity;
			}
			$sId = $sName;
			return $sId;
		}
	}

	public function get($sId, $iTime = 0)
	{
		$oCache = parent::getInstance();
		if (class_exists('Phpfox_Cache_Storage_File') && Phpfox::getParam('core.cache_storage') == 'file')
		{
			return $oCache->get($sId, $iTime);
		}
		if (class_exists('Phpfox_Cache_Storage_Memcache') && Phpfox::getParam('core.cache_storage') == 'memcache')
		{
			if (defined('PHPFOX_INSTALLER'))
			{
				return false;
			}

			if ($this->_bFromMemoryYN)
			{
				$this->_bFromMemoryYN = false;
				return false;
			}

			(PHPFOX_DEBUG ? Phpfox_Debug::start('cache') : false);

			if (Phpfox::getParam('core.cache_skip'))
			{
				return false;
			}

			$oMemcache = $this->getMemcache();

			$sName = $sId;
			if (!($sContent = $oMemcache->get($sName)))
			{
				return false;
			}            
			$aContent = unserialize($sContent);
			if (is_array($aContent) && isset($aContent['data']))
			{
				$aContent = $aContent['data'];
				if (isset($aContent['time_stamp']) && (int) $iTime > 0)
				{
					if ((PHPFOX_TIME - $iTime * 60) > $aContent['time_stamp'])
					{
						$oMemcache->delete($sName);
						return false;
					}
				}
			}

			(PHPFOX_DEBUG ? Phpfox_Debug::end('cache', array('namefile' => $sName)) : false);

			if (!isset($aContent))
			{
				return false;
			}

			if (!is_array($aContent) && empty($aContent))
			{
				return true;
			}

			if (is_array($aContent) && !count($aContent))
			{
				return true;
			}
			return $aContent;
		}
	}

	public function save($sId, $mContent)
	{
		if (defined('PHPFOX_INSTALLER'))
		{
			return;
		}

		if ($this->_bFromMemoryYN)
		{
			$this->_bFromMemoryYN = false;
			$this->close($sId);
			return;
		}

		$sName = $sId;
		$oCache = parent::getInstance();
		if (class_exists('Phpfox_Cache_Storage_File') && Phpfox::getParam('core.cache_storage') == 'file')
		{
			return $oCache->save($sName, $mContent);
		}
		if (class_exists('Phpfox_Cache_Storage_Memcache') && Phpfox::getParam('core.cache_storage') == 'memcache')
		{
			$oMemcache = $this->getMemcache();
			$mContent = serialize(array('time_stamp' => PHPFOX_TIME, 'data' => $mContent));
			return $oMemcache->set($sName, $mContent, MEMCACHE_COMPRESSED, 0);
		}
	}

	public function remove($sName = null, $sType = '')
	{
		if (file_exists(PHPFOX_DIR_CACHE . 'cache.lock'))
		{
			return false;
		}
		$oCache = parent::getInstance();
		if (class_exists('Phpfox_Cache_Storage_File') && Phpfox::getParam('core.cache_storage') == 'file')
		{
			return $oCache->remove($sName, $sType);
		}
        if (class_exists('Phpfox_Cache_Storage_Memcache') && Phpfox::getParam('core.cache_storage') == 'memcache')
		{
			$oMemcache = $this->getMemcache();
			if ($sName === null)
			{
				return $oMemcache->flush();
			}
			switch($sType)
			{
				case 'substr':
					$sPrefix = is_array($sName) ? $sName[0] : $sName;
					$aPrefix = $this->get($sPrefix);
					if ($aPrefix && is_array($aPrefix))
					{
						$oMemcache->delete($sPrefix);
						foreach ($aPrefix as $key)
						{
							if ($key)
							{
								$oMemcache->delete($sPrefix . $key);
							}
						}
					}
					break;
				default:
					if (is_array($sName))
					{
						$sName = $sName[0] . isset($sName[1]) ? $sName[1] : '';
					}
					$oMemcache->delete($sName);
			}
		}
	}   
}
?>