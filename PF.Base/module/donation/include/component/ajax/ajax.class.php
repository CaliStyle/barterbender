<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: ajax.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class Donation_Component_Ajax_Ajax extends Phpfox_Ajax {

    function config()
    {
        $iPageId = (int) $this->get('iPageId');
        if (!Phpfox::getService('donation')->isPageOwner($iPageId, Phpfox::getUserId()))
        {
            $this->alert(_p("donation.do_not_have_permission_to_perform_this_action"));
            return false;
        }
        Phpfox::getBlock('donation.config', array('iPageId' => $iPageId, 'bAjaxCall' => true));
    }

    public function loadMoreDonors()
    {
        $iPageId = (int) $this->get('iPageId');
        $iCurrentOffset = (int) $this->get('iCurrentOffset');
        $iTotalDonors = (int) $this->get('iTotalDonors');
		$iCurrentPage = (int) $this->get('iCurrentPage');
        Phpfox::getBlock('donation.donors', array(
            'iPageId' => $iPageId,
            'iOffset' => $iCurrentPage,
            'iTotalDonors' => $iTotalDonors
        ));
        $iLimit = Phpfox::getParam('donation.number_of_fetched_donors');
        if ($iLimit <= 0)
        {
            $iLimit = 0;
        }
        $iNewOffset = $iCurrentOffset + $iLimit;
		$iNewPage = $iCurrentPage + 1;
        $htmlContent = $this->getContent();
        $this->call("$('#load_more_ajax_holder').append(\" " . $htmlContent . "\");");
		$this->call("$('input[name=current_page]').val('" . $iNewPage . "');");
        $this->call("$('input[name=current_donor_limit]').val('" . $iNewOffset . "');");
        $this->call("$('#donorlist_info').html(\" " .
                _p('donation.display_current_number_of_total_donors', array(
                    'current_number' => (($iNewOffset > $iTotalDonors) ? $iTotalDonors : $iNewOffset),
                    'total' => $iTotalDonors
                ))
                . ( ($iNewOffset >= $iTotalDonors) ? "" : (" <a href='#' name='load_more_link' style='color:blue' id='load_more_link' onclick='loadMoreDonors(); return false;'>" .
                        _p('donation.load_more') . "</a>" ) ) . " \");");
    }

    public function deleteUser()
    {
        $oDonation = Phpfox::getService('donation');
        $iPageId = (int) $this->get('iPageId');
        if (!$oDonation->isPageOwner($iPageId, Phpfox::getUserId()))
        {
            $this->alert(_p("donation.do_not_have_permission_to_perform_this_action"));
            return false;
        }

        $iDonationId = (int) $this->get('iDonationId');
        $bIsGuest = ($this->get('bIsGuest')) ? $this->get('bIsGuest') : 0;
        $iCurrentOffset = (int) $this->get('iCurrentOffset');
        Phpfox::getService('donation.process')->deleteDonation($iPageId, $iDonationId, $bIsGuest);

        $iPageUserId = (int) $oDonation->getUserIdOfPage($iPageId);

        $this->searchAjax($iPageId, $iPageUserId, $iCurrentOffset);
    }

    public function searchAjax($iPageId = 0, $iUserId = 0, $iCurrentOffset)
    {
        Phpfox::getBlock('donation.donorlist', array('iCurrentOffset' => $iCurrentOffset, 'iPageId' => $iPageId, 'iUserId' => $iUserId, 'search' => true, 'friend_module_id' => $this->get('friend_module_id'), 'friend_item_id' => $this->get('friend_item_id'), 'page' => $this->get('page'), 'find' => $this->get('find'), 'letter' => $this->get('letter'), 'input' => $this->get('input'), 'view' => $this->get('view'), 'type' => $this->get('type')));
        $this->call('$(\'#js_friend_search_content\').html(\'' . $this->getContent() . '\'); updateFriendsList();');
    }

    function updateDonationOnPage()
    {
        $iPageId = (int) $this->get('iPageId');
        $iActive = (int) $this->get('iActive');
        if ((int) $iPageId > 0)
        {
            Phpfox::getService('donation')->updateDonationOnPage($iPageId, $iActive);
            $sComplete = _p('donation.update_completed');
            $this->call('$(".message").html("' . $sComplete . '").slideDown(200).delay(1500).fadeOut(1000);');
        }
    }

    //show donation detail
    function detail()
    {
        $iPageId = (int) $this->get('iPageId');
        $sUrl = $this->get('sUrl');
        $iUserId = Phpfox::getService('donation')->getUserIdOfPage($iPageId);

        Phpfox::getBlock('donation.detail', array('iPageId' => $iPageId, 'sUrl' => $sUrl));
    }

    //show donation detail
    function addToDonationLists()
    {
        $oDonation = Phpfox::getService('donation');
        $iPageId = (int) $this->get('iPageId');
        $iUserId = (int) $this->get('iUserId');
        if (!$oDonation->checkPermissions('can_donate'))
        {
            $this->alert(_p("donation.do_not_have_permission_to_perform_this_action"));
            return false;
        }
        $fQuanlity = (float) $this->get('quanlity');
        $bNotShowName = (int) $this->get('bNotShowName');
        $bNotShowMoney = (int) $this->get('bNotShowMoney');
        $bNotShowFeed = (int) $this->get('bNotShowFeed');
        $sCurrency = $this->get('sCurrency');
        $sGuestName = $this->get('sGuestName');
        $sUrl = urlencode($this->get('sUrl'));
        
        $sComplete = _p('donation.thanks_you_for_your_donation');
        $sWaiting = _p('donation.please_waiting_for_redirect_to_paypal');
        
        $this->call('$("#dMessage").html("' . $sComplete . '").slideDown(200).delay(1000).fadeOut(500);');
        $this->call('setTimeout(function(){$("#dMessage").html("' . $sWaiting . '").slideDown(200);},1500);');
        
        $iUserPageId = $oDonation->getUserIdOfPage($iPageId);
        if ($iPageId)
        {
            $sPaypalEmail = $oDonation->getEmailPaypalByPageId($iPageId);
        }
        $sPageTitle = $oDonation->getPageDetail($iPageId);
        $sGateway = 'paypal';
        $aInvoice = array();
        if ($sGateway === 'paypal')
        {
            if (!Phpfox::isUser())
            {
                if (!$oDonation->checkGuestName($sGuestName))
                {
                    $this->alert(_p('donation.wrong_name_format'));
                    return false;
                }
                $sCustomField = 'donation|' . $iPageId . ':' . $sGuestName . ':' . $bNotShowName . ':' . $bNotShowFeed . ':' . $bNotShowMoney . ':' . 1;
                $aInvoice = array(
                    'user_id' => $sGuestName,
                    'not_show_feed' => $bNotShowFeed,
                    'not_show_money' => $bNotShowMoney,
                    'not_show_name' => $bNotShowName,
                    'page_id' => $iPageId,
                    'is_guest' => 1
                );
            }
            else
            {
                $sCustomField = 'donation|' . $iPageId . ':' . $iUserId . ':' . $bNotShowName . ':' . $bNotShowFeed . ':' . $bNotShowMoney . ':' . 0;
                $aInvoice = array(
                    'user_id' => $iUserId,
                    'not_show_feed' => $bNotShowFeed,
                    'not_show_money' => $bNotShowMoney,
                    'not_show_name' => $bNotShowName,
                    'page_id' => $iPageId,
                    'is_guest' => 0,
                    'amount' => $fQuanlity,
                    'currency' => $sCurrency
                );
            }
            $iInvoiceId = Phpfox::getService('donation.process')->createInvoice($aInvoice);
			$sCorePath = Phpfox::getParam('core.path');
     		$sCorePath = str_replace("index.php".PHPFOX_DS, "", $sCorePath);
			$sCorePath .= 'PF.Base'.PHPFOX_DS;
            $aParam = array(
                'paypal_email' => $sPaypalEmail,
                'amount' => $fQuanlity,
                'currency_code' => $sCurrency,
                'item_name' => (($iPageId === -1) ? urlencode(_p('donation.page_donation_title_homepage')) : urlencode(_p('donation.donation_for_page_page_name', array('page_name' => $sPageTitle)))),
                'custom' => 'donation|' . $iInvoiceId,
                'return' => $sCorePath . 'module/donation/static/thankyou.php?sLocation=' . $sUrl,
                'recurring' => 0
            );
        }
        else if ($sGateway === '2checkout')
        {
            $aParam = array(
                '2co_id' => isset($s2checkoutId)?$s2checkoutId:0,
                'amount' => $fQuanlity,
                'currency_code' => _p('donation.currency_type'),
                'item_name' => 'donation',
                'item_number' => 'donation|' . $iPageId . ':' . $iUserId . ':' . $bNotShowName . ':' . $bNotShowFeed . ':' . $bNotShowMoney,
                'return' => '',
                'recurring' => 0
            );
        }
        $oPayment = Phpfox::getService('donation.gateways')->load($sGateway, $aParam);
        if (!$oPayment)
        {
            $this->alert(_p('donation.problem_this_gateway_is_temporary_disabled'));
        }
        else
        {
            $this->call('window.location = "' . $oPayment->getCheckoutUrl() . '"');
        }
    }

    function updateConfig()
    {
        $oDonation = Phpfox::getService('donation');
        $iPageId = $this->get('iPageId');
        if (!$oDonation->isPageOwner($iPageId, Phpfox::getUserId()))
        {
            $this->alert(_p("donation.do_not_have_permission_to_perform_this_action"));
            return false;
        }
        $sEmail = $this->get('email');
        $sContent = $this->get('content');
        $sTermOfService = $this->get('term_of_service');
        $is_active = $this->get('donation');
        $sSubject = $this->get('email_subject');
        $aVals = $this->get('val');
        $sEmailContent = $aVals['email_content'];
        $iUserId = Phpfox::getUserId();
        if ($is_active)
        {  
            // if set yes
            if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+(\.([a-zA-Z0-9\._-]+)){1,2}$/", $sEmail))
            {
                $iResult = $oDonation->updateConfig($iUserId, $iPageId, $sEmail, $sContent, $sTermOfService, $sSubject, $sEmailContent, $is_active);
                if (!$iResult)
                {
                    $sError = _p('donation.problem_found_can_not_update_donation_configuration');
                    $this->call('$(".error_message").html("' . $sError . '").slideDown(200).delay(1500).fadeOut(1000);');
                }
                else
                {
                    $sComplete = _p('donation.update_completed');
                    $this->call('$("#postform_id").find(".message").html("' . $sComplete . '").slideDown(200).delay(1500).fadeOut(1000);');
                }
            }
            else
            {
                $sError = _p('donation.please_input_valid_email_address');
                $this->call('$(".error_message").html("' . $sError . '").slideDown(200).delay(1500).fadeOut(1000);');
            }
        }
        else
        {  
            //if set no dont need to validate;				
            $iResult = $oDonation->updateConfig($iUserId, $iPageId, $sEmail, $sContent, $sTermOfService, $sSubject, $sEmailContent, $is_active);
            if (!$iResult)
            {
                $sError = _p('donation.problem_found_can_not_update_donation_configuration');
                $this->call('$(".error_message").html("' . $sError . '").slideDown(200).delay(1500).fadeOut(1000);');
            }
            else
            {
                $sComplete = _p('donation.update_completed');
                $this->call('$("#postform_id").find(".message").html("' . $sComplete . '").slideDown(200).delay(1500).fadeOut(1000);');
            }
        }
    }
	// payment
	public function updateGatewayActivity()
	{
		if (Phpfox::getService('donation.gateway.process')->updateActivity($this->get('gateway_id'), $this->get('active')))
		{
			
		}		
	}
	
	public function updateGatewayTest()
	{
		if (Phpfox::getService('donation.gateway.process')->updateTest($this->get('gateway_id'), $this->get('active')))
		{
			
		}			
	}
}

?>