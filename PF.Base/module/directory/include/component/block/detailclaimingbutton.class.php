<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailclaimingbutton extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		if(Phpfox::isUser() == false){
			return false;
		}

		$aYnDirectoryDetail = $this->getParam('aYnDirectoryDetail');
		$aBusiness = $aYnDirectoryDetail['aBusiness'];
		if($aBusiness['creating_type'] != 'claiming'){
			return false;
		}
		if ($aBusiness['business_status'] == 5) {
			return false;
		}
		$this->template()->assign(array(
				'sHeader' => '', 
				'aBusiness' => $aBusiness,
				'iUserID' => Phpfox::getUserId(),
                'sCustomClassName' => 'ync-block'
			)
		);
		return 'block';
	}

}

?>
