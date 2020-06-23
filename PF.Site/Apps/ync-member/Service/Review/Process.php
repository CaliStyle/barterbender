<?php

namespace Apps\YNC_Member\Service\Review;

use Phpfox;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Process extends Phpfox_Service
{
    protected $_sTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynmember_review');
    }

    public function add($aVals)
    {
        (($sPlugin = \Phpfox_Plugin::get('ynmember.service_review_process_add__start')) ? eval($sPlugin) : false);

        $oFilter = Phpfox::getLib('parse.input');

        if (!isset($aVals['privacy']))
        {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment']))
        {
            $aVals['privacy_comment'] = 0;
        }

        $aInsert = [
            'user_id' => Phpfox::getUserId(),
            'title' => $oFilter->clean($aVals['title'], 255),
            'rating' => $aVals['rating'],
            'time_stamp' => PHPFOX_TIME,
            'time_update' => PHPFOX_TIME,
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'item_id' => $aVals['user_id'],
            'text' => Phpfox::getParam('core.allow_html') ? $oFilter->prepare($aVals['text']) : $oFilter->clean($aVals['text']),
        ];

        $iId = $this->database()->insert($this->_sTable, $aInsert);

        // update review stats to user field table
        $this->updateReviewStats($aVals['user_id']);

        // FEED AND NOTIFICATION
        if ($iId) {
            if (Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) {
                ((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? $iFeedId = Phpfox::getService('feed.process')->add('ynmember_review', $iId, (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0), 0, 0) : null);
            }
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'ynmember_review', '+');

            if ($aInsert['user_id'] != $aInsert['item_id']) {
                Phpfox::getService('notification.process')->add('ynmember_review_write', $iId, $aVals['user_id']);
            }
        }

        (($sPlugin = \Phpfox_Plugin::get('ynmember.service_review_process_add__end')) ? eval($sPlugin) : false);

        return $iId;
    }

    public function update($aVals)
    {
        (($sPlugin = \Phpfox_Plugin::get('ynmember.service_review_process_update__start')) ? eval($sPlugin) : false);

        $oFilter = Phpfox::getLib('parse.input');

        $aUpdate = [
            'title' => $oFilter->clean($aVals['title'], 255),
            'rating' => $aVals['rating'],
            'time_update' => PHPFOX_TIME,
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'text' => Phpfox::getParam('core.allow_html') ? $oFilter->prepare($aVals['text']) : $oFilter->clean($aVals['text']),
        ];

        $this->database()->update($this->_sTable, $aUpdate, 'review_id = ' . (int) $aVals['review_id']);
        $this->updateReviewStats($aVals['user_id']);

        (($sPlugin = \Phpfox_Plugin::get('ynmember.service_review_process_update__end')) ? eval($sPlugin) : false);

        return $aVals['review_id'];
    }

    public function delete($reviewId)
    {
        $aReviewRow = $this->database()
            ->select('*')
            ->from($this->_sTable)
            ->where('review_id = ' . $reviewId)
            ->executeRow()
        ;
        $this->database()->delete($this->_sTable, 'review_id = ' . $reviewId);
        Phpfox::getService('user.activity')->update($aReviewRow['user_id'], 'ynmember_review', '-');
        $this->updateReviewStats($aReviewRow['item_id']);
    }

    public function vote($aVals)
    {
        $sTable = Phpfox::getT('ynmember_review_useful');
        $iUserId = Phpfox::getUserId();

        $iReviewUsefulId = $this->database()
            ->select('review_useful_id')
            ->from($sTable)
            ->where('user_id = ' . $iUserId . ' AND review_id = ' . $aVals['review_id'])
            ->limit(1)
            ->executeField();

        if ($iReviewUsefulId) {
            $aUpdate = [
                'positive' => $aVals['positive']
            ];
            $this->database()->update($sTable, $aUpdate, 'review_useful_id = ' . $iReviewUsefulId);
        } else {
            $aInsert = [
                'user_id' => $iUserId,
                'review_id' => $aVals['review_id'],
                'positive' => $aVals['positive'],
                'time_stamp' => PHPFOX_TIME
            ];
            $this->database()->insert($sTable, $aInsert);
        }
    }

    public function updateReviewStats($iUserId)
    {
        // calculate rating
        $aRatingRow = $this->database()->select('SUM(rating)/COUNT(review_id) as rating, COUNT(review_id) as total_review')
            ->from($this->_sTable)
            ->where('item_id = ' . $iUserId)
            ->execute('getSlaveRow');
        $fRating = $aRatingRow['rating'] ? $aRatingRow['rating'] : 0;
        $iTotalReview = $aRatingRow['total_review'] ? $aRatingRow['total_review'] : 0;
        $this->database()->update(Phpfox::getT('user_field'), array('ynmember_rating' => $fRating), 'user_id = ' . $iUserId);
        $this->database()->update(Phpfox::getT('user_field'), array('ynmember_total_review' => $iTotalReview), 'user_id = ' . $iUserId);
    }
}