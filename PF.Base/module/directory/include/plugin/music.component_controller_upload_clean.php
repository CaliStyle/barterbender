<?php 
if(Phpfox::isModule('directory')){
	$sModule = Phpfox::getLib('template')->getVar('sModule');

	if($sModule == 'directory'){
		// check permission 
		$iItem = Phpfox::getLib('template')->getVar('iItem');
		$bCanAddMusicInBusiness = Phpfox::getService('directory.permission')->canAddMusicInBusiness($iItem, $bRedirect = false);
		if($bCanAddMusicInBusiness == false){
			Phpfox::getLib('url')->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
		}
	}
}
