<?php

if(Phpfox::isModule('contest') && $sProductId=='younet_contest')
{
    $this->template()->setHeader(array(
        'jquery.minicolors.css' => 'module_contest',
        'jquery.minicolors.js' => 'module_contest',
        '<script type="text/javascript">$Behavior.initColorPicker = function() { 
            $(\'[name="val[value][contest_buttons_bgcolor_1]"]\').minicolors(); 
            $(\'[name="val[value][contest_buttons_bgcolor_2]"]\').minicolors(); 
            $(\'[name="val[value][contest_buttons_text_color]"]\').minicolors(); 
        };</script>'
    ));
}

?>