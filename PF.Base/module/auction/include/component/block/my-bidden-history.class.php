<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_My_Bidden_History extends Phpfox_Component {

    public function process()
    {
        $iProductId = $this->getParam('iProductId');
        $aAuction = Phpfox::getService('auction')->getAuctionById($iProductId);	
        if (!isset($aAuction['product_id']) || $aAuction['start_time'] > PHPFOX_TIME || $aAuction['product_status'] == 'draft' || $aAuction['product_status'] == 'pending' || $aAuction['product_status'] == 'denied')
        {
            return;
        }
        
        $aAuction['time_left'] = Phpfox::getService('auction.helper')->getDuration(PHPFOX_TIME, $aAuction['end_time']);
        $aAuction['duration'] = Phpfox::getService('auction.helper')->getDuration($aAuction['start_time'], $aAuction['end_time']);
        
        $iLimit = Phpfox::getParam('auction.max_number_of_items_on_my_bidden_history_popup');
        
        $iTotalBidders = Phpfox::getService('auction.bid')->getTotalBidders($iProductId);
        
        $aConds = array(
            'AND eab.auctionbid_product_id = ' . (int) $iProductId
        );
        
        list($iCnt, $aRows) = Phpfox::getService('auction.bid')->get($aConds, $sSort = 'eab.auctionbid_creation_datetime DESC', 1, $iLimit);
        
        $fSuggestBidPrice = 0.0;
        $aBidIncrement = Phpfox::getService('auction.bidincrement')->getSetting($aAuction['category_id'], 'user', $aAuction['user_id']);
        if (!$aBidIncrement)
        {
            $aBidIncrement = Phpfox::getService('auction.bidincrement')->getSetting($aAuction['category_id'], 'default', 0);
        }
        
        if (isset($aBidIncrement['data_increasement'])) 
        {
            $aFrom = $aBidIncrement['data_increasement']['from'];
            $aTo = $aBidIncrement['data_increasement']['to'];
            $aIncrement = $aBidIncrement['data_increasement']['increment'];
			
            foreach ($aFrom as $iKey => $fFrom)
            {
                if ($aFrom[$iKey] <= $aAuction['auction_latest_bid_price'] && $aTo[$iKey] >= $aAuction['auction_latest_bid_price'])
                {
                    $fSuggestBidPrice = $aAuction['auction_latest_bid_price'] + $aIncrement[$iKey];
                    break;
                }
            }
        }
        
		$bCanBidAuction = Phpfox::getService('auction.permission')->canBidAuction();
		
        $this->template()->assign(array(
			'bCanBidAuction' => $bCanBidAuction,
            'aAuction' => $aAuction,
            'fSuggestBidPrice' => $fSuggestBidPrice,
            'iTotalBidders' => $iTotalBidders,
            'iTotalBids' => $iCnt,
            'sCustomPagination' => Phpfox::getService('auction.helper')->pagination($iCnt, $iLimit, 1, 'id=' . $iProductId),
            'aRows' => $aRows
                )
        );
    }

}

?>