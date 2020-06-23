<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_showannouncement extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
        $announcement_id = $this->getParam('announcement_id');
        $aAnn = Phpfox::getService('directory')->getAnnouncementsByIdForEdit($announcement_id);
		$this->template()->assign(array(
				'announcement_id' => $announcement_id,
				'aAnn' => $aAnn,
                'sCustomClassName' => 'ync-block'
			)
		);
		return 'block';
	}

}

?>
