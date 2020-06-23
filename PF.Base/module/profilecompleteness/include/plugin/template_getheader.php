<?php
if (Phpfox::isModule('profilecompleteness')) {
    if (Phpfox::getLib('request')->getRequests() == [
            'req1' => 'admincp',
            'req2' => 'app',
            'id' => '__module_profilecompleteness'
        ]) {
        Phpfox::getLib('url')->send('admincp.profilecompleteness.settings');
    }
}

