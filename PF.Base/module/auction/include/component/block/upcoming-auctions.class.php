<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Upcoming_Auctions extends Phpfox_Component {

    public function process()
    {
        $iLimit = Phpfox::getParam('auction.max_items_block_upcoming_auctions');
        
        list($iCnt, $aUpcomingAuctions) = Phpfox::getService('auction')->getUpcomingAuctions($iLimit);
        
        if (!$iCnt)
        {
            return false;
        }
        
        $this->template()->assign(array(
            'aUpcomingAuctions' => $aUpcomingAuctions,
            'bShowViewMoreUpcomingAuctions' => ($iCnt > $iLimit),
            'sHeader' => _p('upcoming_auctions')
                )
        );
        
        return 'block';
    }

}

?>