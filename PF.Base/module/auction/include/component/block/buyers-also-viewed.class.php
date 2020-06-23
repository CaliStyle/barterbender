<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Buyers_Also_Viewed extends Phpfox_Component {

    public function process()
    {
        if (!Phpfox::isUser())
        {
            return false;
        }
        
        $iLimit = Phpfox::getParam('auction.max_items_block_buyers_who_viewed_this_item_also_viewed');
        
        list($iCnt, $aBuyersAlsoViewedAuctions) = Phpfox::getService('auction')->getBuyersAlsoViewedAuctions($iLimit);
        
        if (!$aBuyersAlsoViewedAuctions)
        {
            return false;
        }
        foreach ($aBuyersAlsoViewedAuctions as $iKey => $aProduct) {
            if (empty($aBuyersAlsoViewedAuctions[$iKey]['logo_path'])) {
                $aBuyersAlsoViewedAuctions[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        $this->template()->assign(array(
            'bShowViewMoreAuctionsBuyersAlsoViewed' => ($iCnt > $iLimit),
            'aBuyersAlsoViewedAuctions' => $aBuyersAlsoViewedAuctions,
            'sHeader' => _p('buyers_who_viewed_this_item_also_viewed')
                )
        );
        
        return 'block';
    }

}

?>
