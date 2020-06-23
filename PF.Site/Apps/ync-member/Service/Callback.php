<?php
/**
 * Created by PhpStorm.
 * User: phuong
 * Date: 2/12/17
 * Time: 5:39 PM
 */

namespace Apps\YNC_Member\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Url;
use Phpfox_Plugin;

class Callback extends Phpfox_Service
{
    public function canShareItemOnFeed()
    {}

    public function getActivityFeed($aRow, $aCallback = null, $bIsChildItem = false)
    {
        $aUser = Phpfox::getService('user')->get($aRow['item_id'], true);

        if (!isset($aUser['user_id']))
        {
            return false;
        }

        $aParams = array(
            'user' => $aUser,
            'suffix' => '_120_square',
            'max_width' => '120',
            'max_height' => '120'
        );

        $sImage = Phpfox::getLib('image.helper')->display($aParams);

        $aReturn =  [
            'feed_title' => $aUser['full_name'],
            'feed_title_sub' => $aUser['user_name'],
            'feed_info' => _p('shared a member'),
            'feed_link' => Phpfox_Url::instance()->makeUrl($aUser['user_name']),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/friend_added.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => false,
            'feed_image' => $sImage,
//            'feed_custom_html' => $sContent
        ];

        if ($bIsChildItem)
        {
            $aReturn = array_merge($aReturn, $aRow);
        }
        return $aReturn;
    }
    
    public function getActivityFeedReview($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem)
        {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = re.user_id');
        }

        if(Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynmember_review\' AND l.item_id = re.review_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('re.rating, re.review_id, re.title, re.time_stamp, re.total_comment, re.total_like, re.user_id, re.item_id, re.text')
            ->from(Phpfox::getT('ynmember_review'), 're')
            ->where('re.review_id = ' . (int) $aItem['item_id'])
            ->executeRow();

        if (!isset($aRow['review_id']))
        {
            return false;
        }

        if ($bIsChildItem)
        {
            $aItem = array_merge($aRow, $aItem);
        }

        $aUser = Phpfox::getService('user')->get($aRow['item_id'], true);

        $aParams = array(
            'user' => $aUser,
            'suffix' => '_120_square',
            'max_width' => '120',
            'max_height' => '120'
        );

        $sImage = Phpfox::getLib('image.helper')->display($aParams);
//        $sContent = Phpfox_Template::instance()->getTemplate('ynmember.block.feed_review', true);
        $content = $aRow['text'];
        if (preg_match('/^.{1,200}\b/s', $aRow['text'], $match))
        {
            $content = $match[0];
        }
        $aReturn = array(
            'feed_info' => _p('wrote_a_new_review_for_subject', ['subject' => $aUser['full_name']]),
            'feed_title' => $aRow['title'],
            'feed_link' => Phpfox::getLib('url')->makeUrl('ynmember.review', ['user_id' => $aRow['item_id']]),
            'feed_content' => $content,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'ynmember_review',
            'like_type_id' => 'ynmember_review',
            'feed_image' => $sImage,
            'feed_custom_html' => '<div class="ynmember_rating_block">'.ynmember_rating($aRow['rating']).'</div>'
        );

        if ($bIsChildItem)
        {
            $aReturn = array_merge($aReturn, $aItem);
        }

        return $aReturn;
    }

