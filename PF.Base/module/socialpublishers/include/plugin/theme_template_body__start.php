<?php 
defined('PHPFOX') or exit('NO DICE!');

if ('event' == Phpfox::getLib('request')->getInt('req1') && $iEventId = Phpfox::getLib('request')->getInt('req2'))
{
	if (Phpfox::isModule('socialpublishers'))
	{
        $iUserId = Phpfox::getService('socialpublishers')->getRealUser(Phpfox::getUserId());

		$sIdCache = Phpfox::getLib('cache') -> set("socialpublishers_feed_" . $iUserId);
		$aFeed = Phpfox::getLib('cache') -> get($sIdCache);

		if ($aFeed && isset($aFeed['params']) && $aFeed['params'])
        {
            $aExistSettings = Phpfox::getService('socialpublishers.modules')->getUserModuleSettings(Phpfox::getService('socialpublishers')->getRealUser(Phpfox::getUserId()), 'event');
            if (!isset($aExistSettings['no_ask']) || $aExistSettings['no_ask'] == 0)
            {
                if (!isset($aFeed['is_show']) || $aFeed['is_show'] == 0)
                {
                    echo "<script type=\"text/javascript\">
                        \$Behavior.loadThemePublisher = function(){
                            window.parent.\$Core.box('socialpublishers.share', 500);
                        };
                    </script>";
                }
            }
        }
	}
}
?>
