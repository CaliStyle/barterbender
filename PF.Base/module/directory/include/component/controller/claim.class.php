<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_claim extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('directory.helper')->buildMenu();
		Phpfox::getService('directory.permission')->canClaimBusiness(true); 
		Phpfox::getLib('url')->send(Phpfox::getLib('url')->permalink('directory', null, null) . 'view_claimingbusiness/');
	}
}
?>