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
class Resume_Service_Summary_Process extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_basicinfo');
	}
	
	public function update($aVals)
	{
		if(isset($aVals['summary']) && $aVals['summary']=="")
		{
			return Phpfox_Error::set(_p('resume.add_summary_to_your_resume'));
		}   
		if(isset($aVals['year_exp']) && !Phpfox::getService("resume.process")->check_number($aVals['year_exp']))
		{
			return Phpfox_Error::set(_p('resume.years_of_experience_is_invalid'));
		}
		$oFilter = Phpfox::getLib('parse.input');
		$aSql = array(
            'authorized' => isset($aVals['authorized']) ? $aVals['authorized'] : "",
			'authorized_country_iso' => 0,
			'authorized_country_child_id' => 0,
			'authorized_other_level' => "",
			'authorized_location' => "",
			'authorized_level_id' => 0,
			'year_exp' => isset($aVals['year_exp'])?$aVals['year_exp']:0,
			'level_id' => isset($aVals['level_id'])?$aVals['level_id']:0,
			'headline' => isset($aVals['headline'])?$oFilter->clean($aVals['headline']):0,
			'time_update' => PHPFOX_TIME,
			'summary' => isset($aVals['summary'])?$oFilter->clean($aVals['summary']):"",
			'summary_parsed' => isset($aVals['summary'])?$oFilter->prepare($aVals['summary']):"",
		);

		$support_getInfoLinkedin = true;
		if(!$support_getInfoLinkedin){
			$aSql['country_iso'] = isset($aVals['country_iso'])?$aVals['country_iso']:0;
			$aSql['country_child_id'] = isset($aVals['country_child_id'])==true?$aVals['country_child_id']:0;
			$aSql['city'] = isset($aVals['city'])?$oFilter->clean($aVals['city']):"";
			$aSql['zip_code'] = isset($aVals['zip_code'])?$aVals['zip_code']:"";
		}
		
		if(isset($aVals['category']))
		{
			$aCategoriesData = Phpfox::getService('resume.category')->getCategoriesData($aVals['resume_id']);
			foreach($aCategoriesData as $category)
			{
				Phpfox::getService('resume.category.process')->updateUsedCategory($category['category_id'],-1);
			}
		
			Phpfox::getService("resume.category.process")->deleteAllCategorydata($aVals['resume_id']);
			foreach($aVals['category'] as $category)
			{
				Phpfox::getService("resume.category.process")->addCategorydata($aVals['resume_id'],$category);
				Phpfox::getService('resume.category.process')->updateUsedCategory($category,1);
			}
		}                                                                                       
                 
		if(isset($aVals['level_id']))
		{
			$aResume = Phpfox::getService('resume.basic')->getQuick($aVals['resume_id']);
			if($aResume & $aResume['level_id'] != $aVals['resume_id'])
			{
				$this->database()->updateCounter('resume_level', 'used', 'level_id', $aResume['level_id'], true);
				$this->database()->updateCounter('resume_level', 'used', 'level_id', $aVals['level_id']);
			}
		}   
		                                                                                                                     
		$iId = $this->database()->update($this->_sTable,$aSql,'resume_id='.$aVals['resume_id']);

		return $iId;
	}
}

?>