<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailcoupons extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewCouponInBusiness = Phpfox::getService('directory.permission')->canViewCouponInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		if($bCanViewCouponInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdCoupon();
		$sController = $sModuleId . '.add';

		// list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($aYnDirectoryDetail['aBusiness']['business_id'], $aYnDirectoryDetail['aBusiness']);
		$bCanAddCouponInBusiness = Phpfox::getService('directory.permission')->canAddCouponInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_coupons'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanAddCouponInBusiness' => $bCanAddCouponInBusiness, 
				'bCanViewCouponInBusiness' => $bCanViewCouponInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddCoupon' => Phpfox::getLib('url')->makeUrl($sController, array()) .'module_directory/item_'.$aYnDirectoryDetail['aBusiness']['business_id'].'/',
                'sCustomClassName' => 'ync-block'
			)
		);
	}

}

?>
