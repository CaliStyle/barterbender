<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Detail_Header_Info extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        $aAuction = $aYnAuctionDetail['aAuction'];
        $aModuleView = $aYnAuctionDetail['aModuleView'];
        $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->get($aAuction['user_id']);
        
        $sDetailHeaderSubMenu = 'overview';
        foreach ($aModuleView as $sKey => $aItem)
        {
            if ($aItem['active'])
            {
                $sDetailHeaderSubMenu = $sKey;
                break;
            }
        }

        // Get time left.
        if ($aAuction['start_time'] > PHPFOX_TIME)
        {
            $aAuction['time_view'] = Phpfox::getService('auction.helper')->getDuration(PHPFOX_TIME, $aAuction['start_time']);
        }
        else
        {
            $aAuction['time_view'] = Phpfox::getService('auction.helper')->getDuration(PHPFOX_TIME, $aAuction['end_time']);
        }
        
        // Get suggest bid price.
        $sSuggestBidPrice = '';

        $iReservePrice = $aAuction['auction_item_reserve_price'];

        if($aAuction['is_hide_reserve_price']){
            $iReservePrice = 0;
        }
        $aBidIncrement = Phpfox::getService('auction.bidincrement')->getSetting($aAuction['category_id'], 'user', $aAuction['user_id']);
	    if (!$aBidIncrement)
        {
            $aBidIncrement = Phpfox::getService('auction.bidincrement')->getSetting($aAuction['category_id'], 'default', 0);
        }
        if((int)$aAuction['auction_latest_bid_price'] == 0){
             $sSuggestBidPrice = (string) ($iReservePrice + 1);
        }
        else{
            if($aAuction['auction_latest_bid_price'] <= $iReservePrice){
                $aAuction['auction_latest_bid_price'] = $iReservePrice;
            }
            $sSuggestBidPrice = (string) ($aAuction['auction_latest_bid_price'] + 1);   
        }

        if (!empty($aBidIncrement['data_increasement']))
        {
            $aFrom = $aBidIncrement['data_increasement']['from'];
            $aTo = $aBidIncrement['data_increasement']['to'];
            $aIncrement = $aBidIncrement['data_increasement']['increment'];

            if($aAuction['auction_latest_bid_price'] != 0){

                foreach ($aFrom as $iKey => $fFrom)
                {
                    if ($aFrom[$iKey] <= $aAuction['auction_latest_bid_price'] && $aTo[$iKey] >= $aAuction['auction_latest_bid_price'])
                    {
                        if($aAuction['auction_latest_bid_price'] <= $iReservePrice){
                            $aAuction['auction_latest_bid_price'] = $iReservePrice;
                        }
                        $sSuggestBidPrice = (string) ($aAuction['auction_latest_bid_price'] + $aIncrement[$iKey]);
                        break;
                    }
                }
            }
            else{
                foreach ($aFrom as $iKey => $fFrom)
                {
                    if ($aFrom[$iKey] <= $iReservePrice && $aTo[$iKey] >= $iReservePrice)
                    {
                        $sSuggestBidPrice = (string) ($iReservePrice + $aIncrement[$iKey]);
                        break;
                    }
                }
            }
        }

		//do not suggest if price bid reach limitation
		if(Phpfox::getService('auction.helper')->floatCmp($aAuction['auction_latest_bid_price'], "999999999999.00") == 0)
		{
			$sSuggestBidPrice = "";
		}
		
        // Check can make offer or not.
        $bCanMakeOffer = Phpfox::getService('auction.offer')->canMakeOffer(Phpfox::getUserId(), $aAuction['product_id']);
        if ($aAuction['product_quantity'] == 0)
        {
            $bCanMakeOffer = false;
        }
        
        $fSuggestOfferPrice = 0.00;
        
        // If you can not make offer, then no need to get suggest offer price.
        if ($bCanMakeOffer)
        {
        	if(!isset($aSellerSettings['limit_percent_offer_price']))
			{
				$aSellerSettings['limit_percent_offer_price'] = 100;
			}
            if (isset($aSellerSettings['limit_percent_offer_price']))
            {
                $fSuggestOfferPrice = ($aSellerSettings['limit_percent_offer_price'] * $aAuction['auction_item_reserve_price'])/100;
            }
        }
        $sSuggestOfferPrice = $aAuction['sSymbolCurrency'] . sprintf('%0.2f', $fSuggestOfferPrice);
        
        $bCanBuyItNow = true;
        
        if(isset($aSellerSettings['type_display'])){
             switch ($aSellerSettings['type_display']) {
                case 0:
                    //if has bid => false
                    if($aAuction['auction_latest_bid_price'] != 0){
                        $bCanBuyItNow = false;
                    }
                    else{
                        $bCanBuyItNow = true;
                    }
                    break;
                
                case 1:
					if(!isset($aSellerSettings['percent_reaching_limit']))
					{
						$aSellerSettings['percent_reaching_limit'] = 100;
					}
                    if($aAuction['auction_latest_bid_price'] >= $aAuction['auction_item_buy_now_price'] * $aSellerSettings['percent_reaching_limit'] / 100){
                        $bCanBuyItNow = false;
                    }
                    else{
                        $bCanBuyItNow = true;
                    }
                    // if latest bid price > x% buy it now => false
                    break;

                default:
                    $bCanBuyItNow  = true;
                    break;
            }
     
        }

        if($aAuction['product_quantity'] == 0){
            $bCanBuyItNow = false;
        }

        $aDetailHeaderInfoImages = Phpfox::getService('auction')->getImages($aAuction['product_id']);
        foreach ($aDetailHeaderInfoImages as &$aDetailHeaderInfoImage)
        {
            $aDetailHeaderInfoImage['image_path'] = str_replace('_cover', '', $aDetailHeaderInfoImage['image_path']);
        }
        
        $bCanEditAuction = Phpfox::getService('auction.permission')->canEditAuction($aAuction['user_id'], $aAuction['product_id']);
		$bCanApproveAuction = Phpfox::getService('auction.permission')->canApproveAuction();
		$bCanDenyAuction = Phpfox::getService('auction.permission')->canDenyAuction();
		$bCanDeleteAuction = Phpfox::getService('auction.permission')->canDeleteAuction($aAuction['user_id']);
		$bCanCloseAuction = Phpfox::getService('auction.permission')->canCloseAuction($aAuction['user_id']);
		
		$bShowAuctionFunctions = false;

		if ($bCanEditAuction
			|| ($bCanApproveAuction && $aAuction['product_status'] == 'pending') 
			|| ($bCanDenyAuction && $aAuction['product_status'] == 'pending')
			|| ($aAuction['user_id'] == Phpfox::getUserId() && $aAuction['product_status'] == 'denied')
			|| ($bCanDeleteAuction && ($aAuction['product_status'] == 'draft' || $aAuction['product_status'] == 'pending' || $aAuction['product_status'] == 'approved' || $aAuction['product_status'] == 'denied' || $aAuction['product_status'] == 'running'))
			|| ($bCanCloseAuction && ($aAuction['product_status'] == 'running' || $aAuction['product_status'] == 'bidden'))
			)
		{
			$bShowAuctionFunctions = true;
		}
		
		$bCanBidAuction = Phpfox::getService('auction.permission')->canBidAuction();
        $isLiveAuction = true;
        if (!isset($aAuction['product_id']) || $aAuction['start_time'] > PHPFOX_TIME || $aAuction['end_time'] < PHPFOX_TIME || in_array($aAuction['product_status'], array('draft', 'pending', 'completed', 'denied')))
        {
            $isLiveAuction = false;
		}

		$sSuggestBidPrice = $aAuction['sSymbolCurrency'] . sprintf('%0.2f', $sSuggestBidPrice);
		
		$refreshTime = Phpfox::getParam('auction.refresh_time');

        $aImages = Phpfox::getService('ecommerce')->getImages($aAuction['product_id']);
        $iCoverPhotos  =  count($aImages);

        $this->template()->assign(array(
        	'refreshTime' => $refreshTime,
            'isLiveAuction' => $isLiveAuction,
            'bCanBuyItNow'  => $bCanBuyItNow,
			'bCanBidAuction' => $bCanBidAuction,
			'bShowAuctionFunctions' => $bShowAuctionFunctions,
			'bCanApproveAuction' => $bCanApproveAuction,
			'bCanDenyAuction' => $bCanDenyAuction,
            'bCanEditAuction' => $bCanEditAuction,
			'bCanDeleteAuction' => $bCanDeleteAuction,
			'bCanCloseAuction' => $bCanCloseAuction,
            'bCanMakeOffer' => $bCanMakeOffer,
            'fSuggestOfferPrice' => $fSuggestOfferPrice,
            'aAuction' => $aAuction,
            'aDetailHeaderInfoImages' => $aDetailHeaderInfoImages,
            'sSuggestBidPrice' => $sSuggestBidPrice,
            'sSuggestOfferPrice' => $sSuggestOfferPrice,
            'sDetailHeaderSubMenu' => $sDetailHeaderSubMenu,
            'sCorePath'=> Phpfox::getParam('core.path'),
            'iCoverPhotos' => $iCoverPhotos,
            )
        );
        return 'block';
    }

}

?>
