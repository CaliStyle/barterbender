<?php
if(Phpfox::getLib('request')->getRequests() == [
    'req1' => 'admincp',
    'req2' => 'app',
    'id' => '__module_socialpublishers'
]) {
    Phpfox::getLib('url')->send('admincp.socialpublishers.modules');
}
?>
