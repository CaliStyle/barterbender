<?php

defined('PHPFOX') or exit('NO DICE!');


function ync_install402()
{
    $oDatabase = Phpfox::getLib('database');
    $oDatabase->update(':module',['menu' => 'a:10:{s:32:"coupon.admin_menu_manage_coupons";a:1:{s:3:"url";a:2:{i:0;s:6:"coupon";i:1;s:6:"coupon";}}s:37:"coupon.admin_menu_manage_transactions";a:1:{s:3:"url";a:2:{i:0;s:6:"coupon";i:1;s:11:"transaction";}}s:34:"coupon.admin_menu_add_new_category";a:1:{s:3:"url";a:3:{i:0;s:6:"coupon";i:1;s:8:"category";i:2;s:3:"add";}}s:35:"coupon.admin_menu_manage_categories";a:1:{s:3:"url";a:2:{i:0;s:6:"coupon";i:1;s:8:"category";}}s:31:"coupon.admin_menu_add_new_faq_s";a:1:{s:3:"url";a:3:{i:0;s:6:"coupon";i:1;s:3:"faq";i:2;s:3:"add";}}s:30:"coupon.admin_menu_manage_faq_s";a:1:{s:3:"url";a:2:{i:0;s:6:"coupon";i:1;s:3:"faq";}}s:39:"coupon.admin_menu_manage_email_template";a:1:{s:3:"url";a:2:{i:0;s:6:"coupon";i:1;s:5:"email";}}s:36:"coupon.admin_menu_add_print_template";a:1:{s:3:"url";a:3:{i:0;s:6:"coupon";i:1;s:8:"template";i:2;s:3:"add";}}s:40:"coupon.admin_menu_manage_print_templates";a:1:{s:3:"url";a:2:{i:0;s:6:"coupon";i:1;s:8:"template";}}s:37:"coupon.admin_menu_manage_custom_field";a:1:{s:3:"url";a:2:{i:0;s:6:"coupon";i:1;s:11:"customfield";}}}'],'module_id = \'coupon\'');
}

ync_install402();

?>
