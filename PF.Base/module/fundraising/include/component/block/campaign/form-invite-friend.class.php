<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Form_Invite_Friend extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iId = $this->getParam('iCampaignId', $this->request()->getInt('id', 0));

        $sUrl = $this->getParam('url');

        $aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iId);

		if(!$aCampaign)
		{
			return false;
		}
		
		$aMessage = Phpfox::getService('fundraising.mail')->getEmailMessageFromTemplate(Phpfox::getService('fundraising.mail')->getTypesCode('invitefriendletter_template'), $aCampaign['campaign_id'], $iDonorId = 0, $iInviterId = Phpfox::getUserId());

		// to make message displayed correctly on text area
		$aMessage['message'] = str_replace("\n", "&#10;", $aMessage['message']);

        $this->template()->assign(array(
			'aMessage' => $aMessage,
            'aCampaign' 	=> $aCampaign,
            'sUrl'          => $sUrl,
        ));
    }
    
}

?>