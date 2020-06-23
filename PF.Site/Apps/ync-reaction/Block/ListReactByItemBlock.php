<?php

namespace Apps\YNC_Reaction\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class ListReactByItemBlock extends Phpfox_Component
{
    public function process()
    {
        $sType = $this->getParam('type');
        $iItemId = $this->getParam('item_id');
        $iReactId = $this->getParam('react_id', 0);

        if (empty($sType) || !$iItemId) {
            return false;
        }
        $sPrefix = $this->getParam('table_prefix');
        list($iTotalReacted, $aListReacted) = Phpfox::getService('yncreaction')->getMostReaction($sType, $iItemId,
            $sPrefix);
        $this->template()->assign(array(
                'iCnt' => 0,
                'bIsPaging' => $this->getParam('ajax_paging', 0),
                'aListReacted' => $aListReacted,
                'iTotalReacted' => $iTotalReacted,
                'iReactId' => $iReactId,
                'iItemId' => $iItemId,
                'sType' => $sType,
                'sPrefix' => $sPrefix
            )
        );
        return 'block';
    }
}