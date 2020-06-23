<?php

namespace Apps\P_AdvEvent\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Error;

class ViewController extends Phpfox_Component
{
    private function _boxComment($aItem)
    {
        $share = array(
            'feed_display' => 'mini',
            'privacy' => $aItem['privacy'],
            'comment_privacy' => Phpfox::getUserParam('fevent.can_post_comment_on_event') ? 0 : 3,
            'like_type_id' => 'fevent',
            'feed_is_liked' => (isset($aItem['is_liked']) ? $aItem['is_liked'] : false),
            'feed_is_friend' => (isset($aItem['is_friend']) ? $aItem['is_friend'] : false),
            'item_id' => $aItem['event_id'],
            'user_id' => $aItem['user_id'],
            'feed_total_like' => $aItem['total_like'],
            'total_like' => $aItem['total_like'],
            'feed_link' => Phpfox::getLib('url')->permalink('fevent', $aItem['event_id'], $aItem['title']),
            'feed_title' => $aItem['title'],
            'type_id' => 'fevent',
            'report_module' => 'fevent',
            'report_phrase' => _p('report_an_event'));
        $this->setParam('aFeed', $share);

        $this->template()->assign(array('shareButton' => $share));
    }

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $gcalendar = 0;
        if ($this->request()->get('req4') == "gsuccess") {
            $gcalendar = 1;
        }

        Phpfox::getService('fevent.helper')->buildSectionMenu();
        $oHelper = Phpfox::getService('fevent.helper');
        Phpfox::getService('fevent.helper')->updateDurationOfRepeatEvents();

