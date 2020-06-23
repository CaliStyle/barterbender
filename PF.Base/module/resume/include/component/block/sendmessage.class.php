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
class Resume_Component_Block_SendMessage extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		$user_id = $this->getParam('user_id');
		$resume_id = $this->getParam('resume_id');
		$type = $this->getParam('type');
		$this->template()->assign(array(
			'user_id'   => $user_id,
			'type' => $type,
			'resume_id' => $resume_id
		));
	}
}

?>