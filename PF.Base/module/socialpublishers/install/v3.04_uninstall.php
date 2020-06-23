<?php

defined('PHPFOX') or exit('NO DICE!');

function ynsp_uninstall304()
{
    Phpfox::getLib('database')->query("DROP TABLE IF EXISTS `" . Phpfox::getT('socialpublishers_statistic_date') . "`");
    Phpfox::getLib('database')->query("DROP TABLE IF EXISTS `" . Phpfox::getT('socialpublishers_statistic_user') . "`");
}

//ynsp_uninstall304();
?>
