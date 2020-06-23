<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */


class Socialad_Component_Controller_Admincp_Package_Add extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aVals = $this->request()->getArray('val');
		if($aVals) { //if a form is submitted, we handle the form 
			$this->handleSubmitForm($aVals);
		}
		else { // if no form is submit, we render view 
			$iPackageId = $this->request()->get('id');

			if(!$iPackageId) {// if this is an add new package request 	
				$this->renderAddNewPackage();
			} else { // if this is an edit package request 
				$this->renderEditPackage($iPackageId);
			}
		}
		
	}

	// add + update
	public function handleSubmitForm($aVals = null) { 
		if (!$aVals) { 
			return false;
		}

		if(isset($aVals['package_id']) && $aVals['package_id']) { // editting an ad
			$sMessage =  _p('edit_package_successfully');
		} else {
			$sMessage =  _p('add_package_successfully');
		}

		Phpfox::addMessage($sMessage);
		$iPackageId = Phpfox::getService('socialad.package.process')->handleSubmitForm($aVals);

		// send a request to render edit package through REST API
		if(isset($aVals['package_id']) && $aVals['package_id']) {
			// edit package 
			Phpfox::getLib('url')->send('admincp.socialad.package.add', array('id' => $iPackageId));
		} else {
			// add new package 
			Phpfox::getLib('url')->send('admincp.socialad.package');
		}
	}

	public function renderAddNewPackage() {
		$this->render(array(
			'title' => _p('add_new_package')
		));
	}

	public function renderEditPackage($iPackageId) {
		$aPackage = Phpfox::getService('socialad.package')->getPackageById($iPackageId);
		$this->render(array(
			'package' => $aPackage,
			'title' => _p('edit_package')
		));

	}

	/**
	 * Below is require field to render view
	 * package 
	 * 
	 */
	public function render($aData) {
		$aModules = Phpfox::getService('socialad.ad.placement')->getModules();
		$aBlocks = Phpfox::getService('socialad.ad.placement')->getBlocks();

		$aModules = Phpfox::getService('socialad.helper')->convertModuleToFriendlyName($aModules);
        unset($aModules['socialad']);
		$aItemTypes = Phpfox::getService('socialad.ad.item')->getAllItemTypes();
		$aAdTypes = Phpfox::getService('socialad.ad')->getAllAdTypes();
		// Whatever it is, it always goes to view
        $aCurrentCurrencies = Phpfox::getService('socialad.helper')->getCurrentCurrencies('paypal' );
		$this->template()
			->setTitle($aData['title'])
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb($aData['title'])
			->assign(array(
				'aCurrentCurrencies' =>isset($aCurrentCurrencies[0]) ? $aCurrentCurrencies[0] : null ,
				'aModules' => $aModules,
				'aBlocks' => $aBlocks,
				'aItemTypes' => $aItemTypes,
				'aAdTypes' => $aAdTypes,
				'aBenefitTypes' => Phpfox::getService('socialad.package')->getAllPackageBenefitTypes(),
		));

		if(isset($aData['package'])) {
			$this->template()->assign(array(
				'aForms' => $aData['package']
			));
		}

		Phpfox::getService('socialad.helper')->loadAdminSocialAdJsCss();
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
		(($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Component_Controller_Admincp_Package_Add_clean')) ? eval($sPlugin) : false);
	}

}

