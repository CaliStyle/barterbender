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
class Resume_Service_Viewme_Process extends Phpfox_Service
{
	/**
	 * Class Constructor
	 */
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_viewme');
	}
	
	/**
	 * Add view track into database
	 * @param array $aVals is the input information of the view
	 * (user_id, resume_id, owner_id, time_stamp, total_view)
	 */
	public function addView($aVals)
	{
		$this->database()->insert($this->_sTable, $aVals);
	}
	
	/**
	 * Add view track into database
	 * @param array $aVals is the input information of the view
	 * (view_id, time_stamp, total_view)
	 */
	public function updateView($aVals)
	{
		$this->database()->update($this->_sTable, $aVals,'view_id = ' . $aVals['view_id'] );
	}
	
	public function delete($iId)
	{
		$aViews = $this -> database() 
						-> select('*')
						-> from($this->_sTable)
						-> where('resume_id = '. $iId)
						->execute('getRows');
		
		if($aViews)
		{
			foreach($aViews as $aView)
			{
				$this->database()->delete($this->_sTable,'view_id = '. $aView['view_id']);
			}
		}
		
		return true;
	}
}
	