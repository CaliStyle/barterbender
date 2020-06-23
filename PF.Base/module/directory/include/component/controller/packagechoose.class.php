<?php

defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Controller_Packagechoose extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('directory.helper')->buildMenu();
		// init 
		$type = $this->request()->get('type', false);
		
		if($type === false || in_array($type, array('business', 'claiming')) == false){
			Phpfox::getLib('url')->send($this->url()->makeUrl('directory.businesstype'));
		}

		// process 
		$aPackages = Phpfox::getService('directory')->getAllPackages(1);
		$this->template()
			->setBreadcrumb(_p('directory.module_menu_business'), $this->url()->makeUrl('directory'))
			->setBreadcrumb( _p('directory.create_new_business'), $this->url()->makeUrl('directory.businesstype'))
			->setBreadcrumb(_p('directory.create_new_business'), $this->url()->makeUrl('directory.businesstype'), true);

		$this->template()->setTitle(_p('directory.create_new_business'));

		$this->template()->assign(array(
			'sBackUrl' => $this->url()->makeUrl('directory.businesstype'), 
			'aPackages' => $aPackages, 
		));

		Phpfox::getService('directory.helper')->loadDirectoryJsCss();

		// end 
	}
}
?>