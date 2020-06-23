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
class Resume_Component_Controller_Delete extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	public function process()
	{
		// Check login requirement
		Phpfox::isUser(true);
		$iUserId = Phpfox::getUserBy('user_id');
		//Get related resume
		$iId = $this->request()->getInt('id');
		$aResume = Phpfox::getService('resume.basic')->getQuick($this->request()->getInt('id'));
		
		if ($aResume)
		{
			// Checking delete permission
			if($iUserId == $aResume['user_id'])
			{
				Phpfox::getUserParam('resume.can_delete_own_resumes',true);
			}
			else
			{
				Phpfox::getUserParam('resume.can_delete_other_resumes',true);	
			}
			// Delete process
			Phpfox::getService('resume.process')->delete($iId);
			$this->url()->send('resume.view_my', array(), _p('resume.resume_deleted_successfully'));
		}
		else 
		{
			return Phpfox_Error::set(_p('resume.can_not_find_the_related_resume_to_delete'));
		}
	}
}