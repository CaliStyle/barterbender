<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Block_detailcontactus extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aYnDirectoryDetail  = $this->getParam('aYnDirectoryDetail');
		$sDefaultFullname = '';
		$sDefaultEmail = '';
		if(Phpfox::isUser()){
			$sDefaultFullname = Phpfox::getUserBy('full_name');
			$sDefaultEmail = Phpfox::getUserBy('email');
		}

		$aContactUs = Phpfox::getService('directory')->getPageContactUsByBusinessId($aYnDirectoryDetail['aBusiness']['business_id']);
		$aContactUsCustomfield = array();
		if(isset($aContactUs['contactus_id'])){
			$aContactUsCustomfield = Phpfox::getService('directory.customcontactus.custom')->getCustomFieldByContactUsId($aContactUs['contactus_id']);
			$aContactUs['receiver_data'] = json_decode($aContactUs['receiver_data'],1);
		} else {
			$aContactUs = false;
		}

		$sFormUrl = Phpfox::permalink('directory.detail', $aYnDirectoryDetail['aBusiness']['business_id'], $aYnDirectoryDetail['aBusiness']['name'], false, '') . 'contactus/';


		$this->template()->assign(array(
				'aYnDirectoryDetail' => $aYnDirectoryDetail, 
				'sDefaultFullname' => $sDefaultFullname, 
				'sDefaultEmail' => $sDefaultEmail, 
				'aContactUs' => $aContactUs, 
				'aContactUsCustomfield' => $aContactUsCustomfield, 
				'sFormUrl' => $sFormUrl,
                'sCustomClassName' => 'ync-block'
			)
		);
		$aYnDirectoryDetailContactUs  = $this->getParam('aYnDirectoryDetailContactUs', false);
		if($aYnDirectoryDetailContactUs){
			if(isset($aYnDirectoryDetailContactUs['sInform'])){
				$this->template()->assign(array(
						'sInform' => $aYnDirectoryDetailContactUs['sInform'], 
					)
				);

			} else {
				$this->template()->assign(array(
						'aContactUsCustomfield' => $aYnDirectoryDetailContactUs['aFields'], 
						'aContactUsForm' => $aYnDirectoryDetailContactUs['aForm'], 
					)
				);
				
			}
		}
	}

}

?>
