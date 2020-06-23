<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Controller_PageCalendar extends Phpfox_Component {


    /**
     * Class process method wnich is used to execute this component.
     */

    public function process() {
        
        Phpfox::getUserParam('fevent.can_access_event', true);
        $currentUrl = $this->url()->getFullUrl();
        $sView = $this->request()->get('view', 'all');
        // Check if we are on advanced search mode
        $bIsAdvSearch = FALSE;
        if($this->request()->get('formflag'))
        {
            $bIsAdvSearch = TRUE;
        }
        $oServiceEventBrowse = Phpfox::getService('fevent.browse');
        $sCategory = null;
        $sView = $this->request()->get('view', 'all');
        $aCallback = $this->getParam('aCallback', false);
		/*
        $this->search()->set(array(
            'type' => 'fevent',
            'field' => 'm.event_id',
            'search_tool' => array(
                'default_when' => 'all-time',
                'when_field' => 'start_time',
                'table_alias' => 'm',
                'search' => array(
                    'action' => $this->url()->makeUrl('fevent', array('view' => $this->request()->get('view', 'all'))),
                    'default_value' => _p('search_events'),
                    'name' => 'search',
                    'field' => array('m.title', 'ft.description')
                ),
                'sort' => array(
                    'latest' => array('m.time_stamp', _p('latest'), 'DESC'),
                    'most-viewed' => array('m.total_view', _p('most_viewed')),
                    'most-liked' => array('m.total_like', _p('most_liked')),
                    'most-talked' => array('m.total_comment', _p('most_discussed')),
                ),
                'show' => array(10, 15, 18, 21)
            )
           )
        );*/
		
		$aRows = array();
        Phpfox::getService('fevent.helper')->buildSectionMenu();

        $aTime = array();


        $month =  $this->request()->get('month', Phpfox::getTime('n',PHPFOX_TIME));
        $year =  $this->request()->get('year', Phpfox::getTime('Y',PHPFOX_TIME));
        
        $aJsEvents = Phpfox::getService('fevent')->getJsEventsForCalendar($month,$year);

        $time_request = mktime(0,0,0,$month,1,$year);

        $aTime['month'] 	= $month;
        $aTime['monthText'] = Phpfox::getTime('F',$time_request,false);
        $aTime['year'] 		= $year;

        $current_month = Phpfox::getTime('n',PHPFOX_TIME,false);
        $current_year  = Phpfox::getTime('Y',PHPFOX_TIME,false);

        $aTime['current_month'] 	= $current_month;
        $aTime['current_year'] 		= $current_year;        

        /*next and prev for moth*/
        $next_month = Phpfox::getTime('n',strtotime("+1 month",$time_request),false);
        $next_month_year = Phpfox::getTime('Y',strtotime("+1 month",$time_request),false);
        $prev_month = Phpfox::getTime('n',strtotime("-1 month",$time_request),false);
        $prev_month_year = Phpfox::getTime('Y',strtotime("-1 month",$time_request),false);

        $aTime['next_month'] 			= $next_month;
        $aTime['next_month_year'] 		= $next_month_year;   
        $aTime['prev_month'] 			= $prev_month;   
        $aTime['prev_month_year'] 		= $prev_month_year;   

        /*next and prev for year*/
        $next_year = Phpfox::getTime('Y',strtotime("+1 year",$time_request),false);
        $next_year_month = Phpfox::getTime('n',strtotime("+1 year",$time_request),false);
        $prev_year = Phpfox::getTime('Y',strtotime("-1 year",$time_request),false);
        $prev_year_month = Phpfox::getTime('n',strtotime("-1 year",$time_request),false);

        $aTime['next_year'] 			= $next_year;
        $aTime['next_year_month'] 		= $next_year_month;   
        $aTime['prev_year'] 			= $prev_year;   
        $aTime['prev_year_month'] 		= $prev_year_month;   

        $aCalendars = Phpfox::getService('fevent.process')->drawCalendar($month,$year,$aJsEvents);

        $this->template()->setPhrase(
                        array('fevent.event', 'fevent.time', 'fevent.location', 'fevent.view_this_event','fevent.events','fevent.start_time')
                )
                ->setTitle(_p('events'))->setBreadcrumb(_p('events'), ($aCallback !== false ? $this->url()->makeUrl($aCallback['url_home'][0], array_merge($aCallback['url_home'][1], array('fevent', 'when_upcoming'))) : ( $this->url()->makeUrl('fevent'))))
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'comment.css' => 'style_css',
                    'country.js' => 'module_core',
                    'index.js' => 'module_fevent',
                    'feed.js' => 'module_feed',
                    'fevent.js' => 'module_fevent',
                    'jquery.magnific-popup.js'  => 'module_fevent',
                    )
                )
                ->assign(array(
                    'apiKey' => Phpfox::getParam('core.google_api_key'),
                	'aTime' => $aTime,
                    'aEvents' => $aRows,
                    'aCalendars' => $aCalendars,
                    'currentUrl' => $currentUrl,
                    'bIsAdvSearch' => $bIsAdvSearch,
                    'sView' => $sView,
                    'corepath'=>phpfox::getParam('core.path'),
                    'aCallback' => $aCallback,
                    'sParentLink' => ($aCallback !== false ? $aCallback['url_home'][0] . '.' . implode('.', $aCallback['url_home'][1]) . '.event' : 'fevent'),
                        )
        );

        if ($sCategory !== null) {
            $aCategories = Phpfox::getService('fevent.category')->getParentBreadcrumb($sCategory);
            $iCnt = 0;
            foreach ($aCategories as $aCategory) {
                $iCnt++;

                $this->template()->setTitle($aCategory[0]);

                if ($aCallback !== false) {
                    $sHomeUrl = '/' . Phpfox::getLib('url')->doRewrite($aCallback['url_home'][0]) . '/' . implode('/', $aCallback['url_home'][1]) . '/' . Phpfox::getLib('url')->doRewrite('fevent') . '/';
                    $aCategory[1] = preg_replace('/^http:\/\/(.*?)\/' . Phpfox::getLib('url')->doRewrite('fevent') . '\/(.*?)$/i', 'http://\\1' . $sHomeUrl . '\\2', $aCategory[1]);
                }

                $this->template()->setBreadcrumb($aCategory[0], $aCategory[1], (empty($sView) ? true : false));
            }
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean() {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_pagecalendar_clean')) ? eval($sPlugin) : false);
    }


}