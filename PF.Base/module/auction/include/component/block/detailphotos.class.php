<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_detailphotos extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnAuctionDetail  = $this->getParam('aYnAuctionDetail');
		$bCanViewPhotoInAuction = true;

		$req6 = $this->request()->get('req6'); 
		if($req6 == 'albums'){
			$hidden_select = 'albums';
		} else {
			$hidden_select = 'photos';
		}

		$sModuleId = Phpfox::getService('auction.helper')->getModuleIdPhoto();
		$sController = $sModuleId . '.add';

		$bCanAddPhotoInAuction = true; 
		$sYnAddParamForNavigateBack = Phpfox::getService('auction.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('search_photos'), 
				'aYnAuctionDetail' => $aYnAuctionDetail, 
				'bCanAddPhotoInAuction' => $bCanAddPhotoInAuction, 
				'bCanViewPhotoInAuction' => $bCanViewPhotoInAuction, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddPhoto' => Phpfox::getLib('url')->makeUrl($sController, array('module' => 'ecommerce', 'item' => $aYnAuctionDetail['aAuction']['product_id'])) . $sYnAddParamForNavigateBack . '_' . $aYnAuctionDetail['aAuction']['product_id'] . '/', 
			)
		);
	}

}

?>
