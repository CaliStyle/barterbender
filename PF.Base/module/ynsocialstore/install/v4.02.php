<?php
defined('PHPFOX') or exit('NO DICE!');

function ynstore_install402()
{
    $oDatabase = db();

    if (!$oDatabase->isField(':ynstore_feed_comment', 'total_dislike')) {
        $oDatabase->addField(
            array(
                'table' => Phpfox::getT('ynstore_feed_comment'),
                'field' => 'total_dislike',
                'type' => 'INT(11)',
                'default' => '0'
            )
        );
    }

    if (!$oDatabase->isField(':ynstore_feed_comment', 'location_latlng')) {
        $oDatabase->addField(
            array(
                'table' => Phpfox::getT('ynstore_feed_comment'),
                'field' => 'location_latlng',
                'type' => 'VARCHAR(100)',
                'null' => true
            )
        );
    }

    if (!$oDatabase->isField(':ynstore_feed_comment', 'location_name')) {
        $oDatabase->addField(
            array(
                'table' => Phpfox::getT('ynstore_feed_comment'),
                'field' => 'location_name',
                'type' => 'VARCHAR(255)',
                'null' => true
            )
        );
    }

    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_recently_viewed_product\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_product_bought_by_friend\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_most_liked_products\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_related_products\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_others_from_this_store\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_most_followed_stores\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_most_favorited_stores\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_product_bought_by_friends_stores\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_super_deals_items_stores\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_most_liked_products_stores\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_you_may_like_in_store\'');
    $oDatabase->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'addthis_id_profile\'');
    $oDatabase->update(':module',['menu' => 'a:6:{s:44:"ynsocialstore.admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:13:"ynsocialstore";i:1;s:19:"user-group-settings";}}s:38:"ynsocialstore.admin_menu_manage_stores";a:1:{s:3:"url";a:2:{i:0;s:13:"ynsocialstore";i:1;s:11:"managestore";}}s:40:"ynsocialstore.admin_menu_manage_products";a:1:{s:3:"url";a:2:{i:0;s:13:"ynsocialstore";i:1;s:13:"manageproduct";}}s:40:"ynsocialstore.admin_menu_add_new_package";a:1:{s:3:"url";a:3:{i:0;s:13:"ynsocialstore";i:1;s:7:"package";i:2;s:3:"add";}}s:40:"ynsocialstore.admin_menu_manage_packages";a:1:{s:3:"url";a:2:{i:0;s:13:"ynsocialstore";i:1;s:7:"package";}}s:42:"ynsocialstore.admin_menu_manage_comparison";a:1:{s:3:"url";a:2:{i:0;s:13:"ynsocialstore";i:1;s:10:"comparison";}}}'],'module_id = \'ynsocialstore\'');
}

ynstore_install402();
