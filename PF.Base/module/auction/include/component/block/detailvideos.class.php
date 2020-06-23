<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_detailvideos extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnAuctionDetail  = $this->getParam('aYnAuctionDetail');
		$bCanViewVideoInAuction = true;
		$hidden_select = '';
		$sModuleId = Phpfox::getService('auction.helper')->getModuleIdVideo();
		$sController = $sModuleId . '.add';

		$bCanAddVideoInAuction = true;
		$sYnAddParamForNavigateBack = Phpfox::getService('auction.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('search_videos'),
				'aYnAuctionDetail' => $aYnAuctionDetail, 
				'bCanAddVideoInAuction' => $bCanAddVideoInAuction, 
				'bCanViewVideoInAuction' => $bCanViewVideoInAuction, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddVideo' => Phpfox::getLib('url')->makeUrl($sController, array('module' => 'ecommerce', 'item' => $aYnAuctionDetail['aAuction']['product_id'])) . $sYnAddParamForNavigateBack . '_' . $aYnAuctionDetail['aAuction']['product_id'] . '/', 
			)
		);
	}

}

?>
