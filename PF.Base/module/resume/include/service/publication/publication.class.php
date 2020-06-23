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
class Resume_Service_Publication_Publication extends Phpfox_Service
{
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_publication');
	}
	
	/**
	 * Get all experience if matching ressume_id
	 */
	public function getAllPublication($resume_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('resume_id='.$resume_id);
						
		$Info = $oQuery-> execute('getRows');
		
		// Unserialize Author List
		foreach($Info as $iKey => $aTtem)
		{
			if(Phpfox::getLib('parse.format')->isSerialized($aTtem['author']))
			{
				$sAuthorList = unserialize($aTtem['author']);
				$Info[$iKey]['author_list'] = $sAuthorList;
			}
		}
		return $Info;	
	}
	
	/**
	 * get Exerience (only 1 row)
	 */
	public function getPublication($pub_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('publication_id='.$pub_id);
						
		$Info = $oQuery-> execute('getRow');
		return $Info;	
	}
}

?>