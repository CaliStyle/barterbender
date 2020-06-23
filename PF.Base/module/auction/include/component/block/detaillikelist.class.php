<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Detaillikelist extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        $aAuction = $aYnAuctionDetail['aAuction'];

        $aLikes = Phpfox::getService('like')->getLikes('auction', $aAuction['product_id']);
        $this->template()->assign(array(
            'sHeader' => _p('likes'),
            'aAuction' => $aAuction,
            'iTotalLike' => count($aLikes)
                )
        );
        return 'block';
    }

}

?>
