<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_browsecheckinhere extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
        $iBusinessId = $this->getParam('iBusinessId');

        $aList = Phpfox::getService('directory')->getCheckinhereList($iBusinessId);
		$this->template()->assign(array(
				'iBusinessId' => $iBusinessId,
				'aList' => ($aList),
                'sCustomClassName' => 'ync-block'
			)
		);
		return 'block';
	}

}

?>
