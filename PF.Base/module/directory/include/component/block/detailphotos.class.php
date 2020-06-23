<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailphotos extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewPhotoInBusiness = Phpfox::getService('directory.permission')->canViewPhotoInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		if($bCanViewPhotoInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$req6 = $this->request()->get('req6'); 
		if($req6 == 'albums'){
			$hidden_select = 'albums';
		} else {
			$hidden_select = 'photos';
		}

		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdPhoto();
		$sController = $sModuleId . '.add';

		$bCanAddPhotoInBusiness = Phpfox::getService('directory.permission')->canAddPhotoInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_photos'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanAddPhotoInBusiness' => $bCanAddPhotoInBusiness, 
				'bCanViewPhotoInBusiness' => $bCanViewPhotoInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddPhoto' => Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_'.$aYnDirectoryDetail['aBusiness']['business_id'].'/', 
			)
		);
	}

}

?>
