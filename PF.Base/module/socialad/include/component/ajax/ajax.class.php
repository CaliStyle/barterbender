<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Socialad_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function updatePackageOrder() {
		$sList = $this->get('list');
		$aList = explode(',', $sList);
		Phpfox::getService('socialad.package.process')->updatePackageOrderByList($aList);
	}

	public function hideAdPermanent() {
		$iAdId = $this->get('ad_id');
		Phpfox::getService('socialad.ad.ban')->ban($iAdId, Phpfox::getUserId());
	}

	public function hideAd() {
		$iAdId = $this->get('ad_id');
		$bIsAllowToHideAdPermanently = Phpfox::getParam('socialad.allow_to_hide_ad_permanently');

		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		if($bIsAllowToHideAdPermanently) {
            $this->call("ynsaShowHidePermanentBox({$iAdId});");
		}
	}

	public function actionCampaign() {
		$sAction = $this->get('action');
		$iCampaignId = $this->get('campaign_id');
		$sMessage = '';
		switch($sAction) {
		case 'delete':
			Phpfox::getService('socialad.campaign.process')->delete($iCampaignId);
			$sMessage = _p('delete_campaign_successfully');
			break;
		case 'show_edit_box':
			Phpfox::getBlock('socialad.campaign.edit', array(
				'campaign_id' => $iCampaignId	
			));

			return;
			break;

		case 'edit_campaign':
			$aVals= $this->get('val');
			Phpfox::getService('socialad.campaign.process')->update($aVals);
			$sMessage = _p('edit_campaign_successfully');

		}

		$this->_refresh($sMessage);
	}
	public function refreshAds() {
		$sDivId = $this->get('div_id');
		$sModuleId = $this->get('module_id');
		$iBlockId = $this->get('block_id');

		if(!Phpfox::getUserId()) {
			return false;
		}
		Phpfox::getService('socialad.ad')->displayHtmlAds($aQuery = array(
			'module_id' => $sModuleId,
			'block_id' => $iBlockId,
			'user_id' => Phpfox::getUserId()
		));
		$sHtml = $this->getContent();

		$this->call("$('#{$sDivId}').html('{$sHtml}');");
		$this->call('$Behavior.ynsaRefreshAds();');
		$this->call('$Behavior.ynsaUpadateView(true);');
		$this->call('$Behavior.ynsaInitDisplayAdHiddenJs();');
	}

	public function actionAd() {
		$sAction = $this->get('action');
		$iAdId = $this->get('ad_id');
		$sMessage = '';
		switch($sAction) {
		case 'approve':
			Phpfox::getService('socialad.ad.process')->approveAd($iAdId);
			$sMessage = _p('approve_ad_successfully');
			break;
		case 'deny':
			Phpfox::getService('socialad.ad.process')->denyAd($iAdId);
			$sMessage = _p('deny_ad_successfully');
			break;
		case 'pause':
			Phpfox::getService('socialad.ad.process')->pauseAd($iAdId);
			$sMessage = _p('pause_ad_successfully');
			break;
		case 'resume':
			Phpfox::getService('socialad.ad.process')->resumeAd($iAdId);
			$sMessage = _p('resume_ad_successfully');
			break;
		}

		$this->_refresh($sMessage);
	}

	public function showPayLaterPopup() {
		$iAdId = $this->get('ad_id');
		$bNoButton = $this->get('no_button');

		Phpfox::getBlock('socialad.pay-later', array(
			'iAdId' => $iAdId,
			'bNoButton' => $bNoButton
		));
	}

	public function showAddRequestPopup() {
		Phpfox::getBlock('socialad.addrequest', array(
		));
	}

	public function showAddNewFAQPopup() {
		Phpfox::getBlock('socialad.addnewfaq', array(
		));
	}

	public function showAddRequestPopupInAdmin() {
		Phpfox::getBlock('socialad.addrequestinadmin', array(
		));
	}

	public function showCreditMoneyRequestDetailPopup() {
		$id = $this->get('id');
		Phpfox::getBlock('socialad.creditrequestdetail', array(
			'id' => $id
		));
	}

	public function acceptPendingCreditMoneyRequest() {
		Phpfox::isUser(true);

		//      init
		$creditmoneyrequest_id = $this -> get('id');

		// process
		Phpfox::getService('socialad.ad.process')->updateStatusOfCreditMoneyRequestById($creditmoneyrequest_id
			, Phpfox::getService('socialad.helper')->getConst('creditmoneyrequest.status.approved'));
        $this->alert(_p('accept_request_successfully'));
        $this->call('setTimeout(function(){window.location.reload();},2500);');
	}

	public function rejectPendingCreditMoneyRequest() {
		Phpfox::isUser(true);

		//      init
		$creditmoneyrequest_id = $this -> get('id');
		$creditmoneyrequest_creditmoney_id = $this -> get('creditmoneyrequest_creditmoney_id');

		// process
		$creditMoneyRequest = Phpfox::getService('socialad.ad')->getCreditMoneyRequestById($creditmoneyrequest_id);
		if(isset($creditMoneyRequest['creditmoneyrequest_id'])){
			Phpfox::getService('socialad.ad.process')->updateStatusOfCreditMoneyRequestById($creditmoneyrequest_id
				, Phpfox::getService('socialad.helper')->getConst('creditmoneyrequest.status.rejected'));
	
			$creditMoney = Phpfox::getService('socialad.ad')->getCreditMoneyById($creditmoneyrequest_creditmoney_id);
			if(isset($creditMoney['creditmoney_id'])){
				// update remaining amount of credit money
				Phpfox::getService('socialad.ad.process')->updateRemainingAmountOfCreditMoneyById($creditMoney['creditmoney_id']
					, ( doubleval($creditMoney['creditmoney_remain_amount']) + doubleval($creditMoneyRequest['creditmoneyrequest_amount']) )
				);
			}
		}

		$this->alert(_p('reject_request_successfully'));
        $this->call('setTimeout(function(){window.location.reload();},2500);');
	}

	public function submitAddNewFAQ() {
		Phpfox::isUser(true);

		//      init
		$js_ynsa_question = $this -> get('js_ynsa_question');

		if(strlen(trim($js_ynsa_question)) == 0){
		    $this->call('$(\'#js_ynsa_faq_confirmbtn\').removeClass(\'disabled\').removeAttr(\'disabled\');');
            $this->html('#js_ynsa_question_err',_p('this_field_cannot_be_empty'));
            return false;
		}

		$aVals = array(
			'question' => $js_ynsa_question, 
		);
		Phpfox::getService('socialad.faq.process')->addInFrontEnd($aVals);

		//      end
		$this->alert(_p('add_new_faq_successfully_please_waiting_approval_from_administrator'));
		$this->call('window.location.reload();');
	}

	public function submitAddRequest() {
		Phpfox::isUser(true);

		//      init
		$amount = $this -> get('amount');
		$reason = $this -> get('reason');
		$creditmoney_id = $this -> get('creditmoney_id');
		$yncm_user_id = $this -> get('yncm_user_id');

		//      process		
		$bPass = true;
		if(Phpfox::getService('socialad.helper')->isNumeric($amount) == false || doubleval($amount) <= 0){
			$this->html('#js_ynsa_addrequest_amount_err',_p('please_enter_valid_amount'));
            $bPass = false;
		}
		if(strlen(trim($reason)) == 0){
            $this->html('#js_ynsa_addrequest_reason_err',_p('this_field_cannot_be_empty'));
            $bPass = false;
		}

		$userID = Phpfox::getUserId();
		if((int)$yncm_user_id > 0){
			$userID = $yncm_user_id;
		}

		$creditMoney = Phpfox::getService('socialad.ad')->getCreditMoneyByUserId($userID);
		if(doubleval($amount) > doubleval($creditMoney['creditmoney_remain_amount'])){
            $this->html('#js_ynsa_addrequest_amount_err',_p('amount_cannot_be_greater_than_remaining_amount'));
            $bPass = false;
		}

		if (!$bPass) {
            $this->call('$(\'#js_ynsa_addrequest_confirmbtn\').removeClass(\'disabled\');');
            return false;
        }
		$aVals = array(
			'creditmoneyrequest_creditmoney_id' => $creditmoney_id, 
			'creditmoneyrequest_amount' => $amount, 
			'creditmoneyrequest_reason' => $reason, 
			'creditmoneyrequest_status' => Phpfox::getService('socialad.helper')->getConst('creditmoneyrequest.status.pending'), 
		);

		$id = Phpfox::getService('socialad.ad.process')->addCreditMoneyRequest($aVals);
		if((int)$id > 0){
			// update remaining amount of credit money
			Phpfox::getService('socialad.ad.process')->updateRemainingAmountOfCreditMoneyById($creditMoney['creditmoney_id']
				, ( doubleval($creditMoney['creditmoney_remain_amount']) - doubleval($amount) )
			);
		}
        $this->alert(_p('add_new_request_successfully'));
		$this->call('window.location.reload();');
	}

	public function updateAdView() {
		$iAdIds = $this->get('ad_ids');
		$aIds = explode('-', $iAdIds);

		foreach($aIds as $iId) {
			if((int)$iId > 0){
				Phpfox::getService('socialad.ad.process')->view((int)$iId);
			}
		}
	}

	public function showPackagePopup() {
		$iPackageId = $this->get('package_id');

		Phpfox::getBlock('socialad.package.entry', array(
			'aSaPackage' => Phpfox::getService('socialad.package')->getPackageById($iPackageId),
			'bNoCreateBtn' => true
		));
	}
	public function changeAudience() {
		$aVals = $this->get('val');

		$iAffected = Phpfox::getService('socialad.ad.audience')->getAffectedAudience($aVals);
		$this->call("$(document).trigger('audiencechanged', '{$iAffected}');");
	}

	public function changeCampaignListFilter() { 
		$aVals = $this->get('val');
		Phpfox::getBlock('socialad.campaign.campaign-list', array(
			'aQueryParam' => $aVals	
		));
		$jsEvent = $this->get('custom_event');
		$sHtml = $this->getContent();
		$this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
	}	

	public function changeReportListFilter() { 
		$aVals = $this->get('val');
		Phpfox::getBlock('socialad.report.list', array(
			'aQueryParam' => $aVals	
		));
		$jsEvent = $this->get('custom_event');
		$sHtml = $this->getContent();
		$this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
	}	
	public function changeAdListFilter() { 
		$aVals = $this->get('val');
		Phpfox::getBlock('socialad.ad.ad-list', array(
			'aQueryParam' => $aVals
		));
		$jsEvent = $this->get('custom_event');
		$sHtml = $this->getContent();
		$this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
		$this->call("ynsocialad.helper.initDropdownMenu();");
	}	

	public function changePaymentListFilter() { 
		Phpfox::getBlock('socialad.payment.transaction-list');
		$jsEvent = $this->get('custom_event');
		$sHtml = $this->getContent();
		$this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
	}	

	public function changeCreditMoneyRequestListFilter() { 
		Phpfox::getBlock('socialad.creditmoney-list');
		$jsEvent = $this->get('custom_event');
		$sHtml = $this->getContent();
		$this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
	}	

	public function doItem() {
		$iItemId = $this->get('item_id');
		$iItemTypeId = $this->get('item_type_id');
		if(Phpfox::getService('socialad.ad.item')->doItem($iItemId, $iItemTypeId , Phpfox::getUserId())) {
			$this->_changeContentOfActionOnAdDisplay($iItemId, $iItemTypeId);
		} else {
		}

	}

	public function undoItem() {
		$iItemId = $this->get('item_id');
		$iItemTypeId = $this->get('item_type_id');
		if(Phpfox::getService('socialad.ad.item')->undoItem($iItemId, $iItemTypeId, Phpfox::getUserId() )) {
			$this->_changeContentOfActionOnAdDisplay($iItemId, $iItemTypeId);
		} else {
		}

	}
	public function likeItem() {
		$iItemId = $this->get('item_id');
		$iItemTypeId = $this->get('item_type_id');
		$sTypeId = $this->get('action_type_id');
		if(Phpfox::getService('like.process')->add($sTypeId, $iItemId )) {
			$this->_changeContentOfActionOnAdDisplay($iItemId, $iItemTypeId);
		} else {
		}

		

	}
	private function _changeContentOfActionOnAdDisplay($iItemId, $iItemTypeId) {

		Phpfox::getBlock('socialad.ad.action.action', array(
			'iSaItemTypeId' => $iItemTypeId, 
			'iSaItemId' => $iItemId
		));

		$sHtml = $this->getContent();
		$this->call("$('#js_ynsa_action_holder_{$iItemTypeId}_{$iItemId}').html('{$sHtml}');");
	}

	public function unlikeItem() {
		$iItemId = $this->get('item_id');
		$iItemTypeId = $this->get('item_type_id');
		$sTypeId = $this->get('action_type_id');
		if(Phpfox::getService('like.process')->delete($sTypeId, $iItemId )) {
			$this->_changeContentOfActionOnAdDisplay($iItemId, $iItemTypeId);
		} else {
		}
	}

	public function confirmPayLaterTransaction() {
		$iTransactionId = $this->get('transaction_id');
		Phpfox::getService("socialad.payment.process")->confirmTransaction($iTransactionId);
		$this->_refresh();
	}

	public function cancelPayLaterRequest() {
		$iTransactionId = $this->get('transaction_id');
		Phpfox::getService("socialad.payment.process")->cancelPayLaterRequest($iTransactionId);
		$this->_refresh();
	}

	private function _refresh($sMessage = null) {
		if($sMessage) {
			Phpfox::addMessage($sMessage);
		}
		$this->call("setTimeout(function() { window.location.reload();}, 10)");
	}

	public function changeItem() {
		$iItemId = $this->get('item_id');
		$iItemTypeId = $this->get('item_type_id');
		$aItem = Phpfox::getService('socialad.ad.item')->getItem($iItemId, $iItemTypeId); 

		if($aItem['image_original_full_path']) {
			$sNewImagePath = Phpfox::getService('socialad.ad.image')->createAdImageFromItemImage($aItem['image_original_full_path']);
			$sJs = "ynsocialad.addForm.changeImage('$sNewImagePath');";
			$this->call($sJs);
			// return to client
		 }


	}

	public function changeActionOfPreview() {
		$iItemId = $this->get('item_id');
		$iItemTypeId = $this->get('item_type_id');

		Phpfox::getBlock('socialad.ad.action.action', array(
			'iSaItemTypeId' => $iItemTypeId, 
			'iSaItemId' => $iItemId
		));

		$sHtml = $this->getContent();
		$this->call("$('#js_ynsa_action_holder').html('{$sHtml}');");
	}

	public function getItemsOfType() {
		$iItemTypeId = $this->get('item_type_id');
		$name = $this->get('name');
		$name = trim($name);
		$iUserId = Phpfox::getUserId();
		if(!empty($name)){
			if($iItemTypeId == Phpfox::getService('socialad.ad.item')->getTypeId('external_url')) {
				return false;
			}
			if($name == 'yn_search_all'){
				$name = '';
			}

			list($iCount, $aItems) = Phpfox::getService('socialad.ad.item')->getAllItems($iItemTypeId, $iUserId = Phpfox::getUserId(), $name);
			$result = array();
			foreach($aItems as $key => $val){
				$data_title = Phpfox::getLib('parse.input')->clean($val['title']);
				$data_title = Phpfox::getLib('parse.output')->shorten($data_title, 22, '...');

				$data_description = Phpfox::getLib('parse.input')->clean($val['description']);
				$data_description = Phpfox::getLib('parse.output')->shorten($data_description, 87, '...');

				$result[] = array(
					'value' => $val['id'], 
					'text' => $val['title'], 
					'data_item_type_id' =>$iItemTypeId, 
					'data_title' => $data_title,  
					'data_description' => $data_description,  
					'data_is_have_image' => $val['is_have_image'],  
				);
			}

			echo json_encode($result);
		} else {
			Phpfox::getBlock('socialad.ad.addedit.select-item', array('iSaItemTypeId' => $iItemTypeId));
			$sHtml = $this->getContent();
			$this->call('ynsocialad.addForm.selectItem.$selectItem.html("' . $sHtml . '");');
			$this->call('ynsocialad.addForm.selectItem.initSelectItemList();');
		}		
	}
	
	public function deleteImage()
	{
		Phpfox::getService('socialad.ad.image')->remove(sprintf($this->get('image'), ''));
		
	}

	public function chartGetData() {
		$aVals = $this->get('val');
		$iAdId = $aVals['ad_id'];
		$iLimit = $aVals['period'];	

		$period = array();
		switch ($iLimit) {
			case '1':
				// Today
				$period = Phpfox::getService('socialad.helper')->getPeriodByUserTimeZone('today');
				break;			
			case '2':
				// Yesterday
				$period = Phpfox::getService('socialad.helper')->getPeriodByUserTimeZone('yesterday');
				break;			
			case '3':
				// Last week
				$period = Phpfox::getService('socialad.helper')->getPeriodByUserTimeZone('last_week');
				break;			
			case '4':
				// Range of dates
				$js_end__datepicker = $this->get('js_end__datepicker');
				$js_end__datepicker = explode("/", $js_end__datepicker);
				$js_start__datepicker = $this->get('js_start__datepicker');
				$js_start__datepicker = explode("/", $js_start__datepicker);

				$period['start'] = mktime(0, 0,0, $js_start__datepicker[0], $js_start__datepicker[1], $js_start__datepicker[2]);
				$period['end'] = mktime(0, 0,0, $js_end__datepicker[0], $js_end__datepicker[1], $js_end__datepicker[2]);
				break;			
			default:
				break;
		}

		$sType = $aVals['data_type'];
		$aDatas = Phpfox::getService('socialad.ad.statistic')->getStatisticByDayOfAd($iAdId, 0, $period);
		$aResults = Phpfox::getService('socialad.helper')->convertStatisticDataIntoTableChartFormat($aDatas, array($sType));
		$this->call('ynsocialad.chart.onHandleAdData(\'' . json_encode($aResults) .'\');');
	}

	public function togglePackage() {
		$iPackageId = $this->get('id');
		$iActive = $this->get('active');

		Phpfox::getService('socialad.package.process')->toggle($iPackageId, $iActive);
	}

	public function changePreviewBox() {

		$iAdTypeId = $this->get('ad_type_id');
		$ynsa_ad_id = (int)$this->get('ynsa_ad_id');
		Phpfox::getBlock('socialad.ad.preview.preview', array('iAdTypeId' => $iAdTypeId,'ynsa_ad_id' => $ynsa_ad_id));
		$sHtml = $this->getContent();
        $this->call('ynsocialad.addForm.uploadImage.changePosition();');
		$this->call('ynsocialad.addForm.preview.setPreview("' . $sHtml . '");');
	}

	public function reportChangeAdList() {
		$iCampaignId = $this->get('campaign_id');
		Phpfox::getBlock('socialad.select-ad', array('iCampaignId' => $iCampaignId));

		$sHtml = $this->getContent();

		$this->call("ynsocialad.report.changeHtmlOfSelectAd('{$sHtml}');");
	}

	public function reportLoadData() {

		$aVals = $this->get('val');
		$aQuery = $aVals;
		Phpfox::getBlock('socialad.report.list', array('aQueryParam' => $aQuery));


		$sHtml = $this->getContent();

		$this->call("ynsocialad.report.changeHtmlOfReportTable('{$sHtml}');");
	}
}
