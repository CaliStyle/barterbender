<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Bid_Process extends Phpfox_Service
{
    public function add($aVals)
    {

        $iProductId = isset($aVals['product_id']) ? (int) $aVals['product_id'] : 0;
        $iPrice = isset($aVals['price']) ? (float) $aVals['price'] : 0.0;
        
        $aAuction = Phpfox::getService('auction')->getAuctionById($iProductId);

        $aInsert = array(
            'auctionbid_user_id' => Phpfox::getUserId(),
            'auctionbid_product_id' => $iProductId,
            'auctionbid_ip_address' => Phpfox::getIp(),
            'auctionbid_price' => $iPrice,
            'auctionbid_creation_datetime' => PHPFOX_TIME,
            'auctionbid_modification_datetime' => 0,
            'auctionbid_status' => 0, // Pending
            'auctionbid_currency' => isset($aVals['currency']) ? $aVals['currency'] : ''
        );
        
        $iAuctionBidId = $this->database()->insert(Phpfox::getT('ecommerce_auction_bid'), $aInsert);
        
        // Update latest bidder and latest bid price.
        $aUpdate = array(
            'auction_latest_bidder' => Phpfox::getUserId(), 
            'auction_latest_bid_price' => $iPrice
        );
        $this->database()->update(Phpfox::getT('ecommerce_product_auction'), $aUpdate, 'product_id = ' . $iProductId);
        
        // Update product status to bidden.
        Phpfox::getLib('database')->updateCount('ecommerce_auction_bid', 'auctionbid_product_id = ' . (int) $iProductId . '', 'auction_total_bid', 'ecommerce_product_auction', 'product_id = ' . (int) $iProductId);
        

        /*send notification and send mail*/
        if($aAuction['user_id'] != Phpfox::getUserId() && $aAuction['receive_notification_someone_bid']){
         
            $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']).'/bidhistory';

            $sMessageSellerStartBidding = _p('auction_name_title_bidder_bidder_current_bid_symbol_currency_amount_bid_on_date_time',
                array(
                    'title' => $aAuction['name'],
                    'bidder' => Phpfox::getUserBy('full_name'),
                    'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aVals['currency']),
                    'amount' => $iPrice,
                    'date_time' =>  Phpfox::getTime('F-d-Y h:m:i',PHPFOX_TIME),
                )
            );


            $iReceiveId = $aAuction['user_id'];
	        $aUser = Phpfox::getService('user')->getUser($iReceiveId);
	        $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
	        $email = $aUser['email'];

	        $aExtraData = array();
	        $aExtraData['bidder_id'] = Phpfox::getUserId();
	        $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aVals['currency']);
	        $aExtraData['amount'] = $iPrice;
	        $aExtraData['date_time'] =  Phpfox::getTime('F-d-Y h:m:i',PHPFOX_TIME);
	        $aExtraData['url'] = $sLink;
	       
	        $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction','someone_start_bidding_on_your_auction' , $language_id ,  $iReceiveId, $iProductId, $aExtraData);
	        Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
	        Phpfox::getService('notification.process')->add('auction_bid', $iProductId, $aAuction['user_id']);
			
        }

        if($aAuction['auction_latest_bidder'] !=  NULL && $aAuction['auction_latest_bidder'] != Phpfox::getUserId()){
             $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);
            $aSeller =  Phpfox::getService('user')->get($aAuction['user_id']);
            $sMessageBidderOutBid = _p('auction_name_title_seller_seller_current_bid_symbol_currency_amount_end_date_date_time',
                array(
                    'title' => $aAuction['name'],
                    'seller' => $aSeller['full_name'],
                    'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aVals['currency']),
                    'amount' => $iPrice,
                    'date_time' =>  Phpfox::getTime('F-d-Y h:m:i',$aAuction['end_time']),
                )
            );

            $iReceiveId = $aAuction['auction_latest_bidder'];
            $aUser = Phpfox::getService('user')->getUser($iReceiveId);
            $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
            $email = $aUser['email'];
            $aExtraData = array();
            $aExtraData['seller_id'] = $aAuction['user_id'];
            $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aVals['currency']);
            $aExtraData['amount'] = $iPrice;
            $aExtraData['date_time'] = Phpfox::getTime('F-d-Y h:m:i',$aAuction['end_time']);
            $aExtraData['url'] = $sLink;

            $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction','you_have_been_outbid_bid_again_now' , $language_id ,  $iReceiveId, $iProductId, $aExtraData);
            Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
            Phpfox::getService('notification.process')->add('auction_bid', $iProductId, $aAuction['auction_latest_bidder']);
        }
        /*add feed*/
        Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('auction_bid', $iAuctionBidId, $aAuction['privacy'], (isset($aAuction['privacy_comment']) ? (int) $aAuction['privacy_comment'] : 0), (isset($aAuction['item_id']) ? (int) $aAuction['item_id'] : 0), Phpfox::getUserId()):0;


        // Update total bid
        return $iAuctionBidId;
    }
	
	public function deleteBidsOfAuction($iProductId)
	{
		return $this->database()->delete(Phpfox::getT('ecommerce_auction_bid'), 'auctionbid_product_id = ' . (int) $iProductId);
	}
}
?>