<?php
defined('PHPFOX') or exit('NO DICE!');
if ($iEditId)
{
	if (Phpfox::isModule('socialpublishers'))
	{
		$sIdCache = Phpfox::getLib('cache') -> set("socialpublishers_feed_" . Phpfox::getUserId());
		$aFeed = Phpfox::getLib('cache') -> get($sIdCache);
		if ($aFeed)
        {
            $aExistSettings = Phpfox::getService('socialpublishers.modules')->getUserModuleSettings(Phpfox::getService('socialpublishers')->getRealUser(Phpfox::getUserId()), 'petition');
            if (!isset($aExistSettings['no_ask']) || $aExistSettings['no_ask'] == 0)
            {
                if (!isset($aFeed['is_show']) || $aFeed['is_show'] == 0)
                {
                   
                }
            }
        }
	}
}
?>
