<?php 
;

if(Phpfox::isModule('auction')){
    
    if (isset($aParams['ynauction_overridenoimage'])){
        if (empty($aParams['file'])){
            Phpfox::getLib('setting')->setParam('ynauction.pathnoimage', '');
            $aParams['file'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            $aParams['path'] = 'ynauction.pathnoimage';
        }
    }
}

;
?>