<?php
/**
 * Created by PhpStorm.
 * User: phuong
 * Date: 2/19/17
 * Time: 10:58 AM
 */

namespace Apps\YNC_Member\Service\Review;

use Phpfox;
use Phpfox_Service;

class Review extends Phpfox_Service
{

    public function canWriteReview($iUserId, $bRedirect = false)
    {
        if (!Phpfox::getService('user.privacy')->hasAccess($iUserId, 'ynmember.rate')){
            return false;
        }
        if (Phpfox::getUserId() == $iUserId) {
            return user('ynmember_add_review_self');
        } else {
            return user('ynmember_add_review_others');
        }
    }

    public function getForEdit($iReviewId = 0)
    {
        $aRow = $this->database()->select('*')
            ->from(Phpfox::getT('ynmember_review'))
            ->where('review_id = ' . (int) $iReviewId)
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function getReview($iReviewId)
    {
        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')
                ->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = re.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynmember_review\' AND l.item_id = re.review_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('re.*, COUNT(CASE WHEN useful.positive = 1 THEN 1 ELSE NULL END) as total_yes, COUNT(CASE WHEN useful.positive = 0 THEN 1 ELSE NULL END) as total_no')
            ->from(Phpfox::getT('ynmember_review'), 're')
            ->leftJoin(Phpfox::getT('ynmember_review_useful'), 'useful', 're.review_id = useful.review_id')
            ->where('re.review_id = '. (int) $iReviewId)
            ->executeRow();

        if (!isset($aRow['is_friend'])) {
            $aRow['is_friend'] = 0;
        }

        $this->processRow($aRow);
        $aRow['bookmark_url'] =  Phpfox::permalink('ynmember.review', $aRow['review_id'], $aRow['title']);
        return $aRow;
    }

    public function processRow(&$aRow)
    {
        $bVoteYes = $this->database()
            ->select('review_id')
            ->from(Phpfox::getT('ynmember_review_useful'))
            ->where('user_id = ' . Phpfox::getUserId() . ' AND review_id = ' . $aRow['review_id'] . ' AND positive = 1')
            ->limit(1)
            ->executeField();

        $bVoteNo = $this->database()
            ->select('review_id')
            ->from(Phpfox::getT('ynmember_review_useful'))
            ->where('user_id = ' . Phpfox::getUserId() . ' AND review_id = ' . $aRow['review_id'] . ' AND positive = 0')
            ->limit(1)
            ->executeField();

        // helpful for main page
        if($this->search()->get('form_flag') != 1 && !$this->request()->get('sort') && !$this->request()->get('when') && !$this->request()->get('search')) {
            $aUsefulInfo = $this->database()->select('COUNT(CASE WHEN positive = 1 THEN 1 ELSE NULL END) as total_yes, COUNT(CASE WHEN positive = 0 THEN 1 ELSE NULL END) as total_no')
                ->from(Phpfox::getT('ynmember_review_useful'))
                ->where('review_id = ' . $aRow['review_id'])
                ->executeRow();
                ;

            $aRow['total_yes'] = $aUsefulInfo['total_yes'];
            $aRow['total_no'] = $aUsefulInfo['total_no'];
        }

        $aRow['is_vote_yes'] = $bVoteYes ? 1 : 0;
        $aRow['is_vote_no'] = $bVoteNo ? 1 : 0;

        $aUser = Phpfox::getService('user')->get($aRow['item_id']);
        Phpfox::getService('ynmember.member')->processUser($aUser);
        $aRow['aUser'] = $aUser;
        $aRow['aReviewer'] = Phpfox::getService('user')->get($aRow['user_id']);

        $aRow['aFeed'] = [
            'comment_type_id' => 'ynmember_review',
            'privacy' => $aRow['privacy'],
            'comment_privacy' => $aRow['privacy_comment'],
            'like_type_id' => 'ynmember_review',
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_is_friend' => 0,
            'item_id' => $aRow['review_id'],
            'user_id' => $aRow['user_id'],
            'total_comment' => $aRow['total_comment'],
            'total_like' => $aRow['total_like'],
            'feed_link' => Phpfox::getLib('url')->makeUrl('ynmember.review', ['user_id'=>$aRow['item_id']]),
            'feed_title' => $aRow['title'],
            'feed_display' => 'view',
            'feed_total_like' => $aRow['total_like'],
            'report_module' => 'ynmember_review',
            'report_phrase' => _p('Report this review'),
            'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp'])
        ];
    }

    public function getMyReviewFor($iUserId) {
        return $this->database()->select('*')
            ->from(Phpfox::getT('ynmember_review'))
            ->where(['item_id' => $iUserId, 'user_id' => Phpfox::getUserId()])
            ->execute('getSlaveRow');
    }

    public function isWrittenReviewFor($iUserId) {
        return empty($this->getMyReviewFor($iUserId)) ? false : true;
    }

}