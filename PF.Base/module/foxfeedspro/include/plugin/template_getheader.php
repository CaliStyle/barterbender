<?php
if(Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => '__module_foxfeedspro'
    ]) {
    Phpfox::getLib('url')->send('admincp.setting.edit', ['module-id' => 'foxfeedspro']);
}

?>