<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
class Auction_Component_Block_Custom_Form extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aCustomFields = $this->getParam('aCustomFields');
		$this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
		));
	}

}

?>
