<?php
if (Phpfox::isModule('ynadvancedpayment')) {

    if (Phpfox::getLib('request')->getRequests() == [
            'req1' => 'admincp',
            'req2' => 'app',
            'id' => '__module_ynadvancedpayment'
        ]) {
        Phpfox::getLib('url')->send('admincp.api.gateway');
    }
}

