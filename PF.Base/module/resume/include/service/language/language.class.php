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
class Resume_Service_Language_Language extends Phpfox_Service
{
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_language');
	}
	
	/**
	 * Get all experience if matching ressume_id
	 */
	public function getAllLanguage($resume_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('resume_id='.$resume_id)
						-> order('name DESC');
						
		$Info = $oQuery-> execute('getRows');
		return $Info;	
	}
	
	/**
	 * The number of records
	 */
	public function getItemCount($resume_id)
	{
		$oQuery = $this -> database()
					 	-> select('count(*)')
				   		-> from($this->_sTable)
						-> where('resume_id='.$resume_id);
						
		$iCount = (int)$oQuery-> execute('getSlaveRows');
		return $iCount;	
	}
	
	/**
	 * get Exerience (only 1 row)
	 */
	public function getLanguage($lang_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('language_id='.$lang_id);
						
		$Info = $oQuery-> execute('getRow');
		return $Info;	
	}
}

?>