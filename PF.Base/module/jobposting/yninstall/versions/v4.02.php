<?php
defined('PHPFOX') or exit('NO DICE!');

function jobposting_install402()
{
    $oDatabase = db();

    $oDatabase->delete(':setting', 'module_id=\'jobposting\' AND var_name=\'number_of_items_block_top_mostfollowed_employers\'');
    $oDatabase->delete(':setting', 'module_id=\'jobposting\' AND var_name=\'number_of_items_block_recent_youmay\'');
    $oDatabase->delete(':setting', 'module_id=\'jobposting\' AND var_name=\'subcategories_to_show_at_first\'');
    $oDatabase->delete(':setting', 'module_id=\'jobposting\' AND var_name=\'jobposting_maximum_upload_size\'');
    $oDatabase->update(':module',['menu' => 'a:12:{s:38:"jobposting.admin_menu_add_new_industry";a:1:{s:3:"url";a:3:{i:0;s:10:"jobposting";i:1;s:8:"category";i:2;s:3:"add";}}s:39:"jobposting.admin_menu_manage_industries";a:1:{s:3:"url";a:2:{i:0;s:10:"jobposting";i:1;s:8:"category";}}s:37:"jobposting.admin_menu_add_new_package";a:1:{s:3:"url";a:3:{i:0;s:10:"jobposting";i:1;s:7:"package";i:2;s:3:"add";}}s:37:"jobposting.admin_menu_manage_packages";a:1:{s:3:"url";a:2:{i:0;s:10:"jobposting";i:1;s:7:"package";}}s:33:"jobposting.admin_menu_manage_jobs";a:1:{s:3:"url";a:1:{i:0;s:10:"jobposting";}}s:28:"jobposting.admin_menu_sdfsdf";a:1:{s:3:"url";a:2:{i:0;s:10:"jobposting";i:1;s:3:"job";}}s:27:"jobposting.admin_menu_sdfsd";a:1:{s:3:"url";a:2:{i:0;s:10:"jobposting";i:1;s:11:"transaction";}}s:42:"jobposting.admin_menu_add_new_job_category";a:1:{s:3:"url";a:2:{i:0;s:10:"jobposting";i:1;s:9:"addcatjob";}}s:41:"jobposting.admin_menu_manage_job_category";a:1:{s:3:"url";a:2:{i:0;s:10:"jobposting";i:1;s:12:"managecatjob";}}s:47:"jobposting.admin_menu_add_new_apply_job_package";a:1:{s:3:"url";a:3:{i:0;s:10:"jobposting";i:1;s:15:"applyjobpackage";i:2;s:3:"add";}}s:47:"jobposting.admin_menu_manage_apply_job_packages";a:1:{s:3:"url";a:2:{i:0;s:10:"jobposting";i:1;s:15:"applyjobpackage";}}s:41:"jobposting.admin_menu_manage_custom_field";a:1:{s:3:"url";a:2:{i:0;s:10:"jobposting";i:1;s:17:"managecustomfield";}}}'],'module_id = \'jobposting\'');
    $oDatabase->delete(':block', 'm_connection = \'jobposting.company.add\' AND component=\'menu-edit\'');
}

jobposting_install402();
