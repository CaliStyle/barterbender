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
class Resume_Component_Block_Resume_Completeness extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$bNoResume = FALSE;
		
		// Get user Id and generate the conditions for getting related resumes
		$iUserId =Phpfox::getUserId();
		$aUser = $this->getParam('aUser');
		if(!$aUser || $iUserId != $aUser['user_id'])
		{
			return FALSE;
		}
		
		$iLimit = 3;
		$aConds = array("rbi.user_id = {$iUserId}");
		$sOrder = "rbi.time_update DESC, rbi.resume_id DESC";
		
		// Get resumes
		$oResume = Phpfox::getService('resume');
		$oResumeCompleteness = Phpfox::getService('resume.completeness');
		$aResumes = $oResume ->getResumes($aConds, $sOrder, $iLimit);
		
		// Set flag if no resume gotten
		if(!$aResumes)
		{
			$bNoResume = TRUE;
		}
		else 
		{
			foreach($aResumes as $iKey=>$aResume)
			{
				// Calculate compelte persent and uncompleted list
				list($iScore,$sUncompletedList,$iTotalMark) = $oResumeCompleteness->calculate($aResume['resume_id']);
				$aResumes[$iKey]['completed_percent'] = round((int) $iScore*100/$iTotalMark);
				
				$aResumes[$iKey]['next_suggestion'] = "";
				if($sUncompletedList)
				{
					$aUncomplete = Phpfox::getService("resume.completeness")->showUnComplete($sUncompletedList,$aResume['resume_id']);
					$aFieldList = explode(',', $sUncompletedList);
					$aResumes[$iKey]['next_suggestion']   = $aUncomplete[$aFieldList[0]];
				}
			}
		}
			
		$this->template()->assign(array(
			'bNoResume' 	=> $bNoResume,
			'aResumes'		=> $aResumes,
			'sCreateLink'	=> Phpfox::getLib('url')->makeUrl('resume.add'),
			'core_path' 	=> Phpfox::getParam('core.path'),
			'sHeader' 		=> _p('resume.resume_completeness')
		));

		return 'block';
	}
}

?>