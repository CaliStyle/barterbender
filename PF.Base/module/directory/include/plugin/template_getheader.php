<?php
if(Phpfox::getLib('request')->getRequests() == [
    'req1' => 'admincp',
    'req2' => 'app',
    'id' => '__module_directory'
]) {
    Phpfox::getLib('url')->send('admincp.setting.edit', ['module-id' => 'directory']);
}

if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'directory',
        'req3' => 'user-group-settings'
    ]) {
    Phpfox::getLib('url')->send('admincp.user.group.add', ['setting' => 1, 'hide_app' => 1, 'module' => 'directory','group_id' => 2]);
}

if (Phpfox::getLib('request')->get('req1') == 'admincp'
    && Phpfox::getLib('request')->get('req2') == 'user'
    && Phpfox::getLib('request')->get('req3') == 'group'
    && Phpfox::getLib('request')->get('req4') == 'add'
    && Phpfox::getLib('request')->get('module') == 'directory'
    && Phpfox::getLib('request')->get('hide_app') == '1') {

    Phpfox_Template::instance()->setHeader('<script>$Behavior.onLoadUserSettingYNSS = function(){ 
            if ($(\'.main_holder\').find(\'.btn-group\').length) { 
                    $(\'.main_holder\').find(\'.btn-group > a:eq(1)\').addClass(\'active\'); 
                } 
            };</script>');
}
