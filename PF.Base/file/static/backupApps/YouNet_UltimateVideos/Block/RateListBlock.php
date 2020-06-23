<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Pager;

defined('PHPFOX') or exit('NO DICE!');

class RateListBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iPage = $this->request()->getInt('page', 1);
        $iVideoId = $this->request()->get('video_id');
        $iRatesPerPage = Phpfox::getParam('core.items_per_page', 10);

        $iTotalRates = Phpfox::getService('ultimatevideo.rating')->getRates($iVideoId, true);
        $aViewerRate = Phpfox::getService('ultimatevideo.rating')->getRates($iVideoId, false, $iPage, $iRatesPerPage, true);
        $aRates = Phpfox::getService('ultimatevideo.rating')->getRates($iVideoId, false, $iPage, $iRatesPerPage);

        // Pagination configuration
        $pager = Phpfox_Pager::instance();
        $pager->set(array(
            'page' => $iPage,
            'size' => $iRatesPerPage,
            'count' => $iTotalRates,
            'paging_mode' => 'loadmore',
            'ajax_paging' => [
                'block' => 'like.browse',
                'params' => [
                    'video_id' => $this->request()->getInt('video_id'),
                ],
                'container' => '.popup-user-with-btn-container'
            ]
        ));

        $this->template()->assign(array(
                'aRates' => $aRates,
                'iVideoId' => $iVideoId,
                'aViewerRate' => empty($aViewerRate) ? false : $aViewerRate[0],
                'aViewer' => empty($aViewerRate) ? false : $aUser = Phpfox::getService('user')->getUser(Phpfox::getUserId()),
                'sItemType' => $this->request()->get('type_id'),
                'iItemId' => $this->request()->getInt('item_id'),
                'bIsPaging' => $this->getParam('ajax_paging', 0),
                'hasPagingNext' => $iPage < $pager->getTotalPages()
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_block_rate_list_clean')) ? eval($sPlugin) : false);
    }
}
