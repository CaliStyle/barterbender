<?php

function yncontest303install()
{
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField(Phpfox::getT('contest'), 'is_active'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest')."` ADD  `is_active` tinyint( 1 ) UNSIGNED NOT NULL DEFAULT  '1'");
    }
}

yncontest303install();

?>