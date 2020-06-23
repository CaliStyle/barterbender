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
class Resume_Component_Controller_Education extends Phpfox_Component
{
	public function process()
	{
		
		// User login requirement
		Phpfox::isUser(true);
		// Edit mode
		$bIsEdit = false;
		//Init variable
		$aRows = array();
		$edu_id = 0;
		
		if($iEditId = $this->request()->getInt("id"))
		{
			$bIsEdit = true;
			
			$aRows = Phpfox::getService("resume.education")->getAllEducation($iEditId);
			$aRow = Phpfox::getService("resume.basic")->getBasicInfo($iEditId);
			if($aRow['user_id']!=Phpfox::getUserId())
			{
				if(!Phpfox::getUserParam('resume.can_edit_other_resume'))
					Phpfox::getLib("url")->send("subscribe");
			}
			if($edu_id = $this->request()->getInt("exp"))
			{
				$aExp = Phpfox::getService("resume.education")->getEducation($edu_id);
				$this->template()->assign(array(
					'aForms' => $aExp,
				));			
			}
            Phpfox::getService('resume')->setPageSectionMenu($aRow);
		}
		
		if($iEditId==0)
		{
			Phpfox::getLib("url")->send("resume.add");
		}
		
		$aValidation = array(
			'school_name' => array(
				'def' => 'required',
				'title' => _p('resume.add_school_name_to_your_resume')
			),
			'degree' => array(
				'def' => 'required',
				'title' => _p('resume.add_education_degree_to_your_resume')
			),	
			'field' => array(
				'def' => 'required',
				'title' => _p('resume.add_education_field_to_your_resume')
			),	
			'start_year' => array(
				'def' => 'required',
				'title' => _p('resume.add_start_study_time_to_your_resume')
			),	
			'end_year' => array(
				'def' => 'required',
				'title' => _p('resume.add_end_study_time_to_your_resume')
			),		
		);
		
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_resume_add_form', 
				'aParams' => $aValidation
			)
		);
		
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
		
		$this->template()->assign(array(
        	'sDobStart' => Phpfox::getParam('user.date_of_birth_start'),
			'sDobEnd' => Phpfox::getParam('user.date_of_birth_end'),
			'aMonth' => $aMonth,
			'aYear'=> $aYear,
			'id' => $iEditId,
			'iExp' => $edu_id,
			'bIsEdit' => $bIsEdit,
			'aRows' => $aRows,
			'typesession' => Phpfox::getService("resume.process")->typesesstion($iEditId),
		))
		->setHeader(array(	
			'resume.js' => 'module_resume'
		))
        ->setBreadcrumb(_p('resume.resume'), $this->url()->makeUrl('resume'))
		->setBreadcrumb((!empty($iEditId) ? _p('resume.editing_resume') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['full_name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('resume.create_new_resume')), ($iEditId > 0 ? $this->url()->makeUrl('resume', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('resume', array('add'))), true);
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if ($oValid->isValid($aVals))
			{
				if($edu_id==0)
				{
					$aVals['resume_id'] = $iEditId;	
					if(Phpfox::getService("resume.education.process")->add($aVals))
					{
						Phpfox::getService('resume')->updateStatus($iEditId);
						Phpfox::getLib("url")->send("resume.education",array('id' => $iEditId),_p('resume.your_education_added_successfully'));
					}
					else {
						$this->template()->assign(array(
							'aForms' => $aVals
						));
					}
				}
				else 
				{
					$aVals['edu_id'] = $edu_id;
					if(Phpfox::getService("resume.education.process")->update($aVals))
					{
						Phpfox::getService('resume')->updateStatus($iEditId);
						Phpfox::getLib("url")->send("resume.education",array('id' => $iEditId,'exp' => $edu_id),_p('resume.your_education_updated_successfully'));
					}
					else
					{
						$this->template()->assign(array(
							'aForms' => $aVals
						));
					}
				}
			}
			else
			{
				$this->template()->assign(array(
				'aForms' => $aVals
				));
			}
		}
	}
	
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>