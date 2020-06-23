<?php 
;

if(Phpfox::isModule('directory')){

	$sModule = $this->template()->getVar('yndirectory_module');

	if($sModule == 'directory'){
		// check permission 
		$iItem = $this->template()->getVar('yndirectory_item');
		$bCanAddPollsInBusiness = Phpfox::getService('directory.permission')->canAddPollsInBusiness($iItem, $bRedirect = false);
		if($bCanAddPollsInBusiness == false){
			$this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}
	}
}

;
?>