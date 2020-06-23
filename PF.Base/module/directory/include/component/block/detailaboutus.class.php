<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailaboutus extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
        $aBusiness = $aYnDirectoryDetail['aBusiness'];

        $aAboutUs = Phpfox::getService('directory')->getPageAboutUsByBusinessId($aBusiness['business_id']);

		$this->template()->assign(array(
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'aAboutUs'	=> $aAboutUs
			)
		);

	}

}

?>
