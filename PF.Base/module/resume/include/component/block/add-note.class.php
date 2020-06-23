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
class Resume_Component_Block_Add_Note extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		$iResumeId = $this->getParam('iId');
		
		$aResume = Phpfox::getService('resume.basic')->getQuick($iResumeId);
		
		if(!$aResume)
		{
			return Phpfox_Error::set('resume.cannot_found_the_related_resume');
		}
		
		$this->template()->assign(array(
			'aRes' => $aResume
		));
		
		return 'block';
	}
}

?>