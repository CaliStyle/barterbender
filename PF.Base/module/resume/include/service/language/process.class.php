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
class Resume_Service_Language_Process extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_language');
	}
	
	public function add($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		
		$aSql = array(
			'resume_id' => $aVals['resume_id'],
			'name' => isset($aVals['name'])?$oFilter->clean($aVals['name']):"",
			'level' => isset($aVals['level'])?$aVals['level']:"",
			'note' => isset($aVals['note'])?$oFilter->clean($aVals['note']):"",
			'note_parsed' => isset($aVals['note'])?$oFilter->prepare($aVals['note']):"",
		);
		                                                                                                                                                                                                                                            
		$iId = $this->database()->insert($this->_sTable,$aSql);		
		return $iId;
	}
	
	/**
	 * 
	 */
	public function update($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		
		$aSql = array(
			'name' => $oFilter->clean($aVals['name']),
			'level' => $aVals['level'],
			'note' => $oFilter->clean($aVals['note']),
			'note_parsed' => $oFilter->prepare($aVals['note']),
		);
		                                                                                                                                                                                                                                            
		$iId = $this->database()->update($this->_sTable,$aSql,'language_id='.$aVals['lang_id']);		
		return $iId;
	}
	
	/**
	 * 
	 */
	public function deleteLanguage($lang_id)
	{
		$aLanguage = Phpfox::getService('resume.language')->getLanguage($lang_id);
		if($aLanguage)
		{
			$this->database()->delete($this->_sTable,'language_id='.$lang_id);
			Phpfox::getService('resume')->updateStatus($aLanguage['resume_id']);
		}
	}
	
	/**
	 * Delete language related to resume
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