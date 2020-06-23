<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Image_Process extends Phpfox_Service {

	private $_aSizes = array(120, 300, 600);
	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->_sTable = Phpfox::getT('fundraising_image');
		$this->_sDirFundraising = Phpfox::getService('fundraising.image')->getFundraisingImageDir(); 
		if(!is_dir($this->_sDirFundraising))
		{
			mkdir($this->_sDirFundraising);
		}
	}

	public function delete($iImageId) {
		$aImage = Phpfox::getService('fundraising.image')->getImageById($iImageId);
		if (!isset($aImage['campaign_id'])) {
			return Phpfox_Error::set(_p('unable_to_find_the_image'));
		}

		$iFileSizes = 0;
		$aSizes = $this->_aSizes;
		//delete original image
		$sImage = Phpfox::getParam('core.dir_pic') . sprintf($aImage['image_path'], '');
		if (file_exists($sImage)) {
			$iFileSizes += filesize($sImage);

			@unlink($sImage);
		}
		
		foreach ($aSizes as $iSize) {
			$sImage = Phpfox::getParam('core.dir_pic') . sprintf($aImage['image_path'], (empty($iSize) ? '' : '_' ) . $iSize);
			if (file_exists($sImage)) {
				$iFileSizes += filesize($sImage);

				@unlink($sImage);
			}
		}

		$this->database()->delete($this->_sTable, 'image_id = ' . $aImage['image_id']);
		$iNewImageId = 0;

		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($aImage['campaign_id']);
		//in case the deleted image is default image
		if ($aImage['image_path'] == $aCampaign['image_path']) {
			$aCampaignImages = Phpfox::getService('fundraising.image')->getImagesOfCampaign($aImage['campaign_id']);

			//if still having images, get the first one
			if (!empty($aCampaignImages)) {
				$iNewImageId = $aCampaignImages[0]['image_id'];
			}
			$this->database()->update(Phpfox::getT('fundraising_campaign'), array('image_path' => (empty($aCampaignImages) ? null : $aCampaignImages[0]['image_path'] ), 'server_id' => (empty($aCampaignImages) ? null : $aCampaignImages[0]['server_id'])), 'campaign_id = ' . $aImage['campaign_id']);
		}

		return $iNewImageId;
	}
}

?>
