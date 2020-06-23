<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<?php

class FeedBack_Service_Callback extends Phpfox_Service
{
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('feedback');
    }

    public function addTrack($iId, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_addtrack__start')) ? eval($sPlugin) : false);
        $this->database()->insert(Phpfox::getT('feedback_track'), array(
                'item_id' => (int)$iId,
                'user_id' => Phpfox::getUserBy('user_id'),
                'time_stamp' => PHPFOX_TIME
            )
        );
    }

    public function getTags($sTag, $aConds = array(), $sSort = '', $iPage = '', $sLimit = '')
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_gettags__start')) ? eval($sPlugin) : false);
        $aFeedBacks = array();
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = feedback.feedback_id")
            ->where($aConds)
            ->execute('getSlaveField');

        if ($iCnt) {
            $aRows = $this->database()->select("feedback.*, " . Phpfox::getUserField())
                ->from(Phpfox::getT('feedback'), 'fb')
                ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = feedback.feedback_id")
                ->join(Phpfox::getT('user'), 'u', 'feedback.user_id = u.user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $sLimit, $iCnt)
                ->execute('getSlaveRows');

            if (count($aRows)) {
                foreach ($aRows as $aRow) {
                    $aFeedBacks[$aRow['feedback_id']] = $aRow;
                }
            }
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_gettags__end')) ? eval($sPlugin) : false);
        return array($iCnt, $aFeedBacks);
    }

    public function getTagSearch($aConds = array(), $sSort)
    {


    }

    public function getTopUsers()
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_gettopusers__start')) ? eval($sPlugin) : false);
        $aFeedBacks = $this->database()->select('COUNT(fb.feedback_id) AS top_total, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('feedback'), 'fb', 'fb.user_id = u.user_id  AND fb.privacy = 1')
            ->order('top_total DESC')
            ->group('u.user_id')
            ->limit(0, 10)
            ->execute('getRows');
        if (is_array($aFeedBacks) && count($aFeedBacks)) {
            foreach ($aFeedBacks as $iKey => $aFeedBack) {
                $aFeedBacks[$iKey]['link'] = Phpfox::getService('user')->getLink($aFeedBacks['user_id'], $aFeedBacks['user_name'], 'feedback');
            }
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_gettopusers__end')) ? eval($sPlugin) : false);
        return $aFeedBacks;
    }

    public function getProfileLink()
    {

    }


    public function getTagTypeProfile()
    {
        return 'feedback';
    }

    public function getTagType()
    {
        return 'feedback';
    }

    public function getSqlTitleField()
    {
        return array(
            'table' => 'feedback',
            'field' => 'title'
        );
    }

    public function getFeedRedirect($iId, $iChild = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);
        $aFeedBack = $this->database()->select('fb.feedback_id, fb.title_url, u.user_id, u.user_name')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
            ->where('fb.feedback_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aFeedBack['feedback_id'])) {
            return false;
        }

        if (Phpfox::getParam('core.is_personal_site')) {

            return Phpfox::getLib('url')->makeUrl($aFeedBack['user_name'], $aFeedBack['title_url']);
        }
        if ($iChild > 0) {

            return Phpfox::getLib('url')->makeUrl($aFeedBack['user_name'], array('feedback', $aFeedBack['title_url'], 'comment' => $iChild, '#comment-view'));
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);
        return Phpfox::getLib('url')->makeUrl('', array('feedback/detail', $aFeedBack['title_url']));
    }

    public function getAjaxCommentVar()
    {
        return 'feedback.can_you_post_on_feedback';
    }

    public function getNotificationApproved($aNotification)
    {
        $aRow = $this->database()->select('fb.feedback_id, fb.title_url, fb.title, fb.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
            ->where('fb.feedback_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['feedback_id'])) {
            return false;
        }

        $sPhrase = 'Your feedback "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '" has been approved.';

        return array(
            'link' => Phpfox::getLib('url')->permalink('feedback.detail', $aRow['title_url'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.jpg', 'feedback'),
            'no_profile_image' => true
        );
    }

    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {

        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);
        $aFeedBack = $this->database()->select('u.full_name, u.user_id, u.email, u.gender, u.user_name, fb.title, fb.title_url, fb.feedback_id, fb.privacy')
            ->from($this->_sTable, 'fb')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
            ->where('fb.feedback_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        if (empty($aFeedBack)) {
            $aFeedBack = $this->database()->select('fb.*')
                ->from($this->_sTable, 'fb')
                ->where('fb.feedback_id = ' . (int)$aVals['item_id'])
                ->execute('getSlaveRow');
            $aFeedBack['user_name'] = '';
        }
        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        Phpfox::getService('feedback.process')->updateCounter($aVals['item_id']);
        Phpfox::getService('feedback.process')->updateDateModify($aVals['item_id']);
        if ($aFeedBack['privacy'] == 1) {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('comment_feedfeedback', $aVals['item_id'], $aFeedBack['privacy'], 0, $iUserId, $aFeedBack['user_id'], $aVals['comment_id']) : null);
        }

        // Send the user an email
        if (Phpfox::getParam('core.is_personal_site')) {
            $sLink = Phpfox::getLib('url')->makeUrl('feedback.detail', $aFeedBack['title_url']);
        } else {
            $sLink = Phpfox::getLib('url')->makeUrl('feedback.detail', $aFeedBack['title_url']);
            //$sLink = Phpfox::getService('user')->getLink($aFeedBack['user_id'], $aFeedBack['user_name'], array('feedback', $aFeedBack['title_url']));
        }
        if ($aFeedBack['user_id'] != 0) {
            Phpfox::getService('comment.process')->notify(array(
                    'user_id' => $aFeedBack['user_id'],
                    'item_id' => $aFeedBack['feedback_id'],
                    'owner_subject' => Phpfox::getUserBy('full_name') . ' commented on your feedback "' . $aFeedBack['title'] . '".',
                    'owner_message' => Phpfox::getUserBy('full_name') . " commented on your feedback \"<a href=\"" . $sLink . "\">" . $aFeedBack['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>",
                    'owner_notification' => 'comment.add_new_comment',
                    'notify_id' => 'comment_feedfeedback',
                    'mass_id' => 'feedback',
                    'mass_subject' => (Phpfox::getUserId() == $aFeedBack['user_id'] ? Phpfox::getUserBy('full_name') . ' commented on ' . Phpfox::getService('user')->gender($aFeedBack['gender'], 1) . ' feedback.' : Phpfox::getUserBy('full_name') . ' commented on ' . $aFeedBack['full_name'] . '\'s feedback.'),
                    'mass_message' => (Phpfox::getUserId() == $aFeedBack['user_id'] ? Phpfox::getUserBy('full_name') . " commented on " . Phpfox::getService('user')->gender($aFeedBack['gender'], 1) . " feedback \"<a href=\"" . $sLink . "\">" . $aFeedBack['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>" : Phpfox::getUserBy('full_name') . " commented on " . $aFeedBack['full_name'] . "'s feedback \"<a href=\"" . $sLink . "\">" . $aFeedBack['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
                )
            );
            /*Phpfox::getLib('mail')->to($aFeedBack['user_id'])
             ->subject(array('feedback.user_name_left_you_a_comment_on_site_title', array('user_name' => $sUserName, 'site_title' => Phpfox::getParam('core.site_title'))))
             ->message(array('feedback.user_name_left_you_a_comment_on_site_title_message', array('user_name' => $sUserName, 'site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
             ->notification('comment.add_new_comment')
             ->send();*/
        }


        if ($aFeedBack['user_id']) {
            /*	Phpfox::getService('notification.process')->add('comment_feedback', $aFeedBack['feedback_id'], $aFeedBack['user_id'], array(
             'title' => $aFeedBack['title'],
             'user_id' => Phpfox::getUserId(),
             'image' => Phpfox::getUserBy('user_image'),
             'server_id' => Phpfox::getUserBy('server_id')
             )
             );*/
        }

        if (Phpfox::isModule('request') && !Phpfox::getUserParam('comment.approve_all_comments')) {
            if ($aFeedBack['user_id'] == 0 && Phpfox::getParam('feedback.send_mail_to_visitor_when_someone_comment')) {
                $this->sendMailToNoneUser($aFeedBack['feedback_id'], $sUserName);

            } else {
                /*$sLink = Phpfox::getLib('url')->makeUrl('request', '#comment');
                Phpfox::getLib('mail')->to($aFeedBack['email'])
                ->subject(array('feedback.user_name_left_you_a_comment_on_site_title', array('user_name' => $sUserName, 'site_title' => Phpfox::getParam('core.site_title'))))
                ->message(array('feedback.user_name_left_you_a_comment_on_site_title_however_before_it_can_be_displayed_it', array('user_name' => $sUserName, 'site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
                ->fromName(Phpfox::getUserBy('full_name'))
                ->notification('comment.approve_new_comment')
                ->send();*/
            }
        }

        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }

    public function sendMailToNoneUser($feedback_id, $sUserName)
    {
        $sLink = Phpfox::getLib('url')->makeURL('feedback.detail', array('feedback' => $feedback_id));
        /*$isSendMail = Phpfox::getLib('database')->select('fs.param_values')
         ->from(Phpfox::getT('feedback_settings'), 'fs')
         ->where('fs.settings_type = "send_mail_to_none_user"')
         ->execute('getSlaveField');
         */
        $isSendMail = true;
        if ($isSendMail) {
            $eUser = Phpfox::getLib('database')->select('fb.email')
                ->from(Phpfox::getT('feedback'), 'fb')
                ->where('fb.feedback_id = ' . $feedback_id)
                ->execute('getSlaveField');
            if ($eUser) {
                $sMail = trim($eUser);
                $bSent = Phpfox::getLib('mail')->to($sMail)
                    ->subject(array('feedback.user_name_left_you_a_comment_on_site_title', array('user_name' => $sUserName, 'site_title' => Phpfox::getParam('core.site_title'))))
                    ->message(array('feedback.user_name_added_a_new_comment_on_none_user', array('user_name' => $sUserName, 'site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
                    ->send();
            }
        }
    }

    public function updateCommentText($aVals, $sText)
    {
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_feedback', $aVals['item_id'], $sText, $aVals['comment_id']) : null);
    }

    public function getItemName($iId, $sName)
    {
        return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">On ' . $sName . '\'s feedback.</a>';
    }

    public function getAttachmentField()
    {
        return array('feedback', 'feedback_id');
    }

    public function getCommentItem($iId)
    {

        return $this->database()->select('feedback_id AS comment_item_id, 1 AS comment_view_id, user_id AS comment_user_id')
            ->from($this->_sTable)
            ->where('feedback_id = ' . (int)$iId)
            ->execute('getSlaveRow');
    }

    public function getRssTitle($iId)
    {
        $aRow = $this->database()->select('title')
            ->from($this->_sTable)
            ->where('feedback_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        return 'Comments on: ' . $aRow['title'];
    }

    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    public function getReportRedirect($iId)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);
        $aFeedBack = $this->database()->select('fb.feedback_id, fb.title_url')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where('fb.feedback_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (!isset($aFeedBack['feedback_id'])) {
            return false;
        }

        if (Phpfox::getParam('core.is_personal_site')) {

            return Phpfox::getLib('url')->makeUrl($aFeedBack['user_name'], $aFeedBack['title_url']);
        }
        if ($iChild > 0) {

            return Phpfox::getLib('url')->makeUrl($aFeedBack['user_name'], array('feedback', $aFeedBack['title_url'], 'comment' => $iChild, '#comment-view'));
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);
        return Phpfox::getLib('url')->makeUrl('feedback.detail', array($aFeedBack['title_url']));
    }

    public function getCommentItemName()
    {
        return 'feedback';
    }

    public function globalSearch($sQuery, $bIsTagSearch = false)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
        $sCondition = 'fb.privacy = 1';
        if ($bIsTagSearch == false) {
            $sCondition .= ' AND (fb.title LIKE \'%' . $this->database()->escape($sQuery) . '%\' OR fb.feedback_description LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'fb')
            ->where($sCondition)
            ->execute('getSlaveField');


        $aRows = $this->database()->select('fb.title, fb.title_url, fb.time_stamp, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'fb')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
            ->where($sCondition)
            ->limit(10)
            ->order('fb.time_stamp DESC')
            ->execute('getSlaveRows');

        if (count($aRows)) {
            $aResults = array();
            $aResults['total'] = $iCnt;
            $aResults['menu'] = _p('feedback.search_feedbacks');

            if ($bIsTagSearch == true) {
                $aResults['form'] = '<div class="form-group"><button class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('feedback', array('tag', $sQuery)) . '\';" >' . _p('feedback.view_more_feedbacks') . '</button></div>';
            } else {
                $aResults['form'] = '<form class="form-horizontal" method="post" action="' . Phpfox::getLib('url')->makeUrl('feedback') . '"><div class="form-group"><input type="hidden" name="phpfox[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . _p('feedback.view_more_feedbacks') . '" class="search_button" /></div></form>';
            }

            foreach ($aRows as $iKey => $aRow) {
                $aResults['results'][$iKey] = array(
                    'title' => $aRow['title'],
                    'link' => Phpfox::getLib('url')->makeUrl('feedback.detail/' . $aRow['title_url']),
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
                    'extra_info' => _p('feedback.feedback_created_on_time_stamp_by_full_name', array(
                            'link' => Phpfox::getLib('url')->makeUrl('feedback'),
                            'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
                            'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                            'full_name' => $aRow['full_name']
                        )
                    )
                );
            }
            (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);
            return $aResults;
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);
    }

    public function deleteComment($iId)
    {
        $this->database()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)), 'feedback_id = ' . (int)$iId);
    }

    /**
     * Action to take when user cancelled their account
     * @param int $iUser
     */
    public function onDeleteUser($iUser)
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);
        // get all the feedbacks by this user
        $aFeedBacks = $this->database()
            ->select('feedback_id')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)$iUser)
            ->execute('getSlaveRows');

        foreach ($aFeedBacks as $aFeedBack) {
            Phpfox::getService('feedback.process')->delete($aFeedBack['feedback_id']);
        }

        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
    }

    public function getItemView()
    {
        if (Phpfox::getLib('request')->get('req3') != '') {
            return true;
        }
    }

    public function getNotificationFeedApproved($aRow)
    {
        return array(
            'message' => _p('feedback.your_feedback_feedback_title_has_been_approved',
                array('feedback_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
            'link' => Phpfox::getLib('url')->makeUrl('feedback', array('redirect' => $aRow['item_id'])));
    }

    public function legacyRedirect($aRequest)
    {
        if (isset($aRequest['req2'])) {
            switch ($aRequest['req2']) {
                case 'view':
                    if (isset($aRequest['id'])) {
                        $aItem = Phpfox::getService('core')->getLegacyUrl(array(
                                'url_field' => 'title_url',
                                'table' => 'feedback',
                                'field' => 'upgrade_feedback_id',
                                'id' => $aRequest['id']
                            )
                        );

                        if ($aItem !== false) {
                            return array($aItem['user_name'], array('feedback', $aItem['title_url']));
                        }
                    }
                    break;
                default:
                    return 'feedback';
                    break;
            }
        }

        return false;
    }

    public function getCommentNotificationFeed($aNotification)
    {
        //print_r($aNotification); die(-1);
        $aRow = $this->database()->select('fb.feedback_id, fb.title, fb.title_url, fb.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
            ->where('fb.feedback_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users'])) {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('feedback.commented_on') . ' ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' feedback "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('feedback.commented_on_your_feedback') . ' ' . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        } else {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('feedback.commented_on') . ' ' . '<span class="drop_data_user">' . $aRow['full_name'] . '\'s</span>' . ' ' . _p('feedback.feedback') . ' ' . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('feedback.detail', $aRow['title_url']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.jpg', 'feedback')
        );
    }

    public function getCommentNotificationFeedFeed($aRow)
    {
        //print_r($aRow); die(-1);
        return array(
            'message' => _p('feedback.full_name_wrote_a_comment_on_your_feedback_feedback_title', array(
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                    'full_name' => $aRow['full_name'],
                    'feedback_link' => Phpfox::getLib('url')->makeUrl('feedback.detail', array('feedback' => $aRow['item_id'])),
                    'feedback_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')
                )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('feedback.detail', array('feedback' => $aRow['item_id'])),
            'path' => 'core.url_user',
            'suffix' => '_50'
        );
    }

    public function getNotificationFeedNotifyLike($aRow)
    {
        return array(
            'message' => _p('feedback.a_href_user_link_full_name_a_likes_your_a_href_link_feedback_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                    'link' => Phpfox::getLib('url')->makeUrl('feedback.detail', array('redirect' => $aRow['item_id']))
                )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('feedback', array('redirect' => $aRow['item_id']))
        );
    }

    public function canShareItemOnFeed()
    {
    }

    public function getActivityFeed($aRow, $aCallback = null, $bIsChildItem = false)
    {
        $iFeedbackId = $aRow['item_id'];
        if (Phpfox::isUser()) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feedback\' AND l.item_id = fb.feedback_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = fb.user_id');
        }

        $aRow = $this->database()->select('fb.feedback_id, fb.title, fb.title_url, fb.time_stamp, fb.total_comment, fb.total_like, fb.feedback_description')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where('fb.feedback_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow)) {
            $aGuest = $this->database()->select('full_name, email')->from(Phpfox::getT('feedback'))->where('feedback_id = ' . $iFeedbackId)->executeRow();
            if(!empty($aGuest)) {
                if (Phpfox::isUser()) {
                    $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feedback\' AND l.item_id = fb.feedback_id AND l.user_id = ' . Phpfox::getUserId());
                }
                $aRow = $this->database()->select('fb.feedback_id, fb.title, fb.title_url, fb.time_stamp, fb.total_comment, fb.total_like, fb.feedback_description')
                    ->from(Phpfox::getT('feedback'), 'fb')
                    ->where('fb.feedback_id = ' . (int)$iFeedbackId)
                    ->execute('getSlaveRow');
            } else {
                return false;
            }
        }

        if (empty($aGuest)) {
            $aReturn = array_merge(array('feed_info' => _p('feedback.posted_a_feedback')), $aRow);
        }else{
            $aReturn = $aRow;
        }
        return array_merge(array(
            'feed_title' => $aRow['title'],
            'feed_link' => Phpfox::permalink('feedback.detail', $aRow['title_url']),
            'feed_content' => $aRow['feedback_description'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/feedback.jpg', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'feedback',
            'like_type_id' => 'feedback'
        ), $aReturn);
    }

    public function getNewsFeed($aRow, $iUserId = null)
    {

        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_getnewsfeed__start')) ? eval($sPlugin) : false);
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');

        $aRow['text'] = _p('feedback.a_href_user_link_owner_full_name_a_added_a_new_feedback_a_href_title_link_title_a',
            array(
                'owner_full_name' => $aRow['owner_full_name'],
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['content'], 20, '...'),
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'title_link' => Phpfox::getLib('url')->makeUrl('feedback.detail', array('feedback' => $aRow['item_id']))
            )
        );

        $aRow['icon'] = 'module/feedback.jpg';
        $aRow['enable_like'] = true;
        $aRow['comment_type_id'] = 'feedback';

        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_getnewsfeed__end')) ? eval($sPlugin) : false);
        return $aRow;
    }

    public function getCommentNewsFeed($aRow, $iUserId = null)
    {

        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_getcommentnewsfeed__start')) ? eval($sPlugin) : false);
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');
        if ($aRow['owner_user_id'] == $aRow['item_user_id']) {
            $aRow['text'] = _p('feedback.a_href_user_link_full_name_a_wrote_a_comment_on_your_feedback', array(
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'full_name' => $aRow['owner_full_name'],
                    'feedback_link' => Phpfox::getLib('url')->makeUrl('feedback.detail', array('feedback' => $aRow['item_id'])),
                )
            );
        } elseif ($aRow['item_user_id'] == Phpfox::getUserBy('user_id')) {
            $aRow['text'] = _p('feedback.a_href_user_link_full_name_a_wrote_a_comment_on_your_feedback', array(
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link'],
                    'full_name' => $aRow['owner_full_name'],
                    'feedback_link' => Phpfox::getLib('url')->makeUrl('feedback.detail', array('feedback' => $aRow['item_id']))

                )
            );
        } else {
            $aRow['text'] = _p('feedback.a_href_user_link_full_name_a_wrote_a_comment_on_your_feedback', array(
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link'],
                    'item_user_name' => $aRow['viewer_full_name'],
                    'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id'])),
                    'full_name' => $aRow['owner_full_name'],
                    'feedback_link' => Phpfox::getLib('url')->makeUrl('feedback.detail', array('feedback' => $aRow['item_id']))
                )
            );
        }

        $aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_getcommentnewsfeed__end')) ? eval($sPlugin) : false);
        return $aRow;
    }

    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array(
            'phrase' => _p('feedback.feedback'),
            'value' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('feedback'))
                ->where('time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        );
    }

    public function getFeedRedirectFeedLike($iId, $iChildId = 0)
    {
        return $this->getFeedRedirect($iChildId);
    }


    public function getWhatsNew()
    {
        return array(
            'feedback.feedbacks_title' => array(
                'ajax' => '#feedback.getNew?id=js_new_item_holder',
                'id' => 'feedback',
                'block' => 'feedback.new'
            )
        );
    }

    /*public function getDashboardLinks()
     {
     return array(
     'submit' => array(
     'phrase' => _p('feedback.create_new_feedback'),
     'link' => 'javascript:popupFeedback();',
     'image' => 'misc/chart_pie_edit.png'
     ),
     'edit' => array(
     'phrase' => _p('feedback.manage_feedbacks'),
     'link' => 'feedback.manage',
     'image' => 'misc/chart_pie_edit.png'
     )
     );
     }
     */
    public function getTagCloud()
    {
        return array(
            'link' => 'feedback',
            'category' => 'feedback'
        );
    }


    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
        return array(
            _p('feedback.feedbacks_activity') => $aUser['activity_feedback']
        );
    }


    public function verifyFavorite($iItemId)
    {

        $aItem = $this->database()->select('fb.feedback_id')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->where('fb.feedback_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aItem['feedback_id'])) {
            return false;
        }

        return true;
    }


    public function getFavorite($aFavorites)
    {
        $aItems = $this->database()->select('i.title, i.time_stamp, i.title_url, i.full_name as visitor_name, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'i')
            ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')
            ->where('i.feedback_id IN(' . implode(',', $aFavorites) . ')')
            ->execute('getSlaveRows');

        foreach ($aItems as $iKey => $aItem) {
            if ($aItem['user_id'] == null) {
                $aItems[$iKey]['full_name'] = $aItems[$iKey]['visitor_name'];
            }
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aItem['server_id'],
                    'path' => 'core.url_user',
                    'file' => $aItem['user_image'],
                    'suffix' => '_75',
                    'max_width' => 75,
                    'max_height' => 75
                )
            );

            $aItems[$iKey]['link'] = Phpfox::getLib('url')->makeUrl('feedback.detail', $aItem['title_url']);
            $aItems[$iKey]['title'] = $aItem['title'];
        }

        return array(
            'title' => "Feedbacks",
            'items' => $aItems
        );
    }

    /*
     * Add Like function in v3
     */
    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('feedback_id, title, user_id')
            ->from(Phpfox::getT('feedback'))
            ->where('feedback_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['feedback_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'feedback\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'feedback', 'feedback_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('feedback', $aRow['feedback_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(Phpfox::getUserBy('full_name') . " liked your feedback \"" . $aRow['title'] . "\"")
                ->message(Phpfox::getUserBy('full_name') . " liked your feedback \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\"\nTo view this feedback follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
                ->send();

            Phpfox::getService('notification.process')->add('feedback_like', $aRow['feedback_id'], $aRow['user_id']);
        }
    }

    public function getNotificationLike($aNotification)
    {
        $aRow = $this->database()->select('fb.feedback_id, fb.title_url, fb.title, fb.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('feedback'), 'fb')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
            ->where('fb.feedback_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' own feedback "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked your feedback "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        } else {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> feedback "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('feedback.detail', $aRow['title_url']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.jpg', 'feedback')
        );
    }

    public function deleteLike($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'feedback\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'feedback', 'feedback_id = ' . (int)$iItemId);
    }

    public function getCommentNotificationFeedTag($aNotification)
    {
        if (phpfox::isModule('feedback')) {
            return false;
        }
        $aRow = $this->database()->select('m.feedback_id, m.title_url, u.user_name')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('feedback'), 'm', 'm.feedback_id = c.item_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }
        $sPhrase = _p('feedback.user_name_tagged_you_in_a_feedback', array('user_name' => $aRow['user_name']));

        return array(
            'link' => Phpfox::getLib('url')->permalink('feedback.detail', $aRow['title_url']) . 'comment_' . $aNotification['item_id'],
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.jpg', 'feedback')
        );
    }

    public function globalUnionSearch($sSearch)
    {
        $this->database()->select('item.feedback_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'feedback\' AS item_type_id,  \'\' AS item_photo, \'\' AS item_photo_server')
            ->from(Phpfox::getT('feedback'), 'item')
            ->where($this->database()->searchKeywords('item.title', $sSearch) . ' AND item.is_approved = 1 and item.privacy = 1')
            ->union();
    }

    public function getSearchInfo($aRow)
    {
        $aInfo = array();
        $aFeedback = Phpfox::getService('feedback')->getFeedBackForEdit($aRow['item_id']);
        $aInfo['item_link'] = Phpfox::getLib('url')->permalink('feedback.detail', $aFeedback['title_url'], '');
        $aInfo['item_name'] = _p('feedback.feedback');

        return $aInfo;
    }

    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('feedback.feedback')
        );
    }

    /*public function getActions()
{
    return array(
        'dislike' => array(
            'enabled' => true,
            'action_type_id' => 2, // sort of redundant given the key
            'phrase' => 'Dislike',
            'phrase_in_past_tense' => 'disliked',
            'item_type_id' => 'feedback', // used internally to differentiate between photo albums and photos for example.
            'item_phrase' => 'feedback',
            'table' => 'feedback',
            'column_update' => 'total_dislike',
            'column_find' => 'feedback_id',
            'where_to_show' => array('feedback', '')
                        )
    );
}*/


    //end like

    /*
     *
     */


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
        if ($sPlugin = Phpfox_Plugin::get('feedback.service_callback__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = array();
        if ($iStartTime > 0) {
            $aCond[] = 'time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)$this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('feedback'))
            ->where($aCond)
            ->execute('getSlaveField');

        return array(
            'phrase' => _p('feedback.feedback'),
            'total' => $iCnt,
            'icon' => 'ico ico-warning-circle-o'
        );
    }

    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotal = db()->select('COUNT(*)')
            ->from(':feedback')
            ->where('user_id ='.(int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('feedback'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }

    public function getUploadParams($aParams = null)
    {
        return Phpfox::getService('feedback')->getUploadParams($aParams);
    }

    public function getTagLink()
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_service_callback_gettaglink__start')) ? eval($sPlugin) : false);
        return Phpfox::getLib('url')->makeUrl('feedback.tag');
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('feedback')->getPendingTotal();
        return [
            'message'=> _p('you_have_total_pending_feedbacks', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => \Phpfox_Url::instance()->makeUrl('feedback', array('view' => 'pending'))
        ];
    }
}

?>