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
class Resume_Service_Skill_Skill extends Phpfox_Service
{
	public function getBasicSkill($resume_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('skills')
				   		-> from(Phpfox::getT('resume_basicinfo'))
						-> where('resume_id = '.$resume_id);
		$Info = $oQuery-> execute('getRow');
		
		return $Info;
		
	}
}

?>