    public function getAjaxCommentVarReview()
    {
        return 'ynmember_like_comment_review';
    }
    public function getCommentItemReview($iId)
    {
        $aRow = $this->database()->select('review_id AS comment_item_id, user_id AS comment_user_id')
            ->from(Phpfox::getT('ynmember_review'))
            ->where('review_id = ' . (int) $iId)
            ->executeRow();

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], 0))
        {
            Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }
    public function addCommentReview($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ynmember.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);

        $aItem = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, ynmr.title, ynmr.review_id, ynmr.privacy')
            ->from(Phpfox::getT('ynmember_review'), 'ynmr')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ynmr.user_id')
            ->where('ynmr.review_id = ' . (int) $aVals['item_id'])
            ->execute('getSlaveRow');

        if ($iUserId === null)
        {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id']))
        {
            $this->database()->updateCounter('ynmember_review', 'total_comment', 'review_id', $aVals['item_id']);
        }

        // Send the user an email
        $sLink = Phpfox::permalink('ynmember.review', $aItem['review_id'], $aItem['title']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aItem['user_id'],
                'item_id' => $aItem['review_id'],
                'owner_subject' => Phpfox::getUserBy('full_name')._p(' commented on your review ').$aItem['title'],
                'owner_message' => Phpfox::getUserBy('full_name')._p(' commented on your review ').'<a href="'.$sLink.'">'.$aItem['title'].'</a>"'._p(' To see the comment thread, follow the link below: ').'<a href="'.$sLink.'">'.$sLink.'</a>',
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'ynmember_commentreview',
                'mass_id' => 'ynmember',
                'mass_subject' => (Phpfox::getUserId() == $aItem['user_id']) ? (Phpfox::getUserBy('full_name')._p(' commented on ').Phpfox::getService('user')->gender($aItem['gender'])._p(' review.')) : Phpfox::getUserBy('full_name')._p(' commented on ').$aItem['full_name']._p('\'s review.'),
                'mass_message' =>( Phpfox::getUserId() == $aItem['user_id']) ? (Phpfox::getUserBy('full_name')._p(' commented on ').Phpfox::getService('user')->gender($aItem['gender'], 1)._p(' review ').'"<a href="'. $sLink.'">'.$aItem['title'].'</a>"'._p(' To see the comment thread, follow the link below:').'<a href="'.$sLink.'">'.$sLink.'</a>') : (Phpfox::getUserBy('full_name')._p(' commented on ').$aItem['full_name']._p('\'s review ').'"<a href="'. $sLink.'">'.$aItem['title'].'</a>"'._p(' To see the comment thread, follow the link below:').'<a href="'.$sLink.'">'.$sLink.'</a>'),
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynmember.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }

    public function deleteCommentReview($iId)
    {
        $this->database()->update(Phpfox::getT('ynmember_review'), array('total_comment' => array('= total_comment -', 1)), 'review_id = ' . (int) $iId);
    }

    public function addLikeReview ($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('review_id, title, user_id')
            ->from(Phpfox::getT('ynmember_review'))
            ->where('review_id = ' . (int) $iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['review_id']))
        {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'ynmember_review\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ynmember_review', 'review_id = ' . (int) $iItemId);

        if (!$bDoNotSendEmail)
        {
            $sLink = Phpfox::permalink('ynmember.review', $aRow['review_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(Phpfox::getUserBy('full_name')._p(' liked your review.'))
                ->message(Phpfox::getUserBy('full_name')._p(' liked your review ').'"<a href="'.$sLink.'">'.$aRow['title'].'</a>"'._p(' To view this review follow the link below ').'<a href="'.$sLink.'">'.$sLink.'</a>"')
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ynmember_likereview', $aRow['review_id'], $aRow['user_id']);
        }
    }

    public function deleteLikeReview($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'ynmember_review\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ynmember_review', 'review_id = ' . (int) $iItemId);
    }

    public function getNotificationCommentreview($aNotification)
    {
        $aRow = $this->database()->select('e.review_id, e.title, e.user_id, e.item_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ynmember_review'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.review_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['review_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = $sUsers._p(' commented ').Phpfox::getService('user')->gender($aRow['gender'])._p(' own review ').'"'.$sTitle.'"';
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = $sUsers._p(' commented on your review ').'"'.$sTitle.'"';
        }
        else
        {
            $sPhrase = $sUsers._p(' commented ').'<span class="drop_data_user">'.$aRow['full_name'].'
        \'s</span> review "'.$sTitle.'"';
        }

        return array(
//            'link' => Phpfox::getLib('url')->permalink('ynmember.review', $aRow['review_id'], $aRow['title']),
            'link' => Phpfox::getLib('url')->makeUrl('ynmember.review', ['user_id' => $aRow['item_id']]),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getNotificationLikereview($aNotification)
    {
        $aRow = $this->database()->select('e.review_id, e.title, e.user_id, e.item_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ynmember_review'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.review_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['review_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = $sUsers._p(' liked ').Phpfox::getService('user')->gender($aRow['gender'])._p(' own review ').'"'.$sTitle.'"';
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = $sUsers._p(' liked your review ').'"'.$sTitle.'"';
        }
        else
        {
            $sPhrase = $sUsers._p(' liked ').'<span class="drop_data_user">'.$aRow['full_name'].'
        \'s</span> review "'.$sTitle.'"';
        }

        return array(
//            'link' => Phpfox::getLib('url')->permalink('ynmember.review', $aRow['review_id'], $aRow['title']),
            'link' => Phpfox::getLib('url')->makeUrl('ynmember.review', ['user_id' => $aRow['item_id']]),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getNotificationFollow_Action($aNotification)
    {
        $iUserId = $aNotification['owner_user_id'];
        $aFollowNotification = $this->database()->select('f.*, ' . Phpfox::getUserField()) // fake feed
            ->from(\Phpfox::getT('ynmember_follow_notification'), 'f')
            ->join(\Phpfox::getT('user'), 'u', 'u.user_id = ' . $iUserId)
            ->where(['f.follow_notification_id' => $aNotification['item_id']])
            ->executeRow();

        if (empty($aFollowNotification['follow_notification_id']))
            return false;

        $aFollowNotification['feed_id'] = $aFollowNotification['follow_notification_id'];
        $feed_params = json_decode($aFollowNotification['feed_params'], true);
        $aFollowNotification = array_merge($aFollowNotification, $feed_params);
        unset($aFollowNotification['feed_params']);

        $iItemType = $aFollowNotification['type_id'];

        // this action have no call back
        if (!Phpfox::hasCallback($iItemType, 'getActivityFeed')) {
            return false;
        }

        $aFeed = Phpfox::callback($iItemType . '.getActivityFeed', $aFollowNotification, null);
        if (!$aFeed) {
            return false;
        }
        if (empty($aFeed['feed_info'])) {
            $aFeed['feed_info'] = '';
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);

        // Process title
        $sTitle = '';
        if (!empty($aFeed['feed_title'])) {
            $sTitle = ' "' . $aFeed['feed_title'] . '"';
        } else if (!empty($aFeed['feed_status'])) {
            $sTitle = ' "' . $aFeed['feed_status'] . '"';
        }
        // Process info
        if (!empty($aFeed['feed_info'])) {
            $sInfo = strip_tags(rtrim($aFeed['feed_info'], '.'));
        } else {
            $sInfo = _p('shared') . ' ';
        }

        return array(
            'link' => $aFeed['feed_link'],
            'message' => $sUsers . ' ' . $sInfo . $sTitle,
            'icon' => !empty($aFeed['feed_icon']) ? $aFeed['feed_icon'] : ''
        );
    }

    public function getNotificationReview_Write($aNotification)
    {
        $aRow = $this->database()->select('*')
            ->from(Phpfox::getT('ynmember_review'))
            ->where('review_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['review_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
//        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aRow['rating'] == 1)
            $sPhrase = $sUsers. ' ' . _p('wrote_a_review_for_you_rating_1_star');
        else
            $sPhrase = $sUsers. ' ' . _p('wrote_a_review_for_you_rating_number_stars', ['number' => $aRow['rating']]);

        return array(
//            'link' => Phpfox::getLib('url')->permalink('ynmember.review', $aRow['review_id'], $aRow['title']),
            'link' => Phpfox::getLib('url')->makeUrl('ynmember.review', ['user_id' => $aRow['item_id']]),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getProfileSettings()
    {
        return array(
            'ynmember.rate' => array(
                'phrase' => _p('Rate your profile'),
                'default' => '0'
            ),
            'ynmember.follow' => array(
                'phrase' => _p('Get notification from you'),
                'default' => '0'
            )
        );
    }

    /**
     * @return array
     */
    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
        return array(
            _p('Advanced Member Reviews') => $aUser['activity_ynmember_review']
        );
    }

    /**
     * Action to take when user cancelled their account
     * @param int $iUser
     * @throws \Exception
     */
    public function onDeleteUser($iUser)
    {
        (($sPlugin = Phpfox_Plugin::get('ynmember.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);

        // Delete related information
        db()->delete(Phpfox::getT('ynmember_birthday_wish'), 'item_id = ' . $iUser);
        db()->delete(Phpfox::getT('ynmember_follow'), 'item_id = ' . $iUser);
        db()->delete(Phpfox::getT('ynmember_follow_notification'), 'feed_params LIKE \'' . '"user_id":' . $iUser . '\'');
        db()->delete(Phpfox::getT('ynmember_mod'), 'user_id = ' . $iUser);
        db()->delete(Phpfox::getT('ynmember_place'), 'user_id = ' . $iUser);
        db()->delete(Phpfox::getT('ynmember_review_useful'), 'user_id = ' . $iUser);

        // Get reviews that this user review for and this user was reviewed by others
        $aRows = db()
            ->select('review_id')
            ->from(':ynmember_review')
            ->where(sprintf('user_id = %1$s OR item_id = %1$s', $iUser))
            ->execute('getRows');

        foreach($aRows as $aId) {
            Phpfox::getService('ynmember.review.process')->delete($aId['review_id']);
            $aReviewIds[] = $aId['review_id'];
        }
        if (!empty($aReviewIds)) {
            $sReviewIds = implode(',', $aReviewIds);
            db()->delete(Phpfox::getT('ynmember_review_useful'), 'review_id IN (' . $sReviewIds . ')');
            db()->delete(Phpfox::getT('ynmember_custom_value'), 'review_id IN (' . $sReviewIds . ')');
        }

        (($sPlugin = Phpfox_Plugin::get('ynmember.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
    }
}