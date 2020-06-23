<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailfollowers extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewFollowerInBusiness = true;
		if($bCanViewFollowerInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sModuleId = 'follower';
		$sController = '';

		// list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($aYnDirectoryDetail['aBusiness']['business_id'], $aYnDirectoryDetail['aBusiness']);
		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_followers'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanViewFollowerInBusiness' => $bCanViewFollowerInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController,
                'sCustomClassName' => 'ync-block'
			)
		);
	}

}

?>
