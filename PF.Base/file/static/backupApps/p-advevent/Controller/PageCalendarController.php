<?php

namespace Apps\P_AdvEvent\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class PageCalendarController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */

    public function process()
    {
        Phpfox::getUserParam('fevent.can_access_event', true);

        $sView = $this->request()->get('view', 'all');
        $aCallback = $this->getParam('aCallback', false);

        Phpfox::getService('fevent.helper')->buildSectionMenu();
        if (Phpfox::getUserParam('fevent.can_create_event')) {
            sectionMenu(_p('menu_fevent_add_new_event'), 'fevent.add');
        }

        $calendarPhrases = Phpfox::getService('fevent.helper')->getCalendarPhrases();

        $this->template()
            ->setPhrase(array_merge($calendarPhrases, [
                    'event',
                    'time',
                    'location',
                    'view_this_event',
                    'events',
                    'start_time',
                    'others'
            ]))
            ->setTitle(_p('calendar'))
            ->setTitle(_p('events'))
            ->setBreadcrumb(_p('calendar'), ($this->url()->makeUrl('fevent.pagecalendar')))
            ->setHeader('cache', array(
                    'jscript/index.js' => 'app_p-advevent',
                    'jscript/fevent.js' => 'app_p-advevent',
                    'jscript/jquery.magnific-popup.js' => 'app_p-advevent',
                    'jscript/underscore-min.js' => 'app_p-advevent',
                    'jscript/init_calendar_phrase.js' => 'app_p-advevent',
                    'jscript/calendar.js' => 'app_p-advevent',
                )
            )
            ->assign(array(
                    'apiKey' => Phpfox::getParam('core.google_api_key'),
                    'sView' => $sView,
                    'corepath' => phpfox::getParam('core.path'),
                    'aCallback' => $aCallback,
                    'sParentLink' => ($aCallback !== false ? $aCallback['url_home'][0] . '.' . implode('.', $aCallback['url_home'][1]) . '.event' : 'fevent'),
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_pagecalendar_clean')) ? eval($sPlugin) : false);
    }
}