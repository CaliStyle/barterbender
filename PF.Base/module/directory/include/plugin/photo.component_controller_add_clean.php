<?php 
;

if(Phpfox::isModule('directory')){
	$sModuleContainer = $this->template()->getVar('sModuleContainer');
	if($sModuleContainer == 'directory'){
		// check permission 
		$iItem = $this->template()->getVar('iItem');
		$bCanAddPhotoInBusiness = Phpfox::getService('directory.permission')->canAddPhotoInBusiness($iItem, $bRedirect = false);		
		if($bCanAddPhotoInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}
	}
}

;
?>