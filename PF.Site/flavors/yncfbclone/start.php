<?php
// change location of sub_menu block

Phpfox::getLib('setting')->setParam('core.sub_menu_location', '6');

if (\Phpfox::getMessage()) {
    new \Core\Event('lib_module_page_class', function ($object) {
        $object->cssClass .= ' has-public-message';
    });
}

new \Core\Event('lib_module_page_class', function ($object) {
    $function = new Core\View\Functions('');
    if (((!$function->checkContent(3) && !$function->checkContent(10)) || !Phpfox::getParam('user.allow_user_registration')) && \Phpfox_Module::instance()->getFullControllerName() == 'core.index-visitor') {
        $object->cssClass .= ' welcome-only';
    }

    if(\Phpfox_Module::instance()->getFullControllerName() == 'core.index-member') {
        $object->cssClass .= ' yncfbclone-has-right-placeholder';
    }
});
