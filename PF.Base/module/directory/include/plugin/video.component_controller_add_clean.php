<?php 
;

if(Phpfox::isModule('directory')){
	$sModule = $this->template()->getVar('sModule');
	if($sModule == 'directory'){
		// check permission 
		$iItem = $this->template()->getVar('iItem');
		$bCanAddVideoInBusiness = Phpfox::getService('directory.permission')->canAddVideoInBusiness($iItem, $bRedirect = false);		
		if($bCanAddVideoInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}
	}
}

;
?>