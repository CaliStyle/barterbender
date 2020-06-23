<?php

if ($_POST)
{
    if (isset($_POST['yntour']) && $_POST['yntour'] == "getcontroller")
    {
        $sControllerName = phpfox::getLib('module')->getFullControllerName();
        $sResult = "[controller]".$sControllerName."[/controller]";
        echo $sResult;
        die();
    }
}
if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => '__module_tourguides'
    ]
) {
    Phpfox::getLib('url')->send('admincp.tourguides.manage');
}
if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'tourguides',
        'req3' => 'user-group-settings'
    ]) {
    Phpfox::getLib('url')->send('admincp.user.group.add', ['setting' => 1, 'hide_app' => 1, 'module' => 'tourguides','group_id' => 2]);
}

if (Phpfox::getLib('request')->get('req1') == 'admincp'
    && Phpfox::getLib('request')->get('req2') == 'user'
    && Phpfox::getLib('request')->get('req3') == 'group'
    && Phpfox::getLib('request')->get('req4') == 'add'
    && Phpfox::getLib('request')->get('module') == 'tourguides'
    && Phpfox::getLib('request')->get('hide_app') == '1') {

    Phpfox_Template::instance()->setHeader('<script>$Behavior.onLoadUserSettingYNTG = function(){
            if ($(\'.main_holder\').find(\'.btn-group\').length) {
                    $(\'.main_holder\').find(\'.btn-group > a:eq(0)\').addClass(\'active\');
                }
            };</script>');
}
?>
