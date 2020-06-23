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
class Resume_Service_Skill_Process extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_skill');
	}
	
	public function updateBasicSkill($aVals)
	{
		if($aVals['kill_list']!="")
		{
			$aVals['skills'] = serialize(trim($aVals['kill_list'],","));
		}
		else 
		{
			$aVals['skills'] = $aVals['kill_list'];
		}
		
		$aSql = array(
			'skills' => $aVals['skills'],
			'time_update' => PHPFOX_TIME
		);
		                                                                                                                                                                                                                                            
		$iId = $this->database()->update(Phpfox::getT('resume_basicinfo'),$aSql,'resume_id='.$aVals['resume_id']);		
		return $iId;
	}

}

?>