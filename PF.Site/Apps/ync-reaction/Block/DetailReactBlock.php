<?php

namespace Apps\YNC_Reaction\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class DetailReactBlock extends Phpfox_Component
{
    public function process()
    {
        $sType = $this->getParam('feed_type');
        $iItemId = $this->getParam('item_id');
        $iPageSize = $this->getParam('limit', 10);
        $iPage = $this->getParam('page', 1);
        $iReactId = $this->getParam('react_id', 0);
        $sContainer = ($iReactId ? '.ync_reaction_' . $iReactId : '.ync_reaction_all') . ' .ync-reaction-popup-user-total-outer';

        if (empty($sType) || !$iItemId) {
            return false;
        }
        $sPrefix = $this->getParam('table_prefix');
        $aUsersList = Phpfox::getService('yncreaction')->getListUserReact($sType, $iItemId, $iReactId, $sPrefix,
            $iPageSize, $iPage, $iCnt);
        $aParamsPager = array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt,
            'paging_mode' => 'loadmore',
            'ajax_paging' => [
                'block' => 'yncreaction.detail-react',
                'params' => [
                    'react_id' => $iReactId,
                    'feed_type' => $sType,
                    'item_id' => $iItemId,
                    'table_prefix' => $sPrefix
                ],
                'container' => $sContainer
            ]
        );
        $oPager = Phpfox::getLib('pager');
        $oPager->set($aParamsPager);
        $this->template()->assign([
            'aUsersList' => $aUsersList,
            'bIsPaging' => $this->getParam('ajax_paging', 0),
            'hasPagingNext' => $iPage < $oPager->getTotalPages()
        ]);


        return 'block';
    }
}