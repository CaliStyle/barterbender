<?php 
;

if(Phpfox::isModule('directory')){
    
    if (isset($aParams['yndirectory_overridenoimage'])){
        if (empty($aParams['file'])){
            Phpfox::getLib('setting')->setParam('yndirectory.pathnoimage','');
            $aParams['file'] = Phpfox::getParam('core.path') . 'module/directory/static/image/default_ava.png';
            $aParams['path'] = 'yndirectory.pathnoimage';
        }
    }
}

;
?>