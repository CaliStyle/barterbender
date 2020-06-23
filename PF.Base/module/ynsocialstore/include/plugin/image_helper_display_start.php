<?php 
;

if(Phpfox::isModule('ynsocialstore')){
    
    if (isset($aParams['ynsocialstore_overridenoimage'])){
        if (empty($aParams['file'])){
            Phpfox::getLib('setting')->setParam('ynsocialstore.pathnoimage', '');
            $aParams['file'] = Phpfox::getParam('core.path') . 'module/ynsocialstore/static/image/product_default_small.jpg';
            $aParams['path'] = 'ynsocialstore.pathnoimage';
        }
    }
}

;
?>