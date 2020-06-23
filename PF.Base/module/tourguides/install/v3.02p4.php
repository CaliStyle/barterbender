<?php

defined('PHPFOX') or exit('NO DICE!');


function ync_install302p4()
{
    $oDatabase = Phpfox::getLib('database');

    if (!$oDatabase->isField(Phpfox::getT('tourguides'), 'is_use_controller'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('tourguides')."` ADD `is_use_controller` tinyint(1) unsigned NOT NULL DEFAULT '0' ");
    }
}

ync_install302p4();

?>