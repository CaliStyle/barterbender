<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
class Directory_Component_Controller_Admincp_Package_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	*/
	public function process()
	{
		$bIsEdit = false;
		
		$aValidation = array(
			'name' => array(
				'def' => 'required',
				'title'=> _p('directory.name_of_package_cannot_be_empty')
			),
		);	
		
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_add_package_form', 
				'aParams'   => $aValidation
		));

		if ($iEditId = $this->request()->getInt('id'))
		{
			if ($aPackage = Phpfox::getService('directory.package')->getById($iEditId))
			{
				$bIsEdit = true;
				$this->template()->assign(array(
					'aForms' => $aPackage,
				));
			}
		}		

		if ($aVals = $this->request()->getArray('val'))
		{
			if ($oValid->isValid($aVals)){
				if($bIsEdit){
					if (Phpfox::getService('directory.package.process')->update($aPackage['package_id'], $aVals))
					{
						$this->url()->send('admincp.directory.package.add', array('id' => $aPackage['package_id']), _p('directory.package_successfully_updated'));
					}
					else{
						$aVals['package_id'] = 	$aPackage['package_id'];	
						$this->template()->assign(array(
							'aForms' => $aVals,
						));
					
					}
				}
				else{
					if (Phpfox::getService('directory.package.process')->add($aVals))
					{
						$this->url()->send('admincp.directory.package.add', null, _p('directory.package_successfully_added'));
					}
					else{
						if(!isset($aVals['settings'])){
							$aVals['settings'] = 	array();	
						}
						$this->template()->assign(array(
							'aForms' => $aVals,
						));
					
					}
				}
			}
			else{
				/*retaining data*/
				if($bIsEdit){
					$aVals['package_id'] = 	$aPackage['package_id'];	
				};
				if(!isset($aVals['settings'])){
							$aVals['settings'] = 	array();	
				}	
				$this->template()->assign(array(
							'aForms' => $aVals,
				));
			}
		}

		$aThemes = Phpfox::getService('directory')->getAllThemes();
		$aModules = Phpfox::getService('directory')->getAllModule();
		$aPackageSettings = Phpfox::getService('directory')->getAllPackageSetting();

		$this->template()->setTitle(($bIsEdit ? _p('directory.edit_a_package') : _p('directory.create_a_new_package')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
			->setBreadcrumb(($bIsEdit ? _p('directory.edit_a_package') : _p('directory.create_a_new_package')), $this->url()->makeUrl('admincp.directory.package.add'))
			->assign(array(
					'bIsEdit' => $bIsEdit,
					'sCreateJs'   => $oValid -> createJS(),
					'aThemes'   => $aThemes,
					'aModules'   => $aModules,
					'aPackageSettings'   => $aPackageSettings,
					'aCurrentCurrencies' => Phpfox::getService('directory.helper')->getCurrentCurrencies(),
					'core_path' => Phpfox::getParam('core.path_file'),
				)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
					
	}
}

?>