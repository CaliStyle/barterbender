<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/16/16
 * Time: 1:42 PM
 */

namespace Apps\YouNet_UltimateVideos\Service;


use Phpfox;
use Phpfox_Service;

/**
 * Class Favourite
 * @package Apps\YouNet_UltimateVideos\Service
 */
class Favourite extends Phpfox_Service
{

    /**
     * Favourite constructor.
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_favorites');
    }

    /**
     * @param $iUserId
     * @param $iVideoId
     *
     * @return int | null
     */
    public function findId($iUserId, $iVideoId)
    {
        return (int)$this->database()->select('favorite_id')
            ->from($this->_sTable)
            ->where(strtr('user_id=:user and video_id=:video', [
                ':user' => intval($iUserId),
                ':video' => intval($iVideoId),
            ]))
            ->execute('getSlaveField');
    }

    /**
     * @param $iUserId
     * @param $iVideoId
     * @return bool
     */
    public function add($iUserId, $iVideoId)
    {
        $id = $this->findId($iUserId, $iVideoId);

        if (!$id) {
            $this->database()->insert($this->_sTable, [
                'user_id' => intval($iUserId),
                'video_id' => intval($iVideoId),
                'time_stamp' => time(),
            ]);

            $this->updateTotalFavorite($iVideoId);
        }

        return true;
    }

    /**
     * @param int $iUserId
     * @param int $iVideoId
     * @return bool
     */
    public function delete($iUserId, $iVideoId)
    {
        $id = $this->findId($iUserId, $iVideoId);

        if ($id) {
            $this->database()->delete($this->_sTable, 'favorite_id=' . intval($id));

            $this->updateTotalFavorite($iVideoId);
        }

        return true;
    }

    public function updateTotalFavorite($iVideoId)
    {
        $this->database()->update(Phpfox::getT('ynultimatevideo_videos'), [
            'total_favorite' => $this->getVideoFavoriteTotal($iVideoId),
        ], 'video_id=' . intval($iVideoId));
    }

    /**
     * @param int $iUserId
     * @param int $iVideoId
     * @return bool
     */
    public function isFavorite($iUserId, $iVideoId)
    {
        return $this->findId($iUserId, $iVideoId) != 0;
    }

    /**
     * @param $iVideoId
     * @return int
     */
    public function getVideoFavoriteTotal($iVideoId)
    {
        return intval(
            $this->database()->select('count(1)')
                ->from(Phpfox::getT('ynultimatevideo_favorites'))
                ->where('video_id=' . intval($iVideoId))
                ->execute('getSlaveField'));
    }

    public function deleteAllFavorite()
    {
        $this->database()->delete($this->_sTable, 'user_id = ' . (int)Phpfox::getUserId());
        return true;
    }
}