<?php

function yncontest303p1install()
{
    $oDb = Phpfox::getLib('phpfox.database');
    $oDb->query("DELETE FROM  `".Phpfox::getT('setting')."` WHERE `module_id` = 'contest' AND `var_name` LIKE 'subcategories_to_show_at_first'");
}

yncontest303p1install();

?>