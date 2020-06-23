<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::getLib('request')->getRequests() == [
        'req1' => 'admincp',
        'req2' => 'app',
        'id' => 'YNC_Member'
    ]) {
    Phpfox::getLib('url')->send('admincp.setting.edit', ['module-id' => 'ynmember']);
}
