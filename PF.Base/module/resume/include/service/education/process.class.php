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
class Resume_Service_Education_Process extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_education');
	}
	
	/**
	 * Add Education
	 */
	public function add($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		if(isset($aVals['start_year']) && !PHpfox::getService("resume.process")->check_number($aVals['start_year']))
		{
			return Phpfox_Error::set(_p('resume.start_year_is_invalid'));
		}
		if(isset($aVals['end_year']) && !PHpfox::getService("resume.process")->check_number($aVals['end_year']))
		{
			return Phpfox_Error::set(_p('resume.end_year_is_invalid'));
		}
		if($aVals['start_year']>$aVals['end_year'])
		{
			return Phpfox_Error::set(_p('resume.please_be_sure_the_start_year_is_not_the_end_year'));
		}
		$aSql = array(
			'resume_id' => $aVals['resume_id'],
			'school_name' => isset($aVals['school_name'])?$oFilter->clean($aVals['school_name']):"",
			'degree' => isset($aVals['degree'])?$oFilter->clean($aVals['degree']):"",
			'field' => isset($aVals['field'])?$oFilter->clean($aVals['field']):"",
			'start_year' => isset($aVals['start_year'])?$aVals['start_year']:0,
			'end_year' => isset($aVals['end_year'])?$aVals['end_year']:0,
			'grade' => isset($aVals['grade'])?$oFilter->clean($aVals['grade']):"",
			'activity' => isset($aVals['activity'])?$oFilter->clean($aVals['activity']):"",
			'activity_parsed' => isset($aVals['activity'])?$oFilter->prepare($aVals['activity']):"",
			'note' => isset($aVals['note'])?$oFilter->clean($aVals['note']):"",
			'note_parsed' => isset($aVals['note'])?$oFilter->prepare($aVals['note']):"",
		);
		                                                                                                                                                                                                                                            
		$iId = $this->database()->insert($this->_sTable,$aSql);		
		return $iId;
	}
	
	/**
	 * Update education
	 */
	public function update($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		if(!PHpfox::getService("resume.process")->check_number($aVals['start_year']))
		{
			return Phpfox_Error::set(_p('resume.start_year_is_invalid'));
		}
		if(!PHpfox::getService("resume.process")->check_number($aVals['end_year']))
		{
			return Phpfox_Error::set(_p('resume.end_year_is_invalid'));
		}
		if($aVals['start_year']>$aVals['end_year'])
		{
			return Phpfox_Error::set(_p('resume.please_be_sure_the_start_year_is_not_the_end_year'));
		}
		$aSql = array(
			'school_name' => $oFilter->clean($aVals['school_name']),
			'degree' => $oFilter->clean($aVals['degree']),
			'field' => $oFilter->clean($aVals['field']),
			'start_year' => $aVals['start_year'],
			'end_year' => $aVals['end_year'],
			'grade' => $aVals['grade'],
			'activity' => $oFilter->clean($aVals['activity']),
			'activity_parsed' => $oFilter->prepare($aVals['activity']),
			'note' => $oFilter->clean($aVals['note']),
			'note_parsed' => $oFilter->prepare($aVals['note']),
		);
		                                                                                                                                                                                                                                            
		$iId = $this->database()->update($this->_sTable,$aSql,'education_id='.$aVals['edu_id']);		
		return $iId;
	}
	
	/**
	 * Delete experience with experience_id
	*/
	public function deleteEducation($edu_id)
	{
		$aEducation = Phpfox::getService('resume.education')->getEducation($edu_id);
		if($aEducation)
		{
			$this->database()->delete($this->_sTable,'education_id='.$edu_id);
			Phpfox::getService('resume')->updateStatus($aEducation['resume_id']);
		}
	}
	
	/**
	 * Delete Education
	 * Note: current is being called from ajax file
	 */
	public function delete($iId)
	{
		$this->database()->delete($this->_sTable, 'resume_id = ' . (int) $iId);
		return true;
	}
}

?>