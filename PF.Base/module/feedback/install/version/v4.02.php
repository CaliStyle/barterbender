<?php


function ynfb_install402()
{
    db()->delete(':setting','module_id=\'feedback\' AND var_name=\'addthis_public_id\'');
    db()->delete(':setting','module_id=\'feedback\' AND var_name=\'feedback_time_stamp\'');
    db()->update(':module',['menu' => 'a:5:{s:48:"feedback.admin_menu_feedback_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:8:"feedback";i:1;s:19:"user-group-settings";}}s:39:"feedback.admin_menu_feedback_management";a:1:{s:3:"url";a:2:{i:0;s:8:"feedback";i:1;s:9:"feedbacks";}}s:35:"feedback.admin_menu_global_seetings";a:1:{s:3:"url";a:2:{i:0;s:8:"feedback";i:1;s:8:"category";}}s:40:"feedback.admin_menu_serverity_management";a:1:{s:3:"url";a:2:{i:0;s:8:"feedback";i:1;s:6:"status";}}s:36:"feedback.admin_menu_manage_serverity";a:1:{s:3:"url";a:2:{i:0;s:8:"feedback";i:1;s:9:"serverity";}}}'], 'module_id=\'feedback\' AND product_id = \'younet_feedback4\'');
}
ynfb_install402();