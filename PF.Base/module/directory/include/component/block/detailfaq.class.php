<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailfaq extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$aFAQs = Phpfox::getService('directory')->getFAQsByBusinessId((int)$aYnDirectoryDetail['aBusiness']['business_id']);

		$this->template()->assign(array(
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'aFAQs' => $aFAQs, 
				'sCorePath' => Phpfox::getParam('core.path'),
                'sCustomClassName' => 'ync-block'
			)
		);
	}

}

?>
