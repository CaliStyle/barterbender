<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		younet
 * @package 		Phpfox_Component
 * @version 		3.01
 */
class Resume_Component_Controller_Experience extends Phpfox_Component
{
	public function process()
	{
		
		// User login requirement
		Phpfox::isUser(true);
		// Edit mode
		$bIsEdit = false;
		//Init variable
		$aRows = array();
		$iExp = 0;
		
		//check add or edit
		if($iEditId = $this->request()->getInt("id"))
		{
			$bIsEdit = true;
			
			$aRows = Phpfox::getService("resume.experience")->getAllExperience($iEditId);
			$aBasic = Phpfox::getService("resume.basic")->getBasicInfo($iEditId);
			if($aBasic['user_id']!=Phpfox::getUserId())
			{
				if(!Phpfox::getUserParam('resume.can_edit_other_resume'))
					Phpfox::getLib("url")->send("subscribe");
			}
			
			if($iExp = $this->request()->getInt("exp"))
			{
				
				$aExp = Phpfox::getService("resume.experience")->getExperience($iExp);
				
				//assign to html file to full textbox.
				$this->template()->assign(array(
					'aForms' => $aExp,
				));
				
				
			}
            Phpfox::getService('resume')->setPageSectionMenu($aBasic);
		}
		
		//if don't have id, they should redirect back add
		if($iEditId==0)
		{
			Phpfox::getLib("url")->send("resume.add");
		}

		$aValidation = array(
			'company_name' => array(
				'def' => 'required',
				'title' => _p('resume.add_company_name_to_your_resume')
			),	
			'level_id' => array(
				'def' => 'required',
				'title' => _p('resume.add_position_level_to_your_resume')
			),	
			'title' => array(
				'def' => 'required',
				'title' => _p('resume.add_job_title_to_your_resume')
			),	
		);
		
		//
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_resume_add_form', 
				'aParams' => $aValidation
			)
		);
		
		//get level
		$aLevel = Phpfox::getService("resume.level")->getLevels();
		
		
		//init month to show
		$aMonth = array();
		for($i=1 ; $i<=12 ; $i++)
		{
			$aMonth[] = $i;
		}
		//init year to show
		$aYear = array();
		for($i=(int) date('Y') ; $i >= 1900 ; $i--)
		{
			$aYear[] = $i;
		}
		//assign to html
        $this->template()->assign(array(
        	'sDobStart' => Phpfox::getParam('user.date_of_birth_start'),
			'sDobEnd' => Phpfox::getParam('user.date_of_birth_end'),
			'aMonth' => $aMonth,
			'aYear'	=> $aYear,
			'bIsEdit' => $bIsEdit,
			'id' => $iEditId,
			'iExp' => $iExp,
			'aLevel' => $aLevel,
			'aRows' => $aRows,
			'typesession' => Phpfox::getService("resume.process")->typesesstion($iEditId),
		))
		->setHeader(array(				
			'process_add.js' => 'module_resume',
			'resume.js' => 'module_resume'
		))
       	->setBreadcrumb(_p('resume.resume'), $this->url()->makeUrl('resume'))
		->setBreadcrumb((!empty($iEditId) ? _p('resume.editing_resume') . ': ' . Phpfox::getLib('parse.output')->shorten($aBasic['full_name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('resume.create_new_resume')), ($iEditId > 0 ? $this->url()->makeUrl('resume', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('resume', array('add'))), true);
		
		
		//add or delete
		$is_calloff = true;
		if ($aVals = $this->request()->getArray('val'))
		{
			if ($oValid->isValid($aVals))
			{
				
				if($aVals['start_month']=="-1" || $aVals['start_year']=="-1")
				{
					$is_calloff = false;
					Phpfox_Error::set(_p('resume.add_working_time_period_to_your_resume'));
				}
				else {
					if(!isset($aVals['is_working_here']))
					{
						if($aVals['end_month']=="-1" || $aVals['end_year']=="-1")
						{
							$is_calloff = false;
							Phpfox_Error::set(_p('resume.add_working_time_period_to_your_resume'));
							
						}
						else
						{
							if($aVals['end_year']<$aVals['start_year'] || ($aVals['end_year']==$aVals['start_year'] && $aVals['end_month']<$aVals['start_month']))
							{
								$is_calloff = false;
								Phpfox_Error::set(_p('resume.please_be_sure_the_start_date_is_not_after_the_end_date'));
							}		
						}
					}	
				}
				//is used to add
				if($is_calloff)
				{
	
						if($iExp==0)
						{
							$aVals['resume_id'] = $iEditId;
							Phpfox::getService("resume.experience.process")->add($aVals);
							Phpfox::getService('resume')->updateStatus($iEditId);
							Phpfox::getLib("url")->send("resume.experience",array('id' => $iEditId),_p('resume.your_experience_added_successfully'));
						}
					//is used to edit
						else {
							$aVals['exp_id'] = $iExp;
							Phpfox::getService("resume.experience.process")->update($aVals);
							Phpfox::getService('resume')->updateStatus($iEditId);
							Phpfox::getLib("url")->send("resume.experience",array('id' => $iEditId,'exp'=>$aVals['exp_id']),_p('resume.your_experience_updated_successfully'));
						}
				
				}
				if($iExp = $this->request()->getInt("exp"))
					{
					}
					else {
						$is_calloff = false;
						$aForms['start_month']	= $aVals['start_month'];
						$aForms['start_year']	= $aVals['start_year'];
						$aForms['end_month']	= $aVals['end_month'];
						$aForms['end_year']	= $aVals['end_year'];
						$aForms['level_id']	= $aVals['level_id'];
						$aForms['description']	= $aVals['description'];
						
						if(isset($aVals['is_working_here']) && $aVals['is_working_here']=="on")
						{
							$aForms['is_working_here'] = 1;
						}
						$this->template()->assign(array(
							'aForms' => $aForms,
						));
					}
				
			}
			else {
					//Not edit and only check validate month and year
					if($iExp = $this->request()->getInt("exp"))
					{
						
					}
					else {
						$is_calloff = false;
						$aForms['start_month']	= $aVals['start_month'];
						$aForms['start_year']	= $aVals['start_year'];
						$aForms['end_month']	= $aVals['end_month'];
						$aForms['end_year']	= $aVals['end_year'];
						$aForms['level_id']	= $aVals['level_id'];
						$aForms['description']	= $aVals['description'];
						
						if(isset($aVals['is_working_here']) && $aVals['is_working_here']=="on")
						{
							$aForms['is_working_here'] = 1;
						}
						$this->template()->assign(array(
							'aForms' => $aForms,
						));
					}
				}
		} 
		$this->template()->assign(array(
			'is_calloff' => $is_calloff,
		));
	}
	
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>