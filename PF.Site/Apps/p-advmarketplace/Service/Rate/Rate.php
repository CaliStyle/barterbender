<?php
namespace Apps\P_AdvMarketplace\Service\Rate;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;


class Rate extends Phpfox_Service
{
    /**
     * Rate constructor.
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedmarketplace_rate');
    }

    public function getReviewPermission(&$row) {
        $row['can_delete_own_review'] = $row['can_delete_all_review'] = $row['can_do_action_review'] = false;
        if(!empty($row['rate_id'])) {
            $row['can_delete_own_review'] = ($row['review_user_id'] == Phpfox::getUserId() && phpfox::getUserParam('advancedmarketplace.can_delete_own_review'));
            $row['can_delete_all_review'] = phpfox::getUserParam('advancedmarketplace.delete_other_reviews');
            $row['can_do_action_review'] = $row['can_delete_own_review'] || $row['can_delete_all_review'];
        }
    }

    /**
     * @param $reviewId
     * @return bool
     */
    public function getReview($reviewId, $getMoreInfo = false)
    {
        if(empty($reviewId)) {
            return false;
        }
        $select = 'ar.*, ar.user_id AS review_user_id';
        if($getMoreInfo) {
            $select .= ', '. Phpfox::getUserField();
            db()->join(Phpfox::getT('user'), 'u', 'u.user_id = ar.user_id');
        }
        $row = db()->select($select)
                ->from($this->_sTable, 'ar')
                ->where('ar.rate_id = '. (int)$reviewId)
                ->execute('getSlaveRow');
        if($getMoreInfo && !empty($row)) {
            $row['rating'] = $row['rating'] / 2;
            list($row['rating_star'],) = $this->parseRatingStar($row['rating']);
            $row['author_url'] = \Phpfox_Url::instance()->makeUrl('profile.'. $row['user_name']);
            $row['review_time'] = Phpfox::getLib('date')->convertTime($row['timestamp']);
            $this->getReviewPermission($row);
        }
        return $row;
    }

    /**
     * @param $listingId
     * @param $page
     * @param int $limit
     * @return array
     */
    public function getReviewers($listingId, $page, $limit = 10)
    {
        $rows = [];
        $count = db()->select('COUNT(*)')
            ->from(Phpfox::getT('advancedmarketplace_rate'),'ar')
            ->join(Phpfox::getT('user'),'u', 'u.user_id = ar.user_id')
            ->where('ar.listing_id = '. (int)$listingId)
            ->execute('getSlaveField');
        if($count) {
            $rows = db()->select('ar.*, ar.user_id AS review_user_id, '. Phpfox::getUserField())
                ->from(Phpfox::getT('advancedmarketplace_rate'),'ar')
                ->join(Phpfox::getT('user'),'u', 'u.user_id = ar.user_id')
                ->where('ar.listing_id = '. (int)$listingId)
                ->order('ar.timestamp DESC')
                ->limit($page, $limit)
                ->execute('getSlaveRows');
            foreach($rows as $key => $row) {
                $row['review_time'] = Phpfox::getLib('date')->convertTime($row['timestamp']);
                $row['rating'] = $row['rating'] / 2;
                list($row['rating_star'],) = $this->parseRatingStar($row['rating']);
                $row['author_url'] = \Phpfox_Url::instance()->makeUrl('profile', [$row['user_name']]);
                $this->getReviewPermission($row);
                $rows[$key] = $row;
            }
        }
        return [$rows, (int)$count];
    }

    /**
     * @param $score
     * @param bool $getOnlyScore
     * @return array|float|int
     */
    public function parseRatingStar($score, $getOnlyScore = false)
    {
        return $this->_parseRatingStar($score, $getOnlyScore);
    }

    private function _parseRatingStar($score, $getOnlyScore) {
        $averageScore = round($score, 1);
        $decimal = $averageScore - (int)$averageScore;
        if($decimal == 0.5) {
            $integer = (int)$averageScore;
        }else {
            $integer = round($averageScore);
            $decimal = 0;
        }
        $ratingStar = '';
        if(!$getOnlyScore) {
            for($i=1;$i <= 5;$i++) {
                if($i <= $integer) {
                    $ratingStar .= '<i class="ico ico-star"></i>';
                }
                else {
                    $ratingStar .= '<i class="ico ico-star ' . (($integer + 1) == $i && $decimal == 0.5 ? 'half-star' : 'disable') .' "></i>';
                }
            }
        }

        return $getOnlyScore ? ($integer + $decimal) : [$ratingStar,($integer + $decimal)];
    }

    public function getAverageScoreAndRatingStar($totalScore = 0)
    {
        $averageScore = !empty($totalScore) ? $this->parseRatingStar($totalScore / 2, true) : 0;
        list($ratingStar,) = $this->parseRatingStar($averageScore);
        return [$averageScore, $ratingStar];
    }
}