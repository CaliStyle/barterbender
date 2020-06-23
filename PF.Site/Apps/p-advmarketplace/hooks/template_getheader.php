<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => '__module_advancedmarketplace'
    ]) {
    Phpfox::getLib('url')->send('admincp.advancedmarketplace');
}

if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'advancedmarketplace',
        'req3' => 'user-group-settings'
    ]) {
    Phpfox::getLib('url')->send('admincp.user.group.add', ['setting' => 1, 'hide_app' => 1, 'module' => 'advancedmarketplace','group_id' => 2]);
}

if (Phpfox::getLib('request')->get('req1') == 'admincp'
    && Phpfox::getLib('request')->get('req2') == 'user'
    && Phpfox::getLib('request')->get('req3') == 'group'
    && Phpfox::getLib('request')->get('req4') == 'add'
    && Phpfox::getLib('request')->get('module') == 'advancedmarketplace'
    && Phpfox::getLib('request')->get('hide_app') == '1') {

    Phpfox_Template::instance()->setHeader('<script>$Behavior.onLoadUserSettingYNCAdvmarketplace = function(){ 
            if ($(\'.main_holder\').find(\'.btn-group\').length) { 
                    $(\'.main_holder\').find(\'.btn-group > a:eq(1)\').addClass(\'active\'); 
                } 
            };</script>');
}
