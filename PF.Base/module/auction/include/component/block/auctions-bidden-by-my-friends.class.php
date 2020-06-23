<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Auctions_Bidden_By_My_Friends extends Phpfox_Component {

    public function process()
    {
        if (!Phpfox::isModule('friend'))
        {
            return false;
        }
        
        if(!$this->getParam('bInHomepageFr'))
        {
            return false;
        }
        
        $iLimit = Phpfox::getParam('auction.max_items_block_auctions_bidden_by_my_friends');

        list($iCnt, $aBiddenByMyFriendsAuctions) = Phpfox::getService('auction')->getBiddenByMyFriendsAuctions($iLimit);

        foreach ($aBiddenByMyFriendsAuctions as $iKey => $aBiddenByMyFriendsAuction) {
            if ($aBiddenByMyFriendsAuction['usergroupId'] == 5) {
                $aBiddenByMyFriendsAuctions = null;
            }
            if (empty($aBiddenByMyFriendsAuctions[$iKey]['logo_path'])) {
                $aBiddenByMyFriendsAuctions[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        
        if (!$aBiddenByMyFriendsAuctions)
        {
            return false;
        }
        
        $this->template()->assign(array(
            'bShowViewMoreAuctionsBiddenByMyFriends' => ($iCnt > $iLimit),
            'aBiddenByMyFriendsAuctions' => $aBiddenByMyFriendsAuctions,
            'sHeader' => _p('auctions_bidden_by_my_friends')
                )
        );
        
        return 'block';
    }

}

?>