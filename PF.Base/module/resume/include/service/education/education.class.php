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
class Resume_Service_Education_Education extends Phpfox_Service
{
	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_education');
	}
	
	public function getAllEducation($resume_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('resume_id='.$resume_id)
						-> order('start_year DESC');
						
		$Info = $oQuery-> execute('getRows');

		return $Info;	
	}
	
	/**
	 * Get selected Education throught education id
	 * @param int $edu_id is the id of the selected eduction
	 * @return array of education information 
	 */
	public function getEducation($edu_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('education_id='.$edu_id);
						
		$Info = $oQuery-> execute('getRow');

		return $Info;	
	}
	
	/**
	 * Get latest Education Information of a Resume id
	 * @param int $edu_id is the id of the related resume
	 * @return array of education information 
	 */
	public function getLatestEducation($iResumeId)
	{
		$aEducation = $this -> database()
							-> select('education_id, school_name, degree, field')
							-> from($this->_sTable)
							-> where('resume_id = '. $iResumeId)
							-> order('start_year DESC, start_month DESC')
							-> limit(1)
							-> execute('getRow');
		return $aEducation;
	}
	
	public function getLastEducation($iResumeId)
	 {
		$aEducation = $this -> database()
							-> select('education_id, school_name, degree, field, end_year')
							-> from($this->_sTable)
							-> where('resume_id = '. $iResumeId)
							-> order('end_year DESC')
                                                        -> limit(1)
							-> execute('getRow');
               
		return $aEducation;
	 }
}

?>