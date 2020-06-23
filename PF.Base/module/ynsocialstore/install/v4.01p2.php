<?php

/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author            Raymond Benc
 * @package        Phpfox
 * @version        $Id: ajax.php 2771 2011-07-30 19:34:11Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

function ynstore_install401p2()
{
    $oDatabase = Phpfox::getLib('database');
    if (!$oDatabase->isField(Phpfox::getT('ynstore_store'), 'description_parse')) {
        $oDatabase->query("ALTER TABLE  `" . Phpfox::getT('ynstore_store') . "` ADD COLUMN `description_parse` text");
    }
}

ynstore_install401p2();

