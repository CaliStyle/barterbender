<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailmembers extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewMemberInBusiness = true;
		if($bCanViewMemberInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sModuleId = 'member';
		$sController = '';

		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_members'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanViewMemberInBusiness' => $bCanViewMemberInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
			)
		);
	}

}

?>
