<?php
$sData .= '<script>
oTranslations["fevent.event"] = "' . html_entity_decode(_p("fevent.event")) . '";
oTranslations["fevent.time"] = "' . html_entity_decode(_p("fevent.time")) . '";
oTranslations["fevent.location"] = "' . html_entity_decode(_p("fevent.location")) . '";
oTranslations["fevent.view_this_event"] = "' . html_entity_decode(_p("fevent.view_this_event")) . '";
oTranslations["fevent.events"] = "' . html_entity_decode(_p("fevent.events")) . '";
oTranslations["fevent.start_time"] = "' . html_entity_decode(_p("fevent.start_time")) . '";
oTranslations["fevent.v_getdirections"] = "' . html_entity_decode(_p("fevent.v_getdirections")) . '";
oTranslations["fevent.weekday_sunday_two_characters"] = "' . html_entity_decode(_p("fevent.weekday_sunday_two_characters")) . '";
oTranslations["fevent.weekday_monday_two_characters"] = "' . html_entity_decode(_p("fevent.weekday_monday_two_characters")) . '";
oTranslations["fevent.weekday_tuesday_two_characters"] = "' . html_entity_decode(_p("fevent.weekday_tuesday_two_characters")) . '";
oTranslations["fevent.weekday_wednesday_two_characters"] = "' . html_entity_decode(_p("fevent.weekday_wednesday_two_characters")) . '";
oTranslations["fevent.weekday_thursday_two_characters"] = "' . html_entity_decode(_p("fevent.weekday_thursday_two_characters")) . '";
oTranslations["fevent.weekday_friday_two_characters"] = "' . html_entity_decode(_p("fevent.weekday_friday_two_characters")) . '";
oTranslations["and"] = "' . html_entity_decode(_p("and")) . '";
oTranslations["more event"] = "' . html_entity_decode(_p("more event")) . '";
oTranslations["more events"] = "' . html_entity_decode(_p("more events")) . '";
oTranslations["Start"] = "' . html_entity_decode(_p("Start")) . '";
oTranslations["End"] = "' . html_entity_decode(_p("End")) . '";
oTranslations["fevent.weekday_saturday_two_characters"] = "' . html_entity_decode(_p("fevent.weekday_saturday_two_characters")) . '";
oTranslations["fevent.january"] = "' . html_entity_decode(_p("fevent.january")) . '";
oTranslations["fevent.february"] = "' . html_entity_decode(_p("fevent.february")) . '";
oTranslations["fevent.march"] = "' . html_entity_decode(_p("fevent.march")) . '";
oTranslations["fevent.april"] = "' . html_entity_decode(_p("fevent.april")) . '";
oTranslations["fevent.may"] = "' . html_entity_decode(_p("fevent.may")) . '";
oTranslations["fevent.june"] = "' . html_entity_decode(_p("fevent.june")) . '";
oTranslations["fevent.july"] = "' . html_entity_decode(_p("fevent.july")) . '";
oTranslations["fevent.august"] = "' . html_entity_decode(_p("fevent.august")) . '";
oTranslations["fevent.september"] = "' . html_entity_decode(_p("fevent.september")) . '";
oTranslations["fevent.october"] = "' . html_entity_decode(_p("fevent.october")) . '";
oTranslations["fevent.november"] = "' . html_entity_decode(_p("fevent.november")) . '";
oTranslations["fevent.december"] = "' . html_entity_decode(_p("fevent.december")) . '";
oTranslations["fevent.categories_selected"] = "' . html_entity_decode(_p("fevent.categories_selected")) . '";
oTranslations["fevent.select_categories"] = "' . html_entity_decode(_p("fevent.select_categories")) . '";
oTranslations["fevent.no_events_found_on_map"] = "' . html_entity_decode(_p("fevent.no_events_found_on_map")) . '";
</script>';


/*Check case: event block using google maps to show events address, we will check if this block is added on other controllers to add google maps api and js*/
$controllerName = $this->_aVars['sFullControllerName'];
if(!empty($controllerName)) {
    $controllerParts = explode('_', $controllerName);
    if(!empty($controllerParts[0]) && Phpfox::isModule($controllerParts[0]) && $controllerParts[0] != 'fevent') {
        $controller = $controllerParts[0] . '.' . $controllerParts[1];
        if(Phpfox::getService('fevent')->checkBlockExistOutOfApp($controller)) {
            $sData .= '<script src="//maps.googleapis.com/maps/api/js?v=3.exp&key='. Phpfox::getParam('core.google_api_key') .'&sensor=false&language=en&libraries=places"></script>';
            if(!setting('pf_core_bundle_js_css', false)) {
                $sData .= '<script type="text/javascript" src="'. (Phpfox::getParam('core.path_actual') . 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'p-advevent' . PHPFOX_DS .'assets' .PHPFOX_DS . 'jscript' . PHPFOX_DS . 'fevent.js') .'"></script>';
            }
        }
    }
}
