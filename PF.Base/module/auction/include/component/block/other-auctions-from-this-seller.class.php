<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Other_Auctions_From_This_Seller extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        $aAuction = $aYnAuctionDetail['aAuction'];

        $iSellerId = (int)$aAuction['user_id'];
        
         // Other auctions from this seller.
        $aSuggestedAuctions = Phpfox::getService('auction')->getOtherAuctionsFromThisSeller($aAuction['product_id'],$iSellerId);

        if (count($aSuggestedAuctions))
        {
            foreach ($aSuggestedAuctions as $key_business => $aValueAuctions)
            {
                $aSuggestedAuctions[$key_business]['count_review'] = Phpfox::getService('auction')->getCountReviewOfAuction($aValueAuctions['product_id']);
                if (empty($aSuggestedAuctions[$key_business]['logo_path'])) {
                    $aSuggestedAuctions[$key_business]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
                }
            }
        }
        else
        {
            return false;
        }
        
        $this->template()->assign(array(
            'aSuggestedAuctions' => $aSuggestedAuctions
                )
        );
        
        return 'block';
    }

}

?>