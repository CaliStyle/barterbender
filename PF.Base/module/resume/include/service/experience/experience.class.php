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
class Resume_Service_Experience_Experience extends Phpfox_Service
{
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_experience');
	}
	
	/**
	 * Get all experience if matching ressume_id
	 */
	public function getAllExperience($resume_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('re.*,rl.name as level_name')
				   		-> from($this->_sTable,'re')
                        -> leftjoin(Phpfox::getT('resume_level'),'rl','re.level_id = rl.level_id')
						-> where('re.resume_id='.$resume_id)
						-> order('re.start_year DESC, re.start_month DESC');
		$Info = $oQuery -> execute('getRows');
		return $Info;	
	}
	
	/**
	 * get Exerience (only 1 row)
	 */
	public function getExperience($exp_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('experience_id='.$exp_id);
						
		$Info = $oQuery-> execute('getRow');
		return $Info;	
	}
	
	/**
	 * Get Current Work
	 * @param int $iResumeId is the id of the related resume
	 * @return array of current working place
	 */
	 public function getCurrentWork($iResumeId)
	 {
	 	$aWork = $this -> database()
					 	-> select('level_id, company_name, title')
				   		-> from($this->_sTable)
						-> where('is_working_here = 1 and resume_id='.$iResumeId)
						-> limit(1)
						-> order('experience_id DESC')
						-> execute('getRow');
		return $aWork;
	 }
	 
	   public function getLastWork($iResumeId)
	 {
	 	$aWork = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('resume_id='.$iResumeId)
						-> order('experience_id DESC')
						-> execute('getRows');
                $aLast = array();
                $tmp = array();
                foreach($aWork as $key=>$Work)
                {
                    if($Work['is_working_here']==1)
                    {
                        if($Work['level_id']>0)
                        {
                            return $aWork[$key];
                        }
                        else
                        {
                            $tmp = $aWork[$key];
                        }
                    }
                    else
                    {
                        if($aLast)
                        {
                            if($Work['end_year']>=$aLast['end_year'])
                            {
                                $aLast = $aWork[$key];
                            }
                        }
                        else
                        {
                            $aLast = $aWork[$key];
                        }
                    }
                }
                if($tmp)
                    return $tmp;
		return $aLast;
	 }
}

?>