<?php
function gettingstarted_install402()
{
    $oDatabase = db();
    $oDatabase->update(':module',['menu' => 'a:9:{s:39:"gettingstarted.admin_menu_add_scheduled";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:16:"addscheduledmail";}}s:37:"gettingstarted.admin_menu_manage_mail";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:10:"managemail";}}s:37:"gettingstarted.admin_menu_add_article";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:10:"addarticle";}}s:40:"gettingstarted.admin_menu_manage_article";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:13:"managearticle";}}s:38:"gettingstarted.admin_menu_add_todolist";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:11:"addtodolist";}}s:41:"gettingstarted.admin_menu_manage_todolist";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:14:"managetodolist";}}s:48:"gettingstarted.admin_menu_add_article_categories";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:18:"addarticlecategory";}}s:49:"gettingstarted.admin_menu_manage_article_category";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:21:"managearticlecategory";}}s:45:"gettingstarted.admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:14:"gettingstarted";i:1;s:19:"user-group-settings";}}}'],'module_id = \'gettingstarted\'');
    $oDatabase->delete(':setting', ['module_id' => 'gettingstarted', 'var_name' => 'public_id_addthis']);
    $oDatabase->insert(':setting', [
        'module_id' => 'gettingstarted',
        'product_id' => 'younet_gettingstarted4',
        'is_hidden' => 0,
        'version_id' => '4.02',
        'type_id' => 'drop',
        'var_name' => 'gettingstarted_paging_mode',
        'phrase_var_name' => 'setting_paging_mode',
        'value_actual' => 'a:2:{s:7:"default";s:8:"loadmore";s:6:"values";a:3:{i:0;s:8:"loadmore";i:1;s:9:"next_prev";i:2;s:10:"pagination";}}',
        'value_default' => 'a:2:{s:7:"default";s:8:"loadmore";s:6:"values";a:3:{i:0;s:8:"loadmore";i:1;s:9:"next_prev";i:2;s:10:"pagination";}}',
        'ordering' => 1
    ]);
    Phpfox::getService('language.phrase.process')->updatePhrases([
        'gettingstarted_full_name_sent_you_a_message_subject_without_unsubscribe' => '{message}\r\n--------------------\r\n\r\nTo reply to this message, follow the link below:\r\n<a href=\"{link}\">{link}<\/a>',
        'gettingstarted_gettingstarted_full_name_sent_you_a_message_subject_with_unsubscribe' => '{message}\r\n--------------------\r\n\r\nTo reply to this message, follow the link below:\r\n<a href=\"{link}\">{link}<\/a>\r\nTo unsubscribe this message, follow the link below:\r\n<a href=\"{unsublink}\">Unsubscribe<\/a>'
    ]);
}

gettingstarted_install402();
