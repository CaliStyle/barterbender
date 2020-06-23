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
class Resume_Service_Experience_Process extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_experience');
	}
	
	/**
	 * Add a experience
	 */
	public function add($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		
		$aSql = array(
			'resume_id' => $aVals['resume_id'],
			'level_id' => isset($aVals['level_id'])?$aVals['level_id']:0,
			'company_name' => isset($aVals['company_name'])?$oFilter->clean($aVals['company_name']):"",
			'title' => isset($aVals['title'])?$oFilter->clean($aVals['title']):"",
			'location' => isset($aVals['location'])?$oFilter->clean($aVals['location']):"",
			'start_month' => isset($aVals['start_month'])?$aVals['start_month']:0,
			'start_year' => isset($aVals['start_year'])?$aVals['start_year']:0,
			'end_month' => isset($aVals['end_month'])?$aVals['end_month']:0,
			'end_year' => isset($aVals['end_year'])?$aVals['end_year']:0,
			'is_working_here' => isset($aVals['is_working_here'])?($aVals['is_working_here']=="on"?1:0):0,
			'description' => isset($aVals['description'])?$oFilter->clean($aVals['description']):"",
			'description_parsed' => isset($aVals['description'])?$oFilter->prepare($aVals['description']):"",
		);
		                                                                                                                                                                                                                                            
		$iId = $this->database()->insert($this->_sTable,$aSql);
		$this->database()->updateCounter('resume_level', 'used', 'level_id', $aSql['level_id']);
		return $iId;
	}

	/**
 		* Update a experience
 	*/
	public function update($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		
		$aSql = array(
			'level_id' => $oFilter->clean($aVals['level_id']),
			'company_name' => $oFilter->clean($aVals['company_name']),
			'title' => $oFilter->clean($aVals['title']),
			'location' => $oFilter->clean($aVals['location']),
			'start_month' => $aVals['start_month'],
			'start_year' => $aVals['start_year'],
			'end_month' => $aVals['end_month'],
			'end_year' => $aVals['end_year'],
			'is_working_here' => $aVals['is_working_here']=="on"?1:0,
			'description' => $oFilter->clean($aVals['description']),
			'description_parsed' => $oFilter->prepare($aVals['description']),
		);
		$aExp = Phpfox::getService('resume.experience')->getExperience($aVals['exp_id']);
		if($aExp)
		{
			$this->database()->updateCounter('resume_level', 'used', 'level_id', $aExp['level_id'], true);
			$this->database()->updateCounter('resume_level', 'used', 'level_id', $aVals['level_id']);
			$iId = $this->database()->update($this->_sTable,$aSql,'experience_id='.$aVals['exp_id']);		
			return $iId;
		}
	}
	
	/**
	 * Delete experience with experience_id
	 */
	public function deleteExperience($exp_id)
	{
		$aExperience = Phpfox::getService('resume.experience')->getExperience($exp_id);
		if($aExperience)
		{
			$this->database()->delete($this->_sTable,'experience_id='.$exp_id);
			$this->database()->updateCounter('resume_level', 'used', 'level_id', $aExperience['level_id'], true);
			Phpfox::getService('resume')->updateStatus($aExperience['resume_id']);
		}
	}

	/**
	 * Delete experience related to resume
	 * @param int $iId - the id of the resume need to be deleted
	 * @return true 
	 */
	public function delete($iId)
	{
		$aExps = Phpfox::getService('resume.experience')->getAllExperience($iId);
		if(count($aExps)>0)
		{
			foreach($aExps as $aExp)
			{
				$this->database()->updateCounter('resume_level', 'used', 'level_id', $aExp['level_id'], true);
				$this->database()->delete($this->_sTable, 'experience_id = ' . (int) $aExp['experience_id']);
			}
		}
		return true;
	}
}

?>