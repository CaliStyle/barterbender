<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => '__module_opensocialconnect'
    ]) {
    Phpfox::getLib('url')->send('admincp.opensocialconnect.providers');
}
