<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Form_Gallery_Video extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$iCampaignId = $this->getParam('iCampaignId');
		$aVideo = Phpfox::getService('fundraising.video')->getVideoOfCampaign($iCampaignId);
		$sSetup = $this->getParam('setup');
		$this->template()->assign(array(
			'aVideo' => $aVideo,
			'sSetup' => $sSetup
		));
    }
    
}

?>