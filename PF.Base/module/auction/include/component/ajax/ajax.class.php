<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function updateFeatureAction()
    {
        Phpfox::getService('auction.process')->featureAuctionBackEnd($this->get('id'), $this->get('active'));
    }
    
     public function featureInBox()
    {
        $iProductId = (int) $this->get('iProductId');
        Phpfox::getBlock('auction.featureinbox', array('iProductId' => $iProductId));
        $this->setTitle(_p('auction'));
    }

	public function deleteImage(){
        $id = $this->get('id'); //image_id
        Phpfox::getService('auction.process')->deleteImage($id);
        $this->call('setTimeout(function() {window.location.href = window.location.href},200);');
    }
	
	public function changeCategoryBidIncrement()
	{
		$iCategoryId = $this->get('categoryId');
		$isAdminCp = $this -> get('is_admincp');
		Phpfox::getBlock('auction.bidincrementcontent', array('iCategoryId' => $iCategoryId, 'isAdminCp' => $isAdminCp));
		
		$this->html('.bidincrement_content', $this->getContent(false))->show('.bidincrement_content');
		$this->hide('.bidincrement_content_loading');
	}
	
	public function refreshInfo()
	{
		$iProductId = $this->get('id');
		$aAuction = Phpfox::getService('auction') -> getAuctionById($iProductId);
		if(isset($aAuction)) 
		{
			//update current bid
			$currentBidValue = $aAuction['sSymbolCurrency'] . sprintf('%0.2f', $aAuction['auction_latest_bid_price']);
			$this->call("$('#detail_current_bid_value').html('$currentBidValue');");
			
			//update suggest bid
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
	
	        if (!empty($aBidIncrement['data_increasement']) )
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

			$sSuggestBidPrice = $aAuction['sSymbolCurrency'] . sprintf('%0.2f', $sSuggestBidPrice);
			$suggestValue = _p('enter_price_or_more', array('price' => $sSuggestBidPrice));
			
			$this->call("$('#detail_bid_suggest_value').html('$suggestValue');");
			
			if(Phpfox::getService('auction.helper')->floatCmp($aAuction['auction_latest_bid_price'], "999999999999.00") == 0)
			{
				$this->call("$('#detail_bid_suggest_value').html('');");
			}
			
			//update total bid
			$totalBidValue = $aAuction['auction_total_bid'];
			$this->call("$('#detail_bid_number_value').html('$totalBidValue');");
			
			//update total view
			$totalViewValue = $aAuction['total_view'];
			$this->call("$('#detail_view_number_value').html('$totalViewValue');");
		}	
	}
	
	public function refreshBidder()
	{
        $iAuctionId = $this->get('id');
		Phpfox::getBlock('auction.detailbidhistory', array(
            'id' => $iAuctionId 
        ));
		$this->html('#auction-detail-history', $this->getContent(false));
		
		//fix paging
		$nextLink = $url = $this->get('url');
		if (strpos($url,'page_') == false) {
		   $nextLink = $url."page_2/";
		} else {
		   $nextLink = substr($url, 0, -2).'2/'; 	
		}
		$this->call("$('.pager_next_link').attr('href','$nextLink')");
	}
	
	
	public function updateBidIncrement()
	{
		$aVals = $this->get('val');
		$isAdminCp = $this ->get('is_admincp');
		
		$this->error(false);
		
		$aValidation = array();
		if($isAdminCp)
		{
			$aValidation = array(
				'category_id' => array(
					'def' => 'required',
					'title' => _p('provide_category_for_bid_increment')
				)
			);
			
		}
        else{
            $aValidation = array(
                'limit_percent_buy_it_now_price' => array(
                    'def' => 'money',
                    'title' => _p('provide_a_valid_limit_percent_buy_it_now_price')
                ),
                'limit_percent_offer_price' => array(
                    'def' => 'money',
                    'title' => _p('provide_a_valid_percent_offer_limit_price')
                ),
                'time_complete_transaction' => _p('provide_a_valid_time_for_winner_complete_transaction'),
                'number_of_transfers' => _p('provide_a_valid_number_of_transfers_for_each_auction'),
                'type_display' => _p('provide_option_for_displaying_buy_it_now_button'),
            );

            if (isset($aVals['type_display']) && $aVals['type_display'] == 1)
            {
                $aValidation['percent_reaching_limit'] = array(
                    'def' => 'money',
                    'title' => _p('provide_a_valid_percent_reaching_limit_price')
                );
            }

            if ($aVals['limit_percent_buy_it_now_price'] < 0 )
            {
                Phpfox_Error::set(_p('provide_a_valid_limit_percent_buy_it_now_price'));
            }
            
            if ($aVals['percent_reaching_limit'] < 0 )
            {
                Phpfox_Error::set(_p('provide_a_valid_percent_reaching_limit_price'));
            }
            
            if ($aVals['limit_percent_offer_price'] < 0 )
            {
                Phpfox_Error::set(_p('provide_a_valid_percent_offer_limit_price'));
            }
        }
		
		$oValidator = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_bidincrement_form',
				'aParams' => $aValidation
			)
		);
		
		
		if (isset($aVals['from']) && isset($aVals['to']) && isset($aVals['increment']))
		{
			foreach ($aVals['from'] as $iKey => $fFrom)
			{
				if (!is_numeric($fFrom) || $fFrom < 0)
				{
					Phpfox_Error::set(_p('provide_valid_value_for_from_field'));
					break;
				}
                $aVals['from'][$iKey] = (float) $fFrom;
                
                if (!isset($aVals['to'][$iKey]) || $fFrom >= $aVals['to'][$iKey])
                {
                    Phpfox_Error::set(_p('to_field_must_be_greater_than_from_field_in_each_rows'));
					break;
                }
			}
			
			foreach ($aVals['to'] as $iKey => $fTo)
			{
				if (!is_numeric($fTo) || $fTo < 0)
				{
					Phpfox_Error::set(_p('provide_valid_value_for_to_field'));
					break;
				}
                $aVals['to'][$iKey] = (float) $fTo;
			}
			
			foreach ($aVals['increment'] as $iKey => $fIncrement)
			{
				if (!is_numeric($fIncrement) || $fIncrement < 0)
				{
					Phpfox_Error::set(_p('provide_valid_value_for_increment_field'));
					break;
				}
                $aVals['increment'][$iKey] = (float) $fIncrement;
			}
		}
		
		if ($oValidator->isValid($aVals))
		{
            if($isAdminCp){

                $aVals['data_increasement'] = array();
                
                if (isset($aVals['from']) && isset($aVals['to']) && isset($aVals['increment']))
                {
                    $aVals['data_increasement'] = array(
                        'from' => $aVals['from'],
                        'to' => $aVals['to'],
                        'increment' => $aVals['increment']
                    );
                }
                
                $aVals['type_increasement'] = 'default';
                
                if (isset($aVals['data_id']) && $aVals['data_id'] > 0)
                {
                    Phpfox::getService('auction.bidincrement.process')->editSetting($aVals);
                }
                else
                {
                    Phpfox::getService('auction.bidincrement.process')->addSetting($aVals, $isAdminCp);
                }
            }
            else{

                if (isset($aVals['category'][0][0]) && $aVals['category'][0][0] > 0)
                {
                    $aVals['data_increasement'] = array();

                    if (isset($aVals['from']) && isset($aVals['to']) && isset($aVals['increment']))
                    {
                        $aVals['data_increasement'] = array(
                            'from' => $aVals['from'],
                            'to' => $aVals['to'],
                            'increment' => $aVals['increment']
                        );
                    }

                    $aVals['type_increasement'] = 'user';

                    $aBidIncrementSettings = Phpfox::getService('auction.bidincrement')->getSetting($aVals['category'][0][0], 'user', Phpfox::getUserId());
                    $aVals['category_id'] = $aVals['category'][0][0];
                    if ($aBidIncrementSettings)
                    {
                        $aVals['data_id'] = $aBidIncrementSettings['data_id'];
                        
                        Phpfox::getService('auction.bidincrement.process')->editSetting($aVals);
                    }
                    else
                    {
                        Phpfox::getService('auction.bidincrement.process')->addSetting($aVals, $isAdminCp);
                    }
                }
                $aSettings = array(
                    'limit_percent_buy_it_now_price' => $aVals['limit_percent_buy_it_now_price'],
                    'type_display' => $aVals['type_display'],
                    'percent_reaching_limit' => $aVals['percent_reaching_limit'],
                    'limit_percent_offer_price' => $aVals['limit_percent_offer_price'],
                    'time_complete_transaction' => $aVals['time_complete_transaction'],
                    'number_of_transfers' => $aVals['number_of_transfers']
                        );
                
                $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->get(Phpfox::getUserId());
                if ($aSellerSettings)
                {
                    Phpfox::getService('ecommerce.sellersettings.process')->update($aSettings);
                }
                else
                {
                    Phpfox::getService('ecommerce.sellersettings.process')->add($aSettings);
                }

            }
        }
        
		$sMessage = '';
		$aErrors = Phpfox_Error::get();
		if ($aErrors)
		{
			foreach ($aErrors as $sErrorMessage)
			{
				$sMessage .= '<div class="error_message">' . $sErrorMessage . '</div>';
			}
			
			$this->html('.bidincrement_form_msg', $sMessage)->show('.bidincrement_form_msg');
		}
		else
		{
			if($isAdminCp)
			{
				$sMessage = '<div class="message">' . _p('update_bid_increment_successfully') . '</div>';
			} else
			{
				$sMessage = '<div class="message">' . _p('update_content_successfully') . '</div>';
			}
			$this->html('.bidincrement_form_msg', $sMessage)->show('.bidincrement_form_msg');
		}
		
		$this->show('.bidincrement_submit');
		$this->hide('.bidincrement_loading');
	}
	
    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'delete':
                Phpfox::getUserParam('auction.can_delete_own_auction', true);

                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ecommerce.process')->delete($iId);

                    $this->slideUp('#js_auction_entry' . $iId);
                }

                $sMessage = _p('auction_successfully_deleted');

                break;
			
			case 'approve':
				foreach ((array) $this->get('item_moderate') as $iProductId)
                {
					$aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);
					if (!isset($aProduct['product_id']) || $aProduct['product_status'] != 'pending')
					{
						continue;
					}
					
					$bCanApproveProduct = Phpfox::getService('auction.permission')->canApproveAuction();
					if (!$bCanApproveProduct)
					{
						continue;
					}
					
					if (Phpfox::getService('ecommerce.process')->approveProduct($iProductId,null,'auction'))
					{
			            Phpfox::getService('ecommerce.process')->updateProductStatus($iProductId, 'approved');
						$sMessage = _p('approve_auction_successfully');
					}
				}
				
				$sMessage = _p('approve_auction_successfully');
				$this->alert($sMessage);
        		$this->call('setTimeout(function(){window.location.href = window.location.href}, 2000);');
				break;
				
			case 'deny':
				
				foreach ((array) $this->get('item_moderate') as $iProductId)
                {
					$aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);
					if (!isset($aProduct['product_id']) || $aProduct['product_status'] != 'pending')
					{
						continue;
					}
					
					$bCanDenyProduct = Phpfox::getService('auction.permission')->canDenyAuction();
					if (!$bCanDenyProduct)
					{
						continue;
					}
					
					Phpfox::getService('ecommerce.process')->deny($iProductId);
				}
				$sMessage = _p('deny_auction_successfully');
				$this->alert($sMessage);
        		$this->call('setTimeout(function(){window.location.href = window.location.href}, 2000);');
				break;
				
        }
    }

    public function deleteManyAuctions()
    {
        $aSetAuctions = $this->get('aSetAuctions');
        $aSetAuctions = explode(",", $aSetAuctions);
        
        if (count($aSetAuctions))
        {
            foreach ($aSetAuctions as $key => $iProductId)
            {
                $aProduct = Phpfox::getService('auction')->getQuickAuctionById($iProductId);

                if ($aProduct['user_id'] != Phpfox::getUserId())
                {
                    continue;
                }

                if ($iProductId)
                {
                    Phpfox::getService('ecommerce.process')->delete($iProductId);
                }
            }
        }

        $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");
        $this->call('setTimeout(function() {window.location.href = window.location.href},500);');
    }

	public function deleteAuction()
	{
        // Get Params
        $iProductId = (int)$this->get('iProductId');
        if ($iProductId)
        {
            $aProduct = Phpfox::getService('auction')->getQuickAuctionById($iProductId);

            if ($aProduct['user_id'] != Phpfox::getUserId() && !Phpfox::isAdmin())
            {
                return false;
            }

            Phpfox::getService('ecommerce.process')->delete($iProductId);
        }
        
        $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");
        $this->call('setTimeout(function() {window.location.href = window.location.href},500);');
    }
    public function close()
    {
        // Get Params
        $iProductId = (int)$this->get('iProductId');
        if ($iProductId)
        {

            Phpfox::getService('ecommerce.process')->close($iProductId);
            /*update status after close*/
            $aAuction = Phpfox::getService('auction')->getAuctionById($iProductId); 
            
            $aUpdateCloseAuction = array(
                    'auction_won_bidder_user_id' => $aAuction['auction_latest_bidder'],
                    'auction_won_bid_price' =>      $aAuction['auction_latest_bid_price'],  
            );
                        
            Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_product_auction'), $aUpdateCloseAuction, 'product_id = ' . (int) $iProductId);

        }
        
        $this->call('setTimeout(function() {window.location.href = window.location.href},500);');
    }

    public function changeCustomFieldByMainCategory()
    {
        $oRequest = Phpfox::getLib('request');
        $iMainCategoryId = $oRequest->get('iMainCategoryId');
        $aCustomFields = Phpfox::getService('ecommerce')->getCustomFieldByCategoryId($iMainCategoryId);

        $keyCustomField = array();



        $aCustomData = array();
        if($this->get('iProductId')){
            $aCustomDataTemp = Phpfox::getService('ecommerce.custom')->getCustomFieldByProductId($this->get('iProductId'));
            
                if(count($aCustomFields)){
                    foreach ($aCustomFields as $aField) {
                            foreach ($aCustomDataTemp as $aFieldValue) {
                                if($aField['field_id'] == $aFieldValue['field_id']){
                                    $aCustomData[] = $aFieldValue;
                                }
                            }
                    }
                }

        }

        if(count($aCustomData)){
            $aCustomFields  = $aCustomData; 
        }

        Phpfox::getBlock('auction.custom.form', array(
            'aCustomFields' => $aCustomFields, 
        ));
        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'content' => $this->getContent(false)
        ));
    }

    public function addLike()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('like.process')->add($this->get('type_id'), $this->get('item_id')))
        {
            Phpfox::getLib('database')->updateCount('like', 'type_id = \'auction\' AND item_id = ' . (int) ($this->get('item_id')) . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int) ($this->get('item_id')));
        }

        $this->call('setTimeout(function() {window.location.href = window.location.href},100);');
    }

    public function deleteLike()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('like.process')->delete($this->get('type_id'), $this->get('item_id'), (int) $this->get('force_user_id')))
        {
            Phpfox::getLib('database')->updateCount('like', 'type_id = \'auction\' AND item_id = ' . (int) ($this->get('item_id')) . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int) ($this->get('item_id')));
        }

        $this->call('setTimeout(function() {window.location.href = window.location.href},100);');
    }
    
    public function browselike()
    {
        Phpfox::getBlock('auction.likebrowse');
        
        $sTitle = _p('like.people_who_like_this');
        
        $this->setTitle($sTitle);
    }

    public function addToWatchList()
    {
        Phpfox::isUser(true);

        Phpfox::getService('auction.watch.process')->addToWatchList($this->get('item_id'));

        $this->call('setTimeout(function(){window.location.href = window.location.href}, 100);');
    }
    
    public function removeFromWatchList()
    {
        Phpfox::isUser(true);

        Phpfox::getService('auction.watch.process')->removeFromWatchList($this->get('item_id'));

        $this->call('setTimeout(function(){window.location.href = window.location.href}, 100);');
    }
    
    public function placeBid()
    {
        Phpfox::isUser(true);
        
        $fBidValue = $this->get('value');
        $iAuctionId = $this->get('id');
        $bPopup = $this->get('popup');
        
		$bCanBidAuction = Phpfox::getService('auction.permission')->canBidAuction();
		if (!$bCanBidAuction)
        {
            Phpfox_Error::set(_p('you_dont_have_permission_to_bid_auction'));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'bid_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_bid_loading_' . $iAuctionId . '").hide();');
            return;
        }
		
        $aAuction = Phpfox::getService('auction')->getAuctionById($iAuctionId);	
        if (!isset($aAuction['product_id']) || $aAuction['start_time'] > PHPFOX_TIME || $aAuction['end_time'] < PHPFOX_TIME || $aAuction['product_status'] == 'draft' || $aAuction['product_status'] == 'pending' || $aAuction['product_status'] == 'denied')
        {
            Phpfox_Error::set(_p('auction_is_not_valid'));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'bid_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_bid_loading_' . $iAuctionId . '").hide();');
            return;
        }
        
        if (!is_numeric($fBidValue) || $fBidValue < 0)
        {
            Phpfox_Error::set(_p('your_bid_is_not_valid'));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'bid_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_bid_loading_' . $iAuctionId . '").hide();');
            return;
        }
        $fBidValue = (float) $fBidValue;
		
        $fSuggestBidPrice = 0.0;

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
             $fSuggestBidPrice =  $iReservePrice + 1;
        }
        else{
            if($aAuction['auction_latest_bid_price'] <= $iReservePrice){
                $aAuction['auction_latest_bid_price'] = $iReservePrice;
            }
            $fSuggestBidPrice = $aAuction['auction_latest_bid_price'] + 1 ;   
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

                        $fSuggestBidPrice = (float) $aAuction['auction_latest_bid_price'] + $aIncrement[$iKey];
                        break;
                    }
                }
            }
            else{
                foreach ($aFrom as $iKey => $fFrom)
                {
                    if ($aFrom[$iKey] <= $iReservePrice && $aTo[$iKey] >=  $iReservePrice)
                    {
                        $fSuggestBidPrice = (string) ( $iReservePrice + $aIncrement[$iKey]);
                        break;
                    }
                }
            }

        }

		
		//check if bid price is very large
		if(Phpfox::getService('auction.helper')->floatCmp($fBidValue, "999999999999.00") == 1)
		{
			Phpfox_Error::set(_p('your_bid_has_reached_the_limitation'));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'bid_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_bid_loading_' . $iAuctionId . '").hide();');
            return;
		}
		
        if ($fSuggestBidPrice > 0 && Phpfox::getService('auction.helper')->floatCmp($fBidValue, $fSuggestBidPrice) == -1)
        {
  
            Phpfox_Error::set(_p('your_bid_must_be_greater_than_or_equal_to_price', array('price' => $aAuction['sSymbolCurrency'] . $fSuggestBidPrice)));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'bid_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_bid_loading_' . $iAuctionId . '").hide();');
            return;
        }
        
        $aVals = array('product_id' => $aAuction['product_id'], 'price' => $fBidValue, 'currency' => $aAuction['creating_item_currency']);
        
        if(Phpfox::getService('auction.bid.process')->add($aVals)){
            $this->alert(_p('bid_placed_successfully'));
            Phpfox::getService('auction.process')->updateLastBid($aAuction['product_id'], Phpfox::getUserId(), $fBidValue);

            //$this->call('$("#my_bidden_history_body").hide();');
            //$this->call('$("#my_bidden_history_success").show();');
            
            $this->call('setTimeout(function(){window.location.href = window.location.href}, 2000);');
        }
        

        

    }
    
    public function makeOffer()
    {
        Phpfox::isUser(true);
        
        $fOfferValue = $this->get('value');
        $iAuctionId = $this->get('id');
        $bPopup = $this->get('popup');
        
        $aAuction = Phpfox::getService('auction')->getAuctionById($iAuctionId);	

        if (!isset($aAuction['product_id']) || $aAuction['product_quantity'] == 0 || $aAuction['start_time'] > PHPFOX_TIME || $aAuction['product_status'] == 'draft' || $aAuction['product_status'] == 'pending' || $aAuction['product_status'] == 'denied')
        {
            Phpfox_Error::set(_p('auction_is_not_valid'));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'offer_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_offer_loading_' . $iAuctionId . '").hide();');
            return;
        }
        
        if (!is_numeric($fOfferValue) || $fOfferValue < 0)
        {
            Phpfox_Error::set(_p('your_offer_is_not_valid'));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'offer_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_offer_loading_' . $iAuctionId . '").hide();');
            return;
        }
        
		$bCanMakeOffer = Phpfox::getService('auction.offer')->canMakeOffer(Phpfox::getUserId(), $aAuction['product_id']);
		if (!$bCanMakeOffer)
		{
			Phpfox_Error::set(_p('auction_is_not_valid'));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'offer_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_offer_loading_' . $iAuctionId . '").hide();');
            return;
		}
        $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->get($aAuction['user_id']);
        
        $fSuggestOfferPrice = 0.0;
		
		//set default value
		if(!isset($aSellerSettings['limit_percent_offer_price'])) 
		{
			$aSellerSettings['limit_percent_offer_price'] = 100;
		}
        if (isset($aSellerSettings['limit_percent_offer_price']))
        {
            $fSuggestOfferPrice = ($aSellerSettings['limit_percent_offer_price'] * $aAuction['auction_item_reserve_price'])/100;
        }
		
        if (Phpfox::getService('auction.helper')->floatCmp($fSuggestOfferPrice, $fOfferValue) == 1)
        {
            Phpfox_Error::set(_p('your_offer_must_be_greater_than_or_equal_to_suggest_price', array('price' => $aAuction['sSymbolCurrency'] . $fSuggestOfferPrice)));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'offer_button_' . $iAuctionId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_offer_loading_' . $iAuctionId . '").hide();');
            return;
        }
        
        $aVals = array('product_id' => $aAuction['product_id'], 'price' => $fOfferValue, 'currency' => $aAuction['creating_item_currency']);
        
        Phpfox::getService('auction.offer.process')->add($aVals);
        
		$this->alert(_p('make_offer_desc'));
		
        $this->call('setTimeout(function(){window.location.href = window.location.href}, 4000);');
    }

    public function buyItNow(){
        Phpfox::isUser(true);
        
        $fOfferValue = $this->get('value');
        $iProductId = $this->get('id');
        $bPopup = $this->get('popup');
        
        $aAuction = Phpfox::getService('auction')->getAuctionById($iProductId); 

        if (!isset($aAuction['product_id']) || $aAuction['product_quantity'] == 0  || $aAuction['product_status'] == 'draft' || $aAuction['product_status'] == 'pending' || $aAuction['product_status'] == 'denied')
        {
            Phpfox_Error::set(_p('auction_is_not_valid'));
            $this->call('$("#' . ($bPopup ? 'popup_' : '') . 'offer_button_' . $iProductId . '").prop("disabled", false);');
            $this->call('$(".' . ($bPopup ? 'popup_' : '') . 'place_offer_loading_' . $iProductId . '").hide();');
            return;
        }
        if(Phpfox::getService('ecommerce.process')->buyItNow($iProductId)){
                $this->call('window.location = "'.Phpfox::getLib('url')->makeUrl('auction.mycart').'"');
        }

        
    }
    public function approveOffer(){

        Phpfox::isUser(true);
        
        $iProductId = $this->get('product_id');
        $iOfferId = $this->get('offer_id');
        
        $aAuction = Phpfox::getService('auction')->getQuickAuctionById($iProductId); 

        if($aAuction['user_id'] != Phpfox::getUserId()){
            return;
        }

        Phpfox::getService('auction.offer.process')->approveOffer($iOfferId);
		$this->alert(_p('offer_approved'));
        $this->call('setTimeout(function(){window.location.href = window.location.href}, 4000);');
    
    }

    public function denyOffer(){

        Phpfox::isUser(true);
        
        $iProductId = $this->get('product_id');
        $iOfferId = $this->get('offer_id');
        
        $aAuction = Phpfox::getService('auction')->getQuickAuctionById($iProductId); 
        
        if($aAuction['user_id'] != Phpfox::getUserId()){
            return;
        }

        Phpfox::getService('auction.offer.process')->denyOffer($iOfferId);
		$this->alert(_p('offer_denied'));
        $this->call('setTimeout(function(){window.location.href = window.location.href}, 4000);');
    
    }

    public function getChartData(){
       
       $iProductId = $this->get('iProductId');
       $iFrontEnd = $this->get('iFrontEnd',false);

       $aDuration = array();
       $aDuration['js_start__datepicker'] = $this->get('js_start__datepicker');
       $aDuration['js_end__datepicker'] = $this->get('js_end__datepicker'); 

       $aChart = Phpfox::getService('auction')->getChartData($iProductId,$aDuration,$iFrontEnd);

       $data['data'] = $aChart;
       $data['title'] = 'Views';
       $data['idx_buynow'] = 0;

       echo json_encode($data);
    }

    public function setAuctionSession()
    {
        $type = $this->get('type');
        $product_id = $this->get('product_id');
        Phpfox::getService('auction.helper')->setSessionBeforeAddItemFromSubmitForm($product_id, $type);
        $this->call("window.location.href = $('#ynauction_add_new_item').attr('href');");
    }


    public function changeVideoListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('auction.detailvideolist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function changePhotoListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('auction.detailphotolist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function getMyBiddenHistory()
    {
        $iProductId = $this->get('id');
        Phpfox::getBlock('auction.my-bidden-history', array('iProductId' => $iProductId));
    }
    
    public function reloadMyBiddenHistory()
    {
        $iPage = $this->get('page');
        $sSortAlias = $this->get('sort', '');
        
        $aSortFields = array(
            'full-name-asc' => 'u.full_name ASC', 
            'full-name-desc' => 'u.full_name DESC', 
            'price-asc' => 'eab.auctionbid_price ASC', 
            'price-desc' => 'eab.auctionbid_price DESC', 
            'time-asc' => 'eab.auctionbid_creation_datetime ASC', 
            'time-desc' => 'eab.auctionbid_creation_datetime DESC'
        );
        
        if (isset($aSortFields[$sSortAlias]))
        {
            $sSort = $aSortFields[$sSortAlias];
        }
        else
        {
            $sSort = 'u.full_name ASC';
        }
        
        $iProductId = $this->get('id');
        
        $aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);	
        if (!isset($aProduct['product_id']) || $aProduct['start_time'] > PHPFOX_TIME || $aProduct['product_status'] == 'draft' || $aProduct['product_status'] == 'pending' || $aProduct['product_status'] == 'denied')
        {
            Phpfox_Error::set(_p('auction_is_not_valid'));
            $this->call("$('#my_bidden_history_loading').hide();");
            $this->call("$('#my_bidden_history_holder').show();");
            $this->errorSet('#my_bidden_history_holder');
            return;
        }
        
        $iLimit = Phpfox::getParam('auction.max_number_of_items_on_my_bidden_history_popup');
        
        $aConds = array(
            'AND eab.auctionbid_product_id = ' . (int) $iProductId
        );
        
        list($iCnt, $aRows) = Phpfox::getService('auction.bid')->get($aConds, $sSort, $iPage, $iLimit);
        
        $sCustomPagination = Phpfox::getService('auction.helper')->pagination($iCnt, $iLimit, $iPage, 'id=' . $iProductId . '&sort=' . $sSortAlias);
        
        Phpfox::getBlock('auction.my-bidden-history-list', array(
            'sSortAlias' => $sSortAlias,
            'iPage' => $iPage,
            'aAuction' => $aProduct, 
            'sCustomPagination' => $sCustomPagination, 
            'aRows' => $aRows));
        
        $this->html('#my_bidden_history_holder', $this->getContent(false));
		$this->call("$('#my_bidden_history_loading').hide();");
        $this->call("$('#my_bidden_history_holder').show();");
    }
    
    public function addToCart()
    {
        Phpfox::isUser(true);
        
        $iProductId = $this->get('id');
        $iOfferId = $this->get('offerId');
        $sType = $this->get('type');
        
        $aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);	
        if (!isset($aProduct['product_id']) || $aProduct['start_time'] > PHPFOX_TIME || $aProduct['product_status'] == 'draft' || $aProduct['product_status'] == 'pending' || $aProduct['product_status'] == 'denied')
        {
            Phpfox_Error::set(_p('auction_is_not_valid'));
            $this->call('$("#add_to_cart_button_' . $iProductId . '").prop("disabled", false);');
            $this->call('$("#add_to_cart_loading_' . $iProductId . '").hide();');
            return;
        }
        
        if ($sType != 'offer' && $sType != 'bid')
        {
            Phpfox_Error::set(_p('type_is_not_valid'));
            $this->call('$("#add_to_cart_button_' . $iProductId . '").prop("disabled", false);');
            $this->call('$("#add_to_cart_loading_' . $iProductId . '").hide();');
            return;
        }
        
        if ($sType == 'offer')
        {
            $aOffer = Phpfox::getService('auction.offer')->getOfferByOfferId($iOfferId);
            if (!isset($aOffer['auctionoffer_id']) || $aOffer['auctionoffer_status'] != 1 || $aOffer['auctionoffer_product_id'] != $iProductId)
            {
                Phpfox_Error::set(_p('your_offer_is_not_valid'));
                $this->call('$("#add_to_cart_button_' . $iProductId . '").prop("disabled", false);');
                $this->call('$("#add_to_cart_loading_' . $iProductId . '").hide();');
                return;
            }
        }
        elseif ($sType == 'bid')
        {
            $aBid = Phpfox::getService('auction.bid')->getLatestBidByProductId($iProductId);
            if (!isset($aBid['auctionbid_id']))
            {
                Phpfox_Error::set(_p('bid_is_not_valid'));
                $this->call('$("#add_to_cart_button_' . $iProductId . '").prop("disabled", false);');
                $this->call('$("#add_to_cart_loading_' . $iProductId . '").hide();');
                return;
            }
        }
        
        
        $aCart = Phpfox::getService('ecommerce.cart')->get(Phpfox::getUserId());
        if (!$aCart)
        {
            $iCartId = Phpfox::getService('ecommerce.cart.process')->add(array('user_id' => Phpfox::getUserId()));
            $aCart = array(
                'cart_id' => $iCartId,
                'cart_user_id' => Phpfox::getUserId(),
                'cart_creation_datetime' => PHPFOX_TIME,
                'cart_modification_datetime' => 0
                    );
        }
        

        
        $fPrice = 0.0;
        if ($sType == 'offer')
        {
            $fPrice = $aOffer['auctionoffer_price'];
        }
        elseif ($sType == 'bid')
        {
            $fPrice = $aProduct['auction_won_bid_price'];
        }
        
        $aVals = array(
            'cart_id' => $aCart['cart_id'],
            'product_id' => $iProductId,
            'quantity' => 1,
            'product_data' => $aProduct,
            'price' => $fPrice,
            'type' => $sType,
            'currency' => $aProduct['creating_item_currency']
        );
        
        $aCartProduct = Phpfox::getService('ecommerce.cart')->getProductsByProductId(Phpfox::getUserId(),$iProductId, $sType);

        if(empty($aCartProduct)){
            $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aVals);
            if($sType == 'offer'){
                $this->alert(_p('this_item_has_been_transfered_from_my_offers_page_to_my_cart_page_please_go_to_my_cart_for_more_information'));
                $this->call('$("#add_to_cart_loading_' . $iProductId . '").hide();');
                $this->call('setTimeout(function(){window.location.href = window.location.href}, 4000);');
                
            }
            else{
                $this->call('$("#add_to_cart_loading_' . $iProductId . '").hide();');
                $this->call('setTimeout(function(){window.location.href = window.location.href}, 200);');
                
            }
        }        
        
    }
    
    public function addMultiToCart(){

        $iProductIds = $this->get('ids');

        $sType = $this->get('type');
        
        $aProductIds = array();
        if($iProductIds != ''){
            $aProductIds = explode("|", $iProductIds);
        }

        if(count($aProductIds)){
             $aCart = Phpfox::getService('ecommerce.cart')->get(Phpfox::getUserId());
            if (!$aCart)
            {
                $iCartId = Phpfox::getService('ecommerce.cart.process')->add(array('user_id' => Phpfox::getUserId()));
                $aCart = array(
                    'cart_id' => $iCartId,
                    'cart_user_id' => Phpfox::getUserId(),
                    'cart_creation_datetime' => PHPFOX_TIME,
                    'cart_modification_datetime' => 0
                        );
            }

            foreach ($aProductIds as $key => $iProductId) {
                if ($sType == 'bid')
                {
                    $aBid = Phpfox::getService('auction.bid')->getLatestBidByProductId($iProductId);
                    $aProduct = Phpfox::getService('auction')->getAuctionById($iProductId); 

                    $fPrice = $aProduct['auction_won_bid_price'];
                    
                    $aVals = array(
                        'cart_id' => $aCart['cart_id'],
                        'product_id' => $iProductId,
                        'quantity' => 1,
                        'product_data' => $aProduct,
                        'price' => $fPrice,
                        'type' => $sType,
                        'currency' => $aProduct['creating_item_currency']
                    );
                    
                    
                    $aCartProduct = Phpfox::getService('ecommerce.cart')->getProductsByProductId(Phpfox::getUserId(),$iProductId, $sType);

                    if(empty($aCartProduct)){
                        $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aVals);
                    }        
                    
                }

            }

            $this->call('window.location = "'.Phpfox::getLib('url')->makeUrl('auction.mycart').'"');
        }
    }

    public function getMyOfferHistory()
    {
        $iProductId = $this->get('id');
        Phpfox::getBlock('auction.my-offer-history', array('iProductId' => $iProductId));
    }
    
    public function reloadMyOfferHistory()
    {
        $iPage = $this->get('page');
        $sSortAlias = $this->get('sort', '');
        
        $aSortFields = array( 
            'price-asc' => 'eao.auctionoffer_price ASC', 
            'price-desc' => 'eao.auctionoffer_price DESC', 
            'time-asc' => 'eao.auctionoffer_creation_datetime ASC', 
            'time-desc' => 'eao.auctionoffer_creation_datetime DESC'
        );
        
        if (isset($aSortFields[$sSortAlias]))
        {
            $sSort = $aSortFields[$sSortAlias];
        }
        else
        {
            $sSort = 'eao.auctionoffer_price ASC';
        }
        
        $iProductId = $this->get('id');
        
        $aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);	
        if (!isset($aProduct['product_id']) || $aProduct['start_time'] > PHPFOX_TIME || ($aProduct['product_status'] != 'running' && $aProduct['product_status'] != 'bidden' && $aProduct['product_status'] != 'approved' && $aProduct['product_status'] != 'completed'))
        {
            Phpfox_Error::set(_p('auction_is_not_valid'));
            $this->call("$('#my_offer_history_loading').hide();");
            $this->call("$('#my_offer_history_holder').show();");
            $this->errorSet('#my_offer_history_holder');
            return;
        }
        
        $iLimit = Phpfox::getParam('auction.max_number_of_items_on_my_offer_history_popup');
        
        $aConds = array(
            'AND eao.auctionoffer_product_id = ' . (int) $iProductId
        );
        
        list($iCnt, $aRows) = Phpfox::getService('auction.offer')->get($aConds, $sSort, $iPage, $iLimit);
        
        $sCustomPagination = Phpfox::getService('auction.helper')->pagination($iCnt, $iLimit, $iPage, 'id=' . $iProductId . '&sort=' . $sSortAlias);
        
        Phpfox::getBlock('auction.my-offer-history-list', array(
            'sSortAlias' => $sSortAlias,
            'iPage' => $iPage,
            'aProduct' => $aProduct,
            'sCustomPagination' => $sCustomPagination, 
            'aRows' => $aRows));
        
        $this->html('#my_offer_history_holder', $this->getContent(false));
		$this->call("$('#my_offer_history_loading').hide();");
        $this->call("$('#my_offer_history_holder').show();");
    }
	
	public function publishProduct()
	{
		$iProductId = $this->get('id');
		$aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);
		if (!isset($aProduct['product_id']) || $aProduct['product_status'] != 'denied')
		{
			return Phpfox_Error::set(_p('auction_is_not_valid_or_it_has_been_deleted'));
		}
		
		$bCanEditProduct = Phpfox::getService('auction.permission')->canEditAuction($aProduct['user_id'], $aProduct['product_id']);
		if (!$bCanEditProduct)
		{
			return Phpfox_Error::set(_p('you_dont_have_permission_to_publish_this_auction'));
		}
		
		if (Phpfox::getService('auction.process')->publish($iProductId, $aProduct))
		{
			$this->alert(_p('publish_auction_successfully'));
			$this->call('setTimeout(function() {window.location.href = window.location.href;}, 3000);');
		}
		else
		{
			Phpfox_Error::set(_p('can_not_publish_this_auction'));
		}
	}
	
	public function closeProduct()
	{
		$iProductId = $this->get('id');
		$aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);

		if (!isset($aProduct['product_id']) || ($aProduct['product_status'] != 'running' && $aProduct['product_status'] != 'bidden'))
		{
			return Phpfox_Error::set(_p('auction_is_not_valid_or_it_has_been_deleted'));
		}
		
		$bCanEditProduct = Phpfox::getService('auction.permission')->canEditAuction($aProduct['user_id'], $aProduct['product_id']);
		if (!$bCanEditProduct)
		{
			return Phpfox_Error::set(_p('you_dont_have_permission_to_close_this_auction'));
		}
		
		if (Phpfox::getService('ecommerce.process')->close($iProductId))
		{
			$this->alert(_p('close_auction_successfully'));
			$this->call('setTimeout(function() {window.location.href = window.location.href;}, 3000);');
		}
		else
		{
			Phpfox_Error::set(_p('can_not_close_this_auction'));
		}
	}
	
	public function approveProduct()
	{
		$iProductId = $this->get('id');
		$aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);
		if (!isset($aProduct['product_id']) || $aProduct['product_status'] != 'pending')
		{
			return Phpfox_Error::set(_p('auction_is_not_valid_or_it_has_been_deleted'));
		}
		
		$bCanApproveProduct = Phpfox::getService('auction.permission')->canApproveAuction();
		if (!$bCanApproveProduct)
		{
			return Phpfox_Error::set(_p('you_dont_have_permission_to_approve_this_auction'));
		}
		
		if (Phpfox::getService('ecommerce.process')->approveProduct($iProductId,null,'auction'))
		{
            Phpfox::getService('ecommerce.process')->updateProductStatus($iProductId, 'approved');
			$this->alert(_p('approve_auction_successfully'));
			$this->call('setTimeout(function() {window.location.href = window.location.href;}, 3000);');
		}
		else
		{
			Phpfox_Error::set(_p('can_not_approve_this_auction'));
		}
	}
	
	public function denyProduct()
	{
		$iProductId = $this->get('id');
		$aProduct = Phpfox::getService('auction')->getAuctionById($iProductId);
		if (!isset($aProduct['product_id']) || $aProduct['product_status'] != 'pending')
		{
			return Phpfox_Error::set(_p('auction_is_not_valid_or_it_has_been_deleted'));
		}
		
		$bCanDenyProduct = Phpfox::getService('auction.permission')->canDenyAuction();
		if (!$bCanDenyProduct)
		{
			return Phpfox_Error::set(_p('you_dont_have_permission_to_deny_this_auction'));
		}
		
		if (Phpfox::getService('ecommerce.process')->deny($iProductId))
		{
			$this->alert(_p('deny_auction_successfully'));
			$this->call('setTimeout(function() {window.location.href = window.location.href;}, 3000);');
		}
		else
		{
			Phpfox_Error::set(_p('can_not_deny_this_auction'));
		}
	}

    public function initCompareItemBlock(){

        $listOfAuctionIdToCompare = $this->get('listOfAuctionIdToCompare');
        $listOfAuctionIdToCompare = trim($listOfAuctionIdToCompare);
        $aCategory = array();
        if(strlen($listOfAuctionIdToCompare) > 0){
            $aListOfAuctionIdToCompare = explode(',', $listOfAuctionIdToCompare);
            foreach ($aListOfAuctionIdToCompare as $key => $iProductId) {
                if($category = Phpfox::getService('auction')->getLastChildCategoryIdOfAuction($iProductId)){
                    $aAuction = Phpfox::getService('auction')->getQuickAuctionById($iProductId);
                    if(!empty($aAuction['logo_path'])) {
                        $aAuction['logo_path'] = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aAuction['server_id'],
                            'path' => 'core.url_pic',
                            'file' => $aAuction['logo_path'],
                            'suffix' => '_100',
                            'return_url' => true
                        ));
                    }else{
                        $aAuction['logo_path'] = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aAuction['server_id'],
                            'path' => '',
                            'file' => Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png',
                            'suffix' => '_100',
                            'return_url' => true
                        ));
                    }

                    $aAuction['item_link'] = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);
                    if(isset($aCategory[$category['category_id']])){
                        $aCategory[$category['category_id']]['list_auction'][] = $aAuction;
                    } else {
                        $aCategory[$category['category_id']] = array(
                            'data' => $category, 
                            'list_auction' => array($aAuction), 
                        );                                            
                    }
                }
            }
        }
            
        $sCompareLink = Phpfox::permalink('auction.compareauction', null, null);
        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'message' => '', 
            'aCategory' => ($aCategory), 
            'sCompareLink' => ($sCompareLink), 
        ));
    
    }

    public function compareGetInfoAuction(){
        $iProductId = $this->get('product_id');
        $aCategory = Phpfox::getService('auction')->getLastChildCategoryIdOfAuction($iProductId);
        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'message' => '', 
            'aCategory' => ($aCategory), 
        ));
    }

    public function getCharts()
    {
        $aVals = $this->get('val');
        $sFromDatePicker = $this->get('js_from__datepicker');
        $sToDatePicker = $this->get('js_to__datepicker');
        
        if (empty($sFromDatePicker))
        {
            Phpfox_Error::set(_p('from_date_is_not_valid'));
        }
        if (empty($sToDatePicker))
        {
            Phpfox_Error::set(_p('to_date_is_not_valid'));
        }
        
        $iFromTimestamp = 0;
        $iToTimestamp = 0;
        
        if ($aVals && !empty($sFromDatePicker) && !empty($sToDatePicker))
        {
            // 11th December, 2010
            $sFromDate = $aVals['from_day'] . '-' . $aVals['from_month'] . '-' . $aVals['from_year'];
            $sToDate = $aVals['to_day'] . '-' . $aVals['to_month'] . '-' . $aVals['to_year'];
            
            $iFromTimestamp = strtotime($sFromDate);
            $iToTimestamp = strtotime($sToDate);
            
            if ($iFromTimestamp > $iToTimestamp)
            {
                Phpfox_Error::set(_p('from_date_must_be_less_than_to_date'));
            }
        }
        
        if (Phpfox_Error::isPassed())
        {
            Phpfox::getBlock('auction.charts', array('iFromTimestamp' => $iFromTimestamp, 'iToTimestamp' => $iToTimestamp));
            $this->html('#charts_holder', $this->getContent(false));
            $this->call("$('#charts_holder').show();");
        }
        
        $this->call('$("#statistic_button").prop("disabled", false);');
        $this->call('$("#charts_loading").hide();');
        $this->errorSet('.statistic_search_message');
        $this->call('$Core.loadInit();');
    }

    public function fillEmailTemplate(){

        $iTypeId = $this->get('email_template_id');
        $iLanguageId = $this->get('language_id');

        if (empty($iTypeId)){
            $iTypeId = 0;
        }
        if (empty($iTypeId)){
            $iTypeId = 0;
        }
        $aEmail = Phpfox::getService('ecommerce.mail')->getEmailTemplate($iTypeId,$iLanguageId);
        $aEmail['email_subject'] = Phpfox::getLib('parse.output')->parse($aEmail['email_subject']);
		$aEmail['email_template'] = str_replace('"', '\"', $aEmail['email_template']);
		$aEmail['email_template'] = preg_replace('/[\r]+/', '', $aEmail['email_template']);
        $this->call('$("#email_subject").val("'.$aEmail['email_subject'].'"); $("#email_template").val("'.$aEmail['email_template'].'")');
    
    }

    public function deleteImages()
    {
        $id = $this->get('id'); //image_id
        $is_main = $this->get('is_main');
        $iProductId = $this->get('pId');
        if (Phpfox::getService('ecommerce.process')->deleteImage($id)) {
            $iNewImage = 0;
            if (!empty($is_main) && !empty($iProductId)) {
                Phpfox::getService('auction.process')->removeMainPhoto($iProductId);

                // Set another photo to cover
                $aImages = Phpfox::getService('ecommerce')->getImages($iProductId, 1);
                if (count($aImages) > 0) {
                    Phpfox::getService('auction.process')->setMainProductPhoto($iProductId, $aImages[0]['image_id']);
                    $iNewImage = $aImages[0]['image_id'];
                }
            }

            $this->call('$("#js_photo_holder_' . $id . '").remove(); onAfterDeletePhotoSuccess(' . $iNewImage . ');');
        } else {
            $this->alert(_p('fail_to_delete_this_photo'));
        }
    }

    public function setMainProductPhoto()
    {
        Phpfox::isUser(true);
        // Get Params
        if(!Phpfox::getService('auction.permission')->canDeleteAuction(true, $this->get('iOwnerId'))) {
            return false;
        }

        $iProductId = $this->get('iProductId');
        $iPhotoId = $this->get('iPhotoId');

        if ($iProductId && $iPhotoId) {
            if (Phpfox::getService('auction.process')->setMainProductPhoto($iProductId, $iPhotoId)) {
                $this->alert(_p('successfully set main photo for this product'));
            }
        }
    }

}
?>