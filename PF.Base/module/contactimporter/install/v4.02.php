<?php

function contactimporter_402()
{
    //Update menu in AdminCP
    db()->update(':module',['menu' => 'a:6:{s:46:"contactimporter.admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:15:"contactimporter";i:1;s:19:"user-group-settings";}}s:42:"contactimporter.admin_menu_global_settings";a:1:{s:3:"url";a:2:{i:0;s:15:"contactimporter";i:1;s:8:"settings";}}s:36:"contactimporter.admin_menu_providers";a:1:{s:3:"url";a:2:{i:0;s:15:"contactimporter";i:1;s:9:"providers";}}s:43:"contactimporter.admin_menu_invitations_list";a:1:{s:3:"url";a:2:{i:0;s:15:"contactimporter";i:1;s:11:"invitations";}}s:45:"contactimporter.admin_menu_statistics_by_date";a:1:{s:3:"url";a:2:{i:0;s:15:"contactimporter";i:1;s:16:"statisticsbydate";}}s:49:"contactimporter.admin_menu_statistics_by_provider";a:1:{s:3:"url";a:2:{i:0;s:15:"contactimporter";i:1;s:20:"statisticsbyprovider";}}}'],'module_id = \'contactimporter\'');
    //Delete drop down menu
    db()->delete(':menu','module_id = \'contactimporter\' AND m_connection = \'contactimporter\' AND url_value like \'%contactimporter%\'');
}

contactimporter_402();

