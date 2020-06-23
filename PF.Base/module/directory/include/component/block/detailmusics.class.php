<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailmusics extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewMusicInBusiness = Phpfox::getService('directory.permission')->canViewMusicInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		if($bCanViewMusicInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdMusic();
		$sController = $sModuleId . '.upload';

		$bCanAddMusicInBusiness = Phpfox::getService('directory.permission')->canAddMusicInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_songs'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanAddMusicInBusiness' => $bCanAddMusicInBusiness, 
				'bCanViewMusicInBusiness' => $bCanViewMusicInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddMusic' => Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_directory/item_'.$aYnDirectoryDetail['aBusiness']['business_id'].'/', 
			)
		);
	}

}

?>
