<?php
defined('PHPFOX') or exit('NO DICE!');

function ynd_install402()
{
    $oDatabase = db();

    if (!$oDatabase->isField(':directory_feed_comment', 'total_dislike')) {
        $oDatabase->addField(
            array(
                'table' => Phpfox::getT('directory_feed_comment'),
                'field' => 'total_dislike',
                'type' => 'INT(11)',
                'default' => '0'
            )
        );
    }

    if (!$oDatabase->isField(':directory_feed_comment', 'location_latlng')) {
        $oDatabase->addField(
            array(
                'table' => Phpfox::getT('directory_feed_comment'),
                'field' => 'location_latlng',
                'type' => 'VARCHAR(100)',
                'null' => true
            )
        );
    }

    if (!$oDatabase->isField(':directory_feed_comment', 'location_name')) {
        $oDatabase->addField(
            array(
                'table' => Phpfox::getT('directory_feed_comment'),
                'field' => 'location_name',
                'type' => 'VARCHAR(255)',
                'null' => true
            )
        );
    }

    $oDatabase->insert(':directory_module', array(
        'module_phrase' => '{phrase var=&#039;video&#039;}',
        'module_name' => 'v',
        'module_type' => 'module',
        'module_description' => '',
        'module_landing' => 0
    ));

    $oDatabase->delete(':setting', 'module_id=\'directory\' AND var_name=\'max_items_block_most_liked_business\'');
    $oDatabase->delete(':setting', 'module_id=\'directory\' AND var_name=\'max_items_block_most_reviewed_business\'');
    $oDatabase->delete(':setting', 'module_id=\'directory\' AND var_name=\'max_items_block_most_viewed_business\'');
    $oDatabase->delete(':setting', 'module_id=\'directory\' AND var_name=\'max_items_block_most_discussed_business\'');
    $oDatabase->delete(':setting', 'module_id=\'directory\' AND var_name=\'max_items_block_most_rated_business\'');
    $oDatabase->delete(':setting', 'module_id=\'directory\' AND var_name=\'max_items_block_most_checked_in_business\'');
    $oDatabase->delete(':setting', 'module_id=\'directory\' AND var_name=\'max_items_block_business_you_may_like\'');
    $oDatabase->delete(':setting', 'module_id=\'directory\' AND var_name=\'max_items_block_recent_reviews\'');
    $oDatabase->update(':module',['menu' => 'a:13:{s:37:"directory_admin_menu_add_new_category";a:1:{s:3:"url";a:3:{i:0;s:9:"directory";i:1;s:8:"category";i:2;s:3:"add";}}s:38:"directory_admin_menu_manage_categories";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:8:"category";}}s:41:"directory_admin_menu_add_new_custom_field";a:1:{s:3:"url";a:3:{i:0;s:9:"directory";i:1;s:11:"customfield";i:2;s:3:"add";}}s:41:"directory_admin_menu_manage_custom_fields";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:11:"customfield";}}s:36:"directory_admin_menu_add_new_package";a:1:{s:3:"url";a:3:{i:0;s:9:"directory";i:1;s:7:"package";i:2;s:3:"add";}}s:35:"directory_admin_menu_manage_package";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:7:"package";}}s:38:"directory_admin_menu_manage_businesses";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:14:"managebusiness";}}s:47:"directory_admin_menu_manage_businesses_creators";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:9:"bcreators";}}s:38:"directory_admin_menu_manage_comparison";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:10:"comparison";}}s:39:"directory_admin_menu_manage_transaction";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:11:"transaction";}}s:42:"directory_admin_menu_manage_claim_requests";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:18:"manageclaimrequest";}}s:36:"directory_admin_menu_email_templates";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:5:"email";}}s:36:"directory_admin_menu_global_settings";a:1:{s:3:"url";a:2:{i:0;s:9:"directory";i:1;s:14:"globalsettings";}}}'],'module_id = \'directory\'');
}

ynd_install402();
