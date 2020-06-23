<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Allbusinesses extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('directory.helper')->buildMenu();
		$this->template()->setTitle(_p('directory.all_businesses'))
			->setBreadcrumb(_p('directory.module_menu_business'), $this->url()->makeUrl('directory'))
			->setBreadcrumb( _p('directory.all_businesses'), $this->url()->makeUrl('directory.allbusinesses'), true);

		$this->template()->assign(array(
		));

		Phpfox::getService('directory.helper')->loadDirectoryJsCss();
	}
}
?>