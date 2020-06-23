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
class Resume_Component_Controller_Admincp_EditWeightSettings extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 public function process()
	 {
	 	$aRows = Phpfox::getService("resume.completeness")->getWeightUncomplete();

	 	$this->template()->setBreadCrumb(_p('resume.edit_weight_of_resume_fields'), $this->url()->makeurl('admincp.resume.editweightsettings'));
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if(Phpfox::getService("resume.completeness")->updateWeightComplete($aVals)) {
                Phpfox::getLib("url")->send("admincp.resume.weightsettings");
            } else {
			    $this->template()->assign([
			        'aForms' => $aVals
                ]);
            }
		}
		
		$this->template()->assign(array(
			'aRows' => $aRows,
		));
	 }
}