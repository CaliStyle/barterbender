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
class Resume_Service_Level_Process extends Phpfox_Service
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_level');
	}
	/**
	 * Add level method 
	 * @param array $aLevel is the level information need to be added
	 * @return int $iId is the id of level that recently added  
	 */
	public function add($aLevel)
	{
		$iId = $this->database()->insert($this->_sTable, array(
				'name' => Phpfox::getLib('parse.input')->clean($aLevel['title'], 255),
			)
		);
		return $iId;
	}
		
	/**
	 * Update level method 
	 * @param int $iLevelId is the level id need to be updated
	 * @param array $aVals is the level information need to be updated
	 * @return int $iId is the id of level that recently updated
	 */
	public function update($iLevelId, $aVals) {
		$iId = $this->database()->update($this->_sTable, array(
				'name' => Phpfox::getLib('parse.input')->clean($aVals['title'], 255),
				),'level_id ='. (int) $iLevelId
		);
        return $iId;
    }
	
	/**
	 * Delete level method 
	 *
	 * @param int $iId is the level id need to be deleted
	 * @return boolean
	 */
	public function delete($iId)
	{
		/**
		 * @todo Release resume and experience level to none 		
		 */
		// Process delete level
		$aLevel = Phpfox::getService('resume.level')->getLevel($iId);
		if($aLevel && $aLevel['used'] == 0)
		{
			$this->database()->delete($this->_sTable, 'level_id = ' . (int) $iId);
		}
		return true;
	}
    
    public function updateOrdering($aVal)
	{
		foreach ($aVal as $iId => $iPosition)
		{
			$this->database()->update($this->_sTable, array('ordering' => (int)$iPosition), 'level_id = ' . (int)$iId);
		}
	}
}
?>