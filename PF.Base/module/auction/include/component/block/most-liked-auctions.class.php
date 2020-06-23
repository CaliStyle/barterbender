<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Most_Liked_Auctions extends Phpfox_Component {

    public function process()
    {
        if (!Phpfox::isModule('like'))
        {
            return false;
        }
         if(!$this->getParam('bInHomepageFr'))
        {
            return false;
        }
        $iLimit = Phpfox::getParam('auction.max_items_block_most_liked_auctions');
        
        list($iCnt, $aMostLikedAuctions) = Phpfox::getService('auction')->getMostLikedAuctions($iLimit);
        
        if (!$aMostLikedAuctions)
        {
            return false;
        }
        foreach ($aMostLikedAuctions as $iKey => $auction ) {
            if (empty($aMostLikedAuctions[$iKey]['logo_path'])) {
                $aMostLikedAuctions[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        $this->template()->assign(array(
            'bShowViewMoreMostLikedAuctions' => ($iCnt > $iLimit),
            'aMostLikedAuctions' => $aMostLikedAuctions,
            'sHeader' => _p('most_liked_auctions')
         ));

        if ($iCnt > $iLimit) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeurl('auction') . '?sort=most-liked'
                )
            ));
        }
        
        return 'block';
    }

}

?>