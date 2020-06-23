<?php 
;

if(Phpfox::isModule('directory')){

	$sModule = $this->template()->getVar('sModule');

	if($sModule == 'directory'){
		// check permission 
		$iItem = $this->template()->getVar('iItem');
		$bCanAddEventInBusiness = Phpfox::getService('directory.permission')->canAddEventInBusiness($iItem, $bRedirect = false);
		if($bCanAddEventInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}
	}
}

;
?>