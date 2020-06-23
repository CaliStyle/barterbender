<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Auctions_Ending_Today extends Phpfox_Component {

    public function process()
    {
        $iLimit = Phpfox::getParam('auction.max_items_block_auctions_ending_today');
        
        list($iCnt,$aAuctionsEndingToday) = Phpfox::getService('auction')->getAuctionsEndingToday($iLimit);
        
        if (!$aAuctionsEndingToday)
        {
            return false;
        }
        
        $this->template()->assign(array(
            'aAuctionsEndingToday' => $aAuctionsEndingToday,
            'bShowViewMoreEndingTodayAuctions' => ($iCnt > $iLimit),
            'sHeader' => _p('auctions_ending_today')
                )
        );
        
        return 'block';
    }

}

?>