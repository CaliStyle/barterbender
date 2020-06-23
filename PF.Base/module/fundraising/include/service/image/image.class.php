<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Image_Image extends Phpfox_Service 
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fundraising_image');
    }

	public function getFundraisingImageDir()
	{
		return Phpfox::getParam('core.dir_pic'). 'fundraising' . PHPFOX_DS;
	}


	/**
	 * get all image related information
	 * @by minhta
	 * @return array of information about an image
	 */
	public function getImageById($iImageId)
	{
		$aImage = $this->database()->select('fimg.image_id, fimg.image_path, fimg.server_id, fr.user_id, fr.campaign_id, fr.image_path AS default_image_path, fr.campaign_id as campaign_id')
				->from($this->_sTable, 'fimg')
				->join(Phpfox::getT('fundraising_campaign'), 'fr', 'fr.campaign_id = fimg.campaign_id')
				->where('fimg.image_id = ' . (int) $iImageId)
				->execute('getSlaveRow');

		return $aImage;
	}


	/**
	 * get all images of a campaign
	 * @TODO: none 
	 * <pre>
	 * Phpfox::getService('fundraising.campaign')->getImagesOfCampaign($iId);
	 * </pre>
	 * @by minhta
	 * @param int $iCampaignId 
	 * @return array of images
	 */

	public function getImagesOfCampaign($iCampaignId, $iLimit = 8) {
		// because have some field we needed here

		$aImages = $this->database()->select('*')
				->from($this->_sTable)
				->where('campaign_id = ' . $iCampaignId)
				->limit($iLimit)
				->execute('getSlaveRows');
		return $aImages;
	}
}

?>
