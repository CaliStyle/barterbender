<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailblogs extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
        $sModuleId = Phpfox::getService('directory.helper')->getModuleIdBlog();

        // Get permission
        if ($sModuleId == 'ynblog') {
            $bCanViewBlogInBusiness = Phpfox::getService('directory.permission')->canViewAdvBlogInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
            $bCanAddBlogInBusiness = Phpfox::getService('directory.permission')->canAddAdvBlogInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
		} else {
            $bCanViewBlogInBusiness = Phpfox::getService('directory.permission')->canViewBlogInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
            $bCanAddBlogInBusiness = Phpfox::getService('directory.permission')->canAddBlogInBusiness($aYnDirectoryDetail['aBusiness']['business_id'], $bRedirect = false);
        }
		if($bCanViewBlogInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}

		$hidden_select = '';
		$sController = $sModuleId . '.add';

		$sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack();

		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_blogs'),
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'bCanAddBlogInBusiness' => $bCanAddBlogInBusiness, 
				'bCanViewBlogInBusiness' => $bCanViewBlogInBusiness, 
				'hidden_select' => $hidden_select, 
				'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack, 
				'sModuleId' => $sModuleId, 
				'sController' => $sController, 
				'sUrlAddBlog' => Phpfox::getLib('url')->makeUrl($sController, array()) .'module_directory/item_'.$aYnDirectoryDetail['aBusiness']['business_id'].'/',
                'sCustomClassName' => 'ync-block'
			)
		);
	}

}

?>
