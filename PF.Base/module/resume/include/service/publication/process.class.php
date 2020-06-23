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
class Resume_Service_Publication_Process extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_publication');
	}
	
	public function add($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		
		if(!PHpfox::getService("resume.process")->check_number($aVals['published_year']))
		{
			return Phpfox_Error::set('Year of publication is invalid');
		}
		
		$aSql = array(
			'resume_id' 	  => $aVals['resume_id'],
			'type_id'		  => isset($aVals['type_id'])?$aVals['type_id']:1,
			'other_type'	  => isset($aVals['other_type'])?$oFilter->clean($aVals['other_type']):"",
			'title' 		  => isset($aVals['title'])?$oFilter->clean($aVals['title']):"",
			'publisher'		  => isset($aVals['publisher'])?$oFilter->clean($aVals['publisher']):"",
			'publication_url' => isset($aVals['publication_url'])?$oFilter->clean($aVals['publication_url']):"",
			'published_day'   => isset($aVals['published_day']) && $aVals['published_day'] ?$aVals['published_day']:0,
			'published_month' => isset($aVals['published_month']) && $aVals['published_month'] ?$aVals['published_month']:0,
			'published_year'  => isset($aVals['published_year']) && $aVals['published_year'] ?$aVals['published_year']:0,
			'note' 			  => isset($aVals['note'])?$oFilter->clean($aVals['note']):"",
			'note_parsed' 	  => isset($aVals['note'])?$oFilter->prepare($aVals['note']):"",
		);
		 
		if($aVals['author_list']!="")
		{
			$aSql['author'] = serialize(trim($aVals['author_list'],","));
		}
		else 
		{
			$aSql['author'] = $aVals['author_list'];
		}
		                                                                                                                                                                                                                                       
		$iId = $this->database()->insert($this->_sTable, $aSql);		
		return $iId;
	}
	
	public function update($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		
		if(!PHpfox::getService("resume.process")->check_number($aVals['published_year']))
		{
			return Phpfox_Error::set('Year of publication is invalid');
		}
		
		$aSql = array(
			'type_id'		  => $aVals['type_id'],
			'other_type'	  => $oFilter->clean($aVals['other_type']),
			'title' 		  => $oFilter->clean($aVals['title']),
			'publisher'		  => $oFilter->clean($aVals['publisher']),
			'publication_url' => $oFilter->clean($aVals['publication_url']),
			'published_day'   => isset($aVals['published_day']) && $aVals['published_day'] ?$aVals['published_day']:0,
			'published_month' => isset($aVals['published_month']) && $aVals['published_month'] ?$aVals['published_month']:0,
			'published_year'  => isset($aVals['published_year']) && $aVals['published_year'] ?$aVals['published_year']:0,
			'note' => $oFilter->clean($aVals['note']),
			'note_parsed' => $oFilter->prepare($aVals['note']),
		);
		
		if($aVals['author_list'] != "")
		{
			$aSql['author'] = serialize(trim($aVals['author_list'],","));
		}
		else 
		{
			$aSql['author'] = $aVals['author_list'];
		}
		                                                                                                                                                                                                                                      
		$iId = $this->database()->update($this->_sTable,$aSql,'publication_id='.$aVals['pub_id']);		
		return $iId;
	}
	
	
	public function deletePublication($pub_id)
	{
		$aPublication = Phpfox::getService('resume.publication')->getPublication($pub_id);
		if($aPublication)
		{
			$this->database()->delete($this->_sTable,'publication_id='.$pub_id);
			Phpfox::getService('resume')->updateStatus($aPublication['resume_id']);
		}
	}
	
	/**
	 * Delete publication related to resume
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