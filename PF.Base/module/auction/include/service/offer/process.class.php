<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Offer_Process extends Phpfox_Service
{
    public function add($aVals)
    {
        $aInsert = array(
            'auctionoffer_user_id' => Phpfox::getUserId(),
            'auctionoffer_product_id' => isset($aVals['product_id']) ? (int) $aVals['product_id'] : 0,
            'auctionoffer_ip_address' => Phpfox::getIp(),
            'auctionoffer_price' => isset($aVals['price']) ? (float) $aVals['price'] : 0.0,
            'auctionoffer_creation_datetime' => PHPFOX_TIME,
            'auctionoffer_modification_datetime' => 0,
            'auctionoffer_status' => 0, // Pending
            'auctionoffer_currency' => isset($aVals['currency']) ? $aVals['currency'] : ''
        );
        $iOfferId = $this->database()->insert(Phpfox::getT('ecommerce_auction_offer'), $aInsert);
        $aAuction = Phpfox::getService('auction')->getAuctionById($aVals['product_id']);
        /*send notification and send mail*/
        if($aAuction['user_id'] != Phpfox::getUserId() && $iOfferId ){
            $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']).'offerhistory';
            $sMessageSellerMakeOffer= _p('auction_name_title_offer_price_symbol_currency_amount_offer_by_offer',
                array(
                    'title' => $aAuction['name'],
                    'offer' => Phpfox::getUserBy('full_name'),
                    'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aVals['currency']),
                    'amount' => $aVals['price'],
                )
            );
            $iReceiveId = $aAuction['user_id'];
            $aUser = Phpfox::getService('user')->getUser($iReceiveId);
            $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
            $email = $aUser['email'];
            $iProductId = $aAuction['product_id'];
            $aExtraData = array();
            $aExtraData['offer_user_id'] = Phpfox::getUserId();
           
            $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aVals['currency']);
            $aExtraData['amount'] = $aVals['price'];
            $aExtraData['url'] = $sLink;
        
            $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction','offer_received' , $language_id ,  $iReceiveId, $iProductId, $aExtraData);

            Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);


            Phpfox::getService('notification.process')->add('auction_makeoffer',$iOfferId, $aAuction['user_id']);
        }
    }

    public function approveOffer($iOfferId){
        $aUpdate = array(
            'auctionoffer_status' => 1,
            'auctionoffer_approved_datetime' => PHPFOX_TIME,
            );
        $this->database()->update(Phpfox::getT('ecommerce_auction_offer'), $aUpdate,' auctionoffer_id = '.(int)$iOfferId);


        $aOffer = Phpfox::getService('auction.offer')->getOfferByOfferId($iOfferId);

        $aAuction = Phpfox::getService('auction')->getAuctionById($aOffer['auctionoffer_product_id']);

        $aUserSeller = Phpfox::getService('user')->get($aAuction['user_id']);

        $sLink = Phpfox::permalink('auction.my-offers', null, null);

        $sMessageSellerApproveOffer= _p('auction_name_title_by_seller_offer_price_symbol_currency_amount',
            array(
                'title' => $aAuction['name'],
                'seller' => $aUserSeller['full_name'],
                'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aOffer['auctionoffer_currency']),
                'amount' => $aOffer['auctionoffer_price'],
            )
        );

        $iReceiveId = $aOffer['auctionoffer_user_id'];
        $aUser = Phpfox::getService('user')->getUser($iReceiveId);
        $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
        $email = $aUser['email'];
        $iProductId = $aAuction['product_id'];
        $aExtraData = array();
        $aExtraData['seller_id'] = $aAuction['user_id'];
       
        $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aOffer['auctionoffer_currency']);
        $aExtraData['amount'] = $aOffer['auctionoffer_price'];
        $aExtraData['url'] =  Phpfox::permalink('auction.my-offers', null, null);
    
        $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction','offer_approved' , $language_id ,  $iReceiveId, $iProductId, $aExtraData);
        Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
        Phpfox::getService('notification.process')->add('auction_approveoffer',$iOfferId, $aOffer['auctionoffer_user_id']);
    }

    public function denyOffer($iOfferId){
        $aUpdate = array(
            'auctionoffer_status' => 2,
            );
        
        $this->database()->update(Phpfox::getT('ecommerce_auction_offer'), $aUpdate,' auctionoffer_id = '.(int)$iOfferId);

        $aOffer = Phpfox::getService('auction.offer')->getOfferByOfferId($iOfferId);

        $aAuction = Phpfox::getService('auction')->getAuctionById($aOffer['auctionoffer_product_id']);

        $aUserSeller = Phpfox::getService('user')->get($aAuction['user_id']);

        $sLink = Phpfox::permalink('auction.my-offers', null, null);

        $sMessageSellerDenyOffer= _p('auction_name_title_by_seller_offer_price_symbol_currency_amount',
            array(
                'title' => $aAuction['name'],
                'seller' => $aUserSeller['full_name'],
                'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aOffer['auctionoffer_currency']),
                'amount' => $aOffer['auctionoffer_price'],
            )
        );

        $iReceiveId = $aOffer['auctionoffer_user_id'];
        $aUser = Phpfox::getService('user')->getUser($iReceiveId);
        $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
        $email = $aUser['email'];
        $iProductId = $aAuction['product_id'];
        $aExtraData = array();
        $aExtraData['seller_id'] = $aAuction['user_id'];
       
        $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aOffer['auctionoffer_currency']);
        $aExtraData['amount'] = $aOffer['auctionoffer_price'];
        $aExtraData['url'] =  Phpfox::permalink('auction.my-offers', null, null);
    
        $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction','offer_denied' , $language_id ,  $iReceiveId, $iProductId, $aExtraData);
        Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);


        Phpfox::getService('notification.process')->add('auction_denyoffer',$iOfferId, $aOffer['auctionoffer_user_id']);

    }
	
	public function deleteOffersByAuction($iProductId)
	{
		return $this->database()->delete(Phpfox::getT('ecommerce_auction_offer'), 'auctionoffer_product_id = ' . (int) $iProductId);
	}
}
?>