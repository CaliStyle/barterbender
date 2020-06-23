<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Video_Process extends Phpfox_Service {

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->_sTable = Phpfox::getT('fundraising_video');
	}

	/**
	 * add vieo url, return false if unsuccessful
	 * <pre>
	 * Phpfox::getService('fundraising.addVideoUrl')->addVideoUrl($sVideoUrl, $iCampaignId);
	 * </pre>
	 * @by minhta
	 * @return boolean
	 */
	public function addVideoUrl($sVideoUrl, $iCampaignId) {

		if (Phpfox::getService('fundraising.grab')->get($sVideoUrl)) {
			$aInsert = array(
				'video_url' => $sVideoUrl,
				'campaign_id' => $iCampaignId
			);
			if (!($sEmbed = Phpfox::getService('fundraising.grab')->embed())) {
				return Phpfox_Error::set(_p('unable_to_embed_this_video_due_to_privacy_settings'));
			}

			$aInsert['embed_code'] = $sEmbed;

			$sTitle = '';
			if ($sTitle = Phpfox::getService('fundraising.grab')->title()) {
				$aInsert['title'] = $this->preParse()->clean($sTitle, 255);

			}

			$this->deleteVideo($iCampaignId);

			$sImageName =  md5($iCampaignId . 'fundraising'. PHPFOX_TIME);
			if (Phpfox::getService('fundraising.grab')->image($iCampaignId, $sModule='fundraising', $sImageName )) {
				$sImageLocation = Phpfox::getLib('file')->getBuiltDir(Phpfox::getService('fundraising.image')->getFundraisingImageDir()) . $sImageName . '%s.jpg';

				$aInsert['image_path'] = 'fundraising'. PHPFOX_DS . str_replace(Phpfox::getService('fundraising.image')->getFundraisingImageDir(), '', $sImageLocation);
				$aInsert['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
			}

			//make sure having only 1 video for 1 campaign
			$this->database()->insert($this->_sTable, $aInsert);
			Phpfox::getService('fundraising.campaign')->notifyToAllFollowers($iCampaignId, 'video');
		} else {
			return false;
		}
	}

	public function deleteVideo($iCampaignId)
	{
		$aVideo = Phpfox::getService('fundraising.video')->getVideoOfCampaign($iCampaignId);
		if(isset($aVideo['image_path'])) {
			$sImage = Phpfox::getParam('core.dir_pic') . sprintf($aVideo['image_path'], '_120');
			if (file_exists($sImage)) {
				@unlink($sImage);
			}
		}
			
		$this->database()->delete($this->_sTable, 'campaign_id = ' . $iCampaignId);
	}

}

?>
