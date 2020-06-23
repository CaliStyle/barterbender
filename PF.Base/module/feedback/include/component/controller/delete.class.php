<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Feedback_Component_Controller_Delete extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		Phpfox::isUser(true);		
		
		if ($iId = $this->request()->getInt('id'))
		{
			Phpfox::getService('feedback.process')->delete($iId);
			$this->url()->send('feedback');
		}
	}
}

?>