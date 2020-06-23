<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Detailphotomenu extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        $aAuction = $aYnAuctionDetail['aAuction'];

        $aAuction['total_auction'] = Phpfox::getService('auction')->getTotalMyAuction($aAuction['user_id']);
        
        if ($aAuction['theme_id'] == 2)
        {
            return false;
        }

        $this->template()->assign(array(
            'aAuction' => $aAuction
                )
        );
        return 'block';
    }

}

?>
