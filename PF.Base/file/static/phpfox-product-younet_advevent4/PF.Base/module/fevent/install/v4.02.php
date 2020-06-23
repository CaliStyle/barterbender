<?php


function ynfe_install402()
{
    // Update old settings
    $aSettingsLimit = array(
        'limit' => Phpfox::getParam('fevent.fevent_number_of_event_most_liked_viewed_discussed_block', 4),
    );
    $aSettingsLimitRightSide = array(
        'limit' => Phpfox::getParam('fevent.fevent_number_of_event_upcoming_past_block_right_side', 4),
    );

    db()->update(':block', array('params' => json_encode($aSettingsLimit)), 'component = \'mostdiscussed\' AND module_id = \'fevent\' AND params IS NULL');
    db()->update(':block', array('params' => json_encode($aSettingsLimit)), 'component = \'mostviewed\' AND module_id = \'fevent\' AND params IS NULL');
    db()->update(':block', array('params' => json_encode($aSettingsLimit)), 'component = \'mostliked\' AND module_id = \'fevent\' AND params IS NULL');

    db()->update(':block', array('params' => json_encode($aSettingsLimitRightSide)), 'component = \'past\' AND module_id = \'fevent\' AND params IS NULL');
    db()->update(':block', array('params' => json_encode($aSettingsLimitRightSide)), 'component = \'upcoming\' AND module_id = \'fevent\' AND params IS NULL');

    db()->update(':menu', array('var_name' => 'menu_fevent_add_new_event'),'module_id = \'fevent\' AND url_value = \'fevent.add\'');
    // remove settings
    db()->delete(':setting','module_id=\'fevent\' AND var_name=\'fevent_number_of_event_most_liked_viewed_discussed_block\'');
    db()->delete(':setting','module_id=\'fevent\' AND var_name=\'fevent_number_of_event_upcoming_past_block_right_side\'');
    db()->delete(':setting','module_id=\'fevent\' AND var_name=\'fevent_view_time_stamp_profile\'');
    db()->update(':module',['menu' => 'a:9:{s:37:"fevent.admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:19:"user-group-settings";}}s:30:"fevent.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:3:"add";}}s:35:"fevent.admin_menu_manage_categories";a:1:{s:3:"url";a:1:{i:0;s:6:"fevent";}}s:34:"fevent.admin_menu_add_custom_field";a:1:{s:3:"url";a:3:{i:0;s:6:"fevent";i:1;s:6:"custom";i:2;s:3:"add";}}s:38:"fevent.admin_menu_manage_custom_fields";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:6:"custom";}}s:33:"fevent.admin_menu_manage_location";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:8:"location";}}s:37:"fevent.admin_menu_google_api_settings";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:11:"settinggapi";}}s:34:"fevent.admin_menu_migration_events";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:10:"migrations";}}s:31:"fevent.admin_menu_manage_events";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:12:"manageevents";}}}a:9:{s:37:"fevent.admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:19:"user-group-settings";}}s:30:"fevent.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:3:"add";}}s:35:"fevent.admin_menu_manage_categories";a:1:{s:3:"url";a:1:{i:0;s:6:"fevent";}}s:34:"fevent.admin_menu_add_custom_field";a:1:{s:3:"url";a:3:{i:0;s:6:"fevent";i:1;s:6:"custom";i:2;s:3:"add";}}s:38:"fevent.admin_menu_manage_custom_fields";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:6:"custom";}}s:33:"fevent.admin_menu_manage_location";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:8:"location";}}s:37:"fevent.admin_menu_google_api_settings";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:11:"settinggapi";}}s:34:"fevent.admin_menu_migration_events";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:10:"migrations";}}s:31:"fevent.admin_menu_manage_events";a:1:{s:3:"url";a:2:{i:0;s:6:"fevent";i:1;s:12:"manageevents";}}}'],'module_id = \'fevent\'');
    db()->delete(':block','m_connection = \'fevent.add\' AND component=\'managemenus\' AND module_id = \'fevent\'');
    $aUpdatePhrases = [
        "user_setting_can_edit_other_event" => "Can edit all events?",
        "user_setting_can_delete_other_event" => "Can delete all events?",
        "user_setting_can_manage_custom_fields" => "Can manage event custom fields?\r\n\r\nNotice: this setting only apply for members who have permission to go to AdminCP",
        "user_setting_can_add_custom_fields" => "Can add event custom fields?\r\n\r\nNotice: this setting only apply for members who have permission to go to AdminCP",
    ];
    Phpfox::getService('language.phrase.process')->updatePhrases($aUpdatePhrases);
}

ynfe_install402();
