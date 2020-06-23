<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/16/16
 * Time: 11:21 AM
 */

namespace Apps\YouNet_UltimateVideos\Service;


use Phpfox;
use Phpfox_Service;

/**
 * Class History
 * @package Apps\YouNet_UltimateVideos\Service
 */
class History extends Phpfox_Service
{
    /**
     * History constructor.
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_history');
    }

    /**
     * @param int $iUserId
     * @param string $iItemType
     * @param string $iItemId
     * @return bool
     */
    private function _add($iUserId, $iItemType, $iItemId)
    {
        if (!$iItemId || !$iUserId)
            return false;

        $historyId = $this->database()
            ->select('history_id')
            ->from($this->_sTable, 'history')
            ->where(strtr('user_id=:user AND item_type=\':type\' AND item_id=:id', [
                ':user' => intval($iUserId),
                ':type' => $iItemType,
                ':id' => intval($iItemId),
            ]))->execute('getSlaveField');

        if ($historyId) {
            $this->database()->update($this->_sTable, [
                'time_stamp' => time(),
            ], 'history_id=' . intval($historyId));
        } else {
            $this->database()->insert($this->_sTable, [
                'user_id' => $iUserId,
                'item_id' => $iItemId,
                'item_type' => $iItemType,
                'time_stamp' => time(),
            ]);
        }
        return true;
    }

    /**
     * @param $iUserId
     * @param $iVideoId
     * @return bool
     */
    public function addVideo($iUserId, $iVideoId)
    {
        return $this->_add($iUserId, 0, $iVideoId);
    }

    /**
     * @param $iUserId
     * @param $iPlaylistId
     */
    public function addPlaylist($iUserId, $iPlaylistId)
    {
        $this->_add($iUserId, 1, $iPlaylistId);
    }

    private function _delete($iUserId, $iItemType, $iItemId)
    {
        if (!$iItemId || !$iUserId)
            return false;
        $historyId = $this->database()
            ->select('history_id')
            ->from($this->_sTable, 'history')
            ->where(strtr('user_id=:user AND item_type=\':type\' AND item_id=:id', [
                ':user' => intval($iUserId),
                ':type' => $iItemType,
                ':id' => intval($iItemId),
            ]))->execute('getSlaveField');
        if ($historyId) {
            $this->database()->delete($this->_sTable, 'history_id =' . (int)$historyId);
        }
        return true;
    }

    /**
     * @param $iUserId
     * @param $iVideoId
     * @return bool
     */
    public function deleteVideo($iUserId, $iVideoId)
    {
        return $this->_delete($iUserId, 0, $iVideoId);
    }

    /**
     * @param $iUserId
     * @param $iPlaylistId
     * @return bool
     */
    public function deletePlaylist($iUserId, $iPlaylistId)
    {
        return $this->_delete($iUserId, 1, $iPlaylistId);
    }

    public function deleteAllHistory($iItemType)
    {
        $this->database()->delete($this->_sTable, 'user_id = ' . (int)Phpfox::getUserId() . ' AND item_type =\'' . $iItemType . '\'');
        return true;
    }
}