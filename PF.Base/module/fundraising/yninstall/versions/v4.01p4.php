<?php

defined('PHPFOX') or exit('NO DICE!');

function ynfr_install401p4()
{
    $oDatabase = Phpfox::getLib('database');
    $oDatabase->query("ALTER TABLE  ".Phpfox::getT('fundraising_campaign'). " CHANGE  `short_description`  `short_description` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
    $oDatabase->query("ALTER TABLE  ".Phpfox::getT('fundraising_campaign'). " CHANGE  `short_description_parsed`  `short_description_parsed` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
}

ynfr_install401p4();

?>