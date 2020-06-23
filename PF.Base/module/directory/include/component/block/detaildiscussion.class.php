<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detaildiscussion extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$bCanViewDiscussionInBusiness = Phpfox::getService('directory.permission')->canViewDiscussionInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		if($bCanViewDiscussionInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sModuleId = Phpfox::getService('directory.helper')->getModuleIdDiscussion();
		$sController = $sModuleId . '.post.thread';

		// list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($aYnDirectoryDetail['aBusiness']['business_id'], $aYnDirectoryDetail['aBusiness']);
		$bCanAddDiscussionInBusiness = Phpfox::getService('directory.permission')->canAddDiscussionInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack(); 

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_threads'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanAddDiscussionInBusiness' => $bCanAddDiscussionInBusiness, 
				'bCanViewDiscussionInBusiness' => $bCanViewDiscussionInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddDiscussion' => Phpfox::getLib('url')->makeUrl($sController, array()) .'module_directory/item_'.$aYnDirectoryDetail['aBusiness']['business_id'].'/',
                'sCustomClassName' => 'ync-block'
			)
		);
	}

}

?>
