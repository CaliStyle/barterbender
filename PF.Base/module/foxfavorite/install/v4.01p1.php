<?php

defined('PHPFOX') or exit('NO DICE!');


function ynff_install401p1()
{
    $oDatabase = Phpfox::getLib('database');
    $oDatabase->query("DELETE FROM `".Phpfox::getT('foxfavorite_setting')."` WHERE `module_id` = 'auction';");
}

ynff_install401p1();

?>