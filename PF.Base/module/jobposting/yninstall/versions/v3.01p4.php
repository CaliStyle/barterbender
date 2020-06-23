<?php

function jobposting_install301p4()
{
    $oDb = Phpfox::getLib('phpfox.database');
    
    $oDb->update(Phpfox::getT('language_phrase'), array('text' => 'Can add a company?', 'text_default' => 'Can add a company?'), 'module_id = "jobposting" AND product_id = "younet_jobposting" AND var_name = "user_setting_can_add_company" AND language_id="en"');
}

jobposting_install301p4();

?>