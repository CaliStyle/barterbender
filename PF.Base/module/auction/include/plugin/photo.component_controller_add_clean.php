<?php 
;

if(Phpfox::isModule('auction')){
	$sModuleContainer = $this->template()->getVar('sModuleContainer');
	if($sModuleContainer == 'auction'){
		// check permission 
		$iItem = $this->template()->getVar('iItem');
		$bCanAddPhotoInAuction = true;
		if($bCanAddPhotoInAuction == false){
			$this->url()->send('subscribe', null, _p('unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}
	}
}

;
?>