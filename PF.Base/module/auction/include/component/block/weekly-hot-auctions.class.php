<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Weekly_Hot_Auctions extends Phpfox_Component {

    public function process()
    {
        $iLimit = Phpfox::getParam('auction.max_items_block_weekly_hot_auctions');
        
        $aWeeklyHotAuctions = Phpfox::getService('auction')->getWeeklyHotAuctions($iLimit);
        
        if (!$aWeeklyHotAuctions)
        {
            return false;
        }
        foreach ($aWeeklyHotAuctions as $iKey => $auction ) {
            if (empty($aWeeklyHotAuctions[$iKey]['logo_path'])) {
                $aWeeklyHotAuctions[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        $aWeeklyHotAuctions = array_chunk($aWeeklyHotAuctions, 3);

        $this->template()->assign(array(
            'aWeeklyHotAuctions' => $aWeeklyHotAuctions,
            'sHeader' => _p('weekly_hot_auctions')
                )
        );
        
        return 'block';
    }

}

?>