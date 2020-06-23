<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Controller_Admincp_Addlevel extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 public function process()
	 {
	 	// Add validation for form elements
	 	$aValidation = array(
			'title' => _p('resume.please_insert_level_title')
		);	
		$oValid = phpFox::getLib('validator')->set(array('sFormName' => 'resume_add_level_form', 'aParams' => $aValidation));
		
		// Set breadcrumb
		$this->template()
			 ->setTitle(_p('resume.add_new_level'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
			 ->setBreadCrumb(_p('resume.add_new_level'), $this->url()->makeUrl('admincp.resume.addlevel'));
		
		// Add singer
		if ($aVals = $this->request()->getArray('val'))
        {
           if ($oValid->isValid($aVals))
				{
	                if (phpFox::getService('resume.level.process')->add($aVals))
	                {
	                    $this->url()->send('admincp.resume.levels', null, _p('resume.level_successfully_added'));
	                }
           		}
        }
	 }
}
?>
	