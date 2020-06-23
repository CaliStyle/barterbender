<?php
namespace Apps\P_AdvMarketplace\Service\Rate;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;


class Process extends Phpfox_Service
{
    /**
     * @param $iReviewId
     * @return bool
     */
    public function deleteReview($iReviewId)
    {
        phpfox::isUser(true);
        $rate = phpfox::getLib('database')->select('user_id, listing_id')
            ->from(phpfox::getT('advancedmarketplace_rate'))
            ->where('rate_id = ' . $iReviewId)
            ->execute('getRow');

        if (($rate['user_id'] == phpfox::getUserId() && phpfox::getUserParam('advancedmarketplace.can_delete_own_review')) || phpfox::getUserParam('advancedmarketplace.delete_other_reviews')) {
            db()->delete(phpfox::getT('advancedmarketplace_rate'), "rate_id = ". (int)$iReviewId);
            db()->updateCount('advancedmarketplace_rate',['listing_id' => $rate['listing_id']['listing_id']],'total_rate','advancedmarketplace','listing_id = '. (int)$rate['listing_id']);
            $averageScore = $this->database()->select('AVG(rating) AS average_score')
                ->from(Phpfox::getT('advancedmarketplace_rate'))
                ->where('listing_id = ' . (int)$rate['listing_id'])
                ->execute('getSlaveField');
            $totalScore = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($averageScore, true);
            db()->update(phpfox::getT('advancedmarketplace'),[
                'total_score' => $totalScore,
            ], 'listing_id = ' . $rate['listing_id']);
            return true;
        }
        return false;
    }

    /**
     * @param $aVals
     * @return int
     */
    public function addReview($aVals) {
        $table = Phpfox::getT('advancedmarketplace_rate');
        $userId = Phpfox::getUserId();
        $count = db()->select('COUNT(*)')
                    ->from($table)
                    ->where('listing_id = '. (int)$aVals['listing_id'] .' AND user_id = '. $userId)
                    ->execute('getSlaveField');
        if($count) {
            return false;
        }

        $reviewId = db()->insert($table, [
            'listing_id' => $aVals['listing_id'],
            'user_id' => $userId,
            'timestamp' => PHPFOX_TIME,
            'rating' => $aVals['rating'] * 2,
            'content' => Phpfox::getLib('parse.input')->clean($aVals['text'])
        ]);
        db()->updateCount('advancedmarketplace_rate',['listing_id' => $aVals['listing_id']],'total_rate','advancedmarketplace','listing_id = '. (int)$aVals['listing_id']);
        $averageScore = db()->select('AVG(rating) AS average_score')
            ->from($table)
            ->where('listing_id = '. (int)$aVals['listing_id'])
            ->execute('getSlaveField');
        $totalScore = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($averageScore, true);
        db()->update(Phpfox::getT('advancedmarketplace'),['total_score' => $totalScore], 'listing_id = '. (int)$aVals['listing_id']);
        return $reviewId;
    }
}