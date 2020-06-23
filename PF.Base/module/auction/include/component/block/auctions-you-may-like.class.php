<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Auctions_You_May_Like extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        $aAuction = $aYnAuctionDetail['aAuction'];
        // Auctions you may like
        $aAuctionsYouMayLike = Phpfox::getService('auction')->getAuctionsYouMayLike($aAuction['product_id']);
        
        if (!$aAuctionsYouMayLike)
        {
            return false;
        }
        foreach ($aAuctionsYouMayLike as $iKey => $aProduct) {
            if (empty($aAuctionsYouMayLike[$iKey]['logo_path'])) {
                $aAuctionsYouMayLike[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        $this->template()->assign(array(
            'aAuctionsYouMayLike' => $aAuctionsYouMayLike
                )
        );
        
        return 'block';
    }

}

?>