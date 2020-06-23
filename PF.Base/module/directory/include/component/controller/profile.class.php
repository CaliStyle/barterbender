<?php

defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Controller_Profile extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('directory.helper')->buildMenu();
		$this->setParam('bIsProfile', true);
		$aUser = $this->getParam('aUser');
				
		Phpfox::getComponent('directory.index', array('bNoTemplate' => true), 'controller');		
	}

	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('directory.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}

}
?>