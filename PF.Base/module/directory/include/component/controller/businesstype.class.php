<?php

defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Controller_Businesstype extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('directory.helper')->buildMenu();
		// check permission 
		$bCanCreateBusiness = false;
		if(Phpfox::getService('directory.permission')->canCreateBusiness()){
			$bCanCreateBusiness = true; 
		}
		$bCanCreateBusinessForClaiming = false; 
		if(Phpfox::getService('directory.permission')->canCreateBusinessForClaiming()){
			$bCanCreateBusinessForClaiming = true; 
		}
		if($bCanCreateBusiness == false && $bCanCreateBusinessForClaiming == false){
			return Phpfox::getService('directory.permission')->canCreateBusiness(true); 
		}
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);

        //https://jira.younetco.com/browse/PFBIZPAGE-606
        if (in_array($sModule, array('groups', 'pages'))) {
            Phpfox::addMessage(_p('you_do_not_have_permission_for_this_action_please_contact_administrator_for_more_details'));
            $this->url()->send('directory');
        }
        if (!empty($sModule) && Phpfox::hasCallback($sModule, 'getItem')) {
            $aCallback = Phpfox::callback($sModule . '.getItem', $iItem);
            if ($aCallback === false) {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
        }
		if(Phpfox::getService('directory.permission')->canCreateBusinessWithLimit() == false){
			return Phpfox_Error::display(_p('directory.you_have_reached_your_creating_limit_please_contact_administrator'));
		}

		$aPackages = Phpfox::getService('directory')->getAllPackages(1);
		$this->template()->assign(array(
			'sBackUrl' => $this->url()->makeUrl('directory.businesstype'), 
			'sNextUrl' => $this->url()->makeUrl('directory.add'), 
			'aPackages' => $aPackages, 
			'bCanCreateBusiness' => $bCanCreateBusiness, 
			'bCanCreateBusinessForClaiming' => $bCanCreateBusinessForClaiming,
			'sModule' => $this->request()->get('module',''),
			'iItem' => $this->request()->getInt('item','')
		));

		$this->template()
			->setBreadcrumb(_p('directory.module_menu_business'), $this->url()->makeUrl('directory'))
			->setBreadcrumb( _p('directory.create_new_business'), $this->url()->makeUrl('directory.businesstype'))
			->setBreadcrumb(_p('directory.create_new_business'), $this->url()->makeUrl('directory.businesstype'), true);

		$this->template()->setTitle(_p('directory.create_new_business'));

		Phpfox::getService('directory.helper')->loadDirectoryJsCss();
	}
}
?>