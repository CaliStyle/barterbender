<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Video_Video extends Phpfox_Service 
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fundraising_video');
    }


	/**
	 * get video by campaign Id 
	 * @by minhta
	 * @return array of information about a video
	 */
	public function getVideoOfCampaign($iCampaignId)
	{
		$aVideo = $this->database()->select('fv.video_id, fv.embed_code,fv.video_url, fv.image_path, fv.server_id, fr.user_id, fr.campaign_id,  fr.campaign_id as campaign_id')
				->from($this->_sTable, 'fv')
				->join(Phpfox::getT('fundraising_campaign'), 'fr', 'fr.campaign_id = fv.campaign_id')
				->where('fv.campaign_id = ' . (int) $iCampaignId)
				->execute('getSlaveRow');

		if($aVideo)
		{
			$aVideo['embed_code'] = preg_replace('/height=\"(.*?)\"/i', 'height="300"', $aVideo['embed_code']);
            if( isset($_SERVER['HTTPS']))
            {
                $aVideo['embed_code'] = str_replace("https://www.youtube.com", "https://www.youtube.com", $aVideo['embed_code']);
				$aVideo['video_url'] = str_replace("https://www.youtube.com", "https://www.youtube.com", $aVideo['video_url']);
            }
			else
			{
				$aVideo['embed_code'] = str_replace("https://www.youtube.com", "https://www.youtube.com", $aVideo['embed_code']);
				$aVideo['video_url'] = str_replace("https://www.youtube.com", "https://www.youtube.com", $aVideo['video_url']);
			}
			$aVideo['video_url'] = str_replace('watch?v=', 'embed/', $aVideo['video_url']);
			
		}
		return $aVideo;
	}
}

?>
