<?php

defined('PHPFOX') or exit('NO DICE!');

function ynfr_install301p9()
{
    $oDatabase = Phpfox::getLib('database');

    if (!$oDatabase->isField(Phpfox::getT('fundraising_campaign'), 'is_active'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('fundraising_campaign')."` ADD `is_active` tinyint(1) NOT NULL DEFAULT '1' ");
    }

}

ynfr_install301p9();

?>