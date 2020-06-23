<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailcustompage extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$iCustomPage  = $this->getParam('iCustomPage');
    
        $aBusiness = $aYnDirectoryDetail['aBusiness'];

        $aCustomPage = Phpfox::getService('directory')->getCustomPageOfBusiness($iCustomPage,$aBusiness['business_id']);

		$this->template()->assign(array(
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'aCustomPage'		 => $aCustomPage,
                'sCustomClassName' => 'ync-block'
			)
		);

	}

}

?>
