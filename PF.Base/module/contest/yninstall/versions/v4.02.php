<?php
function contest_install402()
{
    $oDatabase = db();
    $oDatabase->update(':module',['menu' => 'a:6:{s:38:"contest_admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:7:"contest";i:1;s:19:"user-group-settings";}}s:34:"contest.admin_menu_manage_category";a:1:{s:3:"url";a:2:{i:0;s:7:"contest";i:1;s:8:"category";}}s:31:"contest.admin_menu_add_category";a:1:{s:3:"url";a:3:{i:0;s:7:"contest";i:1;s:8:"category";i:2;s:3:"add";}}s:33:"contest.admin_menu_manage_contest";a:1:{s:3:"url";a:1:{i:0;s:7:"contest";}}s:41:"contest.admin_menu_manage_email_templates";a:1:{s:3:"url";a:2:{i:0;s:7:"contest";i:1;s:5:"email";}}s:38:"contest.admin_menu_manage_transactions";a:1:{s:3:"url";a:2:{i:0;s:7:"contest";i:1;s:11:"transaction";}}}'],'module_id = \'contest\'');
}

contest_install402();

?>