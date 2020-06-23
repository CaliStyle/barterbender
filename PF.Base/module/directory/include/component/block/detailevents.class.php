<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailevents extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewEventInBusiness = Phpfox::getService('directory.permission')->canViewEventInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		if($bCanViewEventInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdEvent();
		$sController = $sModuleId . '.add';

		$bCanAddEventInBusiness = Phpfox::getService('directory.permission')->canAddEventInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_events'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanAddEventInBusiness' => $bCanAddEventInBusiness, 
				'bCanViewEventInBusiness' => $bCanViewEventInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddEvent' => Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_'.$aYnDirectoryDetail['aBusiness']['business_id'].'/',
                'sCustomClassName' => 'ync-block'
			)
		);
	}

}

?>
