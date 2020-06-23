<?php

namespace Apps\P_AdvEvent\Ajax;

use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{
    public function sendWish()
    {
        Phpfox::isUser(true);
        $userId = $this->get('user_id');
        $message = urldecode($this->get('message'));

        if(!Phpfox::getService('user.privacy')->hasAccess($userId, 'feed.share_on_wall') || empty($userId) || ($userId == Phpfox::getUserId()) || empty($message)) {
            $this->alert(_p('fevent_you_can_not_send_this_wish'));
            return false;
        }

        $insert = [
            'user_id' => Phpfox::getUserId(),
            'target_user_id' => $userId,
            'message' => Phpfox::getLib('parse.input')->clean($message),
            'time_stamp' => PHPFOX_TIME
        ];

        if(Phpfox::getService('fevent.process')->addSendWish($insert)) {
            Phpfox::getBlock('fevent.send-wish',[
                'load_content' => true
            ]);
            $blockContent = $this->getContent(false);
            $this->html('.js_send_wish_content', $blockContent);
            $this->alert(_p('fevent_send_wish_successfully'), null, 300, 150, true);
        }

    }

    public function openSendWish()
    {
        Phpfox::isUser(true);
        Phpfox::getBlock('fevent.send-wish');
        echo '<script type="text/javascript">$(\'.p-fevent-popup-send-wish-container\').closest(\'.js_box\').addClass(\'p-fevent-popup-box-container\');</script>';
    }

    public function migrateData()
    {
        $is_migrate_album = false;
        $event_list = phpfox::getService('fevent')->getAllEventPhpfox();

        if (count($event_list) > 0) {
            $is_migrate = true;
            foreach ($event_list as $event) {
                $fevent = array();
                $fevent['view_id'] = $event['view_id'];
                $fevent['is_featured'] = $event['is_featured'];
                $fevent['is_sponsor'] = $event['is_sponsor'];
                $fevent['privacy'] = $event['privacy'];
                $fevent['privacy_comment'] = $event['privacy_comment'];
                $fevent['module_id'] = $event['module_id'];
                $fevent['item_id'] = $event['item_id'];
                $fevent['user_id'] = $event['user_id'];
                $fevent['title'] = $event['title'];
                $fevent['location'] = $event['location'];
                $fevent['country_iso'] = $event['country_iso'];
                $fevent['country_child_id'] = $event['country_child_id'];
                $fevent['postal_code'] = $event['postal_code'];
                $fevent['city'] = $event['city'];
                $fevent['time_stamp'] = $event['time_stamp'];
                $fevent['start_time'] = $event['start_time'];
                $fevent['end_time'] = $event['end_time'];
                $fevent['image_path'] = $event['image_path'];
                $fevent['server_id'] = $event['server_id'];
                $fevent['total_comment'] = 0;
                $fevent['total_like'] = 0;
                $fevent['total_view'] = 0;
                $fevent['total_attachment'] = 0;
                $fevent['mass_email'] = $event['mass_email'];
                $fevent['start_gmt_offset'] = $event['start_gmt_offset'];
                $fevent['end_gmt_offset'] = $event['end_gmt_offset'];
                $fevent['gmap'] = $event['gmap'];
                $fevent['address'] = $event['address'];
                $fevent['lat'] = 0;
                $fevent['lng'] = 0;
                $fevent['gmap_address'] = "";
                $fevent['isrepeat'] = -1;
                $fevent['timerepeat'] = 0;
                $fevent['range_value'] = 0;
                $fevent['range_type'] = 0;
                $fevent['range_value_real'] = 0;

                $last_insert_id_event = phpfox::getLib('database')->insert(Phpfox::getT('fevent'), $fevent);
                $fevent['event_id'] = $last_insert_id_event;
                $aCategory = phpfox::getService("fevent")->getAllCategorydataPhpfox($event['event_id']);

                $category_data = array();
                if (isset($aCategory['event_id'])) {
                    $category_data['event_id'] = $fevent['event_id'];
                    $category_data['category_id'] = $aCategory['category_id'];
                    phpfox::getLib("database")->insert(Phpfox::getT('fevent_category_data'), $category_data);
                }

                if (!empty($event['image_path'])) {
                    db()->insert(':fevent_image', [
                        'image_path' => $event['image_path'],
                        'server_id' => $event['server_id'],
                        'event_id' => $last_insert_id_event
                    ]);
                }

                $aFeedEvent = phpfox::getService('fevent')->getAllFeedEventPhpfox($event['event_id']);

                foreach ($aFeedEvent as $FeedEvent) {
                    $FeedComment = array();

                    $FeedCommentById = phpfox::getService("fevent")->getFeedCommentPhpfox($FeedEvent['item_id']);

                    if (isset($FeedCommentById['feed_comment_id'])) {
                        $Feed = array();

                        $Feed['privacy'] = $FeedEvent['privacy'];
                        $Feed['privacy_comment'] = $FeedEvent['privacy_comment'];
                        $Feed['user_id'] = $FeedEvent['user_id'];
                        $Feed['type_id'] = $FeedEvent['type_id'];
                        $Feed['parent_user_id'] = $fevent['event_id'];
                        $Feed['item_id'] = 0;
                        $Feed['time_stamp'] = $FeedEvent['time_stamp'];
                        $feed_id = phpfox::getLib("database")->insert(phpfox::getT('fevent_feed'), $Feed);
                    }
                }

                $event_test = phpfox::getService('fevent')->getEventTextPhpfox($event['event_id']);
                if (isset($event_test['event_id'])) {
                    $eText = array();
                    $eText['event_id'] = $fevent['event_id'];
                    $eText['description'] = $event_test['description'];
                    $eText['description_parsed'] = $event_test['description_parsed'];
                    phpfox::getLib("database")->insert(phpfox::getT('fevent_text'), $eText);
                }

                $ainviteEvent = phpfox::getService("fevent")->getInviteEventPhpfox($event['event_id']);
                foreach ($ainviteEvent as $inviteEvent) {
                    $InEvent = array();
                    $InEvent['event_id'] = $fevent['event_id'];
                    $InEvent['type_id'] = $inviteEvent['type_id'];
                    $InEvent['rsvp_id'] = $inviteEvent['rsvp_id'];
                    $InEvent['user_id'] = $inviteEvent['user_id'];
                    $InEvent['invited_user_id'] = $inviteEvent['invited_user_id'];
                    $InEvent['invited_email'] = $inviteEvent['invited_email'];
                    $InEvent['time_stamp'] = $inviteEvent['time_stamp'];

                    phpfox::getLib("database")->insert(phpfox::getT('fevent_invite'), $InEvent);
                }

                if ($last_insert_id_event > 0) {
                    $this->html('#info_process', _p('imported_event') . " '" . $fevent['title'] . "' " . _p('successfully'));
                }
            }

        } else {
            $is_migrate = false;
            $this->html('#info_process', _p('there_is_no_event_to_import'));
        }
        if ($is_migrate == true || $is_migrate_album == true) {
            $this->html('#info_process', _p('import_successfully'));
            $this->html('#contener_pro', '<div id="contener_percent" style="background-color: green;height:100%;width:100%">
                   100%
                </div>');
            $this->alert(_p('import_successfully'));
        } else {
            if ($is_migrate == false && $is_migrate_album == false) {
                $this->html('#contener_pro', '<div id="contener_percent" style="background-color: green;height:100%;width:100%">
                       100%
                    </div>');
                $this->html('#info_process', "There is no event to import.");
                $this->alert(_p('there_is_no_event_to_import'));
            }
        }
    }

    public function deleteImage()
    {
        Phpfox::isUser(true);
        $image_id = $this->get('id');
        if (Phpfox::getService('fevent.process')->deleteImage($image_id)) {
            $this->remove('#js_photo_holder_' . $image_id);
        }
    }

    public function addRsvp()
    {
        Phpfox::isUser(true);

        $eventId = $this->get('id');
        if (empty($eventId)) {
            return false;
        }

        $sTypeEvent = $this->get('type_event', '');
        $rsvpId = $this->get('rsvp');
        $isInline = $this->get('inline');

        if ($sTypeEvent == 'only_this_event' || $sTypeEvent == 'following_events') {
            $bResult = Phpfox::getService('fevent.process')->addRsvpForRecurrentEvent($eventId, $rsvpId, Phpfox::getUserId(), $sTypeEvent);
        } else {
            $bResult = Phpfox::getService('fevent.process')->addRsvp($eventId, $rsvpId, Phpfox::getUserId());
        }

        if ($bResult) {
            if ($rsvpId == 3) {
                $sRsvpMessage = _p('not_attending');
            } elseif ($rsvpId == 2) {
                $sRsvpMessage = _p('maybe_attending');
            } elseif ($rsvpId == 1) {
                $sRsvpMessage = _p('attending');
            }

            $isInvited = Phpfox::getService('fevent')->isInvitedByOwner($eventId);
            $rsvpType = $this->get('rsvp_type');
            if ($rsvpType == 'list') {
                $params = [
                    'rsvpActionType' => 'list',
                    'aItem' => [
                        'rsvp_id' => !$isInvited && $rsvpId == 0 ? null : $rsvpId,
                        'is_invited' => $isInvited,
                        'event_id' => $eventId
                    ]
                ];
            } else {
                $params = [
                    'rsvpActionType' => '',
                    'aEvent' => [
                        'rsvp_id' => !$isInvited && $rsvpId == 0 ? null : $rsvpId,
                        'is_invited' => $isInvited
                    ]
                ];
            }
            $tempContent = $this->getContent(false);
            \Phpfox_Template::instance()->assign($params)->getTemplate('fevent.block.rsvp-action');
            $rsvpActionContent = $this->getContent(false);
            $this->html('.js_rsvp_action_' . ($this->get('rsvp_type') == 'list' ? 'list_' : '') . $eventId, $rsvpActionContent);
            echo $tempContent;
            if ($isInline) {
                $this->html('#js_event_rsvp_' . $eventId, $sRsvpMessage);
            } else {
                $this->html('#js_event_rsvp_update', _p('done'), '.fadeOut(5000);')
                    ->html('#js_event_rsvp_' . $eventId, $sRsvpMessage)
                    ->call('$(\'#js_event_rsvp_button\').find(\'input:first\').attr(\'disabled\', false);')
                    ->call('tb_remove();');

                $this->call('$.ajaxCall(\'fevent.listGuests\', \'&rsvp=' . $rsvpId . '&id=' . $eventId . '' . ($this->get('module') ? '&module=' . $this->get('module') . '&item=' . $this->get('item') . '' : '') . '\');')
                    ->call('$(function(){ $(\'#js_block_border_event_list .menu:first ul li\').removeClass(\'active\'); $(\'#js_block_border_event_list .menu:first ul li a\').each(function() { var aParts = explode(\'rsvp=\', this.href); var aParts2 = explode(\'&\', aParts[1]); if (aParts2[0] == ' . $this->get('rsvp') . ') {  $(this).parent().addClass(\'active\'); } }); });');

                if ($rsvpId == 1 && Phpfox::getService('fevent.gapi')->getForManage()) {
                    $this->call('tb_show("' . _p('google_calendar') . '",$.ajaxBox("fevent.glogin","height=300;width=350&id="+' . $eventId . '))');
                }
            }
        }
    }

    public function listGuests()
    {
        Phpfox::getBlock('fevent.list');

        $this->html('#js_event_item_holder', $this->getContent(false));
    }

    public function browseList()
    {
        Phpfox::getBlock('fevent.browse');

        if ((int)$this->get('page') > 0) {
            $this->html('#js_event_browse_guest_list', $this->getContent(false));
        } else {
            $this->setTitle(_p('guest_list'));
        }
    }

    public function deleteGuest()
    {
        if (Phpfox::getService('fevent.process')->deleteGuest($this->get('id'))) {

        }
    }

    public function delete()
    {
        $iEventId = (int)$this->get('id');
        $event = Phpfox::getService('fevent')->getEvent($iEventId);
        $bCanDelete = Phpfox::getService('fevent.helper')->canDeleteEvent($event);
        if ($iEventId <= 0 || !$bCanDelete) {
            return $this->alert(_p('unable_to_delete_this_item_due_to_privacy_settings'), null, null, null, true);
        }

        Phpfox::getService('fevent.process')->delete($iEventId);
        if ($this->get('is_detail')) {
            Phpfox::addMessage(_p('successfully_deleted_event'));
            $sLink = Phpfox::getLib('url')->makeUrl('fevent');
            $this->call('window.location.href = \'' . $sLink . '\'');
        } else {
            $this->alert(_p('successfully_deleted_event'));
            return $this->call('setTimeout(function() {window.$Core.reloadPage();}, 1800);');
        }
    }

    public function deleteEvent()
    {
        // Get Params
        $event_id = (int)$this->get('event_id');
        if ($event_id) {
            Phpfox::getService('fevent.process')->delete($event_id);
        }
        Phpfox::addMessage(_p('event_successfully_deleted'));
        $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");
        $this->call('setTimeout(function() {window.location.reload();},500);');

    }

    public function rsvp()
    {
        Phpfox::getBlock('fevent.rsvp');
    }

    public function feature()
    {
        if (Phpfox::getService('fevent.process')->feature($this->get('event_id'), $this->get('type'))) {
            if($this->get('reload_content')) {
                $event = Phpfox::getService('fevent')->getSimpleEventForStatusIcon($this->get('event_id'));
                if(!empty($event)) {
                    \Phpfox_Template::instance()->assign([
                        'aItem' => $event
                    ])->getTemplate('fevent.block.status-icon');
                    $content = $this->getContent(false);
                    $this->html('.js_status_icon_'. $this->get('event_id'), $content);
                }
            }

            $this->alert(($this->get('type') == '1' ? _p('event_successfully_featured') : _p('event_successfully_un_featured')));
            if ($this->get('type') == '1') {
                //$this->addClass('#js_photo_id_' . $this->get('photo_id'), 'row_featured_image');
                $this->call('$(\'.fe_feature_' . $this->get('event_id') . '\').show();');
            } else {
                //$this->removeClass('#js_photo_id_' . $this->get('photo_id'), 'row_featured_image');
                $this->call('$(\'.fe_feature_' . $this->get('event_id') . '\').hide();');
            }
        }
    }

    public function updateFeaturedBackEnd()
    {
        Phpfox::isAdmin(true);
        $event_id = (int)$this->get('event_id');
        $iIsFeatured = (int)$this->get('iIsFeatured');

        if ($event_id) {
            Phpfox::getService('fevent.process')->feature($event_id, $iIsFeatured);
        }
    }

    public function sponsor()
    {
        if (Phpfox::getService('fevent.process')->sponsor($this->get('event_id'), $this->get('type'))) {
            $aEvent = Phpfox::getService('fevent')->getEventByID($this->get('event_id'));
            if ($this->get('type') == '1') {
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'fevent',
                    'item_id' => $this->get('event_id'),
                    'name' => _p('default_campaign_custom_name', ['module' => _p('event'), 'name' => $aEvent['title']])
                ));
                $this->call('$("#js_event_unsponsor_' . $this->get('event_id') . '").show();');
                $this->call('$("#js_event_sponsor_' . $this->get('event_id') . '").hide();');
                $this->addClass('#js_event_item_holder_' . $this->get('event_id'), 'row_sponsored');
                $this->show('#js_sponsor_phrase_' . $this->get('event_id'));
                $this->alert(_p('event_successfully_sponsored'), null, 300, 150, true);
                $this->call('$(\'.fe_sponsor_' . $this->get('event_id') . '\').show();');
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('fevent', $this->get('event_id'));
                $this->call('$("#js_event_unsponsor_' . $this->get('event_id') . '").hide();');
                $this->call('$("#js_event_sponsor_' . $this->get('event_id') . '").show();');
                $this->removeClass('#js_event_item_holder_' . $this->get('event_id'), 'row_sponsored');
                $this->hide('#js_sponsor_phrase_' . $this->get('event_id'));
                $this->alert(_p('event_successfully_un_sponsored'), null, 300, 150, true);
                $this->call('$(\'.fe_sponsor_' . $this->get('event_id') . '\').hide();');
            }
        }
    }

    public function updateSponsorBackEnd()
    {
        Phpfox::isAdmin(true);
        $event_id = (int)$this->get('event_id');
        $iSponsor = (int)$this->get('iSponsor');

        if ($event_id) {
            if (Phpfox::getService('fevent.process')->sponsor($event_id, $iSponsor)) {
                $aEvent = Phpfox::getService('fevent')->getEventByID($this->get('event_id'));
                if ($iSponsor == '1') {
                    Phpfox::getService('ad.process')->addSponsor(array(
                        'module' => 'fevent',
                        'item_id' => $event_id,
                        'name' => _p('default_campaign_custom_name',
                            ['module' => _p('event'), 'name' => $aEvent['title']])
                    ));
                } else {
                    Phpfox::getService('ad.process')->deleteAdminSponsor('fevent', $event_id);
                }
            }
        }
    }


    public function approve()
    {
        if (Phpfox::getService('fevent.process')->approve($this->get('event_id'))) {
            $this->alert(_p('event_has_been_approved'), _p('event_approved'), 300, 100, true);
            $this->hide('#js_item_bar_approve_image');
            $this->hide('.js_moderation_off');
            $this->show('.js_moderation_on');
            if (!$this->get('is_inline')) {
                $this->call('window.location.reload();');
            }
        }
    }

    public function approveEvent()
    {
        // Get Params
        $event_id = (int)$this->get('event_id');
        if ($event_id) {
            Phpfox::getService('fevent.process')->approve($event_id);
        }

        $this->call("window.location = window.location;");
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sMessage = '';
        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('fevent.can_approve_events', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('fevent.process')->approve($iId);
                    $this->remove('#js_event_item_holder_' . $iId);
                }
                $this->updateCount();
                $sMessage = _p('event_s_successfully_approved');
                break;
            case 'feature':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    \Phpfox::getService('fevent.process')->feature($iId, 1);
                }
                $sMessage = _p('event_s_successfully_featured');
                break;
            case 'un-feature':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    \Phpfox::getService('fevent.process')->feature($iId, 0);
                }
                $sMessage = _p('event_s_successfully_unfeatured');
                break;
            case 'delete':
                Phpfox::getUserParam('fevent.can_delete_other_event', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('fevent.process')->delete($iId);
                    $this->slideUp('#js_event_item_holder_' . $iId);
                }
                $sMessage = _p('event_s_successfully_deleted');
                break;
        }

        $this->alert($sMessage, 'Moderation', 300, 150, true);
        $this->hide('.moderation_process');
        $this->call('setTimeout(function(){ window.location.href = window.location.href; },3000);');
    }

    public function massEmail()
    {
        $iPage = $this->get('page', 1);
        $sSubject = $this->get('subject');
        $sText = $this->get('text');

        if ($iPage == 1 && !Phpfox::getService('fevent')->canSendEmails($this->get('id'))) {
            $this->hide('#js_event_mass_mail_li');
            $this->alert(_p('you_are_unable_to_send_out_any_mass_emails_at_the_moment'));

            return;
        }

        if (empty($sSubject) || empty($sText)) {
            $this->hide('#js_event_mass_mail_li');
            $this->alert(_p('fill_in_both_a_subject_and_text_for_your_mass_email'));

            return;
        }

        $iCnt = Phpfox::getService('fevent.process')->massEmail($this->get('id'), $iPage, $this->get('subject'), $this->get('text'));

        if ($iCnt === false) {
            $this->hide('#js_event_mass_mail_li');
            $this->alert(_p('you_are_unable_to_send_a_mass_email_for_this_event'));

            return;
        }

        Phpfox::getLib('pager')->set(array('ajax' => 'fevent.massEmail', 'page' => $iPage, 'size' => 20, 'count' => $iCnt));

        if ($iPage < Phpfox::getLib('pager')->getLastPage()) {
            $this->call('$.ajaxCall(\'fevent.massEmail\', \'id=' . $this->get('id') . '&page=' . ($iPage + 1) . '&subject=' . $this->get('subject') . '&text=' . $this->get('text') . '\');');

            $this->html('#js_event_mass_mail_send', _p('email_progress_page_total', array('page' => $iPage, 'total' => Phpfox::getLib('pager')->getLastPage())));
        } else {
            if (!Phpfox::getService('fevent')->canSendEmails($this->get('id'), true)) {
                $this->hide('#js_send_email')
                    ->show('#js_send_email_fail')
                    ->html('#js_time_left', Phpfox::getTime(Phpfox::getParam('core.global_update_time'), Phpfox::getService('fevent')->getTimeLeft($this->get('id'))));
            }

            $this->hide('#js_event_mass_mail_li');
            $this->alert(_p('done'));
        }
    }

    public function removeInvite()
    {
        Phpfox::getService('fevent.process')->removeInvite($this->get('id'));
    }

    public function addFeedComment()
    {
        Phpfox::isUser(true);

        $aVals = (array)$this->get('val');

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            $this->alert(_p('user.add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

        $aEvent = Phpfox::getService('fevent')->getForEdit($aVals['callback_item_id']);

        if (!isset($aEvent['event_id'])) {
            $this->alert(_p('unable_to_find_the_event_you_are_trying_to_comment_on'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

        $sLink = Phpfox::permalink('fevent', $aEvent['event_id'], $aEvent['title']);
        $aCallback = array(
            'module' => 'fevent',
            'table_prefix' => 'fevent_',
            'link' => $sLink,
            'email_user_id' => $aEvent['user_id'],
            'subject' => _p('full_name_wrote_a_comment_on_your_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aEvent['title'])),
            'message' => _p('full_name_wrote_a_comment_on_your_event_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aEvent['title'])),
            'notification' => 'fevent_comment',
            'feed_id' => 'fevent_comment',
            'item_id' => $aEvent['event_id']
        );

        $aVals['parent_user_id'] = $aVals['callback_item_id'];

        if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->callback($aCallback)->addComment($aVals))) {
            Phpfox::getLib('database')->updateCounter('fevent', 'total_comment', 'event_id', $aEvent['event_id']);

            Phpfox::getService('feed')->callback($aCallback)->processAjax($iId);
        } else {
            $this->call('$Core.activityFeedProcess(false);');
        }
    }

    public function setDefault()
    {
        Phpfox::getService('fevent.process')->setDefault($this->get('id'));
    }

    public function toggleActiveField()
    {
        Phpfox::getUserParam('fevent.can_manage_custom_fields', true);
        if (Phpfox::getService('fevent.custom.process')->toggleActivity($this->get('id'))) {
            $this->call('$Core.custom.toggleFieldActivity(' . $this->get('id') . ')');
        }
    }

    public function deleteField()
    {
        Phpfox::getUserParam('fevent.can_manage_custom_fields', true);
        if (Phpfox::getService('fevent.custom.process')->delete($this->get('id'))) {
            $this->call('$(\'#js_field_' . $this->get('id') . '\').parents(\'li:first\').remove();');
        }
    }

    public function getCustomFields()
    {
        $iId = $this->get("id");
        $event_id = $this->get('event_id');
        $parent_id = $this->get('parent_id');

        $oCustomService = Phpfox::getService('fevent.custom');
        $aCustomFields = $oCustomService->getFieldsByCateId($iId);

        if ($aEvent = Phpfox::getService('fevent')->getForEdit($event_id)) {
            if ($aCustomDefault = Phpfox::getService('fevent.custom')->getCustomFieldsForEdit($event_id)) {
                foreach ($aCustomFields as $key => $aCustom) {
                    $iKey = $oCustomService->checkKeyCustomFields($aCustomDefault, $aCustom['field_id']);
                    if ($iKey != -1) {
                        $aCustomFields[$key] = $aCustomDefault[$iKey];
                    }
                }
            }
        }
        foreach ($aCustomFields as $key => $field) {
            if (strpos($field['value'], "\n") !== FALSE) {
                $aCustomFields[$key]['value'] = str_replace(array("\n"), '', $field['value']);
            }
        }
        Phpfox::getBlock('fevent.custom', array("aCustomFields" => $aCustomFields));
        $this->html('#ajax_custom_fields', $this->getContent(false));
        $aRequired = array();
        foreach ($aCustomFields as $iKey => $aField) {
            if ($aField['is_required'] == 1) {
                $aRequired[] = '{"field_name":"' . $aField['field_name'] . '", "phrase_name":"' . _p($aField['phrase_var_name']) . '","var_type":"' . $aField['var_type'] . '"}';
            }
        }
        $sOutJs = '[' . join(',', $aRequired) . ']';
        $this->call('$(\'#required_custom_fields\').val(\'' . $sOutJs . '\');');
    }

    public function gmap()
    {
        Phpfox::getBlock('fevent.gmap');
    }

    public function getEventsForGmap()
    {
        $sIds = $this->get('ids');

        $sIds = trim($sIds, ',');
        $aIds = array();
        $aIds = explode(',', $sIds);
        foreach ($aIds as $iKey => $sId) {
            $aIds[$iKey] = (int)$sId;
        }
        $aEvents = Phpfox::getService('fevent')->getEventsByIds($aIds);

        $sJson = json_encode($aEvents);
        $this->call('displayMarkers("' . str_replace('"', '\\"', $sJson) . '");');
    }

    public function reloadGmap()
    {
        $sLocation = $this->get('location');
        $sCity = $this->get('city');
        $sRadius = (int)$this->get('radius');

        if ($sLocation == "Location...")
            $sLocation = "";
        if ($sCity != "" && $sCity != "City...")
            $sLocation = $sLocation . " , " . $sCity;

        list($aCoordinates, $sGmapAddress) = Phpfox::getService('fevent.process')->address2coordinates($sLocation);
        $radius = 0;
        if (is_int($sRadius)) {
            $radius = $sRadius;
        }

        $sIds = $this->get('ids');

        $sIds = trim($sIds, ',');
        $aIds = array();
        $aIds = explode(',', $sIds);
        foreach ($aIds as $iKey => $sId) {
            $aIds[$iKey] = (int)$sId;
        }
        $aEvents = Phpfox::getService('fevent')->getEventsByIds($aIds);

        $sJson = json_encode($aEvents);

        $this->call('panGmapTo(' . $aCoordinates[1] . ',' . $aCoordinates[0] . ',' . $radius . ',' . $sJson . ');'); // lat, lng
        $this->call('$(\'#fevent_gmap_loading\').addClass(\'hide\');');
    }

    public function reloadGmapOne()
    {
        $sLocation = $this->get('location');
        $sCity = $this->get('city');
        $sRadius = (int)$this->get('radius');

        if ($sCity != "" && $sCity != "City...")
            $sLocation = $sLocation . "," . $sCity;
        list($aCoordinates, $sGmapAddress) = Phpfox::getService('fevent.process')->address2coordinates($sLocation);
        $radius = 0;
        if (is_int($sRadius)) {
            $radius = $sRadius;
        }
        $this->call('panGmapTo(' . $aCoordinates[1] . ',' . $aCoordinates[0] . ',' . $radius . ');'); // lat, lng
    }

    public function repeat()
    {
        $value = $this->get('value');
        $txtrepeat = $this->get('txtrepeat');
        $daterepeat = $this->get('daterepeat');

        $daterepeat_hour = $this->get('daterepeat_hour');
        $daterepeat_min = $this->get('daterepeat_min');
        $daterepeat_dur_day = $this->get('daterepeat_dur_day');
        $daterepeat_dur_hour = $this->get('daterepeat_dur_hour');

        $eventID = $this->get('eventID');

        phpfox::getBlock("fevent.repeat"
            , array("value" => $value
            , "txtrepeat" => $txtrepeat
            , "daterepeat" => $daterepeat
            , "daterepeat_hour" => $daterepeat_hour
            , "daterepeat_min" => $daterepeat_min
            , "daterepeat_dur_day" => $daterepeat_dur_day
            , "daterepeat_dur_hour" => $daterepeat_dur_hour
            , "eventID" => $eventID
            )
        );
    }

    public function glogin()
    {
        $id = $this->get('id');
        Phpfox::getBlock("fevent.glogin", array("id" => $id));
    }

    public function donerepeat()
    {
        $selrepeat = $this->get('relrepeat');
        $daterepeat = $this->get('txtdisable');
        $bIsEdit = $this->get('bIsEdit');

        // if($bIsEdit && Phpfox::getUserParam('fevent.can_edit_end_date') == false && Phpfox::getUserParam('fevent.can_edit_duration') == false){
        // 	$this->__enableBtnOnRepeatPopup();
        // 	return false;
        // }

        if ($daterepeat != "") {
            $end_on_hour = $this->get('end_on_hour');
            $end_on_min = $this->get('end_on_min');
            $duration_days = $this->get('duration_days');
            $duration_hours = $this->get('duration_hours');

            $start_month = $this->get('start_month');
            $start_day = $this->get('start_day');
            $start_year = $this->get('start_year');
            $start_hour = $this->get('start_hour');
            $start_minute = $this->get('start_minute');

            $oHelper = Phpfox::getService('fevent.helper');

            // validate input data
            $atimerepeat = explode("/", $daterepeat);
            $timerepeat = Phpfox::getLib('date')->mktime($end_on_hour, $end_on_min, 0, $atimerepeat[0], $atimerepeat[1], $atimerepeat[2]);
            $numbersOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $atimerepeat[0], $atimerepeat[2]);
            // if(!$bIsEdit)
            {
                //	FOR ADD NEW EVENT
                $start_time = Phpfox::getLib('date')->mktime($start_hour, $start_minute, 0, $start_month, $start_day, $start_year);
                if ($timerepeat < $start_time) {
                    echo " document.getElementById(\"error_end_on\").innerHTML = " . "'<- " . _p('ru_greater_st') . "'" . "; ";
                    $this->__enableBtnOnRepeatPopup();
                    return;
                }

                if ($selrepeat == 0) {
                    //	daily
                    if ($oHelper->isIntegerNumber($duration_hours) == false || ((int)$duration_hours < 1 || (int)$duration_hours > 23)) {
                        echo " document.getElementById(\"error_duration_hours\").innerHTML = " . "'<- " . _p('correct_hours') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }

                    $next_start_time = $start_time + (1 * 24 * 60 * 60);
                    if ($timerepeat < $next_start_time) {
                        echo " document.getElementById(\"error_end_on\").innerHTML = " . "'<- " . _p('correct_ru_logic_st') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                } else if ($selrepeat == 1) {
                    //	weekly
                    if (strlen($duration_days) > 0 && $oHelper->isIntegerNumber($duration_days) == false) {
                        echo " document.getElementById(\"error_duration_days\").innerHTML = " . "'<- " . _p('correct_days') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                    if (strlen($duration_hours) > 0 && $oHelper->isIntegerNumber($duration_hours) == false) {
                        echo " document.getElementById(\"error_duration_hours\").innerHTML = " . "'<- " . _p('correct_hours') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                    if ((int)$duration_days == 0 && (int)$duration_hours == 0) {
                        echo " document.getElementById(\"error_duration_days\").innerHTML = " . "'<- " . _p('correct_days') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                    if (((int)$duration_days * (int)$duration_hours) == 0) {
                        if ((int)$duration_days == 0 && ((int)$duration_hours < 1 || (int)$duration_hours > 23)) {
                            echo " document.getElementById(\"error_duration_hours\").innerHTML = " . "'<- " . _p('correct_hours') . "'" . "; ";
                            $this->__enableBtnOnRepeatPopup();
                            return;
                        }
                        if ((int)$duration_hours == 0 && ((int)$duration_days < 1 || (int)$duration_days > 6)) {
                            echo " document.getElementById(\"error_duration_days\").innerHTML = " . "'<- " . _p('correct_days') . "'" . "; ";
                            $this->__enableBtnOnRepeatPopup();
                            return;
                        }
                    } else {
                        if (((int)$duration_days < 1 || (int)$duration_days > 6)) {
                            echo " document.getElementById(\"error_duration_days\").innerHTML = " . "'<- " . _p('correct_days') . "'" . "; ";
                            $this->__enableBtnOnRepeatPopup();
                            return;
                        }
                        if (((int)$duration_hours < 1 || (int)$duration_hours > 23)) {
                            echo " document.getElementById(\"error_duration_hours\").innerHTML = " . "'<- " . _p('correct_hours') . "'" . "; ";
                            $this->__enableBtnOnRepeatPopup();
                            return;
                        }
                    }

                    $next_start_time = $start_time + (7 * 24 * 60 * 60);
                    if ($timerepeat < $next_start_time) {
                        echo " document.getElementById(\"error_end_on\").innerHTML = " . "'<- " . _p('correct_ru_logic_st') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                } else {
                    //	monthly
                    $next_start_time = $oHelper->getSameDayInNextMonth($start_day, $start_month, $start_year);
                    $numberOfDays = (int)$oHelper->daysToDate($start_day, $start_month, $start_year, $next_start_time['day'], $next_start_time['month'], $next_start_time['year']);

                    if (strlen($duration_days) > 0 && $oHelper->isIntegerNumber($duration_days) == false) {
                        echo " document.getElementById(\"error_duration_days\").innerHTML = " . "'<- " . _p('correct_days') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                    if (strlen($duration_hours) > 0 && $oHelper->isIntegerNumber($duration_hours) == false) {
                        echo " document.getElementById(\"error_duration_hours\").innerHTML = " . "'<- " . _p('correct_hours') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                    if ((int)$duration_days == 0 && (int)$duration_hours == 0) {
                        echo " document.getElementById(\"error_duration_days\").innerHTML = " . "'<- " . _p('correct_days') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                    if (((int)$duration_days * (int)$duration_hours) == 0) {
                        if ((int)$duration_days == 0 && ((int)$duration_hours < 1 || (int)$duration_hours > 23)) {
                            echo " document.getElementById(\"error_duration_hours\").innerHTML = " . "'<- " . _p('correct_hours') . "'" . "; ";
                            $this->__enableBtnOnRepeatPopup();
                            return;
                        }
                        if ((int)$duration_hours == 0 && ((int)$duration_days < 1 || (int)$duration_days > ($numberOfDays - 1))) {
                            echo " document.getElementById(\"error_duration_days\").innerHTML = " . "'<- " . _p('correct_days') . "'" . "; ";
                            $this->__enableBtnOnRepeatPopup();
                            return;
                        }
                    } else {
                        if (((int)$duration_days < 1 || (int)$duration_days > ($numberOfDays - 1))) {
                            echo " document.getElementById(\"error_duration_days\").innerHTML = " . "'<- " . _p('correct_days') . "'" . "; ";
                            $this->__enableBtnOnRepeatPopup();
                            return;
                        }
                        if (((int)$duration_hours < 1 || (int)$duration_hours > 23)) {
                            echo " document.getElementById(\"error_duration_hours\").innerHTML = " . "'<- " . _p('correct_hours') . "'" . "; ";
                            $this->__enableBtnOnRepeatPopup();
                            return;
                        }
                    }

                    $next_start_time_number = Phpfox::getLib('date')->mktime($start_hour, $start_minute, 0, $next_start_time['month'], $next_start_time['day'], $next_start_time['year']);
                    if ($timerepeat < $next_start_time_number) {
                        echo " document.getElementById(\"error_end_on\").innerHTML = " . "'<- " . _p('correct_ru_logic_st') . "'" . "; ";
                        $this->__enableBtnOnRepeatPopup();
                        return;
                    }
                }
            }
            // else {
            // 	//	FOR EDIT EVENT
            // 	if($selrepeat==0){
            // 		//	daily
            // 	} else if($selrepeat==1){
            // 		//	weekly
            // 	} else {
            // 		//	monthly
            // 	}
            // }


            if ($selrepeat == 0)
                $chuoi = _p('daily');
            else if ($selrepeat == 1)
                $chuoi = _p('weekly');
            else {
                $chuoi = _p('monthly');
            }
            $until = "";
            if ($daterepeat != "")
                $until = ", " . _p('until') . " " . $daterepeat;

            $chuoi .= $until;

            //	update hidden data
            echo "$('#daterepeat').val('" . $daterepeat . "');";
            echo "$('#daterepeat_hour').val('" . $end_on_hour . "');";
            echo "$('#daterepeat_min').val('" . $end_on_min . "');";
            echo "$('#daterepeat_dur_day').val('" . $duration_days . "');";
            echo "$('#daterepeat_dur_hour').val('" . $duration_hours . "');";

            echo "$('#txtrepeat').val('" . $selrepeat . "');";
            echo "$('#chooserepeat').html(': " . $chuoi . "');";
            echo "$('#editrepeat').html('" . _p('edit') . "');";

            if (!$bIsEdit) {
                echo "$('#extra_info_date').css('display','none');";
            } else {
                echo "$('#js_event_add_end_time').css('display','none');";
            }

            $this->__enableBtnOnRepeatPopup();

            echo ' tb_remove(); ';
        } else {
            //	Display error
            // echo "$('#cbrepeat').removeAttr('checked');";
            echo " document.getElementById(\"error_end_on\").innerHTML = " . "'<- " . _p('select_repeat_until_date') . "'" . "; ";

            $this->__enableBtnOnRepeatPopup();
        }
    }

    private function __enableBtnOnRepeatPopup()
    {
        echo ' $(\'#btnDone\').attr(\'disabled\', false); ';
        echo ' $(\'#btnCancel\').attr(\'disabled\', false); ';
    }

    public function showEditYourCurrentLocationBlock()
    {
        Phpfox::getBlock('fevent.editlocation');
    }

    public function reloadGmapLocationBlock()
    {
        $sLocation = $this->get('location');
        $sCity = $this->get('city');

        if ($sLocation == "Location...") {
            $sLocation = "";
        }
        if ($sCity != "" && $sCity != "City...") {
            $sLocation = $sLocation . " , " . $sCity;
        }

        list($aCoordinates, $sGmapAddress) = Phpfox::getService('fevent.process')->address2coordinates($sLocation);

        $this->call('ynfeIndexPage.panGmapTo(' . $aCoordinates[1] . ',' . $aCoordinates[0] . ');'); // lat, lng
    }

    public function loadAjaxMapView()
    {
        $typeEventMap = $this->get('typeEventMap');
        $bIsPage = false;
        $aEventLocation = array();
        $pageID = -1;
        $iLimit = 3;
        if ($typeEventMap == 'upcoming') {
            list(, $aEventLocation) = Phpfox::getService('fevent')->getOnHomepageByType('upcoming', $iLimit, $bIsPage, false, false, $pageID);
        } else if ($typeEventMap == 'ongoing') {
            list(, $aEventLocation) = Phpfox::getService('fevent')->getOnHomepageByType('ongoing', $iLimit, $bIsPage, false, false, $pageID);
        }

        $aEventLocation = Phpfox::getService('fevent.process')->prePareDataForMap($aEventLocation);
        echo json_encode(array(
            'status' => 'SUCCESS',
            'sCorePath' => Phpfox::getParam('core.path_actual'),
            'data' => $aEventLocation
        ));
    }

    public function loadAjaxDetailMapView()
    {
        $iEventId = $this->get('iEventId');
        $aEventLocation = Phpfox::getService('fevent')->getMapEventForDeatail($iEventId);
        $aEventLocation = Phpfox::getService('fevent.process')->prePareDataForMap($aEventLocation);
        echo json_encode(array(
            'status' => 'SUCCESS',
            'sCorePath' => Phpfox::getParam('core.path_actual'),
            'data' => $aEventLocation
        ));
    }

    public function subscribeEvent()
    {
        $email = $this->get('email');
        $categories = $this->get('categories');
        $location_lat = $this->get('location_lat');
        $location_lng = $this->get('location_lng');
        $address = $this->get('address');
        $radius = $this->get('radius');

        if ($email == '') {
            $this->alert(_p('fevent.you_have_to_fill_email_field'));
            return false;
        }

        if ($radius > 0 && ($location_lat == '' && $location_lng == '' && $address == '')) {
            $this->alert(_p('fevent.you_have_to_input_location'));
            return false;
        }

        if ($radius == '' && ($location_lat != '' && $location_lng != '' && $address != '')) {
            $this->alert(_p('fevent.you_have_to_input_radius'));
            return false;
        }


        $aData = array(
            'email' => $email,
            'categories' => $categories,
            'location_lat' => $location_lat,
            'location_lng' => $location_lng,
            'radius' => $radius
        );

        if (Phpfox::getService('fevent.process')->subscribeEvent($aData)) {
            $this->alert(_p('fevent.subscribe_successfully'));
        }
    }

    public function deleteOption()
    {
        $id = $this->get('id');

        if (Phpfox::getService('fevent.custom.process')->deleteOption($id)) {
            $this->remove('#js_current_value_' . $id);
        } else {

        }
    }

    public function gnotif()
    {
        if ($this->get('type') == "success") {
            $this->alert(_p('event_has_been_successfully_added_to_your_google_calendar'));
        }
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'fevent_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove();
    }

    public function updateActivity()
    {
        if (Phpfox::getService('fevent.category.process')->updateActivity($this->get('id'), $this->get('active'), $this->get('sub'))) {

        }
    }

    public function copyRecurringImage()
    {
        $iEditId = $this->get('event_id');
        $sIds = $this->get('sIds');
        $sConfirm = $this->get('confirm_type');
        if (!in_array($sConfirm, ['all_events_uppercase', 'following_events']) || empty($sIds)) {
            return false;
        }
        $aEvent = Phpfox::getService('fevent')->getForEdit($iEditId);
        if (!$aEvent || $aEvent['event_type'] != 'repeat') {
            return false;
        }
        $aImages = Phpfox::getService('fevent')->getImagesByIds($sIds);
        $aRecurringImages = [];
        foreach ($aImages as $aImage) {
            $aRecurringImages[] = $aImage['image_path'];
        }
        if ((int)$aEvent['org_event_id'] > 0) {
            // edit repeat type, check affecting to other brother events
            switch ($sConfirm) {
                case 'all_events_uppercase':
                    // get all brother events
                    $aEventId = Phpfox::getService('fevent')->getBrotherEventByEventId($iEditId, $aEvent['org_event_id']);
                    foreach ($aEventId as $key => $value) {
                        Phpfox::getService('fevent.process')->copyRecurringImage($value['event_id'], ['recurring_image' => $aRecurringImages], true);
                    }
                    break;

                case 'following_events':
                    // get younger brother events
                    $aConds = array();
                    $aConds[] = ' AND e.event_id > ' . (int)$iEditId;
                    $aEventId = Phpfox::getService('fevent')->getBrotherEventByEventId($iEditId, $aEvent['org_event_id'], $aConds);
                    foreach ($aEventId as $key => $value) {
                        Phpfox::getService('fevent.process')->copyRecurringImage($value['event_id'], ['recurring_image' => $aRecurringImages], true);
                    }
                    break;
            }
        }
    }

    public function toggleUploadSection()
    {
        $bShowUpload = $this->get('show_upload');
        $isCreating = $this->get('is_creating');
        $iId = $this->get('id');
        if (!$iId) {
            return false;
        }
        $item = Phpfox::getService('fevent')->getForEdit($iId);
        $iTotalImage = Phpfox::getService('fevent')->countImages($iId);
        $iTotalImageLimit = Phpfox::getUserParam('fevent.max_upload_image_event');
        if ($bShowUpload) {
            $this->template()->
            assign([
                'aForms' => $item,
                'iTotalImage' => $iTotalImage,
                'iTotalImageLimit' => $iTotalImageLimit,
                'iRemainUpload' => $iTotalImageLimit - $iTotalImage,
                'iEventId' => $iId,
                'isCreating' => $isCreating,
                'aParamsUpload' => array('id' => $iId),
                'iMaxFileSize' => (Phpfox::getUserParam('fevent.max_upload_size_event') === 0 ? '' : (Phpfox::getUserParam('fevent.max_upload_size_event'))),
            ])->getTemplate('fevent.block.upload-photo');
            $this->call('$("#js-p-fevent-photos-container").html("' . $this->getContent() . '");');
            if ($isCreating) {
                $this->call('ynfeAddPage.toggleCreatingUploadMorePhotos();');
            }
            $this->call('$Core.loadInit();');
        } else {
            $this->template()->
            assign([
                'isCreating' => $isCreating,
                'iTotalImage' => $iTotalImage,
                'iTotalImageLimit' => $iTotalImageLimit,
                'iRemainUpload' => $iTotalImageLimit - $iTotalImage,
            ]);
            Phpfox::getBlock('fevent.photo', [
                'aEvent' => $item
            ]);
            $this->call('$(\'#js-p-fevent-photos-container\').html(\'' . $this->getContent() . '\');');
            if ($isCreating) {
                $this->call('ynfeAddPage.toggleCreatingBackToManagePhotos();');
            }
            $this->call('$Core.loadInit();');
        }

        return true;
    }

    public function loadCalendar()
    {
        $from = $this->get('from');
        $to = $this->get('to');
        $browser_timezone = $this->get('browser_timezone');

        $my = $this->get('my', 0);
        $attending = $this->get('attending', 0);
        $maybeAttending = $this->get('maybe_attending', 0);
        $other = $this->get('other', 0);
        $userId = Phpfox::getUserId();

        $aConds = array();
        if ($my) {
            $aConds[] = "(fe.user_id = $userId)";
        }
        if ($attending) {
            $aConds[] = "(fei.invited_user_id = $userId AND fei.rsvp_id = 1)";
        }
        if ($maybeAttending) {
            $aConds[] = "(fei.invited_user_id = $userId AND fei.rsvp_id = 2)";
        }
        if ($other) {
            $aConds[] = "((fe.user_id <> $userId) AND (fei.invited_user_id <> $userId OR (fei.invited_user_id = $userId AND fei.rsvp_id NOT IN (1,2))))";
        }
        $conds = implode(' OR ', $aConds);

        $from = Phpfox::getService('fevent.helper')->correctTimeStampReverse($from, $browser_timezone);
        $to = Phpfox::getService('fevent.helper')->correctTimeStampReverse($to, $browser_timezone);
        $events = Phpfox::getService('fevent')->getJsEventsForCalendar($from, $to, $conds);
        $oHelper = Phpfox::getService('fevent.helper');

        $result = array();
        foreach ($events as $event) {
            $d_type = $oHelper->getTimeLineStatus($event['start_time'], $event['end_time']);
            $isBirthday = !empty($event['birthday_display_name']);
            $result[] = array(
                'id' => $isBirthday ? $event['user_id'] : $event['event_id'],
                'title' => $isBirthday ? html_entity_decode($event['birthday_display_name']) : htmlentities($event['title']),
                'url' => $isBirthday ? Phpfox::getLib('url')->makeUrl('profile', [$event['user_name']]) : Phpfox::getLib('url')->permalink('fevent', $event['event_id'], $event['title']),
                'class' => $d_type,
                'color_class' => $this->_getJsCalendarClass($d_type),
                'start' => Phpfox::getService('fevent.helper')->correctTimeStamp($event['start_time'], $browser_timezone) * 1000, // Milliseconds
                'end' => Phpfox::getService('fevent.helper')->correctTimeStamp($event['end_time'], $browser_timezone) * 1000,
                'is_birthday' => $isBirthday
            );
        }
        echo json_encode(array(
            'success' => 1,
            'result' => $result
        ));
        exit;
    }

    public function _getJsCalendarClass($d_type)
    {
        switch ($d_type) {
            case 'upcoming':
                return 'bg-primary';
            case 'ongoing':
                return 'bg-success';
            case 'past':
                return 'bg-gray';
        }
    }

    public function showGuestList()
    {
        $sTab = $this->get('tab');
        $iEventId = $this->get('event_id');
        Phpfox::getBlock('fevent.guest-list',[
            'tab' => $sTab,
            'iEventId' => $iEventId,
            'statistic' => $this->get('statistic')
        ]);
    }
}