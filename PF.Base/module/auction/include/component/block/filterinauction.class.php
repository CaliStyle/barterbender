<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_filterinauction extends Phpfox_Component {

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
		$hidden_productid  = $this->getParam('hidden_productid');
		$aYnAuctionDetail  = $this->getParam('aYnAuctionDetail');
		$hidden_select  = $this->getParam('hidden_select');


		$this->template()->assign(array(
				'sPlaceholderKeyword' => $sPlaceholderKeyword, 
				'ajax_action' => $ajax_action, 
				'result_div_id' => $result_div_id, 
				'custom_event' => $custom_event, 
				'is_prevent_submit' => $is_prevent_submit, 
				'hidden_type' => $hidden_type, 
				'hidden_productid' => $hidden_productid, 
				'aYnAuctionDetail' => $aYnAuctionDetail, 
				'hidden_select' => $hidden_select, 
			)
		);
	}

}

?>
