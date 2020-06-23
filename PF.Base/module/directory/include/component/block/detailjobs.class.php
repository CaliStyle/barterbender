<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailjobs extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewJobInBusiness = Phpfox::getService('directory.permission')->canViewJobInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		if($bCanViewJobInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdJob();
		$sController = $sModuleId . '.add';

		$bCanAddJobInBusiness = Phpfox::getService('directory.permission')->canAddJobInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_jobs'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanAddJobInBusiness' => $bCanAddJobInBusiness, 
				'bCanViewJobInBusiness' => $bCanViewJobInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddJob' => Phpfox::getLib('url')->makeUrl($sController, array()).'module_directory/item_'.$aYnDirectoryDetail['aBusiness']['business_id'].'/',
			)
		);
	}

}

?>
