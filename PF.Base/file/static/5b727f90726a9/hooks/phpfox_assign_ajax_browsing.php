<?php

new \Core\Event('lib_module_page_class', function ($object) {
    $aSubMenus = Phpfox::getLib('template')->getVar('aFilterMenus');
    if(!empty($aSubMenus) || \Phpfox_Module::instance()->getFullControllerName() == 'core.index-member') {
        $object->cssClass .= ' yncfbclone-has-left-menu';
    }
    $function = new Core\View\Functions('');
    if ($function->checkContent(1) || $function->checkContent(3) || $function->checkContent(9) || $function->checkContent(10)) {
        $object->cssClass .= ' yncfbclone-has-right-column';
    }
});

if (!empty($aBreadCrumbs) && empty($aBreadCrumbTitle)) {
    list($value, $key) = array(end($aBreadCrumbs), key($aBreadCrumbs));
    unset($aBreadCrumbs[$key]);
    $aBreadCrumbTitle = [$value, $key, 1];
}