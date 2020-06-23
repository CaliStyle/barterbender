<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => '__module_socialbridge'
    ]) {
    Phpfox::getLib('url')->send('admincp.socialbridge.providers');
}
