<?php
namespace Apps\P_AdvEvent\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Template;
use Phpfox_Url;

class Callback extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent');
    }

    public function mobileMenu()
    {
        return array(
            'phrase' => _p('fevent.events'),
            'link' => Phpfox::getLib('url')->makeUrl('fevent'),
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_events.png'))
        );
    }

    public function getAjaxCommentVar()
    {
        return 'fevent.can_post_comment_on_event';
    }

    public function getDashboardActivity()
    {
        if(!Phpfox::getUserParam('fevent.can_access_event'))
        {
            return false;
        }

        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('advanced_events') => $aUser['activity_fevent']
        );
    }

    public function getCommentItem($iId)
    {
        $aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from(Phpfox::getT('fevent_feed_comment'))
            ->where('feed_comment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            Phpfox_Error::set(_p('fevent.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }

    public function getFeedDetails($iItemId)
    {
        return array(
            'module' => 'fevent',
            'table_prefix' => 'fevent_',
            'item_id' => $iItemId
        );
    }

    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.user_id, e.event_id, e.title, u.full_name, u.gender')
            ->from(Phpfox::getT('fevent_feed_comment'), 'fc')
            ->join(Phpfox::getT('fevent'), 'e', 'e.event_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('fevent_feed_comment', 'total_comment', 'feed_comment_id', $aRow['feed_comment_id']);
        }

        // Send the user an email
        $sLink = Phpfox::getLib('url')->permalink(array('fevent', 'comment-id' => $aRow['feed_comment_id']), $aRow['event_id'], $aRow['title']);
        $sItemLink = Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aRow['user_id'],
                'item_id' => $aRow['feed_comment_id'],
                'owner_subject' => _p('fevent.full_name_commented_on_a_comment_posted_on_the_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])),
                'owner_message' => _p('fevent.full_name_commented_on_one_of_your_comments_you_posted_on_the_event', array('full_name' => Phpfox::getUserBy('full_name'), 'item_link' => $sItemLink, 'title' => $aRow['title'], 'link' => $sLink)),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'fevent_comment_feed',
                'mass_id' => 'fevent',
                'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('fevent.full_name_commented_on_one_of_gender_event_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) : _p('fevent.full_name_commented_on_one_of_row_full_name_s_event_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name']))),
                'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('fevent.full_name_commented_on_one_of_gender_own_comments_on_the_event', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'item_link' => $sItemLink, 'title' => $aRow['title'], 'link' => $sLink)) : _p('fevent.full_name_commented_on_one_of_row_full_name_s', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name'], 'item_link' => $sItemLink, 'title' => $aRow['title'], 'link' => $sLink)))
            )
        );
    }

    public function getNotificationComment_Feed($aNotification)
    {
        return $this->getCommentNotification($aNotification);
    }

    public function uploadVideo($aVals)
    {
        return array(
            'module' => 'fevent',
            'item_id' => $aVals['callback_item_id']
        );
    }

    public function convertVideo($aVideo)
    {
        return array(
            'module' => 'fevent',
            'item_id' => $aVideo['item_id'],
            'table_prefix' => 'fevent_'
        );
    }

    public function getCommentNotification($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.event_id, e.title')
            ->from(Phpfox::getT('fevent_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('fevent'), 'e', 'e.event_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('fevent.users_commented_on_span_class_drop_data_user_row_full_name_s_span_comment_on_the_event_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
            } else {
                $sPhrase = _p('fevent.users_commented_on_gender_own_comment_on_the_event_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('fevent.users_commented_on_one_of_your_comments_on_the_event_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('fevent.users_commented_on_one_of_span_class_drop_data_user_row_full_name_s_span_comments_on_the_event_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink(array('fevent', 'comment-id' => $aRow['feed_comment_id']), $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationComment($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.event_id, e.title')
            ->from(Phpfox::getT('fevent_feed_comment'), 'fc')
            ->join(Phpfox::getT('fevent'), 'e', 'e.event_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!$aRow) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('fevent.users_commented_on_span_class_drop_data_user_row_full_name_s_span_event_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
            } else {
                $sPhrase = _p('fevent.users_commented_on_gender_own_event_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('fevent.users_commented_on_your_event_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('fevent.users_commented_on_span_class_drop_data_user_row_full_name_s_span_event_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink(array('fevent', 'comment-id' => $aRow['feed_comment_id']), $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationComment_Like($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.event_id, e.title')
            ->from(Phpfox::getT('fevent_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('fevent'), 'e', 'e.event_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('fevent.users_liked_span_class_drop_data_user_row_full_name_s_span_comment_on_the_event_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
            } else {
                $sPhrase = _p('fevent.users_liked_gender_own_comment_on_the_event_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('fevent.users_liked_one_of_your_comments_on_the_event_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('fevent.users_liked_one_on_span_class_drop_data_user_row_full_name_s_span_comments_on_the_event_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink(array('fevent', 'comment-id' => $aRow['feed_comment_id']), $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * Enables a sponsor after being paid for or admin approved
     * @param <type> $iId
     * @param <type> $sSection
     */
    public function enableSponsor($aParams)
    {
        return Phpfox::getService('fevent.process')->sponsor((int)$aParams['item_id'], 1);
    }

    public function updateCommentText($aVals, $sText)
    {
        $aEvent = $this->database()->select('m.event_id, m.title, m.title_url, u.full_name, u.user_id, u.user_name')
            ->from($this->_sTable, 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where('m.event_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_event', $aVals['item_id'], serialize(array('content' => $sText, 'title' => $aEvent['title'])), $aVals['comment_id']) : null);
    }

    public function getItemName($iId, $sName)
    {
        return _p('fevent.a_href_link_on_name_s_event_a', array('link' => Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)), 'name' => $sName));
    }

    public function getLink($aParams)
    {
        // get the owner of this song
        $aEvent = $this->database()->select('e.event_id, e.title')
            ->from(Phpfox::getT('fevent'), 'e')
            ->where('e.event_id = ' . (int)$aParams['item_id'])
            ->execute('getSlaveRow');
        if (empty($aEvent)) {
            return false;
        }
        //return Phpfox::getLib('url')->makeUrl('fevent.view.' . $aEvent['title_url'] );
        return Phpfox::permalink('fevent', $aEvent['event_id'], $aEvent['title']);
    }

    public function getCommentNewsFeed($aRow)
    {
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');

        if (!Phpfox::getLib('parse.format')->isSerialized($aRow['content'])) {
            return false;
        }

        $aParts = unserialize($aRow['content']);
        $aRow['text'] = _p('fevent.a_href_user_link_user_name_a_added_a_comment_on_the_event_a_href_title_link_title_a', array(
                'user_name' => $aRow['owner_full_name'],
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'title_link' => $aRow['link'],
                'title' => Phpfox::getService('feed')->shortenTitle($aParts['title'])
            )
        );

        $aRow['text'] .= Phpfox::getService('feed')->quote($aParts['content']);

        return $aRow;
    }

    public function getFeedRedirectFeedLike($iId, $iChild)
    {
        return $this->getFeedRedirect($iChild);
    }

    public function getFeedRedirect($iId, $iChild = 0)
    {
        $aEvent = $this->database()->select('m.event_id, m.title')
            ->from($this->_sTable, 'm')
            ->where('m.event_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aEvent['event_id'])) {
            return false;
        }

        return Phpfox::permalink('fevent', $aEvent['event_id'], $aEvent['title']);
    }

    public function deleteComment($iId)
    {
        $this->database()->updateCounter('fevent', 'total_comment', 'event_id', $iId, true);
    }

    public function getReportRedirect($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    public function getNewsFeed($aRow)
    {
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_callback_getnewsfeed_start')) {
            eval($sPlugin);
        }
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');

        $aRow['text'] = _p('fevent.owner_full_name_added_a_new_event_title', array(
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'owner_full_name' => $aRow['owner_full_name'],
                'title_link' => $aRow['link'],
                'title' => Phpfox::getService('feed')->shortenTitle($aRow['content'])
            )
        );

        $aRow['icon'] = 'module/event.png';
        $aRow['enable_like'] = true;

        return $aRow;
    }

    public function groupMenu($sGroupUrl, $iGroupId)
    {
        if (!Phpfox::getService('group')->hasAccess($iGroupId, 'can_use_event')) {
            return false;
        }

        return array(
            _p('fevent.events') => array(
                'active' => 'fevent',
                'url' => Phpfox::getLib('url')->makeUrl('group', array($sGroupUrl, 'fevent')
                )
            )
        );
    }

    public function deleteGroup($iId)
    {
        $aEvents = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('module_id = \'group\' AND item_id = ' . (int)$iId)
            ->execute('getRows');

        foreach ($aEvents as $aEvent) {
            Phpfox::getService('fevent.process')->delete($aEvent['event_id'], $aEvent);
        }

        return true;
    }

    public function getDashboardLinks()
    {
        return array(
            'submit' => array(
                'phrase' => _p('fevent.create_an_event'),
                'link' => 'fevent.add',
                'image' => 'misc/calendar_add.png'
            ),
            'edit' => array(
                'phrase' => _p('fevent.manage_events'),
                'link' => 'fevent.view_my',
                'image' => 'misc/calendar_edit.png'
            )
        );
    }

    public function getBlockDetailsProfile()
    {
        return array(
            'title' => _p('fevent.events')
        );
    }

    public function hideBlockProfile($sType)
    {
        return array(
            'table' => 'user_design_order'
        );
    }

    /**
     * Action to take when user cancelled their account
     * @param int $iUser
     */
    public function onDeleteUser($iUser)
    {
        $aEvents = $this->database()
            ->select('event_id')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)$iUser)
            ->execute('getSlaveRows');

        foreach ($aEvents as $aEvent) {
            Phpfox::getService('fevent.process')->delete($aEvent['event_id']);
        }
    }

    public function getGroupPosting()
    {
        return array(
            _p('fevent.can_create_event') => 'can_create_event'
        );
    }

    public function getGroupAccess()
    {
        return array(
            _p('fevent.view_events') => 'can_use_event'
        );
    }

    public function getNotificationFeedapproved($aRow)
    {
        return array(
            'message' => _p('fevent.your_event_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
            'link' => Phpfox::getLib('url')->makeUrl('fevent', array('redirect' => $aRow['item_id'])),
            'path' => 'event.url_image',
            'suffix' => '_120'
        );
    }

    public function getGlobalPrivacySettings()
    {
        return array(
            'fevent.display_on_profile' => array(
                'phrase' => _p('fevent.advanced_events'),
                'icon_class' => 'ico ico-calendar-check-o',
                'default' => '0'
            )
        );
    }

    public function pendingApproval()
    {
        return array(
            'phrase' => _p('fevent.events'),
            'value' => Phpfox::getService('fevent')->getPendingTotal(),
            'link' => Phpfox::getLib('url')->makeUrl('fevent', array('view' => 'pending'))
        );
    }

    public function getUserCountFieldInvite()
    {
        return 'fevent_invite';
    }

    public function getNotificationFeedinvite($aRow)
    {
        return array(
            'message' => _p('fevent.full_name_invited_you_to_an_event', array(
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                    'full_name' => $aRow['full_name']
                )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('fevent', array('redirect' => $aRow['item_id']))
        );
    }

    public function getRequestLink()
    {
        if (!Phpfox::getParam('request.display_request_box_on_empty') && !Phpfox::getUserBy('fevent_invite')) {
            return null;
        }

        return '<li><a href="' . Phpfox::getLib('url')->makeUrl('fevent', array('view' => 'invitation')) . '"' . (!Phpfox::getUserBy('fevent_invite') ? ' onclick="alert(\'' . _p('fevent.no_event_invites') . '\'); return false;"' : '') . '><img src="' . Phpfox::getLib('template')->getStyle('image', 'module/event.png') . '" class="v_middle" /> ' . _p('fevent.event_invites_total', array('total' => Phpfox::getUserBy('fevent_invite'))) . '</span></a></li>';
    }

    public function reparserList()
    {
        return array(
            'name' => _p('fevent.event_text'),
            'table' => 'fevent_text',
            'original' => 'description',
            'parsed' => 'description_parsed',
            'item_field' => 'event_id'
        );
    }

    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array(
            'phrase' => _p('fevent.events'),
            'value' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('fevent'))
                ->where('view_id = 0 AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        );
    }

    /**
     * @param int $iId video_id
     * @return array in the format:
     * array(
     *    'title' => 'item title',            <-- required
     *  'link'  => 'makeUrl()'ed link',            <-- required
     *  'paypal_msg' => 'message for paypal'        <-- required
     *  'item_id' => int                <-- required
     *  'user_id;   => owner's user id            <-- required
     *    'error' => 'phrase if item doesnt exit'        <-- optional
     *    'extra' => 'description'            <-- optional
     *    'image' => 'path to an image',            <-- optional
     *    'image_dir' => 'photo.url_photo|...        <-- optional (required if image)
     * )
     */
    public function getToSponsorInfo($iId)
    {
        // check that this user has access to this group
        $aEvent = $this->database()->select('e.user_id, e.event_id as item_id, e.title, e.privacy, e.location, e.start_time, e.end_time, e.image_path as image, e.server_id,e.user_id')
            ->from($this->_sTable, 'e')
            ->where('e.event_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (empty($aEvent)) {
            return array('error' => _p('fevent.sponsor_error_not_found'));
        }

        if ($aEvent['privacy'] > 0) {
            return array('error' => _p('fevent.sponsor_error_privacy'));
        }

        $aEvent['title'] = _p('fevent.sponsor_title', array('sEventTitle' => $aEvent['title']));
        $aEvent['paypal_msg'] = _p('fevent.sponsor_paypal_message', array('sEventTitle' => $aEvent['title']));
        $aEvent['link'] = Phpfox::permalink('fevent', $aEvent['item_id'], $aEvent['title']);

        //TODO: replace format time in this line with core format
        $aEvent['extra'] = '<b>' . _p('fevent.date') . '</b> ' . Phpfox::getTime('l, F j, Y g:i a', $aEvent['start_time']) . ' - ';

        if (date('dmy', $aEvent['start_time']) === date('dmy', $aEvent['end_time'])) {
            $aEvent['extra'] .= Phpfox::getTime('g:i a', $aEvent['end_time']);
        } else {
            $aEvent['extra'] .= Phpfox::getTime('l, F j, Y g:i a', $aEvent['end_time']);
        }

        if (isset($aEvent['image']) && $aEvent['image'] != '') {
            $aEvent['image_dir'] = 'event.url_image';
            $aEvent['image'] = sprintf($aEvent['image'], '_200');
        }
        $aEvent = array_merge($aEvent, [
            'redirect_completed' => 'fevent',
            'message_completed' => _p('purchase_fevent_sponsor_completed'),
            'redirect_pending_approval' => 'fevent',
            'message_pending_approval' => _p('purchase_fevent_sponsor_pending_approval')
        ]);
        return $aEvent;
    }

    public function getNewsFeedFeedLike($aRow)
    {
        if ($aRow['owner_user_id'] == $aRow['viewer_user_id']) {
            $aRow['text'] = _p('fevent.a_href_user_link_full_name_a_liked_their_own_a_href_link_event_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
                    'link' => $aRow['link']
                )
            );
        } else {
            $aRow['text'] = _p('fevent.a_href_user_link_full_name_a_liked_a_href_view_user_link_view_full_name_a_s_a_href_link_event_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
                    'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
                    'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
                    'link' => $aRow['link']
                )
            );
        }

        $aRow['icon'] = 'misc/thumb_up.png';

        return $aRow;
    }

    public function getNotificationFeednotifylike($aRow)
    {
        return array(
            'message' => _p('fevent.a_href_user_link_full_name_a_liked_your_a_href_link_event_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                    'link' => Phpfox::getLib('url')->makeUrl('fevent', array('redirect' => $aRow['item_id']))
                )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('fevent', array('redirect' => $aRow['item_id'])),
            'path' => 'event.url_image',
            'suffix' => '_120'
        );
    }

    public function sendLikeEmail($iItemId)
    {
        return _p('fevent.a_href_user_link_full_name_a_liked_your_a_href_link_event_a', array(
                'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
                'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
                'link' => Phpfox::getLib('url')->makeUrl('fevent', array('redirect' => $iItemId))
            )
        );
    }

    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    public function getSqlTitleField()
    {
        return array(
            'table' => 'fevent',
            'field' => 'title'
        );
    }

    public function updateCounterList()
    {
        $aList = [];

        $aList[] = array(
            'name' => _p('fevent.event_invite_count'),
            'id' => 'event-invite-count'
        );

        return $aList;
    }

    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        if ($iId == 'event-invite-count') {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, COUNT(gi.invite_id) AS total_invites')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('fevent_invite'), 'gi', 'gi.invited_user_id = u.user_id')
                ->group('u.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $this->database()->update(Phpfox::getT('user_count'), array('fevent_invite' => $aRow['total_invites']), 'user_id = ' . (int)$aRow['user_id']);
            }

            return $iCnt;
        }
    }

    public function getActivityFeedComment($aItem)
    {
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'fevent_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('fc.*, e.event_id, e.title')
            ->from(Phpfox::getT('fevent_feed_comment'), 'fc')
            ->join(Phpfox::getT('fevent'), 'e', 'e.event_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sLink = Phpfox::getLib('url')->permalink(array('fevent', 'comment-id' => $aRow['feed_comment_id']), $aRow['event_id'], $aRow['title']);

        $aReturn = array(
            'no_share' => true,
            'feed_status' => $aRow['content'],
            'feed_link' => $sLink,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/advevent.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'fevent',
            'like_type_id' => 'fevent_comment',
            'parent_user_id' => 0
        );

        return $aReturn;
    }

    public function addLikeComment($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, e.event_id, e.title')
            ->from(Phpfox::getT('fevent_feed_comment'), 'fc')
            ->join(Phpfox::getT('fevent'), 'e', 'e.event_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'fevent_comment\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'fevent_feed_comment', 'feed_comment_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::getLib('url')->permalink(array('fevent', 'comment-id' => $aRow['feed_comment_id']), $aRow['event_id'], $aRow['title']);
            $sItemLink = Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(_p('fevent.full_name_liked_a_comment_you_posted_on_the_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
                ->message(_p('fevent.full_name_liked_your_comment_message_event', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'content' => Phpfox::getLib('parse.output')->shorten($aRow['content'], 50, '...'), 'item_link' => $sItemLink, 'title' => $aRow['title'])))
                ->send();

            Phpfox::getService('notification.process')->add('fevent_comment_like', $aRow['feed_comment_id'], $aRow['user_id']);
        }
    }

    public function deleteLikeComment($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'fevent_comment\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'fevent_feed_comment', 'feed_comment_id = ' . (int)$iItemId);
    }

    public function addPhoto($iId)
    {
        return array(
            'module' => 'fevent',
            'item_id' => $iId,
            'table_prefix' => 'fevent_'
        );
    }

    public function addLink($aVals)
    {
        return array(
            'module' => 'fevent',
            'item_id' => $aVals['callback_item_id'],
            'table_prefix' => 'fevent_'
        );
    }

    public function getFeedDisplay($iEvent)
    {
        return array(
            'module' => 'fevent',
            'table_prefix' => 'fevent_',
            'ajax_request' => 'fevent.addFeedComment',
            'item_id' => $iEvent
        );
    }

    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('event_id, title, user_id')
            ->from(Phpfox::getT('fevent'))
            ->where('event_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'fevent\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'fevent', 'event_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('fevent', $aRow['event_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(_p('fevent.full_name_liked_your_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
                ->message(_p('fevent.full_name_liked_your_event_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
                ->send();

            Phpfox::getService('notification.process')->add('fevent_like', $aRow['event_id'], $aRow['user_id']);
        }
    }

    public function deleteLike($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'fevent\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'fevent', 'event_id = ' . (int)$iItemId);
    }

    public function getNotificationLike($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('fevent'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = _p('fevent.users_liked_gender_own_event_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('fevent.users_liked_your_event_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('fevent.users_liked_span_class_drop_data_user_row_full_name_s_span_event_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationRemindchangetime($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('fevent'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('fevent.the_event_title_has_changed_it_s_time', array('title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationRemindchangelocation($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('fevent'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('fevent.the_event_title_has_changed_it_s_location', array('title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationNotify($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name, e.start_time')
            ->from(Phpfox::getT('fevent'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $startTime = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['start_time']);

        $sPhrase = _p('reminder_for_event_title_start_time_which_you_are_attending_maybe_attending', array('title' => $sTitle, 'start_time' => $startTime));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function canShareItemOnFeed()
    {
    }

    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = e.user_id');
        }
        $sSelect = 'e.*, et.description_parsed';
        if (Phpfox::isModule('like')) {
            $sSelect .= ', l.like_id AS is_liked';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'fevent\' AND l.item_id = e.event_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select($sSelect .', fi.rsvp_id, fi.user_id AS inviter_id, fi.invited_user_id AS invitee_id')
            ->from(Phpfox::getT('fevent'), 'e')
            ->leftJoin(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')
            ->leftJoin(Phpfox::getT('fevent_invite'), 'fi', 'fi.event_id = e.event_id AND fi.invited_user_id = '. Phpfox::getUserId())
            ->where('e.event_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $feedId = $aItem['feed_id'];

        if ($bIsChildItem) {
            $aItem = array_merge($aRow, ['feed_id' => $feedId]);
        }

        if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null,
                        'fevent.view_browse_events'))
                || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && Phpfox::isModule('pages') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'],
                        'fevent.view_browse_events')))
            || ($aRow['module_id'] && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'],
                    'canShareOnMainFeed') && !Phpfox::callback($aRow['module_id'] . '.canShareOnMainFeed',
                    $aRow['item_id'], 'fevent.view_browse_events', $bIsChildItem))
        ) {
            return false;
        }

        $aRow['is_on_feed'] = true;
        $aRow = array_merge($aRow, [
            'is_on_feed' => true,
            'is_child_item' => $bIsChildItem
        ]);
        $aRows = [$aRow];
        Phpfox::getService('fevent.browse')->processRows($aRows);

        $aReturn = array(
            'feed_title' => '',
            'feed_info' => _p('created_an_event'),
            'feed_link' => Phpfox::permalink('fevent', $aRow['event_id'], $aRow['title']),
            'feed_content' => '',
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/advevent.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'enable_like' => true,
            'like_type_id' => 'fevent',
            'total_comment' => $aRow['total_comment'],
            'custom_data_cache' => $aRow,
            'load_block' => 'fevent.feed-event'
        );

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['item_id'])
                ->execute('getSlaveRow');
            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        \Phpfox_Component::setPublicParam('custom_param_advanced_event_' . $aItem['feed_id'], $aRow);

        (($sPlugin = Phpfox_Plugin::get('fevent.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

        return $aReturn;
    }

    public function getActivityFeedAttendevent($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = e.user_id');
        }
        $sSelect = 'e.*, et.description_parsed';
        if (Phpfox::isModule('like')) {
            $sSelect .= ', l.like_id AS is_liked';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'fevent\' AND l.item_id = e.event_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select($sSelect)
            ->from(Phpfox::getT('fevent'), 'e')
            ->leftJoin(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')
            ->where('e.event_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        if (!defined('PHPFOX_IS_CAR_VIEW')
            && (!Phpfox::isModule('directory'))
            && (!Phpfox::hasCallback($aRow['module_id'], '.canShareOnMainFeed') || !Phpfox::callback('directory.canShareOnMainFeed', $aRow['item_id']))) {
            return false;
        }
        if (!Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aRow['user_id'])) {
            return false;
        }

        if ($bIsChildItem) {
            $aItem = $aRow;
        }

        if (((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'fevent.view_browse_events'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'fevent.view_browse_events')))
        ) {
            return false;
        }
        $aRow['is_on_feed'] = true;
        $aRows = [$aRow];
        Phpfox::getService('fevent.browse')->processRows($aRows);
        $sContent = Phpfox_Template::instance()->assign(['aEvent' => $aRows[0]])->getTemplate('fevent.block.feed-event', true);
        $aReturn = array(
            'feed_title' => '',
            'feed_info' => _p('attend_event'),
            'feed_link' => Phpfox::permalink('fevent', $aRow['event_id'], $aRow['title']),
            'feed_content' => '',
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/advevent.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'enable_like' => true,
            'like_type_id' => 'fevent',
            'total_comment' => $aRow['total_comment'],
            'feed_custom_html' => $sContent
        );

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['item_id'])
                ->execute('getSlaveRow');
            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        (($sPlugin = Phpfox_Plugin::get('fevent.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

        return $aReturn;
    }

    public function getNotificationApproved($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('fevent'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sPhrase = _p('fevent.your_event_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
            // 'no_profile_image' => true
        );
    }

    public function getNotificationAdmins($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('fevent'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sPhrase = _p('fevent.n_admins', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
            //'no_profile_image' => true
        );
    }

    public function getNotificationRepeattonormalwarning($aNotification)
    {
        $sPhrase = _p('fevent.n_repeattonormal');

        return array(
            'link' => Phpfox::getLib('url')->makeUrl('fevent', array('view' => 'my')),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
            //'no_profile_image' => true
        );
    }

    public function getNotificationInvite($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.full_name')
            ->from(Phpfox::getT('fevent'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sPhrase = _p('fevent.users_invited_you_to_the_event_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getProfileMenu($aUser)
    {
        if (!Phpfox::getParam('profile.show_empty_tabs')) {
            if (!isset($aUser['total_fevent'])) {
                return false;
            }

            if (isset($aUser['total_fevent']) && (int)$aUser['total_fevent'] === 0) {
                return false;
            }
        }
        $aTotal = $this->getTotalItemCount($aUser['user_id']);
        $aMenus[] = array(
            'phrase' => _p('fevent.menu_fevent_events'),
            'url' => 'profile.fevent',
            'total' => $aTotal['total'],
            'icon' => 'feed/advevent.png',
            'icon_class' => 'ico ico-calendar-check-o'
        );

        return $aMenus;
    }

    public function globalSearch($sQuery, $bIsTagSearch = false)
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
        $sCondition = ' p.privacy = 1 ';
        if ($bIsTagSearch == false) {
            $sCondition .= ' AND (p.title LIKE \'%' . $this->database()->escape($sQuery) . '%\' OR pt.description_parsed LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
        }

        if ($bIsTagSearch == true) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.event_id AND tag.category_id = \'fevent\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'');
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('fevent_text'), 'pt', 'pt.event_id = p.event_id')
            ->where($sCondition)
            ->execute('getSlaveField');

        if ($bIsTagSearch == true) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.event_id AND tag.category_id = \'fevent\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'')->group('p.event_id');
        }

        $aRows = $this->database()->select('p.title, p.title_url, p.time_stamp, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('fevent_text'), 'pt', 'pt.event_id = p.event_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where($sCondition)
            ->limit(10)
            ->order('p.time_stamp DESC')
            ->execute('getSlaveRows');

        if (count($aRows)) {
            $aResults = [];
            $aResults['total'] = $iCnt;
            $aResults['menu'] = _p('fevent.search_items');

            if ($bIsTagSearch == true) {
                $aResults['form'] = '<div><input type="button" value="' . _p('fevent.view_more_items') . '" class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('fevent', array('tag', $sQuery)) . '\';" /></div>';
            } else {
                $aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('fevent') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . _p('fevent.view_more_items') . '" class="search_button" /></div></form>';
            }

            foreach ($aRows as $iKey => $aRow) {
                $aResults['results'][$iKey] = array(
                    'title' => $aRow['title'],
                    'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('fevent', $aRow['title_url'])),
                    'image' => Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aRow['server_id'],
                            'title' => $aRow['full_name'],
                            'path' => 'core.url_user',
                            'file' => $aRow['user_image'],
                            'suffix' => '_120',
                        )
                    ),
                    'extra_info' => _p('fevent.item_created_on_time_stamp_by_full_name', array(
                            'link' => Phpfox::getLib('url')->makeUrl('fevent'),
                            'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
                            'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                            'full_name' => $aRow['full_name']
                        )
                    )
                );
            }
            (($sPlugin = Phpfox_Plugin::get('fevent.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);
            return $aResults;
        }
        (($sPlugin = Phpfox_Plugin::get('fevent.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);
    }

    public function getTotalItemCount($iUserId)
    {
        return array(
            'field' => 'total_fevent',
            'total' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('fevent'))
                ->where('view_id = 0 AND item_id = 0 AND user_id = ' . (int)$iUserId)
                ->execute('getSlaveField')
        );
    }

    public function getProfileLink()
    {
        return 'profile.fevent';
    }

    public function getPhotoDetails($aPhoto)
    {
        $aRow = $this->database()->select('event_id, title')
            ->from(Phpfox::getT('fevent'))
            ->where('event_id = ' . (int)$aPhoto['group_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sLink = Phpfox::permalink('fevent', $aRow['event_id'], $aRow['title']);

        return array(
            'breadcrumb_title' => _p('fevent.events'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('fevent'),
            'module_id' => 'fevent',
            'item_id' => $aRow['event_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => Phpfox::permalink(array('fevent', 'photo'), $aRow['event_id'], $aRow['title']),
            'theater_mode' => _p('fevent.in_the_event_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['title'])),
            'feed_table_prefix' => 'fevent_'
        );
    }

    public function globalUnionSearch($sSearch)
    {
        $sConds = Phpfox::getService('fevent')->getConditionsForSettingPageGroup('item');
        $this->database()->select('item.event_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'fevent\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
            ->from(Phpfox::getT('fevent'), 'item')
            ->where('item.view_id = 0 AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch) . $sConds)
            ->union();
    }

    public function getSearchInfo($aRow)
    {
        $aInfo = [];
        $aInfo['item_link'] = Phpfox::getLib('url')->permalink('fevent', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('fevent.events');
        if (!empty($aRow['item_photo'])) {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => $aRow['item_photo'],
                    'path' => 'event.url_image',
                    'suffix' => '_120',
                    'max_width' => '320',
                    'max_height' => '320'
                )
            );
        } else {
            $aInfo['item_display_photo'] = '<img src="' . Phpfox::getService('fevent')->getDefaultPhoto() . '"/>';
        }
        return $aInfo;
    }

    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('advanced_events')
        );
    }

    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'fevent.view_browse_events')) {
            return null;
        }

        $aMenus[] = array(
            'phrase' => _p('fevent.menu_fevent_events'),
            'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'fevent/',
            'icon' => 'module/event.png',
            'landing' => 'fevent',
            'menu_icon' => 'ico ico-calendar-check-o'
        );

        return $aMenus;
    }

    public function getGroupMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'fevent.view_browse_events')) {
            return null;
        }

        $aMenus[] = array(
            'phrase' => _p('fevent.menu_fevent_events'),
            'url' => Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'fevent/',
            'icon' => 'module/event.png',
            'landing' => 'fevent',
            'menu_icon' => 'ico ico-calendar-check-o'
        );

        return $aMenus;
    }

    public function getPageSubMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'fevent.share_events')) {
            return null;
        }

        return array(
            array(
                'phrase' => _p('fevent.create_new_event'),
                'url' => Phpfox::getLib('url')->makeUrl('fevent.add', array('module' => 'pages', 'item' => $aPage['page_id']))
            )
        );
    }

    public function getGroupSubMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'fevent.share_events')) {
            return null;
        }

        return array(
            array(
                'phrase' => _p('fevent.create_new_event'),
                'url' => Phpfox_Url::instance()->makeUrl('fevent.add', array('module' => 'groups', 'item' => $aPage['page_id']))
            )
        );
    }

    public function getPagePerms()
    {
        $aPerms = [];

        $aPerms['fevent.share_events'] = _p('fevent.who_can_share_events');
        $aPerms['fevent.view_browse_events'] = _p('fevent.who_can_view_browse_events');

        return $aPerms;
    }

    public function getGroupPerms()
    {
        $aPerms = [];

        $aPerms['fevent.share_events'] = _p('fevent.who_can_share_events');
        $aPerms['fevent.view_browse_events'] = _p('fevent.who_can_view_browse_events');

        return $aPerms;
    }

    public function canViewPageSection($iPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($iPage, 'fevent.view_browse_events')) {
            return false;
        }

        return true;
    }

    public function getVideoDetails($aItem)
    {
        $aRow = $this->database()->select('event_id, title')
            ->from(Phpfox::getT('fevent'))
            ->where('event_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sLink = Phpfox::permalink(array('fevent', 'video'), $aRow['event_id'], $aRow['title']);

        return array(
            'breadcrumb_title' => _p('fevent.event'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('fevent'),
            'module_id' => 'fevent',
            'item_id' => $aRow['event_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink,
            //	'theater_mode' => 'In the page <a href="' . $sLink . '">' . $aRow['title'] . '</a>'
        );
    }

    public function getAttachmentField()
    {
        return array('fevent', 'event_id');
    }

    public function getCommentNotificationTag($aNotification)
    {

        $aRow = $this->database()->select('f.event_id, f.title, u.user_name,c.comment_id')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(phpfox::getT('fevent_feed'), 'ff', 'c.item_id=ff.item_id')
            ->join(Phpfox::getT('fevent'), 'f', 'f.event_id = ff.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!$aRow) {
            return false;
        }

        $sPhrase = _p('fevent.user_name_tagged_you_in_a_comment_in_a_event', array('user_name' => $aRow['user_name']));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']) . 'comment_' . $aNotification['item_id'],
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param int $iStartTime
     * @param int $iEndTime
     *
     * @return array
     */
    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = [];
        $aCond[] = 'view_id = 0';
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . db()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . db()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)$this->database()->select('COUNT(*)')
            ->from(':fevent')
            ->where($aCond)
            ->execute('getSlaveField');

        return [
            'phrase' => 'advanced_events',
            'total' => $iCnt,
            'icon' => 'ico ico-calendar-star-o'
        ];
    }

    /**
     * @param $iUserId
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotal = db()->select('COUNT(*)')
            ->from(':fevent')
            ->where('user_id =' . (int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('advanced_events'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }

    public function getUploadParams($aParams = null)
    {
        return Phpfox::getService('fevent')->getUploadParams($aParams);
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('fevent')->getPendingTotal();
        return [
            'message' => _p('you_have_total_pending_events', ['total' => $iTotalPending]),
            'value' => $iTotalPending,
            'link' => \Phpfox_Url::instance()->makeUrl('fevent', array('view' => 'pending'))
        ];
    }

    public function getUploadParamsDefault($aParams = null)
    {
        return Phpfox::getService('fevent')->getUploadDefaultParams($aParams);
    }

    /** Start View Event on Map */

    /**
     * Get events for map view
     *
     * @param $aParams
     */
    public function getMapViewItems($aParams)
    {
        $aParentModule = isset($aParams['aParentModule']) ? $aParams['aParentModule'] : null;
        $bIsUserProfile = isset($aParams['aUser']);
        $aUser = $bIsUserProfile ? $aParams['aUser'] : null;
        $sView = isset($aParams['view']) ? $aParams['view'] : '';
        $oServiceBrowse = Phpfox::getService('fevent.browse');
        $aCallback = isset($aParams['aCallback']) ? $aParams['aCallback'] : false;
        switch ($sView) {
            case 'pending':
                Phpfox::isUser(true);
                Phpfox::getUserParam('fevent.can_approve_events', true);
                $this->search()->setCondition('AND m.view_id = 1');
                break;
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
                break;
            default:
                if ($bIsUserProfile) {
                    $this->search()->setCondition('AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND m.module_id = \'event\' AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND m.user_id = ' . (int)$aUser['user_id']);
                } elseif ($aParentModule !== null) {
                    $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.module_id = \'' . Phpfox_Database::instance()->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int)$aParentModule['item_id'] . '');
                } else {
                    switch ($sView) {
                        case 'attending':
                            $oServiceBrowse->attending(1);
                            break;
                        case 'may-attend':
                            $oServiceBrowse->attending(2);
                            break;
                        case 'not-attending':
                            $oServiceBrowse->attending(3);
                            break;
                        case 'invites':
                            $oServiceBrowse->attending(0);
                            break;
                    }

                    if ($sView == 'attending' || $sView === 'invites' || $sView == 'may-attend') {
                        Phpfox::isUser(true);
                        $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)');
                    } else {
                        if ($aCallback !== false) {
                            $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.item_id = ' . $aCallback['item'] . '');
                        } else {
                            if ((Phpfox::getParam('fevent.fevent_display_event_created_in_page') || Phpfox::getParam('fevent.fevent_display_event_created_in_group'))) {
                                $aModules = [];
                                if (Phpfox::getParam('fevent.fevent_display_event_created_in_group') && Phpfox::isAppActive('PHPfox_Groups')) {
                                    $aModules[] = 'groups';
                                }
                                if (Phpfox::getParam('fevent.fevent_display_event_created_in_page') && Phpfox::isAppActive('Core_Pages')) {
                                    $aModules[] = 'pages';
                                }
                                if (count($aModules)) {
                                    $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND (m.module_id IN ("' . implode('","',
                                            $aModules) . '") OR m.module_id = \'fevent\')');
                                } else {
                                    $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.module_id = \'fevent\'');
                                }
                            } else {
                                $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.item_id = 0');
                            }
                        }
                    }

                    if ($this->request()->getInt('user') && ($aUserSearch = Phpfox::getService('user')->getUser($this->request()->getInt('user')))) {
                        $this->search()->setCondition('AND m.user_id = ' . (int)$aUserSearch['user_id']);
                    }

                }
                break;
        }
    }

    /**
     * Get params for search events
     *
     * @param $aParams
     * @return array
     */
    public function getMapViewParams($aParams)
    {
        $aParentModule = isset($aParams['aParentModule']) ? $aParams['aParentModule'] : null;
        $bIsUserProfile = isset($aParams['aUser']);
        $aUser = $bIsUserProfile ? $aParams['aUser'] : null;
        $aSearchFields = [
            'type' => 'fevent',
            'field' => 'm.event_id',
            'ignore_blocked' => true,
            'search_tool' => [
                'default_when' => Phpfox::getParam('fevent.fevent_default_sort_time', 'all-time'),
                'when_field' => 'start_time',
                'when_end_field' => 'end_time',
                'when_upcoming' => true,
                'when_ongoing' => true,
                'location_field' => [
                    'latitude_field' => 'lat',
                    'longitude_field' => 'lng'
                ],
                'table_alias' => 'm',
                'search' => [
                    'action' => ($aParentModule === null ? ($bIsUserProfile === true ? Phpfox_Url::instance()->makeUrl($aUser['user_name'],
                        ['fevent', 'view' => $this->request()->get('view')]) : Phpfox_Url::instance()->makeUrl('fevent',
                        ['view' => $this->request()->get('view')])) : $aParentModule['url'] . 'fevent/view_' . $this->request()->get('view') . '/'),
                    'default_value' => _p('search_events'),
                    'name' => 'search',
                    'field' => 'm.title'
                ],
                'sort' => [
                    'latest' => ['m.start_time', _p('latest'), 'ASC'],
                    'most-liked' => ['m.total_like', _p('most_liked')],
                    'most-talked' => ['m.total_comment', _p('most_discussed')]
                ],
                'show' => [20],
            ],
            'location_field' => [
                'latitude_field' => 'lat',
                'longitude_field' => 'lng'
            ],

        ];
        $aBrowseParams = [
            'module_id' => 'fevent',
            'alias' => 'm',
            'field' => 'event_id',
            'table' => Phpfox::getT('fevent'),
            'hide_view' => ['pending', 'my']
        ];

        return [
            'search_params' => $aSearchFields,
            'pagination_style' => Phpfox::getParam('fevent.fevent_paging_mode'),
            'browse_params' => $aBrowseParams,
            'card_view' => [
                'title' => _p('events'),
                'no_item_message' => _p('no_events_found'),
            ],
            'map_marker' => [
                'icon' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/p-advevent/assets/image/map_ico.png',
                'hover_icon' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/p-advevent/assets/image//map_ico_hover.png'
            ]
        ];
    }

    /**
     * convert item info to show on map
     *
     * @param $aEvent
     * @return array
     */
    public function convertItemOnMap($aEvent)
    {
        if (empty($aEvent['event_id'])) {
            return $aEvent;
        }
        $template = Phpfox::getLib('template');


        $actions = $rsvp_action = '';
        if (Phpfox::isUser()) {
            $aEvent['rsvp_id'] = Phpfox::getService('fevent')->getRsvp($aEvent['event_id']);
            $aEvent['is_invited'] = Phpfox::getService('fevent')->isInvitedByOwner($aEvent['event_id'], $aEvent['user_id'], Phpfox::getUserId());

            // get actions
            $template->assign(['aItem' => $aEvent,'rsvpActionType' => 'list']);
            $template->getTemplate('fevent.block.action-link');
            $actions = ob_get_contents();
            ob_clean();

            // get rsvp action
            $template->assign(['aEvent' => $aEvent]);
            $template->getTemplate('fevent.block.rsvp-action-map');
            $rsvp_action = ob_get_contents();
            ob_clean();
        }

        // get status and status class
        $label = $label_class = '';
        $fevent_basic_information_time = "l, F j, Y g:i a";
        $date = Phpfox::getTime($fevent_basic_information_time, $aEvent['start_time'], true, true);
        if ($aEvent['start_time'] <= PHPFOX_TIME && $aEvent['end_time'] >= PHPFOX_TIME) {
            $label = _p('ongoing');
            $label_class = 'success';
        } elseif ($aEvent['end_time'] < PHPFOX_TIME) {
            $label = _p('end');
            $label_class = 'danger';
            $date = _p('end_at') . Phpfox::getTime($fevent_basic_information_time, $aEvent['end_time'], true, true);
        }

        // get statistics
        $statistics = [];
        if ($aEvent['total_like'] > 0) {
            $statistics[] = [
                'label' => '',
                'value' => Phpfox::getService('core.helper')->shortNumber($aEvent['total_like']) . ' ' . ($aEvent['total_like'] == 1 ? _p('like_lowercase') : _p('likes_lowercase'))
            ];
        }

        // get guests
        $total_attending = Phpfox::getService('fevent')->getNumbersOfAttendee($aEvent['event_id'], 1);
        $guests = [];
        if ($total_attending > 0) {
            $statistics[] = [
                'label' => '',
                'value' => Phpfox::getService('core.helper')->shortNumber($total_attending) . ' ' . ($total_attending == 1 ? _p('fevent_feed_guest') : _p('fevent_feed_guests'))
            ];

            $total_show = 3;
            list(, $invites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 1, 0, $total_show);
            $guests = [
                'total_remaining' => $total_attending - $total_show,
                'all_href' => 'javascript:void(0);',
                'all_click' => 'tb_show(\'' . _p('guest_list', ['phpfox_squote' => true]). '\', $.ajaxBox(\'fevent.showGuestList\', \'height=300&amp;width=500&amp;tab=attending&amp;event_id=' . $aEvent['event_id'] . '\')); return false;',
                'list' => $invites
            ];
        }

        return [
            'item_link' => Phpfox_Url::instance()->permalink('fevent', $aEvent['event_id'], $aEvent['title']),
            'item_title' => $aEvent['title'],
            'item_is_featured' => $aEvent['is_featured'],
            'item_is_sponsor' => $aEvent['is_sponsor'],
            'item_actions' => trim($actions),
            'item_image' => $aEvent['image_path'],
            'item_author' => [
                'user_id' => $aEvent['user_id'],
                'full_name' => $aEvent['full_name'],
                'user_name' => $aEvent['user_name']
            ],
            'item_label' => $label,
            'item_label_class' => $label_class,
            'item_date' => $date,
            'item_statistics' => $statistics,
            'item_first_minor_info' => Phpfox::getLib('parse.output')->clean($aEvent['location']),
            'item_members' => $guests,
            'item_action_specific' => $rsvp_action
        ];
    }

    /** End View Event on Map */
}