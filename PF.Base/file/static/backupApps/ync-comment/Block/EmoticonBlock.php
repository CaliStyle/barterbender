<?php

namespace Apps\YNC_Comment\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class EmoticonBlock extends Phpfox_Component
{
    public function process()
    {
        $iFeedId = $this->getParam('feed_id', 0);
        $iParentId = $this->getParam('parent_id', 0);
        $iEditId = $this->getParam('edit_id', 0);
        if (!$iFeedId && !$iParentId && !$iEditId) {
            return false;
        }
        $this->template()->assign([
            'iFeedId' => $iFeedId,
            'iParentId' => $iParentId,
            'iEditId' => $iEditId,
            'bIsReply' => $iParentId > 0 ? "true" : "false",
            'bIsEdit' => $iEditId > 0 ? "true" : "false",
            'aEmoticons' => Phpfox::getService('ynccomment.emoticon')->getAll(),
            'aRecentEmoticons' => Phpfox::getService('ynccomment.emoticon')->getRecentEmoticon()
        ]);
        return 'block';
    }
}