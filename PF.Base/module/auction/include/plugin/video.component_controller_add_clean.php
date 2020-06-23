<?php 
;

if(Phpfox::isModule('auction')){
	$sModule = $this->template()->getVar('sModule');
	if($sModule == 'auction'){
		// check permission 
		$iItem = $this->template()->getVar('iItem');
		$bCanAddVideoInAuction = true;
		if($bCanAddVideoInAuction == false){
			$this->url()->send('subscribe', null, _p('unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}
	}
}

;
?>