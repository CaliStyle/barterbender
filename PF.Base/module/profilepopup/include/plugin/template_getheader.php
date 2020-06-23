<?php

defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::isModule('profilepopup') && Phpfox::getUserParam('profilepopup.can_view_profile_popup') && !Phpfox::isAdminPanel()) {
    PhpFox::getLib('template')->setPhrase(array(
        "profilepopup.loading"
    , "profilepopup.loading_error"
    ));
    $aRet = Phpfox::getService('profilepopup')->initThemeTemplateBodyPlugin();
    PhpFox::getLib('template')->setHeader(array(
            'redefineuserinfo.js' => 'module_profilepopup',
            'imagesloaded.min.js' => 'static_script',
            'profilepopup.js' => 'module_profilepopup',
            '<script type="text/javascript">var iOpeningDelayTime = ' . $aRet['iOpeningDelayTime'] . ', iClosingDelayTime = ' . $aRet['iClosingDelayTime'] . ', sEnableCache = ' . $aRet['sEnableCache'] . '; $Behavior.ynppSetDataForYnfbpp = function() {ynfbpp.rewriteData = $.parseJSON(\'' . $aRet['rewriteData'] . '\');};</script>'
        )
    );
}

if(Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => '__module_profilepopup'
    ]) {
    Phpfox::getLib('url')->send('admincp.setting.edit', ['module-id' => 'profilepopup']);
}
if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'profilepopup',
        'req3' => 'user-group-settings'
    ]) {
    Phpfox::getLib('url')->send('admincp.user.group.add', ['setting' => 1, 'hide_app' => 1, 'module' => 'profilepopup','group_id' => 2]);
}

if (Phpfox::getLib('request')->get('req1') == 'admincp'
    && Phpfox::getLib('request')->get('req2') == 'user'
    && Phpfox::getLib('request')->get('req3') == 'group'
    && Phpfox::getLib('request')->get('req4') == 'add'
    && Phpfox::getLib('request')->get('module') == 'profilepopup'
    && Phpfox::getLib('request')->get('hide_app') == '1') {

    Phpfox_Template::instance()->setHeader('<script>$Behavior.onLoadUserSettingYNSS = function(){ 
            if ($(\'.main_holder\').find(\'.btn-group\').length) { 
                    $(\'.main_holder\').find(\'.btn-group > a[href*="admincp/profilepopup/user-group-settings/"]\').addClass(\'active\'); 
                } 
            };</script>');
}



