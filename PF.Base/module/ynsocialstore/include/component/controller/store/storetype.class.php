<?php

defined('PHPFOX') or exit('NO DICE!');


class Ynsocialstore_Component_Controller_Store_Storetype extends Phpfox_Component
{
	public function process()
	{
		Phpfox::isUser(true);
		Phpfox::getService('ynsocialstore.helper')->buildMenu();
		$sError = "";
		// check permission 
		$bCanCreateStore = false; 
		if(Phpfox::getService('ynsocialstore.permission')->canCreateStore()){
			$bCanCreateStore = true; 
		}
		else{
			$sError = _p('you_do_not_have_permission_to_create_a_store_please_contact_administrator');
		}
		
		if(Phpfox::getService('ynsocialstore.permission')->canCreateStoreWithLimit() == false){
			$sError = _p('you_have_reached_your_creating_store_limit_please_contact_administrator');
		}

		$aPackages = Phpfox::getService('ynsocialstore')->getAllPackages(true);


		$this->template()->assign(array(
			'sBackUrl' => $this->url()->makeUrl('ynsocialstore.store.storetype'), 
			'sNextUrl' => $this->url()->makeUrl('ynsocialstore.store.add'), 
			'aPackages' => $aPackages, 
			'bCanCreateStore' => $bCanCreateStore, 
			'sError' => $sError,
			'sModule' => $this->request()->get('module',''),
			'iItem'	=> $this->request()->get('item','')
		));

		$this->template()
			->setTitle(_p('open_new_store'))
			->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
			->setBreadcrumb(_p('open_new_store'), $this->url()->makeUrl('ynsocialstore.store.storetype'),true);


		$this->template()->setTitle(_p('open_new_store'));

		Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
	}
}
?>