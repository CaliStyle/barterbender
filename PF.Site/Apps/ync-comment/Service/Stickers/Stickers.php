<?php

namespace Apps\YNC_Comment\Service\Stickers;

use Phpfox;
use Phpfox_Service;


class Stickers extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynccomment_sticker_set');
    }

    public function getForAdmin()
    {
        $sCacheId = $this->cache()->set('ynccomment_sticker_set_admin');
        $this->cache()->group('ynccomment', $sCacheId);
        if (!($aRows = $this->cache()->get($sCacheId))) {
            $aRows = db()->select('ss.*,s.image_path,s.server_id')
                ->from($this->_sTable, 'ss')
                ->leftJoin(':ynccomment_stickers', 's', 'ss.thumbnail_id = s.sticker_id')
                ->order('ss.ordering ASC')
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aRows);
        }
        foreach ($aRows as $key => $aRow) {
            if (!empty($aRow['thumbnail_id'])) {
                $this->getStickerImage($aRows[$key]);
            }
        }
        return $aRows;
    }

    /**
     * @param $iId
     * @return array|int|string
     */
    public function countStickers($iId)
    {
        return db()->select('total_sticker')
            ->from($this->_sTable)
            ->where('set_id =' . (int)$iId)
            ->execute('getField');
    }

    /**
     * @param $iId
     * @return array|int|string
     */
    public function getForEdit($iId)
    {
        return db()->select('*')
            ->from($this->_sTable)
            ->where('set_id =' . (int)$iId)
            ->execute('getRow');
    }

    /**
     * @return array|int|string
     */
    public function countDefaultSet()
    {
        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('is_default = 1')
            ->execute('getField');
    }

    /**
     * @param $iUserId
     * @return array|int|string
     */
    public function getRecentSticker($iUserId)
    {
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }
        //Get 40 recent stickers
        $aRecent = Phpfox::getService('ynccomment.tracking')->getTracking($iUserId, 'sticker', null, 40);
        foreach ($aRecent as $iKey => $aSet) {
            $this->getStickerImage($aRecent[$iKey]);
        }
        return $aRecent;
    }

    /**
     * @param $iUserId
     * @param null $iLimitSticker
     * @return array|int|string
     */
    public function getAllStickerSetByUser($iUserId, $iLimitSticker = null)
    {
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }
        $iCnt = db()->select('COUNT(*)')
            ->from(':ynccomment_user_sticker_set', 'uss')
            ->join($this->_sTable, 'ss', 'ss.set_id = uss.set_id')
            ->where('uss.user_id = ' . (int)$iUserId)
            ->execute('getField');
        $aCache = storage()->get('ynccomment_user_sticker_set_' . $iUserId);
        $bFirstTime = empty($aCache);
        //If first time, get default sticker
        if (!$iCnt && $bFirstTime) {
            $aSets = db()->select('ss.*, s.image_path, s.server_id')
                ->from($this->_sTable, 'ss')
                ->join(':ynccomment_stickers', 's', 's.sticker_id = ss.thumbnail_id AND s.is_deleted = 0')
                ->where('ss.is_default = 1 AND ss.is_active = 1')
                ->order('ss.ordering ASC')
                ->execute('getSlaveRows');
            storage()->set('ynccomment_user_sticker_set_' . $iUserId, $iUserId);
        } else {
            $aSets = db()->select('ss.*, s.image_path, s.server_id')
                ->from(':ynccomment_user_sticker_set', 'uss')
                ->join($this->_sTable, 'ss', 'ss.set_id = uss.set_id AND ss.is_active = 1')
                ->join(':ynccomment_stickers', 's', 's.sticker_id = ss.thumbnail_id AND s.is_deleted = 0')
                ->where('uss.user_id = ' . (int)$iUserId)
                ->order('uss.time_stamp DESC')
                ->execute('getSlaveRows');
        }
        if (count($aSets)) {
            foreach ($aSets as $iKey => $aSet) {
                if ($bFirstTime) {
                    db()->insert(':ynccomment_user_sticker_set', [
                        'user_id' => $iUserId,
                        'set_id' => $aSet['set_id']
                    ]);

                }
                if ($aSet['thumbnail_id']) {
                    $this->getStickerImage($aSets[$iKey], true);
                }
                $aSets[$iKey]['stickers'] = $this->getStickersBySet($aSet['set_id'], $iLimitSticker);
                $aSets[$iKey]['is_my'] = true;
                $aSets[$iKey]['is_added'] = true;
                if (!count($aSets[$iKey]['stickers'])) {
                    unset($aSet[$iKey]);
                }
            }
        }
        return $aSets;
    }

    /**
     * @param $iId
     * @param null $iLimit
     * @return array|int|string
     */
    public function getStickersBySet($iId, $iLimit = null)
    {
        if ($iLimit != null) {
            db()->limit($iLimit);
        }
        $aStickers = db()->select('*')
            ->from(':ynccomment_stickers')
            ->where('is_deleted = 0 AND set_id =' . (int)$iId)
            ->order('ordering ASC')
            ->execute('getSlaveRows');
        if ($aStickers) {
            foreach ($aStickers as $key => $aSticker) {
                $this->getStickerImage($aStickers[$key]);
            }
        }
        return $aStickers;
    }

    public function getStickerById($iId)
    {
        if (!$iId) {
            return false;
        }
        $aSticker = db()->select('*')
            ->from(':ynccomment_stickers')
            ->where('sticker_id =' . (int)$iId)
            ->order('ordering ASC')
            ->execute('getRow');
        if ($aSticker) {
            $this->getStickerImage($aSticker);
        }
        return $aSticker;
    }

    public function getAllSticker($iUserId, $iLimitSticker = null)
    {
        $sCacheId = $this->cache()->set('ynccomment_sticker_set_browse');
        $this->cache()->group('ynccomment', $sCacheId);
        if (!($aRows = $this->cache()->get($sCacheId))) {
            $aRows = db()->select('ss.*,s.image_path,s.server_id')
                ->from($this->_sTable, 'ss')
                ->join(':ynccomment_stickers', 's', 'ss.thumbnail_id = s.sticker_id AND s.is_deleted = 0')
                ->where('ss.is_active = 1')
                ->order('ss.ordering ASC')
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aRows);
        }
        if (count($aRows)) {
            foreach ($aRows as $iKey => $aSet) {
                $this->getStickerImage($aRows[$iKey]);
                $aRows[$iKey]['stickers'] = $this->getStickersBySet($aSet['set_id'], $iLimitSticker);
                $aRows[$iKey]['is_added'] = $this->checkIsAddedSet($aSet['set_id'], $iUserId);
            }
        }
        return $aRows;
    }

    public function checkIsAddedSet($iSetId, $iUserId)
    {
        return db()->select('COUNT(*)')
            ->from(':ynccomment_user_sticker_set', 'uss')
            ->where('uss.user_id = ' . (int)$iUserId . ' AND uss.set_id = ' . (int)$iSetId)
            ->execute('getField');
    }

    public function getStickerSetById($iSetId, $iLimitSticker = null)
    {
        if (!$iSetId) {
            return false;
        }
        $aSet = db()->select('ss.*,s.image_path,s.server_id')
            ->from($this->_sTable, 'ss')
            ->join(':ynccomment_stickers', 's', 'ss.thumbnail_id = s.sticker_id AND s.is_deleted = 0')
            ->where('ss.set_id =' . (int)$iSetId)
            ->order('ss.ordering ASC')
            ->execute('getRow');
        if ($aSet) {
            $this->getStickerImage($aSet);
            $aSet['stickers'] = $this->getStickersBySet($iSetId, $iLimitSticker);
        }
        return $aSet;
    }

    public function countActiveStickerSet()
    {
        $sCacheId = $this->cache()->set('ynccomment_sticker_active_count');
        $this->cache()->group('ynccomment', $sCacheId);
        $iCount = $this->cache()->get($sCacheId);
        if ($iCount === false) {
            $iCount = db()->select('COUNT(*) as total')
                ->from(':ynccomment_sticker_set', 'uss')
                ->where('uss.is_active = 1')
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId,$iCount);
        }
        return $iCount;
    }

    public function getStickerImage(&$aSticker, $noCanvas = false) {
        if (!empty($aSticker['image_path'])) {
            if ($aSticker['view_only']) {
                $aSticker['full_path'] = '<img src="'.Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-comment/assets/images/stickers/set_'.$aSticker['set_id'].'/'.$aSticker['image_path'].'" class="'.($noCanvas?'':'ync_comment_gif').'"/>';
            } else {
                $aSticker['full_path'] = Phpfox::getLib('image.helper')->display([
                    'server_id' => $aSticker['server_id'],
                    'path' => 'core.url_pic',
                    'file' => 'ynccomment/'.$aSticker['image_path'],
                    'suffix' => '',
                    'class' => ($noCanvas?'':'ync_comment_gif')
                ]);
            }
        }
    }
}