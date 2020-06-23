<?php
$uPermission = Phpfox::getUserParam('feedback.can_view_feedback');
if(!$uPermission){
    foreach ($aMenus as $key => $iItems) {
        if($iItems['module'] != 'feedback'){
            $aCustomMenu[$key] = $iItems;
        }
    }
    $aMenus = $aCustomMenu;
}