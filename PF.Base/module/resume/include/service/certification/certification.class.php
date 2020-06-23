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
class Resume_Service_Certification_Certification extends Phpfox_Service
{
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_certification');
	}
	
	public function getAllCertification($resume_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('resume_id='.$resume_id);
						
		$Info = $oQuery-> execute('getRows');
		
		//return results 
		return $Info;	
	}
	
	public function getCertification($cer_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('certification_id='.$cer_id);
						
		$Info = $oQuery-> execute('getRow');
		
		//return results 
		return $Info;	
	}
}

?>