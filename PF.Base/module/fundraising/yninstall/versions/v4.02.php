<?php
defined('PHPFOX') or exit('NO DICE!');

function ynfundraising_install402()
{
    $oDatabase = db();

    // Update settings
    db()->update(':block', array('params' => json_encode(array('limit' => Phpfox::getParam('fundraising.number_of_donors_in_highlight_campaign_block', 6)))), 'component = \'highlight-campaign\' AND module_id = \'fundraising\' AND params IS NULL');
    db()->update(':block', array('params' => json_encode(array('limit' => Phpfox::getParam('fundraising.number_of_donors_on_top_donors_block', 12)))), 'component = \'top-donors\' AND module_id = \'fundraising\' AND params IS NULL');
    db()->update(':block', array('params' => json_encode(array('limit' => Phpfox::getParam('fundraising.number_of_supporters_on_top_suporters_block', 12)))), 'component = \'top-supporters\' AND module_id = \'fundraising\' AND params IS NULL');

    // Delete settings
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'number_of_donors_in_highlight_campaign_block\'');
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'number_of_campaigns_on_featured_slideshow\'');
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'number_of_donors_on_top_donors_block\'');
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'number_of_supporters_on_top_suporters_block\'');
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'subcategories_to_show_at_first\'');
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'google_api_key_location\'');
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'fundraising_time_stamp\'');
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'is_use_shorten_form\'');
    $oDatabase->delete(':setting', 'module_id=\'fundraising\' AND var_name=\'currency_display_type\'');

    // Delete all redundant component
    _ynfundraisingRemoveComponents();

    $oDatabase->update(':module',['menu' => 'a:6:{s:40:"fundraising.admin_menu_manage_categories";a:1:{s:3:"url";a:2:{i:0;s:11:"fundraising";i:1;s:8:"category";}}s:39:"fundraising.admin_menu_add_new_category";a:1:{s:3:"url";a:3:{i:0;s:11:"fundraising";i:1;s:8:"category";i:2;s:3:"add";}}s:38:"fundraising.admin_menu_manage_campaign";a:1:{s:3:"url";a:2:{i:0;s:11:"fundraising";i:1;s:9:"statistic";}}s:34:"fundraising.admin_menu_manage_help";a:1:{s:3:"url";a:2:{i:0;s:11:"fundraising";i:1;s:5:"email";}}s:39:"fundraising.admin_menu_manage_campaigns";a:1:{s:3:"url";a:1:{i:0;s:11:"fundraising";}}s:45:"fundraising.admin_menu_manage_payment_gateway";a:1:{s:3:"url";a:2:{i:0;s:11:"fundraising";i:1;s:7:"gateway";}}}'],'module_id = \'fundraising\'');
    $oDatabase->delete(':block', 'm_connection = \'fundraising.add\' AND component=\'edit-menu \'');
}

function _ynfundraisingRemoveComponents() {
    $oDatabase = db();

    $oDatabase->delete(':component', 'module_id = \'fundraising\' AND component = \'recent\'');
    $oDatabase->delete(':component', 'module_id = \'fundraising\' AND component = \'direct\'');
    $oDatabase->delete(':component', 'module_id = \'fundraising\' AND component = \'featured\'');
}

ynfundraising_install402();
