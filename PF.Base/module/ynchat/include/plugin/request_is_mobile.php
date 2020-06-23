<?php
;

if(Phpfox::isModule('ynchat')){
    $ynchatScriptUri = $this->getServer('SCRIPT_URI');
    if(empty($ynchatScriptUri) == false){
        $pos1 = strpos($ynchatScriptUri, 'ynchat' . PHPFOX_DS . 'js.php');
        $pos2 = strpos($ynchatScriptUri, 'ynchat' . PHPFOX_DS . 'css.php');
        if($pos1 === false && $pos2 === false){
        } else {
            $bRedirect = false;
        }
    }                    
}

;
?>