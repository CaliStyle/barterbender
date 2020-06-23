<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_User_Image_Entry extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() 
	{
		$userName = 'An'; //anonymous
		$this->template()->assign(array(
			'userName' => $userName
			)
		);
	}

}

?>
