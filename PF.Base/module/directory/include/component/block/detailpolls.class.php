<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailpolls extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewPollsInBusiness = Phpfox::getService('directory.permission')->canViewPollsInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		if($bCanViewPollsInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdPolls();
		$sController = $sModuleId . '.add';

		// list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($aYnDirectoryDetail['aBusiness']['business_id'], $aYnDirectoryDetail['aBusiness']);
		$bCanAddPollsInBusiness = Phpfox::getService('directory.permission')->canAddPollsInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_polls'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanAddPollsInBusiness' => $bCanAddPollsInBusiness, 
				'bCanViewPollsInBusiness' => $bCanViewPollsInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddPolls' => Phpfox::getLib('url')->makeUrl($sController, array()).'module_directory/item_'.$aYnDirectoryDetail['aBusiness']['business_id'].'/',
			)
		);
	}

}

?>
