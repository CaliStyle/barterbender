<?php

defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::isUser() && !Phpfox::isAdminPanel() && Phpfox::isModule('notification')) {
    PhpFox::getLib('template')->setHeader(array(
            'fanot.js' => 'module_fanot',
        )
    );
}

if(Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => '__module_fanot'
    ]) {
    Phpfox::getLib('url')->send('admincp.setting.edit', ['module-id' => 'fanot']);
}

?>



