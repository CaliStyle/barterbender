<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Ajax_Ajax extends Phpfox_Ajax {
	
	public function denyRequestMoney()
    {
        Phpfox::isUser(true);
        
        $aVals = $this->get('val');
		$id = $aVals['id'];
		$message = $aVals['reason'];
        Phpfox::getService('ecommerce.request.process')->deny($id, $message);
        
        $this->call('setTimeout(function(){window.location.href = window.location.href}, 100);');
    }
	
	public function getDenyRequestForm()
	{
		Phpfox::isUser(true);
		$idRequest = $this->get('id');
		$this->setTitle(_p('deny_request'));
		
		Phpfox::getBlock('ecommerce.deny-request', array('id' => $idRequest));		
	}
	
    public function addNewAddress(){
        Phpfox::getBlock('ecommerce.add-new-address', array());
        $this->setTitle(_p('add_new_address'));
    }

    public function editAddress(){
        $iAddressId = $this->get('address_id');

        Phpfox::getBlock('ecommerce.add-new-address', array('address_id' => $iAddressId));

        $this->setTitle(_p('edit_address'));
    }
    public function deleteAddress(){
        $iAddressId = $this->get('address_id');
        Phpfox::getService('ecommerce.process')->deleteAddress($iAddressId);
        $this->call('window.location.reload();');

    }
    public function showPopupCustomGroup()
    {
        $iCategoryId = $this->get('category_id');
        Phpfox::getBlock('ecommerce.popup-customfield-category', array('category_id' => $iCategoryId));
    }

    public function updateField()
    {
        $aVals = $this->get('val');
        if(Phpfox::getService('ecommerce.custom.process')->update($aVals['id'], $aVals))
        {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("window.location.reload();");
        }
        else{
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->alert(_p('please_input_name_of_field'));
        }
    }

    public function addField()
    {
        $aVals = $this->get('val');

        list($iFieldId, $aOptions) = Phpfox::getService('ecommerce.custom.process')->add($aVals);
        if(!empty($iFieldId))
        {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("window.location.reload();");
        }
        else{
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->alert(_p('please_input_name_of_field'));
        }
    }
    
    public function deleteOption()
    {
        $id = $this->get('id');

        if (Phpfox::getService('ecommerce.custom.process')->deleteOption($id))
        {
            $aFields = Phpfox::getService('ecommerce.custom')->getCustomField();
            $this->remove('#js_current_value_'.$id);
        }
        else
        {
            $this->alert(_p('could_not_delete'));
        }
    }
    
    public function AdminAddCustomFieldBackEnd()
    {
        $iGroupId = $this->get('iGroupId');
        if (intval($this->get('id'))) {
            $this->setTitle(_p('edit_custom_field'));
        } else {
            $this->setTitle(_p('add_custom_field'));
        }
        Phpfox::getComponent('ecommerce.admincp.customfield.add-field', array('iGroupId' => $iGroupId), 'controller');
    }
    
    public function toggleActiveGroup()
    {
        if (Phpfox::getService('ecommerce.custom.group')->toggleActivity($this->get('id')))
        {
            $this->call('$Core.ecommerce_customgroup.toggleGroupActivity(' . $this->get('id') . ')');
        }       
    }
    
    public function getRequestMoneyForm()
	{
		Phpfox::isUser(true);
		
		$this->setTitle(_p('request_money'));
		
		Phpfox::getBlock('ecommerce.request-money-form');		
	}
    
    public function addRequestMoney()
    {
        Phpfox::isUser(true);
        
        $aVals = $this->get('val');

        $aCreditMoney = Phpfox::getService('ecommerce.creditmoney')->getCreditMoney();

        $sAvailableAmount = $aCreditMoney['creditmoney_remain_amount'];

        $fMax = (float) $aVals['maximum'];
        $fMin = (float) Phpfox::getParam('ecommerce.ecommerce_minimum_amount_to_request');
        
        if (!isset($aVals['amount']) || !is_numeric($aVals['amount']))
        {
            Phpfox_Error::set(_p('your_amount_is_not_valid'));
        }
        
        if (isset($aVals['amount']) && ($aVals['amount'] < $fMin || $aVals['amount'] > $fMax))
        {
            $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
            $sCurrencySymbol = Phpfox::getService('core.currency')->getSymbol($sDefaultCurrency);
            
            Phpfox_Error::set(_p('your_request_has_to_be_between_maximum_and_minimum', array('maximum' => $sCurrencySymbol . $fMax, 'minimum' => $sCurrencySymbol . $fMin)));
        }
        
        if (!isset($aVals['reason']) || Phpfox::getLib('parse.format')->isEmpty($aVals['reason']))
        {
            Phpfox_Error::set(_p('please_fill_your_message'));
        }
        
        if (!Phpfox_Error::isPassed())
        {
            $this->call('$("#request_money_submit").prop("disabled", false);');
            $this->call('$(".add_request_loading").hide();');
            $this->errorSet('.request_error_message');
            return;
        }
        
        $aCreditMoney = Phpfox::getService('ecommerce.creditmoney')->getCreditMoney();
        
        $aVals['creditmoney_id'] = $aCreditMoney['creditmoney_id'];
        $aVals['creditmoney_remain_amount'] = $aCreditMoney['creditmoney_remain_amount'];
        
        Phpfox::getService('ecommerce.request.process')->add($aVals);
        
        $this->call('setTimeout(function(){window.location.href = window.location.href}, 100);');
    }
    
    public function cancelRequest()
    {
        $iRequestId = $this->get('id');
        
        $aRequest = Phpfox::getService('ecommerce.request')->get($iRequestId);
        
        if (!$aRequest || $aRequest['user_id'] != Phpfox::getUserId() || $aRequest['creditmoneyrequest_status'] != 'pending')
        {
            $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");
            $this->call('setTimeout(function() {window.location.href = window.location.href},500);');
            $this->alert(_p('your_request_is_not_valid'));
            return;
        }
        
        Phpfox::getService('ecommerce.request.process')->delete($iRequestId, $aRequest['creditmoneyrequest_amount']);
        
        $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");
        $this->call('setTimeout(function() {window.location.href = window.location.href},500);');
    }

	
	public function changeCategoryBidIncrement()
	{
		$iCategoryId = $this->get('categoryId');
		
		Phpfox::getBlock('ecommerce.bidincrementcontent', array('iCategoryId' => $iCategoryId));
		
		$this->html('.bidincrement_content', $this->getContent(false))->show('.bidincrement_content');
		$this->hide('.bidincrement_content_loading');
	}
	
	public function updateBidIncrement()
	{
		$aVals = $this->get('val');
		
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
		
		$oValidator = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_bidincrement_form',
				'aParams' => $aValidation
			)
		);
		
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
            if (isset($aVals['category_id']) && $aVals['category_id'] > 0)
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

                $aBidIncrementSettings = Phpfox::getService('auction.bidincrement')->getSetting($aVals['category_id'], 'user', Phpfox::getUserId());

                if ($aBidIncrementSettings)
                {
                    $aVals['data_id'] = $aBidIncrementSettings['data_id'];
                    
                    Phpfox::getService('auction.bidincrement.process')->editSetting($aVals);
                }
                else
                {
                    Phpfox::getService('auction.bidincrement.process')->addSetting($aVals);
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
		
		if (Phpfox_Error::isPassed())
		{
            $sMessage = '<div class="message">' . _p('update_bid_increment_successfully') . '</div>';
			
			$this->html('.bidincrement_form_msg', $sMessage)->show('.bidincrement_form_msg');
		}
        
		$this->errorSet('.bidincrement_form_msg');
		$this->show('.bidincrement_submit');
		$this->hide('.bidincrement_loading');
	}

    public function addFeedComment()
    {
        Phpfox::isUser(true);
        
        $aVals = (array) $this->get('val'); 

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status']))
        {
            $this->alert(_p('user.add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return;         
        }       
        
        $aProduct = Phpfox::getService('ecommerce')->getQuickProductById($aVals['callback_item_id']);
        
        if (!isset($aProduct['product_id']))
        {
            $this->alert(_p('unable_to_find_the_product_you_are_trying_to_comment_on'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }
        
        $sLink = Phpfox::getLib('url')->permalink('ecommerce.detail', $aProduct['product_id'], $aProduct['name']);
        $aCallback = array(
            'module' => 'ecommerce',
            'table_prefix' => 'ecommerce_',
            'link' => $sLink,
            'email_user_id' => $aProduct['user_id'],
            'subject' => _p('full_name_wrote_a_comment_on_your_product_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aProduct['name'])),
            'message' => _p('full_name_wrote_a_comment_on_your_product_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aProduct['name'])),
            'notification' => 'ecommerce_comment',
            'feed_id' => 'ecommerce_comment',
            'item_id' => $aProduct['product_id']
        );
        
        $aVals['parent_user_id'] = $aVals['callback_item_id'];
        
        if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->callback($aCallback)->addComment($aVals)))
        {
            Phpfox::getLib('database')->updateCounter('ecommerce_product', 'total_comment', 'product_id', $aProduct['product_id']);
            
            Phpfox::getService('feed')->callback($aCallback)->processAjax($iId);
        }
        else 
        {
            $this->call('$Core.activityFeedProcess(false);');
        }       
    }

    public function saveAddress()
    {
		$oFilter = Phpfox::getLib('parse.input');
		
        $aData = array();
        $aData['address_id'] = $this->get('address_id');
        $aVal = $this->get('val'); 
        $aData['country_iso'] = $aVal['country_iso']; 
        $aSearch = $this->get('search'); 
        $aData['country_child_id'] = $aSearch['country_child_id']; 

        $aData['contact_name'] = $oFilter->clean(strip_tags($this->get('contact_name')));
        $aData['address_street'] = $oFilter->clean(strip_tags($this->get('address_street')));
        $aData['address_street_2'] = $oFilter->clean(strip_tags($this->get('address_street_2')));
        $aData['address_city'] = $oFilter->clean(strip_tags($this->get('address_city')));
        $aData['address_postal_code'] = $oFilter->clean(strip_tags($this->get('address_postal_code')));
        $aData['address_country_code'] = $oFilter->clean(strip_tags($this->get('address_country_code')));
        $aData['address_city_code'] = $oFilter->clean(strip_tags($this->get('address_city_code')));
        $aData['address_phone_number'] = $oFilter->clean(strip_tags($this->get('address_phone_number')));
        $aData['address_mobile_number'] = $oFilter->clean(strip_tags($this->get('address_mobile_number')));

        if((int)$aData['address_id']){
            if(count($aData) && Phpfox::getService('ecommerce.process')->saveAddressUser($aData,(int)$aData['address_id'])){
                $this->alert(_p('edit_address_successfully'));
                $this->call("window.location.reload()");
            }   
        }
        else
        {

            if(count($aData) && Phpfox::getService('ecommerce.process')->saveAddressUser($aData)){
                $this->alert(_p('add_address_successfully'));
                $this->call("window.location.reload()");
            }    
        }
        
    }
    public function updateOrderStatus()
    {
        $sStatus = $this->get('status');
        $iOrderId = $this->get('id');
        
        $iViewerId = Phpfox::getUserId();
        
        $aOrder = Phpfox::getService('ecommerce.order')->getOrder($iOrderId);
        if (!$aOrder)
        {
            Phpfox_Error::set(_p('order_is_not_valid'));
            $this->call('$("#popup_order_status_button_' . $iOrderId . '").prop("disabled", false);');
            $this->call('$(".popup_order_status_loading_' . $iOrderId . '").hide();');
            return;
        }
        
        if ($aOrder['seller_user_id'] != $iViewerId)
        {
            Phpfox_Error::set(_p('you_dont_have_permission_to_change_this_order'));
            $this->call('$("#popup_order_status_button_' . $iOrderId . '").prop("disabled", false);');
            $this->call('$(".popup_order_status_loading_' . $iOrderId . '").hide();');
            return;
        }
        
        if (Phpfox::getService('ecommerce.order.process')->updateStatusManageOrders($iOrderId, $sStatus))
        {
            $this->alert(_p('update_order_successfully'));
            $this->call('$("#popup_order_status_button_' . $iOrderId . '").prop("disabled", false);');
            $this->call('$(".popup_order_status_loading_' . $iOrderId . '").hide();');
        }
        else
        {
            $this->call('$("#popup_order_status_button_' . $iOrderId . '").prop("disabled", false);');
            $this->call('$(".popup_order_status_loading_' . $iOrderId . '").hide();');
        }
    }

    public function updateQuantityProduct(){
        $iProductId = $this->get('iProductId');
        $iCartId    = $this->get('iCartId');
        $iQuantity    = $this->get('iQuantity');

        Phpfox::getService('ecommerce.process')->updateQuantityProduct($iCartId,$iProductId,$iQuantity);
    }

    public function updateStatusManageOrders(){
        $order_id = $this->get('order_id');
        $status = $this->get('status');

        if(Phpfox::getService('ecommerce.order.process')->updateStatusManageOrders($order_id,$status)){
            $this->alert(_p('update_status_successfully'));
            $this->call('setTimeout(function() { window.location.reload();}, 1000)');
        }

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
        $aEmail['email_template'] = str_replace('"', '\"', $aEmail['email_template']);
        $aEmail['email_subject'] = Phpfox::getLib('parse.output')->parse($aEmail['email_subject']);
		$aEmail['email_template'] = preg_replace('/[\r]+/', '', $aEmail['email_template']);
        $this->call('$("#email_subject").val("'.$aEmail['email_subject'].'"); $("#email_template").val("'.$aEmail['email_template'].'")');
    
    }

    public function getAllItemBelongToCategory()
    {
        $iNumberItems = Phpfox::getService('ecommerce.category')->getAllItemBelongToCategory($this->get('iCategoryId'));

        echo json_encode(array('status' => 'SUCCESS', 'iNumberItems' => $iNumberItems));

    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'ecommerce_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('ecommerce_category');
    }

    public function updateActivity()
    {
        Phpfox::getService('ecommerce.category.process')->updateActivity($this->get('id'), $this->get('active'), $this->get('sub'));
    }

    public function uomOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'ecommerce_uom',
                'key' => 'uom_id',
                'values' => $aVals['ordering']
            ));

        Phpfox::getLib('cache')->remove('ecommerce_uom', 'substr');
    }

    public function updateUomActivity()
    {
        Phpfox::getService('ecommerce.uom.process')->updateUomActivity($this->get('id'), $this->get('active'));
    }
}
