<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Todays_Live_Auctions extends Phpfox_Component {

    public function process()
    {
        $iLimit = Phpfox::getParam('auction.max_items_block_todays_live_auctions');
        
        list($iCnt,$aTodaysLiveAuctions) = Phpfox::getService('auction')->getTodaysLiveAuctions($iLimit);
        
        if (!$aTodaysLiveAuctions)
        {
            return false;
        }
        
        $this->template()->assign(array(
            'aTodaysLiveAuctions' => $aTodaysLiveAuctions,
            'bShowViewMoreToDayLiveAuctions' => ($iCnt > $iLimit),
            'sHeader' => _p('todays_live_auctions')
                )
        );
        
        return 'block';
    }

}

?>