<?php

defined('PHPFOX') or exit('NO DICE!');

function remove_home_menu()
{
    $sTable = Phpfox::getT('menu');
    $oDb = Phpfox::getLib('database');
    $sql = "Delete from `" . $sTable . "` where module_id = 'socialpublishers' AND product_id = 'younet_socialpublisher'";
    $oDb->query($sql);
}

function remove_admincp_menu()
{
    $sTable = Phpfox::getT('module');
    $oDb = Phpfox::getLib('database');
    $sql = "Update `" . $sTable . "` Set `menu` = 'a:1:{s:42:\"socialpublishers.admin_menu_manage_modules\";a:1:{s:3:\"url\";a:2:{i:0;s:16:\"socialpublishers\";i:1;s:7:\"modules\";}}}' where `module_id` = \"socialpublishers\" AND `product_id` = \"younet_socialpublisher\"";
    $oDb->query($sql);
}
remove_home_menu();
remove_admincp_menu();
?>