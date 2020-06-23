<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Promote_Campaign extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$iCampaignId = $this->getParam('iCampaignId');
		$sFrameUrl = Phpfox::getService('fundraising')->getFrameUrl($iCampaignId, $iStatus = Phpfox::getService('fundraising')->getBadgeStatusNumber('both'));
		$sBadgeCode = Phpfox::getService('fundraising')->getBadgeCode($sFrameUrl);
		$this->template()->assign(array(
				'sHeader' => _p('promote_campaign'),
				'sBadgeCode' => $sBadgeCode,
				'iCampaignId' => $iCampaignId
			)
		);
    }
    
}

?>