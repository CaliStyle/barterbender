<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Images extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aCampaign = $this->getParam('aFrCampaign');

		if (!$aCampaign) {
			return false;
		}

		$aImages = Phpfox::getService('fundraising.image')->getImagesOfCampaign($aCampaign['campaign_id']);
        $sUrl = Phpfox::getLib('url')->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']);
		$this->template()->assign(array(
			'aImages' => $aImages,
			'corepath' => Phpfox::getParam('core.path'),
            'sUrl'  => $sUrl,
				)
		);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean() {
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_block_images_clean')) ? eval($sPlugin) : false);
	}

}

?>