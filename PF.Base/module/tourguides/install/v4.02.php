<?php

defined('PHPFOX') or exit('NO DICE!');


function ync_install402()
{
    db()->update(':module',['menu' => 'a:4:{s:41:"tourguides.admin_menu_user_group_settings";a:1:{s:3:"url";a:2:{i:0;s:10:"tourguides";i:1;s:19:"user-group-settings";}}s:35:"tourguides.admin_menu_add_new_guide";a:1:{s:3:"url";a:2:{i:0;s:10:"tourguides";i:1;s:3:"add";}}s:40:"tourguides.admin_menu_manage_tour_guides";a:1:{s:3:"url";a:2:{i:0;s:10:"tourguides";i:1;s:6:"manage";}}s:45:"tourguides.make_a_tour_position_setting_title";a:1:{s:3:"url";a:2:{i:0;s:10:"tourguides";i:1;s:8:"position";}}}'],'module_id = \'tourguides\' AND product_id = \'younet_tourguides4\'');
}

ync_install402();

?>