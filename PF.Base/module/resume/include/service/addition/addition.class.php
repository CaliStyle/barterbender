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
class Resume_Service_Addition_Addition extends Phpfox_Service
{
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_addition');
	}
	
	public function getAddition($resume_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('resume_id = '.$resume_id);
		$Info = $oQuery-> execute('getRow');
		
		//Process result
		
		//parse serialize website variable to array
		if(isset($Info['website']) && count($Info['website'])>0)
		{
			if (Phpfox::getLib('parse.format')->isSerialized($Info['website']))
			{
				$website = unserialize($Info['website']);
				$Info['website'] = $website;
			}
			else {
				$Info['website'] = array();
			}
		}
		else {
			$Info['website'] = array();
		}
		
		// Return result					 
		return $Info;	
	}
	
}

?>