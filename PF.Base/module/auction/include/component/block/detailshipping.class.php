<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Detailshipping extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        
        $aAuction = $aYnAuctionDetail['aAuction'];
        
        $this->template()->assign(array(
            'aAuction' => $aAuction,
            )
        );
    }

}

?>
