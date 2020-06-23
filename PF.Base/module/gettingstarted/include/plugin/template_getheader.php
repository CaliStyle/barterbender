<?php
defined('PHPFOX') or exit('NO DICE!');

$settings = phpfox::getService('gettingstarted.settings')->getSettings(0);
if (isset($settings['active_knowledge_base']) != null)
{
	if($settings['active_knowledge_base'] == true)
	{
		Phpfox::getLib('database')->update(phpfox::getT('menu'),array('is_active'=>1),'m_connection="'.'main'.'" and module_id="'."gettingstarted".'"');
		
	}
	else 
	{
		Phpfox::getLib('database')->update(phpfox::getT('menu'),array('is_active'=>0),'m_connection="'.'main'.'" and module_id="'."gettingstarted".'"');
		
	}
}
else 
{
		Phpfox::getLib('database')->update(phpfox::getT('menu'),array('is_active'=>0),'m_connection="'.'main'.'" and module_id="'."gettingstarted".'"');
		
}

if(Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => '__module_gettingstarted'
    ]) {
    Phpfox::getLib('url')->send('admincp.setting.edit', ['module-id' => 'gettingstarted']);
}
if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'gettingstarted',
        'req3' => 'user-group-settings'
    ]) {
    Phpfox::getLib('url')->send('admincp.user.group.add', ['setting' => 1, 'hide_app' => 1, 'module' => 'gettingstarted','group_id' => 2]);
}

if (Phpfox::getLib('request')->get('req1') == 'admincp'
    && Phpfox::getLib('request')->get('req2') == 'user'
    && Phpfox::getLib('request')->get('req3') == 'group'
    && Phpfox::getLib('request')->get('req4') == 'add'
    && Phpfox::getLib('request')->get('module') == 'gettingstarted'
    && Phpfox::getLib('request')->get('hide_app') == '1') {

    Phpfox_Template::instance()->setHeader('<script>$Behavior.onLoadUserSettingYNSS = function(){ 
            if ($(\'.main_holder\').find(\'.btn-group\').length) { 
                    $(\'.main_holder\').find(\'.btn-group > a:eq(1)\').addClass(\'active\'); 
                } 
            };</script>');
}