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
class Resume_Service_Certification_Process extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_certification');
	}
	
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
		if(isset($aVals['start_year']) && isset($aVals['start_month']) && isset($aVals['end_year']) && isset($aVals['end_month']))
		{
			if($aVals['end_year']<$aVals['start_year'] || ($aVals['end_year']==$aVals['start_year'] && $aVals['end_month']<$aVals['start_month']))
			{
				return Phpfox_Error::set(_p('resume.please_be_sure_the_start_date_is_not_after_the_end_date'));
			}
		}
		$aSql = array(
			'resume_id' => $aVals['resume_id'],
			'certification_name' => isset($aVals['certification_name'])?$oFilter->clean($aVals['certification_name']):"",
			'course_name' => isset($aVals['course_name'])?$oFilter->clean($aVals['course_name']):"",
			'training_place' => isset($aVals['training_place'])?$oFilter->clean($aVals['training_place']):"",
			'start_month' => isset($aVals['start_month']) && $aVals['start_month'] ?$aVals['start_month']:0,
			'start_year' => isset($aVals['start_year']) && $aVals['start_year'] ?$aVals['start_year']:0,
			'end_month' => isset($aVals['end_month']) && $aVals['end_month'] ?$aVals['end_month']:0,
			'end_year' => isset($aVals['end_year']) && $aVals['end_year'] ?$aVals['end_year']:0,
			'note' => isset($aVals['note'])?$oFilter->clean($aVals['note']):"",
			'note_parsed' => isset($aVals['note'])?$oFilter->prepare($aVals['note']):"",
		);
		                                                                                                                                                                                                                                            
		$iId = $this->database()->insert($this->_sTable,$aSql);		
		return $iId;
	}
	
	/**
	 * Update
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
		if(isset($aVals['start_year']) && isset($aVals['start_month']) && isset($aVals['end_year']) && isset($aVals['end_month']))
		{
			
			if($aVals['end_year']<$aVals['start_year'] || ($aVals['end_year']==$aVals['start_year'] && $aVals['end_month']<$aVals['start_month']))
			{
				return Phpfox_Error::set(_p('resume.please_be_sure_the_start_date_is_not_after_the_end_date'));
			}
		}
		$aSql = array(
			'certification_name' => $oFilter->clean($aVals['certification_name']),
			'course_name' => $oFilter->clean($aVals['course_name']),
			'training_place' => $oFilter->clean($aVals['training_place']),
			'start_month' => isset($aVals['start_month']) && $aVals['start_month'] ?$aVals['start_month']:0,
			'start_year' => isset($aVals['start_year']) && $aVals['start_year'] ?$aVals['start_year']:0,
			'end_month' => isset($aVals['end_month']) && $aVals['end_month'] ?$aVals['end_month']:0,
			'end_year' => isset($aVals['end_year']) && $aVals['end_year'] ?$aVals['end_year']:0,
			'note' => $oFilter->clean($aVals['note']),
			'note_parsed' => $oFilter->prepare($aVals['note']),
		);
		                                                                                                                                                                                                                                            
		$iId = $this->database()->update($this->_sTable,$aSql,'certification_id='.$aVals['cer_id']);		
		return $iId;
	}
	
	/**
	 * Delete experience with experience_id
	*/
	public function deleteCertification($cer_id)
	{
		$aCertification = Phpfox::getService('resume.certification')->getCertification($cer_id);
		if($aCertification)
		{
			$this->database()->delete($this->_sTable,'certification_id='.$cer_id);
			Phpfox::getService('resume')->updateStatus($aCertification['resume_id']);
		}
	}
	
	
	/**
	 * Delete certification related to resume
	 * @param int $iId - the id of the resume need to be deleted
	 * @return true 
	 */
	public function delete($iId)
	{
		$this->database()->delete($this->_sTable, 'resume_id = ' . (int) $iId);
		return true;
	}
}

?>