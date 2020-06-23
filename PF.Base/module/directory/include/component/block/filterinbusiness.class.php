<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_filterinbusiness extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

		$sPlaceholderKeyword  = $this->getParam('sPlaceholderKeyword');
		$ajax_action  = $this->getParam('ajax_action');
		$result_div_id  = $this->getParam('result_div_id');
		$custom_event  = $this->getParam('custom_event');
		$is_prevent_submit  = $this->getParam('is_prevent_submit');
		$hidden_type  = $this->getParam('hidden_type');
		$hidden_businessid  = $this->getParam('hidden_businessid');
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$hidden_select  = $this->getParam('hidden_select');

		// $iCampaignId  = $this->getParam('iFilterCampaignId');
		// $iDefaultStatusId = Phpfox::getService('socialad.helper')->getConst('ad.status.running');

		// // check param if existing
		// $aQueryParam = $this->getParam('aQueryParam');
		// if(is_array($aQueryParam)){
		// 	$aQueryParam = array_merge($aQueryParam, array('ad_status' => $iDefaultStatusId));
		// } else {
		// 	$aQueryParam = array('ad_status' => $iDefaultStatusId);
		// }
		// $this->setParam('aQueryParam', $aQueryParam);
		
		// $this->template()->assign(array(
		// 	'aAdStatuses' => Phpfox::getService('socialad.ad')->getAllStatuses(),
		// 	'aAdTypes' => Phpfox::getService('socialad.ad')->getAllAdTypes(),
		// 	'iFilterCampaignId' => $iCampaignId,
		// 	'iFilterDefaultStatusId' => $iDefaultStatusId
		// ));

		$this->template()->assign(array(
				'sPlaceholderKeyword' => $sPlaceholderKeyword, 
				'ajax_action' => $ajax_action, 
				'result_div_id' => $result_div_id, 
				'custom_event' => $custom_event, 
				'is_prevent_submit' => $is_prevent_submit, 
				'hidden_type' => $hidden_type, 
				'hidden_businessid' => $hidden_businessid, 
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'hidden_select' => $hidden_select, 
			)
		);
	}

}

?>
