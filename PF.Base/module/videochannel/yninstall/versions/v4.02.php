<?php
function videochannel_install402()
{
    $oDatabase = db();
    $oDatabase->update(':module',['menu' => 'a:3:{s:43:"videochannel_admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:12:"videochannel";i:1;s:19:"user-group-settings";}}s:36:"videochannel.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:12:"videochannel";i:1;s:3:"add";}}s:41:"videochannel.admin_menu_manage_categories";a:1:{s:3:"url";a:1:{i:0;s:12:"videochannel";}}}'],'module_id = \'videochannel\'');
}

videochannel_install402();

?>