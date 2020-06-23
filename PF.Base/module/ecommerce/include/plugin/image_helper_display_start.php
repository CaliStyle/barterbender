<?php 
;

if(Phpfox::isModule('ecommerce')){
    
    if (isset($aParams['ecommerce_overridenoimage'])){
        if (empty($aParams['file'])){
            Phpfox::getLib('setting')->setParam('ecommerce.pathnoimage', '');
            $aParams['file'] = Phpfox::getParam('core.path') . 'module/ecommerce/static/image/default_ava.png';
            $aParams['path'] = 'ecommerce.pathnoimage';
        }
    }
}

;
?>