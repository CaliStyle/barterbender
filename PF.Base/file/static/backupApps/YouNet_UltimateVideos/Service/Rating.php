<?php
/**
 * Created by PhpStorm.
 * User: hainm
 * Date: 8/18/16
 */

namespace Apps\YouNet_UltimateVideos\Service;


use Phpfox;
use Phpfox_Service;

/**
 * Class History
 * @package Apps\YouNet_UltimateVideos\Service
 */
class Rating extends Phpfox_Service
{
    /**
     * Rating constructor.
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_ratings');
    }

    public function add($iUserId, $iVideoId, $iRate)
    {
        //iRate: 1,2,3,4,5
        if (!$iVideoId || !$iUserId || !$iRate)
            return false;

        $ratingId = $this->database()
            ->select('video_id')
            ->from($this->_sTable, 'rating')
            ->where(strtr('user_id=:user AND video_id=:id', [
                ':user' => intval($iUserId),
                ':id' => intval($iVideoId),
            ]))->execute('getSlaveField');

        if ($ratingId) {
            $this->database()->update($this->_sTable, [
                'time_stamp' => time(), 'rating' => intval($iRate)
            ], 'user_id = ' . $iUserId . ' AND video_id =' . $iVideoId);
        } else {
            $this->database()->insert($this->_sTable, [
                'user_id' => $iUserId,
                'video_id' => $iVideoId,
                'rating' => $iRate,
                'time_stamp' => time(),
            ]);
        }

        $this->database()->update(Phpfox::getT('ynultimatevideo_videos'), [
            'rating' => $this->getAVGRatingOfVideo($iVideoId),
            'total_rating' => $this->getCountRatingOfVideo($iVideoId)
        ], 'video_id=' . intval($iVideoId));

        return true;
    }

    public function getCountRatingOfVideo($iVideoId)
    {
        if (!$iVideoId)
            return false;
        $iCount = $this->database()->select('COUNT(rating)')
            ->from($this->_sTable, 'uvr')
            ->where('uvr.video_id = ' . (int)$iVideoId)
            ->execute('getSlaveField');

        return $iCount;

    }

    public function getAVGRatingOfVideo($iVideoId)
    {
        if (!$iVideoId)
            return false;
        $iAvg = $this->database()->select("AVG(rating)")
            ->from($this->_sTable, 'uvr')
            ->where('uvr.video_id = ' . (int)$iVideoId)
            ->execute("getSlaveField");

        return $iAvg;
    }

    public function getRatingVideoByUser($iUserId, $iVideoId)
    {
        if (!$iVideoId)
            return false;
        $iRating = $this->database()->select('rating')
            ->from($this->_sTable, 'uvr')
            ->where('uvr.video_id = ' . (int)$iVideoId . ' AND uvr.user_id =' . (int)$iUserId)
            ->execute("getSlaveField");
        return $iRating;
    }

    public function getRates($iVideoId, $bGetCount = false, $iPage = 0, $iTotal = null, $bGetViewerRate = false)
    {
        $this->database()->from($this->_sTable, 'ratings');
        $iViewerId = Phpfox::getUserId();

        if ($bGetCount) {
            $this->database()->where('video_id = ' . $iVideoId);
            return $this->database()->select('COUNT(*)')->executeField();
        } else {
            if ($bGetViewerRate) {
                $this->database()->where('video_id = ' . $iVideoId . ' AND ratings.user_id = ' . $iViewerId);
            } else {
                $this->database()->where('video_id = ' . $iVideoId . ' AND ratings.user_id <> ' . $iViewerId);
            }
            $this->database()
                ->join(':user', 'u', 'ratings.user_id = u.user_id');
            if ($iPage) {
                $this->database()->limit($iPage, $iTotal);
            }

            $aRates = $this->database()->select('ratings.rating,' . Phpfox::getUserField())
                ->order('ratings.time_stamp DESC')
                ->executeRows();

            return $aRates;
        }
    }
}