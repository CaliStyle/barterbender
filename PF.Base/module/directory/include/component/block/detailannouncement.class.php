<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailannouncement extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {


	    $aYnDirectoryDetail = $this->getParam('aYnDirectoryDetail');
	    $sView = $this->getParam('sView');
        $aBusiness = $aYnDirectoryDetail['aBusiness'];
 		
		list($iCnt,$aAnnoucements)= Phpfox::getService('directory')->getAnnouncementsByBusinessIdForDetail($aBusiness['business_id']);

		if (count($aAnnoucements) < 1) {
			return false;
		}

		foreach ($aAnnoucements as $key_annoucement => $aAnnoucement) {
			$aAnnoucements[$key_annoucement]['current_text'] =  ($key_annoucement + 1);  
			$aAnnoucements[$key_annoucement]['prev'] =  ($key_annoucement - 1) <= 0 ? 0 : ($key_annoucement - 1);  
			$aAnnoucements[$key_annoucement]['next'] =  ($key_annoucement + 1) >= ($iCnt -1) ? ($iCnt - 1) : ($key_annoucement + 1);  
		}
		$this->template()->assign(array(
				'aAnnoucements' => $aAnnoucements,
				'core_path'     =>Phpfox::getParam('core.path'),
				'iCnt' => $iCnt,
                'sCustomClassName' => 'ync-block'
			)
		);
        $this->template()->clean(['sHeader']);
        return 'block';
	}

}

?>
