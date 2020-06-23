<?php
if(Phpfox::isModule('fanot')
    && Phpfox::getLib('request')->get('req1') == 'admincp'
    && Phpfox::getLib('request')->get('req2') == 'setting'
    && Phpfox::getLib('request')->get('req3') == 'edit'
    && Phpfox::getLib('request')->get('module-id') == 'fanot')
{
    Phpfox::getLib('template')->setHeader(array(
        'jquery.minicolors.js' => 'module_fanot',
        'jquery.minicolors.css' => 'module_fanot',
        '<script type="text/javascript">$Behavior.FanotInitColorPicker = function() { $(\'[name="val[value][notification_bgcolor]"]\').minicolors(); };</script>'
    ));
}