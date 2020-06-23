<?php

namespace Apps\YNC_Reaction\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class ReactionDisplayBlock extends Phpfox_Component
{
    public function process()
    {
        $aFeed = $this->getParam('aFeed');
        if (!$aFeed || empty($aFeed['feed_display']) || !in_array($aFeed['feed_display'], ['view', 'mini'])) {
            return false;
        }
        Phpfox::getService('yncreaction')->getReactionsPhrase($aFeed);
        $aFeed['is_detail_item'] = true;
        $this->template()->assign([
            'aFeed' => $aFeed
        ]);
    }
}