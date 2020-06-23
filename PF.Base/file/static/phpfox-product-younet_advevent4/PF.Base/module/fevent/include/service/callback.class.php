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
class Fevent_Service_Callback extends Phpfox_Service
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
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('fevent.events') => $aUser['activity_fevent']
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
        $aListing = $this->database()->select('m.event_id, m.title')
            ->from($this->_sTable, 'm')
            ->where('m.event_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aListing['event_id'])) {
            return false;
        }

        return Phpfox::permalink('fevent', $aListing['event_id'], $aListing['title']);
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
                'phrase' => _p('fevent.events'),
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
        $aEvent['extra'] = '<b>' . _p('fevent.date') . '</b> ' . Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aEvent['start_time']) . ' - ';

        if (date('dmy', $aEvent['start_time']) === date('dmy', $aEvent['end_time'])) {
            $aEvent['extra'] .= Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time_short'), $aEvent['end_time']);
        } else {
            $aEvent['extra'] .= Phpfox::getTime(Phpfox::getParam('fevent.fevent_basic_information_time'), $aEvent['end_time']);
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
        $aList = array();

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
        $aRow = $this->database()->select($sSelect)
            ->from(Phpfox::getT('fevent'), 'e')
            ->leftJoin(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')
            ->where('e.event_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
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
            'url' => 'profile.fevent.view_all',
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
            $aResults = array();
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
                            'suffix' => '_75',
                            'max_width' => 75,
                            'max_height' => 75
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
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

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
            'theater_mode' => _p('fevent.in_the_event_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['title']))
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
        $aInfo = array();
        $aInfo['item_link'] = Phpfox::getLib('url')->permalink('fevent', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('fevent.events');

        if (!empty($aRow['item_photo'])) {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => $aRow['item_photo'],
                    'path' => 'event.url_image',
                    'suffix' => '_120',
                    'max_width' => '120',
                    'max_height' => '120'
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
        $aPerms = array();

        $aPerms['fevent.share_events'] = _p('fevent.who_can_share_events');
        $aPerms['fevent.view_browse_events'] = _p('fevent.who_can_view_browse_events');

        return $aPerms;
    }

    public function getGroupPerms()
    {
        $aPerms = array();

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
        $aCond = array();
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

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_callback__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

?>