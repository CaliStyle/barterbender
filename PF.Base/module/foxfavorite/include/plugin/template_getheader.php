<?php
if(Phpfox::getLib('request')->getRequests() == [
    'req1' => 'admincp',
    'req2' => 'app',
    'id' => '__module_foxfavorite'
]) {
    Phpfox::getLib('url')->send('admincp.foxfavorite.settings', ['module-id' => 'foxfavorite']);
}
?>
