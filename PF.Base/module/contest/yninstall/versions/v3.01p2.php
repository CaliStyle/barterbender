<?php

defined('PHPFOX') or exit('NO DICE!');

function yncontest301p2install()
{
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField(Phpfox::getT('contest'), 'server_id'))
    {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('contest') . "` ADD COLUMN server_id tinyint(3) DEFAULT 0 ");
    }

    if (!$oDb->isField(Phpfox::getT('contest_entry'), 'server_id'))
    {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('contest_entry') . "` ADD COLUMN server_id tinyint(3) DEFAULT 0 ");
    }
}

yncontest301p2install();

?>