<?php
defined('PHPFOX') or exit('NO DICE!');

function document_install402()
{
    $oDatabase = db();

    $oDatabase->delete(':setting', 'module_id=\'document\' AND var_name=\'document_time_stamp\'');
    $oDatabase->delete(':setting', 'module_id=\'document\' AND var_name=\'document_width\'');
    $oDatabase->delete(':setting', 'module_id=\'document\' AND var_name=\'document_height\'');
    $oDatabase->delete(':user_group_setting', 'module_id=\'document\' AND name=\'top_view_documents\'');
    $oDatabase->delete(':user_group_setting', 'module_id=\'document\' AND name=\'top_users_document\'');
    $oDatabase->delete(':user_group_setting', 'module_id=\'document\' AND name=\'can_control_comments_on_documents\'');
    $oDatabase->update(':module',['menu' => 'a:6:{s:39:"document_admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:8:"document";i:1;s:19:"user-group-settings";}}s:36:"document.admin_menu_manage_documents";a:1:{s:3:"url";a:2:{i:0;s:8:"document";i:1;s:6:"manage";}}s:32:"document.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:8:"document";i:1;s:3:"add";}}s:37:"document.admin_menu_manage_categories";a:1:{s:3:"url";a:1:{i:0;s:8:"document";}}s:31:"document.admin_menu_add_license";a:1:{s:3:"url";a:2:{i:0;s:8:"document";i:1;s:10:"addlicense";}}s:34:"document.admin_menu_manage_license";a:1:{s:3:"url";a:2:{i:0;s:8:"document";i:1;s:13:"managelicense";}}}'],'module_id = \'document\'');

    $oDatabase->update(':language_phrase', array(
        'text' => 'Can delete documents of all users?',
        'text_default' => 'Can delete documents of all users?'
    ), 'var_name = \'user_setting_can_delete_other_document\' AND language_id = \'en\'');

    $oDatabase->update(':language_phrase', array(
        'text' => 'Can edit documents of all users?',
        'text_default' => 'Can edit documents of all users?'
    ), 'var_name = \'user_setting_can_edit_other_document\' AND language_id = \'en\'');

    $oDatabase->delete(':feed_share', 'module_id = \'document\'');
}

document_install402();
