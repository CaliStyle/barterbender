<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Gmap extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$item = $this->request()->get('item', false);
		$this->template()->assign(array(
            'item' => $item,
		));
	}

}

?>
