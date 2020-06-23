<?php
$uPermission = Phpfox::getUserParam('document.can_view_documents');
if (!$uPermission) {
    foreach ($aMenus as $key => $iItems) {
        if ($iItems['module'] != 'document') {
            $aCustomMenu[$key] = $iItems;
        }
    }
    $aMenus = $aCustomMenu;
}
