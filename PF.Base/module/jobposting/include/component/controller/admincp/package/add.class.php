<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

class Jobposting_Component_Controller_Admincp_Package_Add extends Phpfox_Component
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
				'title'=> _p('name_of_package_cannot_be_empty')
			),
			'post_number' => array(
				'def' => 'number',
				'title'=> _p('post_job_number_have_to_be_a_number')
			),
			'expire_number' => array(
				'def' => 'number',
				'title' => _p('valid_peried_have_to_be_a_number')
			),
			'fee' => array(
				'def' => 'number',
				'title' => _p('package_fee_have_to_be_a_number')
			)
		);	
		
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_add_package_form', 
				'aParams'   => $aValidation
		));
		
		if ($iEditId = $this->request()->getInt('id'))
		{
			if ($aPackage = Phpfox::getService('jobposting.package')->getById($iEditId))
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
					if (Phpfox::getService('jobposting.package.process')->update($aPackage['package_id'], $aVals))
					{
						$this->url()->send('admincp.jobposting.package.add', array('id' => $aPackage['package_id']), _p('package_successfully_updated'));
					}
				}
				else{
					if (Phpfox::getService('jobposting.package.process')->add($aVals))
					{
						$this->url()->send('admincp.jobposting.package.add', null, _p('package_successfully_added'));
					}
				}
			}
		}
		$this->template()->setTitle(($bIsEdit ? _p('edit_a_package') : _p('create_a_new_package')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_jobposting'), $this->url()->makeUrl('admincp.app').'?id=__module_jobposting')
			->setBreadcrumb(($bIsEdit ? _p('edit_a_package') : _p('create_a_new_package')), $this->url()->makeUrl('admincp.jobposting.package.add'))
			->assign(array(
					'bIsEdit' => $bIsEdit,
					'sCreateJs'   => $oValid -> createJS(),
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