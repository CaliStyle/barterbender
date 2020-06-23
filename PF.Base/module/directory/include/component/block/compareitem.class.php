<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_compareitem extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$this->template()->assign(array(
				'sPlaceholderKeyword' => _p('directory.search_blogs'),
			)
		);
	}

}

?>
