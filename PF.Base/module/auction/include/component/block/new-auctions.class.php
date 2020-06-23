<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_New_Auctions extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iPage = $this->getParam('page');
        $viewType = 'listview';
        if ($this->getParam('viewType')) {
            $viewType = $this->getParam('viewType');
        }
        $iLimit = 12;
        list($iCnt, $aAuctionsHomepage) = Phpfox::getService('auction')->getNewAuctions('', $iLimit, $iPage);
        foreach ($aAuctionsHomepage as $iKey => $auction ) {
            if (empty($aAuctionsHomepage[$iKey]['logo_path'])) {
                $aAuctionsHomepage[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
        $this->template()->assign(array(
                'aAuctionsHomepage' => $aAuctionsHomepage,
                'sCorePath' => Phpfox::getParam('core.path'),
                'viewType' => $viewType
            )
        );
        return 'block';
    }
}
?>
