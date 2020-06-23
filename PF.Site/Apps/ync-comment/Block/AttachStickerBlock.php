<?php

namespace Apps\YNC_Comment\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class AttachStickerBlock extends Phpfox_Component
{
    public function process()
    {
        $iFeedId = $this->getParam('feed_id', 0);
        $iParentId = $this->getParam('parent_id', 0);
        $iEditId = $this->getParam('edit_id', 0);
        $bUpdateOpened = $this->getParam('bUpdateOpened',false);
        if (!$iFeedId && !$iParentId && !$iEditId && !$bUpdateOpened) {
            return false;
        }
        $iUserId = Phpfox::getUserId();
        $this->template()->assign([
            'iFeedId' => $iFeedId,
            'iParentId' => $iParentId,
            'iEditId' => $iEditId,
            'bUpdateOpened' => $bUpdateOpened,
            'aStickerSets' => Phpfox::getService('ynccomment.stickers')->getAllStickerSetByUser($iUserId),
            'aRecentStickers' => Phpfox::getService('ynccomment.stickers')->getRecentSticker($iUserId)
        ]);
        return 'block';
    }
}