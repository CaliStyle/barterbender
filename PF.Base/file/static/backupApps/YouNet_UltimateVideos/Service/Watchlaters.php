<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/16/16
 * Time: 11:42 AM
 */

namespace Apps\YouNet_UltimateVideos\Service;

use Phpfox_Service;
use Phpfox;

/**
 * Class Watchlaters
 * @package Apps\YouNet_UltimateVideos\Service
 */
class Watchlaters extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_watchlaters');
    }

    public function findId($iUserId, $iVideoId)
    {
        return (int)$this->database()
            ->select('watchlater_id')
            ->from($this->_sTable)
            ->where(strtr('user_id=:user AND video_id=:video', [
                ':user' => intval($iUserId),
                ':video' => intval($iVideoId),
            ]))
            ->execute('getSlaveField');
    }

    public function add($iUserId, $iVideoId)
    {
        $id = $this->findId($iUserId, $iVideoId);

        if ($id) {
            $this->database()->update($this->_sTable, [
                'time_stamp' => time(),
                'watched' => 0,
                'watched_time' => 0,
            ], 'watchlater_id=' . intval($id));
        } else {
            $this->database()->insert($this->_sTable, [
                'time_stamp' => time(),
                'user_id' => intval($iUserId),
                'video_id' => intval($iVideoId),
                'watched' => 0,
                'watched_time' => 0,
            ]);
        }
    }

    public function updateViewStatus($iUserId, $iVideoId)
    {
        $id = $this->findId($iUserId, $iVideoId);

        if ($id) {
            $this->database()->update($this->_sTable, [
                'time_stamp' => time(),
                'watched' => 1,
                'watched_time' => time(),
            ], 'watchlater_id=' . intval($id));
        }
    }

    public function delete($iUserId, $iVideoId)
    {
        $id = $this->findId($iUserId, $iVideoId);
        if ($id) {
            $this->database()->delete($this->_sTable, 'watchlater_id =' . intval($id));
        }
    }

    public function isWatchLater($iUserId, $iVideoId)
    {
        return $this->findId($iUserId, $iVideoId) != 0;
    }

    public function deleteAllWatchlater()
    {
        $this->database()->delete($this->_sTable, 'user_id = ' . (int)Phpfox::getUserId());
        return true;
    }
}