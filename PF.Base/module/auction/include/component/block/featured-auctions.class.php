<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Featured_Auctions extends Phpfox_Component {

    public function process()
    {
        $iLimit = Phpfox::getParam('auction.max_items_block_featured_auctions');
        
        $aFeaturedAuctions = Phpfox::getService('auction')->getFeaturedAuctions($iLimit);
        
        if (!$aFeaturedAuctions)
        {
            return false;
        }
        foreach ($aFeaturedAuctions as $iKey => $aProduct) {
            if (empty($aFeaturedAuctions[$iKey]['logo_path'])) {
                $aFeaturedAuctions[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        $this->template()->assign(array(
            'aFeaturedAuctions' => $aFeaturedAuctions,
            'sHeader' => _p('featured_auctions')
                )
        );
        
        return 'block';
    }

}

?>