        if ($this->request()->get('req2') == 'view' && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle)) {
            Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('event_id', 'title'),
                    'table' => 'fevent',
                    'redirect' => 'fevent',
                    'title' => $sLegacyTitle
                )
            );
        }

        Phpfox::getUserParam('fevent.can_access_event', true);

        $sEvent = $this->request()->get('req2');

        if (!($aEvent = Phpfox::getService('fevent')->getEvent($sEvent))) {
            return Phpfox_Error::display(_p('the_event_you_are_looking_for_does_not_exist_or_has_been_removed'));
        }
        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('fevent', $aEvent['event_id'], $aEvent['user_id'], $aEvent['privacy'], $aEvent['is_friend']);
        }

        if (isset($aEvent['module_id']) && Phpfox::isModule($aEvent['module_id']) && Phpfox::hasCallback($aEvent['module_id'],
                'checkPermission')) {
            if (!Phpfox::callback($aEvent['module_id'] . '.checkPermission', $aEvent['item_id'],
                'blog.view_browse_blogs')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        Phpfox::getService('fevent.process')->updateView($aEvent['event_id']);

        $bCanPostComment = (Phpfox::getUserParam('fevent.can_post_comment_on_event') || $aEvent['user_id'] == Phpfox::getUserId()) ? true : false;

        $aCallback = false;
        if ($aEvent['item_id'] && Phpfox::hasCallback($aEvent['module_id'], 'viewEvent')) {
            $aCallback = Phpfox::callback($aEvent['module_id'] . '.viewEvent', $aEvent['item_id']);
            $aCallback['url_home_pages'] = $aCallback['url_home'] . 'fevent/when_upcoming';
            (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_view_process_getcallback')) ? eval($sPlugin) : false);
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
            if ($aEvent['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aCallback['item_id'], 'fevent.view_browse_events')) {
                return Phpfox_Error::display('Unable to view this item due to privacy settings.');
            }
        }

        $this->setParam('aFeedCallback', array(
                'module' => 'fevent',
                'table_prefix' => 'fevent_',
                'ajax_request' => 'fevent.addFeedComment',
                'item_id' => $aEvent['event_id'],
                'disable_share' => ($bCanPostComment ? false : true),
                'disable_sort' => true
            )
        );


        if (Phpfox::getUserId() == $aEvent['user_id']) {
            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->delete('event_approved', $this->request()->getInt('req2'), Phpfox::getUserId());
            }

            define('PHPFOX_FEED_CAN_DELETE', true);
        }

        $bCanViewMap = true;
        $content_repeat = "";
        $until = "";
        if ($aEvent['isrepeat'] == 0) {
            $content_repeat = _p('daily');
        } else if ($aEvent['isrepeat'] == 1) {
            $content_repeat = _p('weekly');
        } else if ($aEvent['isrepeat'] == 2) {
            $content_repeat = _p('monthly');
        }
        if ($content_repeat != "") {
            if ($aEvent['timerepeat'] != 0) {
                $sDefault = null;
                $until = Phpfox::getTime("M j, Y", $aEvent['timerepeat']);
                $content_repeat .= ", " . _p('until') . " " . $until;
            }
        }

        //add count down
        $seconds_taken = $aEvent['start_time'] - PHPFOX_TIME;
        if ($seconds_taken > 0) {
            $aEvent['time_left'] = Phpfox::getService('fevent.browse')->seconds2string($seconds_taken);
        } else {
            $aEvent['time_left'] = '';
        }

        //	get extra information in event
        $aEvent['d_type'] = $oHelper->getTimeLineStatus($aEvent['start_time'], $aEvent['end_time']);
        if ('upcoming' == $aEvent['d_type']) {
            $aEvent['d_start_in'] = $oHelper->timestampToCountdownString($aEvent['start_time'], 'upcoming');
            $aEvent['d_start_in'] = str_replace(':', '', $aEvent['d_start_in']);
        }
        if ('ongoing' == $aEvent['d_type']) {
            $aEvent['d_left'] = $oHelper->timestampToCountdownString($aEvent['end_time'], 'ongoing');
        }

        if ((int)$aEvent['isrepeat'] > 0) {
            $aEvent['d_repeat_time'] = $oHelper->displayRepeatTime((int)$aEvent['isrepeat'], (int)$aEvent['timerepeat']);
        }

        $aEvent['d_start_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('core.global_update_time'), (int)$aEvent['start_time']);
        //  any status event (upcoming, ongoing, past) has start time
        //  with: upcoming event: start time at this time is next start time
        $aEvent['d_next_start_time'] = $aEvent['d_start_time'];
        $aEvent['d_end_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('core.global_update_time'), (int)$aEvent['end_time']);

        $aEvent = Phpfox::getService('fevent.helper')->retrieveEventPermissions($aEvent);

        if ($aEvent['view_id'] == '1') {
            $aPendingItem = [
                'message' => _p('event_is_pending_approval'),
                'actions' => []
            ];
            if (Phpfox::getUserParam('fevent.can_approve_events')) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'fevent.approve\', \'inline=false&amp;event_id=' . $aEvent['event_id'] . '\', \'POST\')'
                ];
            }
            if ($aEvent['can_edit_event']) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('fevent.add', ['id' => $aEvent['event_id']]),
                ];
            }
            if ($aEvent['can_delete_event']) {
                $aPendingItem['actions']['delete'] = [
                    'is_confirm' => true,
                    'confirm_message' => _p('are_you_sure_you_want_to_delete_this_event_permanently'),
                    'label' => _p('delete'),
                    'action' => $this->url()->makeUrl('fevent', ['delete' => $aEvent['event_id']]),
                ];
            }

            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }
        $this->_boxComment($aEvent);

        if (Phpfox::getParam('core.allow_html') && !empty($aEvent['description'])) {
            $oFilter = Phpfox::getLib('parse.input');
            $aEvent['description'] = $oFilter->prepare(htmlspecialchars_decode($aEvent['description']));
        }

        $aTitleLabel = [
            'type_id' => 'ync-type-id'
        ];

        if ($aEvent['is_featured']) {
            Phpfox::getLib('module')->appendPageClass('item-featured');
            $aTitleLabel['label']['featured'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'diamond'
            ];
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;

        #Google calendar API
        $bIsGapi = false;
        if ($aGapi = Phpfox::getService('fevent.gapi')->getForManage()) {
            $bIsGapi = true;
        }

        Phpfox::getService('fevent.helper')->parseDisplayStartTime($aEvent);
        Phpfox::getService('fevent')->getAllNumbersOfAttendee($aEvent);
        $aImages = Phpfox::getService('fevent')->getImages($aEvent['event_id']);
        $defaultImage = Phpfox::getService('fevent')->getDefaultPhoto();
        $repeat = Phpfox::getService('fevent.helper')->getRepeatPhrase($aEvent);
        $aEvent['total_guest'] = $aEvent['iAttendingCnt'] + $aEvent['iMaybeCnt'];

        $timeToShowCountDown = Phpfox::getParam('fevent.fevent_time_to_show_countdown', 7) * DAY_IN_SECONDS;
        $timeToShowCountDown += PHPFOX_TIME;
        $isTimeToShowCountDown = $timeToShowCountDown > $aEvent['start_time'];

        $diff = abs($aEvent['start_time'] - PHPFOX_TIME);
        $days_diff = floor($diff / 86400);
        $hours_diff = floor(($diff - $days_diff * 86400) / 3600);

        if (!($days_diff || $hours_diff)) {
            $hours_diff = 1;
        }

        $locationText = $aEvent['location'];
        if (!empty($aEvent['address'])) {
            $locationText .= ', ' . $aEvent['address'];
        }
        if (!empty($aEvent['city'])) {
            $locationText .= ', ' . $aEvent['city'];
        }
        if (!empty($aEvent['country_iso'])) {
            $locationText .= ', ' . Phpfox::getPhraseT(Phpfox::getService('core.country')->getCountry($aEvent['event_country_iso']), 'country');
        }

        list($iAttendingCnt,) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 1);
        list($iMaybeCnt,) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 2);
        list($iAwaitingCnt,) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 0);

        Phpfox::getService('fevent')->getMoreInfoForEventItem($aEvent);
        if (!empty($aEvent['custom']) && is_array($aEvent['custom'])) {
            foreach ($aEvent['custom'] as $key => $field) {
                if (strpos($field['value'], "\n") !== FALSE) {
                    $aEvent['custom'][$key]['value'] = nl2br($field['value']);
                }
            }
        }
        $this->setParam([
            'aEvent' => $aEvent,
            'allowTagFriends' => false
        ]);

        $this->template()->setTitle($aEvent['title'])
            ->setMeta('description', $aEvent['description'])
            ->setMeta('og:description', $aEvent['description'])
            ->setMeta('keywords', $this->template()->getKeywords($aEvent['title']))
            ->setMeta('og:image', Phpfox::getLib('image.helper')->display(
                array(
                    'server_id' => $aEvent['server_id'],
                    'path' => 'event.url_image',
                    'file' => $aEvent['image_path'],
                    'suffix' => '',
                    'return_url' => true
                )
            ))
            ->setMeta('og:image:width', 600)
            ->setMeta('og:image:height', 500)
            ->setMeta('og:image:type', 'image/jpeg')
            ->setMeta('keywords', Phpfox::getParam('fevent.fevent_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('fevent.fevent_meta_description'))
            ->setBreadcrumb(_p('events'), ($aCallback === false ? $this->url()->makeUrl('fevent') : $this->url()->makeUrl($aCallback['url_home_pages'])))
            ->setBreadcrumb($aEvent['title'], $this->url()->permalink('fevent', $aEvent['event_id'], $aEvent['title']), true)
            ->setEditor(
                array(
                    'load' => 'simple'
                )
            )
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                'jscript/view.js' => 'app_p-advevent',
                'jscript/fevent.js' => 'app_p-advevent',
                'feed.js' => 'module_feed',
                'jscript/jquery.magnific-popup.js' => 'app_p-advevent',
            ))
            ->setPhrase(array(
                'start_time',
                'v_getdirections',
                'please_choose_the_type_of_event_to_edit',
                'confirm',
                'cancel',
                'how_would_you_want_to_submit_your_rsvp',
                'please_choose_the_type_of_event_to_apply',
                'guest_list',
                'view_more'
            ))
            ->assign(
                array(
                    'apiKey' => Phpfox::getParam('core.google_api_key'),
                    'bIsGapi' => $bIsGapi,
                    'aEvent' => $aEvent,
                    'aItem' => $aEvent,
                    'aImages' => $aImages,
                    'defaultImage' => $defaultImage,
                    'isTimeToShowCountDown' => $isTimeToShowCountDown,
                    'days_diff' => $days_diff ? $days_diff . 'd' : '',
                    'hours_diff' => $hours_diff ? $hours_diff . 'h' : '',
                    'repeat' => $repeat,
                    'locationText' => $locationText,
                    'content_repeat' => $content_repeat,
                    'aCallback' => $aCallback,
                    'bCanViewMap' => $bCanViewMap,
                    'gcalendar' => $gcalendar,
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aEvent['description']),
                    'aTitleLabel' => $aTitleLabel,
                    'iAttendingCnt' => $iAttendingCnt,
                    'iMaybeCnt' => $iMaybeCnt,
                    'iAwaitingCnt' => $iAwaitingCnt,
                    'bIsDetail' => true,
                    'showApproveAction' => true
                )
            );

        Phpfox::getLib('module')->appendPageClass('p-detail-page p-custom-detail');
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}