<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Block_Edit_Note extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		// User Login Requirement		
		Phpfox::isUser(true);
		
		// Get Params
		$iResumeId = $this->getParam('iResumeId');
		$iViewerId = Phpfox::getUserId();
		
		// Get view note
		$aView = Phpfox::getService('resume.viewme')->getViewByIds($iViewerId, $iResumeId);
		
		$this->template()->assign(array(
			'aView' => $aView
		));
		
		return 'block';
	}
}

?>