<?php

namespace Apps\YNC_Comment\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class EditHistory extends Phpfox_Component
{
    public function process()
    {
        $iCommentId = $this->getParam('comment_id');
        if (!$iCommentId) {
            return false;
        }
        $aEditHistory = Phpfox::getService('ynccomment.history')->getEditHistory($iCommentId);
        if (!$aEditHistory) {
            return false;
        }
        $this->template()->assign([
            'aEditHistory' => $aEditHistory
        ]);
        return 'block';
    }
}