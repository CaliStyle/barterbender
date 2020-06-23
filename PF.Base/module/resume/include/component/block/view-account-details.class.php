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
class Resume_Component_Block_View_Account_Details extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		// Get selected account
		$iAccountId = $this->getParam('iAccountId');
		$aAccount = Phpfox::getService('resume.account')->getAccountById($iAccountId);
		
		// Assign variable to layout
		$this->template()->assign(array(
			'aAccount' => $aAccount
		));
	}
}