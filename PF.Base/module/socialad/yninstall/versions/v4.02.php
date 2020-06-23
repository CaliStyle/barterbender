<?php
defined('PHPFOX') or exit('NO DICE!');

function ynsocialad402install () {
    db()->update(':module',['menu' => 'a:12:{s:39:"socialad.admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:8:"socialad";i:1;s:19:"user-group-settings";}}s:31:"socialad.admin_menu_add_package";a:1:{s:3:"url";a:3:{i:0;s:8:"socialad";i:1;s:7:"package";i:2;s:3:"add";}}s:35:"socialad.admin_menu_manage_packages";a:1:{s:3:"url";a:2:{i:0;s:8:"socialad";i:1;s:7:"package";}}s:38:"socialad.admin_menu_pay_later_requests";a:1:{s:3:"url";a:3:{i:0;s:8:"socialad";i:1;s:7:"payment";i:2;s:8:"paylater";}}s:38:"socialad.admin_menu_custom_information";a:1:{s:3:"url";a:2:{i:0;s:8:"socialad";i:1;s:11:"custominfor";}}s:30:"socialad.admin_menu_manage_ads";a:1:{s:3:"url";a:2:{i:0;s:8:"socialad";i:1;s:2:"ad";}}s:36:"socialad.admin_menu_manage_campaigns";a:1:{s:3:"url";a:2:{i:0;s:8:"socialad";i:1;s:8:"campaign";}}s:39:"socialad.admin_menu_manage_transactions";a:1:{s:3:"url";a:2:{i:0;s:8:"socialad";i:1;s:7:"payment";}}s:42:"socialad.admin_menu_pending_credit_request";a:1:{s:3:"url";a:3:{i:0;s:8:"socialad";i:1;s:6:"credit";i:2;s:7:"pending";}}s:33:"socialad.admin_menu_manage_credit";a:1:{s:3:"url";a:2:{i:0;s:8:"socialad";i:1;s:6:"credit";}}s:32:"socialad.admin_menu_add_new_faqs";a:1:{s:3:"url";a:3:{i:0;s:8:"socialad";i:1;s:3:"faq";i:2;s:3:"add";}}s:31:"socialad.admin_menu_manage_faqs";a:1:{s:3:"url";a:2:{i:0;s:8:"socialad";i:1;s:3:"faq";}}}'],'module_id = \'socialad\'');
}

ynsocialad402install();
?>
