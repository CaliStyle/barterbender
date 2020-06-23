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
class Resume_Component_Controller_Admincp_WeightSettings extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 public function process()
	 {
	 	$aRows = Phpfox::getService("resume.completeness")->getWeightUncomplete();

	 	$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
            ->setBreadCrumb(_p('resume.admin_menu_weight_settings'), $this->url()->makeurl('admincp.resume.weightsettings'));
		
		$this->template()->assign(array(
			'aRows' => $aRows,
		));
	 }
}
	