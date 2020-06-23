<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_My_Offer_History extends Phpfox_Component {

    public function process()
    {
        $iProductId = $this->getParam('iProductId');
        $aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);	
        if (!isset($aProduct['product_id']) || $aProduct['start_time'] > PHPFOX_TIME || ($aProduct['product_status'] != 'running' && $aProduct['product_status'] != 'bidden' && $aProduct['product_status'] != 'approved' && $aProduct['product_status'] != 'completed'))
        {
            return;
        }
        
        $aProduct['time_left'] = Phpfox::getService('auction.helper')->getDuration(PHPFOX_TIME, $aProduct['end_time']);
        $aProduct['duration'] = Phpfox::getService('auction.helper')->getDuration($aProduct['start_time'], $aProduct['end_time']);
        
        $iLimit = Phpfox::getParam('auction.max_number_of_items_on_my_offer_history_popup');
        
        $aConds = array(
            'AND eao.auctionoffer_product_id = ' . (int) $iProductId,
            'AND eao.auctionoffer_user_id   = '  . (int) Phpfox::getUserId() ,
        );
        
        list($iCnt, $aRows) = Phpfox::getService('auction.offer')->get($aConds, $sSort = 'eao.auctionoffer_price ASC', 1, $iLimit);
        
        $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->get($aProduct['user_id']);
        
        $fSuggestOfferPrice = 0;
		
		if(!isset($aSellerSettings['limit_percent_offer_price']))
		{
			$aSellerSettings['limit_percent_offer_price'] = 100;
		}
		
        if (isset($aSellerSettings['limit_percent_offer_price']))
        {
            $fSuggestOfferPrice = ($aSellerSettings['limit_percent_offer_price'] * $aProduct['auction_item_reserve_price'])/100;
        }
        
        $bCanMakeOffer = Phpfox::getService('auction.offer')->canMakeOffer(Phpfox::getUserId(), $iProductId);
        
        if ($aProduct['product_quantity'] == 0)
        {
            $bCanMakeOffer = false;
        }
        
        $this->template()->assign(array(
            'bCanMakeOffer' => $bCanMakeOffer,
            'aProduct' => $aProduct,
            'fSuggestOfferPrice' => $fSuggestOfferPrice,
            'iTotalOffers' => $iCnt,
            'sCustomPagination' => Phpfox::getService('auction.helper')->pagination($iCnt, $iLimit, 1, 'id=' . $iProductId),
            'aRows' => $aRows
                )
        );
    }

}

?>