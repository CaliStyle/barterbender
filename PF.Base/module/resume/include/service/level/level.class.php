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
class Resume_Service_Level_Level extends Phpfox_Service
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_level');
	}
	
	/**
	 * Get total item count from query
	 * @param array $aConds is input filter conditions
	 * @return number of item gotten
	 */
	public function getItemCount($aConds)
	{
		// Generate query object	
		$oQuery = $this -> database()
						-> select('count(*)')
						-> from($this->_sTable);
		
		// Filfer conditions
		if($aConds)
		{
			$oQuery-> where($aConds);
		}						
		return $oQuery->execute('getSlaveField');
	}
	
	/**
	 * Get level list from database
	 * @param array $aConds is input filter conditions
	 * @param int $iPage is the current page
	 * @param int $iLimit is the item per page number
	 * @return list contains number of rows and array of level data
	 */
	public function getLevels($aConds = array(), $iPage = 0, $iLimit = 0)
	{
		// Get total item from this query		
		$iCnt = $this -> getItemCount($aConds);
		
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
				  		-> order('ordering ASC');
				  
		// Apply pagination
		if($iLimit)
		{
			$oQuery -> limit($iPage, $iLimit, $iCnt);
		}	
		
		$Levels = $oQuery-> execute('getRows');
		
		foreach($Levels as $key => $val){
			$Levels[$key]['name'] = Phpfox::getLib('locale')->convert($val['name']); 
		}
		// Return results					 
		return $Levels;	
	}
	/**
	 * Get level name by Id
	 * @param int $iLevelId is the id of the level need to get data
	 * @return string the name of the level
	 */
	public function getLevelById($iLevelId)
	{
		$name = $this-> database()
					-> select('name')
					-> from($this->_sTable)
					-> where('level_id = '.$iLevelId)
					-> execute('getSlaveField');

		return Phpfox::getLib('locale')->convert($name); 
	}
	
	/**
	 * Get level name by Id
	 * @param int $iLevelId is the id of the level need to get data
	 * @return string the name of the level
	 */
	public function getLevel($iLevelId)
	{
		$result = $this-> database()
					-> select('*')
					-> from($this->_sTable)
					-> where('level_id = '.$iLevelId)
					-> execute('getRow');

		if(isset($result['level_id'])){
			$result['name'] = Phpfox::getLib('locale')->convert($result['name']); 
		}

		return $result;
	}
}	
?>