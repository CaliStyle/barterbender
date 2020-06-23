<?php

namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class EventList extends Phpfox_Component
{
    public function process()
    {
        $oHelper = Phpfox::getService('fevent.helper');

        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = $oHelper->isSideLocation($sBlockLocation);
        $isSlider = $this->getParam('is_slider', 0);

        if ($bIsSearch && !$bIsSideLocation) {
            return false;
        }

        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aParentModule = $this->getParam('aParentModule');
        $bIsPage = $aParentModule['module_id'] == 'pages' ? true : false;
        $pageId = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : -1;

        $aFooter = [];
        $sDataSource = $this->getParam('data_source', 'ongoing');
        $hideStatus = false;

        switch ($sDataSource) {
            case 'ongoing':
                list($iTotal, $events) = Phpfox::getService('fevent')->getOnHomepageByType('ongoing', $iLimit, $bIsPage, false, $pageId);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('fevent', array('when' => 'ongoing', 'view' => 'all')));
                $hideStatus = true;
                break;
            case 'upcoming':
                list($iTotal, $events) = Phpfox::getService('fevent')->getOnHomepageByType('upcoming', $iLimit, $bIsPage, false, $pageId);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('fevent', array('when' => 'upcoming', 'view' => 'all')));
                $hideStatus = true;
                break;
            case 'past':
                list($iTotal, $events) = Phpfox::getService('fevent')->getPast($bIsPage, false, $iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('fevent', array('when' => 'past', 'view' => 'all')));
                $hideStatus = true;
                break;
            case 'featured':
                list($iTotal, $events) = Phpfox::getService('fevent')->getFeatured($bIsPage, false, $iLimit, true);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('fevent', array('view' => 'all', 'sort' => 'featured')));
                break;
            case 'sponsored':
                $events = Phpfox::getService('fevent')->getRandomSponsored($iLimit);
                if (Phpfox::isAppActive('Core_BetterAds')) {
                    foreach ($events as $event) {
                        Phpfox::getService('ad.process')->addSponsorViewsCount($event['sponsor_id'], 'event');
                    }
                }
                $aFooter = array(_p('encourage_sponsor_fevents') => $this->url()->makeUrl('fevent', array('view' => 'my')));
                break;
            case 'suggest':
                list($iTotal, $events) = Phpfox::getService('fevent')->getBlockData($sDataSource, $bIsPage, false, $iLimit);
                break;
            case 'popular':
                list($iTotal, $events) = Phpfox::getService('fevent')->getBlockData($sDataSource, $bIsPage, false, $iLimit);
                break;
            case 'related':
                $currentEvent = $this->getParam('aEvent');
                list($iTotal, $events) = Phpfox::getService('fevent')->getBlockData($sDataSource, $bIsPage, false, $iLimit, ['event_id' => $currentEvent['event_id']]);
                break;
            case 'reminder':
                list($iTotal, $events) = Phpfox::getService('fevent')->getBlockData($sDataSource, $bIsPage, false, $iLimit);
                break;
            case 'invited':
                list($iTotal, $events) = Phpfox::getService('fevent')->getBlockData($sDataSource, $bIsPage, false, $iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('fevent', array('view' => 'invites')));
                break;
            case 'most-liked':
                list($iTotal, $events) = Phpfox::getService('fevent')->getTopEvent('liked', $iLimit, $bIsPage, false, $pageId);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('fevent', array('sort' => 'most-liked')));
                break;
            case 'most-viewed':
                list($iTotal, $events) = Phpfox::getService('fevent')->getTopEvent('viewed', $iLimit, $bIsPage, false, $pageId);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('fevent', array('sort' => 'most-viewed')));
                break;
            case 'most-discussed':
                list($iTotal, $events) = Phpfox::getService('fevent')->getTopEvent('discussed', $iLimit, $bIsPage, false, $pageId);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('fevent', array('sort' => 'most-talked')));
                break;
        }
        if (empty($events)) {
            return false;
        }
        $len = count($events);
        $iCurYear = Phpfox::getTime('Y');

        for ($i = 0; $i < $len; $i++) {
            $events[$i]['d_type'] = $oHelper->getTimeLineStatus($events[$i]['start_time'], $events[$i]['end_time']);
            $events[$i]['d_left'] = $oHelper->timestampToCountdownString($events[$i]['end_time'], 'ongoing');

            if ((int)$events[$i]['isrepeat'] >= 0) {
                $events[$i]['d_repeat_time'] = $oHelper->displayRepeatTime((int)$events[$i]['isrepeat'], (int)$events[$i]['timerepeat']);
            }

            $events[$i]['d_start_time'] = $oHelper->displayTimeByFormat('j', (int)$events[$i]['start_time']);
            $events[$i]['M_start_time'] = $oHelper->displayTimeByFormat('M', (int)$events[$i]['start_time']);

            $events[$i]['short_start_time'] = $oHelper->displayTimeByFormat('g:i a', (int)$events[$i]['start_time']);

            $events[$i]['year'] = $oHelper->displayTimeByFormat('Y', (int)$events[$i]['end_time']);
            $events[$i]['check'] = abs($iCurYear - $events[$i]['year']);
            $oHelper->getImageDefault($events[$i], 'home');

            if(!$isSlider) {
                $timeNeedToFormatted = in_array($events[$i]['d_type'], ['ongoing', 'past']) ? $events[$i]['end_time'] : $events[$i]['start_time'];
                $events[$i]['date_formatted'] = Phpfox::getService('fevent.helper')->formatTimeToDate($events[$i]['d_type'], $timeNeedToFormatted, $bIsSideLocation);
            }
            else {
                $events[$i]['start_time_basic_information_time'] = Phpfox::getService('fevent.helper')->formatTimeToDate($events[$i]['d_type'], $events[$i]['start_time'], false, true);
                $events[$i]['end_time_basic_information_time'] = Phpfox::getService('fevent.helper')->formatTimeToDate($events[$i]['d_type'], (int)$events[$i]['end_time']);
            }

            $location = '';
            if(!empty($events[$i]['location'])) {
                $location .= $events[$i]['location'] .' - ';
            }
            if(!empty($events[$i]['address'])) {
                $location .= $events[$i]['address'] . ' - ';
            }
            if(!empty($events[$i]['city'])) {
                $location .= $events[$i]['city'];
            }
            $events[$i]['location_parsed'] = trim($location, ' - ');

            if($events[$i]['isrepeat'] != -1) {
                if ($events[$i]['isrepeat'] == 0) {
                    $events[$i]['repeat_title'] = _p('daily');
                } else if ($events[$i]['isrepeat'] == 1) {
                    $events[$i]['repeat_title'] = _p('weekly');
                } else if ($events[$i]['isrepeat'] == 2) {
                    $events[$i]['repeat_title'] = _p('monthly');
                }
            }


            Phpfox::getService('fevent')->getMoreInfoForEventItem($events[$i]);
        }

        $iBlockId = $this->getParam('id', 0);

        $aViewModes = $this->getParam('view_modes', []);

        if (!$this->getParam('display_view_more', 0)) {
            $aFooter = [];
        }

        if ($bIsSideLocation) {
            if ($sDataSource == 'sponsored') {
                $sModeViewDefault = 'grid';
            } else {
                $sModeViewDefault = 'list';
            }
            $aSupportedViewModes = [];
        } else {
            $sModeViewDefault = 'grid';
            $aSupportedViewModes = Phpfox::getService('fevent.helper')->getSupportedViewModes(in_array($sDataSource, array('ongoing', 'upcoming')) ? $iBlockId : 0);
            foreach ($aSupportedViewModes as $key => $aViewMode) {
                if (!in_array($key, $aViewModes)) {
                    unset($aSupportedViewModes[$key]);
                } else {
                    $aSupportedViewModes[$key]['callback_data'] = $sDataSource;
                }
            }
            if (!empty($aSupportedViewModes)) {
                $sModeViewDefault = '';
            }
        }

        $this->setParam('sModeViewDefault', $sModeViewDefault);
        $this->setParam('aSupportedViewModes', $aSupportedViewModes);
        $this->setParam('sModeViewId', 'p-fevent-' . $iBlockId);

        $this->template()->assign(array(
            'events' => $events,
            'aFooter' => $aFooter,
            'isSlider' => $isSlider,
            'iTotal' => $iTotal,
            'iLimit' => $iLimit,
            'sCustomClassName' => 'p-block',
            'sHeader' => $this->getHeader($sDataSource),
            'sModeViewDefault' => $sModeViewDefault,
            'rsvpActionType' => 'list',
            'dataSource' => $sDataSource,
            'id' => $iBlockId,
            'hideStatus' => $hideStatus,
            'canDoModeration' => false,
            'additionalClass' => $this->getAdditionalClass($sDataSource),
            'isSideLocation' => $bIsSideLocation
        ));

        return 'block';
    }

    public function getAdditionalClass($dataSource) {
        $class = '';

        switch($dataSource) {
            case 'reminder':
                $class = 'reminder-block';
                break;
            case 'invited':
                $class = 'invitation-block';
                break;
        }

        return $class;
    }

    public function getSettings()
    {
        return array(
            array(
                'info' => _p('fevent.data_source'),
                'value' => 'latest',
                'options' => array(
                    'ongoing' => _p('ongoing_events'),
                    'upcoming' => _p('upcoming_events'),
                    'past' => _p('past_events'),
                    'featured' => _p('featured_events'),
                    'sponsored' => _p('sponsored_events'),
                    'suggest' => _p('fevent.suggested_events'),
                    'popular' => _p('fevent.popular_events'),
                    'related' => _p('fevent.related_events'),
                    'reminder' => _p('fevent.reminder'),
                    'invited' => _p('fevent.invited_events'),
                    'most-liked' => _p('most_liked'),
                    'most-viewed' => _p('most_viewed'),
                    'most-discussed' => _p('most_discussed'),
                ),
                'type' => 'select',
                'var_name' => 'data_source',
            ),
            array(
                'info' => _p('event_limit'),
                'description' => _p('event_limit_desc'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ),
            array(
                'info' => _p('fevent.display_view_more_link'),
                'description' => _p('fevent_display_view_more_link_desc'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'display_view_more',
            ),
            array(
                'info' => _p('fevent.slider_format'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'is_slider',
            ),
            array(
                'info' => _p('fevent.view_modes'),
                'description' => _p('fevent.view_modes_desc'),
                'value' => array(
                    'grid',
                ),
                'options' => array(
                    'list' => _p('list_view'),
                    'grid' => _p('grid_view'),
                    'map' => _p('map_view'),
                ),
                'type' => 'multi_checkbox',
                'var_name' => 'view_modes',
            )
        );
    }

    private function getHeader($dataSource) {
        $header = '';
        switch ($dataSource) {
            case 'ongoing':
                $header = _p('oge_title');
                break;
            case 'upcoming':
                $header = _p('ue_title');
                break;
            case 'past':
                $header = _p('past_events_title');
                break;
            case 'featured':
                $header = _p('feature_events_title');
                break;
            case 'sponsored':
                $header = _p('sponsored_events');
                break;
            case 'suggest':
                $header = _p('suggested_events_title');
                break;
            case 'popular':
                $header = _p('popular_events_title');
                break;
            case 'related':
                $header = _p('related_events_title');
                break;
            case 'reminder':
                $header = _p('event_reminder_title');
                break;
            case 'invited':
                $header = _p('invitation_title');
                break;
            case 'most-liked':
                $header = _p('most_liked');
                break;
            case 'most-viewed':
                $header = _p('most_viewed');
                break;
            case 'most-discussed':
                $header = _p('most_discussed');
                break;
        }

        return $header;
    }

    public function getValidation()
    {
        return array(
            'limit' => array(
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('events_limit_is_required_and_must_greater_or_equal_zero')
            )
        );
    }
}
