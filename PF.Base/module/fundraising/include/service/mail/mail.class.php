<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Mail_Mail extends Phpfox_Service 
{

	/**
	 * key is value will be replaced, value is the field in aCampiagn array
	 * remember every entry having a corresponding phrase with prefix keywordsub_
	 * @var array
	 */
	private $_aReplace= array(
		'[title]' => 'title',
		'[campaign_url]' => 'campaign_url',
		'[financial_goal]' => 'financial_goal',
		'[short_description]' => 'short_description',
		'[description]' => 'description',
		'[start_time]' => 'start_time',
		'[end_time]' => 'end_time',
		'[total_amount]' => 'total_amount',
		'[owner_name]' => 'owner_name', 
       '[donor_name]' => 'donor_name',
       '[inviter_name]' => 'inviter_name',
       '[site_name]' => 'site_name'
	);

	// match with [admin_reason] keyword in email template
	private $_sCloseReason = null;
	private $_bIsHavingCloseReason = false;



	private $_types = array(
        'createcampaignsuccessful_owner' => 1,
        'thankdonor_donor' => 2,
        'updatedonor_owner' => 3,
        'campaignexpired_owner' => 4,
        'campaignexpired_donor' => 5,
        'campaigncloseduetoreach_owner' => 6,
        'campaigncloseduetoreach_donor' => 7,
        'campaignclose_owner' => 8,
		'invitefriendletter_template' => 9
    );


	public function setCloseReason($sReason)
	{
		$this->_sCloseReason = $sReason;
		$this->_bIsHavingCloseReason = true;
	}

	public function getAllReplaces()
	{
		return $this->_aReplace;
	}

	public function getTypesCode($sType) {
        if (isset($this->_types[$sType])) {
            return $this->_types[$sType];
        } else {
            return false;
        }
    }

    public function getAllTypes() {
        return $this->_types;
    }

	public function getEmailTemplate($iTemplateType)
	{
		$aRow = $this->database()->select('*')
				->from(Phpfox::getT('fundraising_email_template'))
				->where('type = ' . $iTemplateType)
				->execute('getSlaveRow');

        if(!isset($aRow['email_subject']))
            $aRow['email_subject'] = "";
        if(!isset($aRow['email_template']))
            $aRow['email_template'] = "";

		return $aRow;
	}

	/**
	 * get email template and generate message based on campaign_id
	 * @TODO: static cache email template here , write test
	 * @by minhta
	 * @param
	 * @return
	 */
	public function getEmailMessageFromTemplate($iTemplateType, $iCampaignId, $iDonorId = 0, $iInviterId = 0)
	{
		if($iTemplateType == Phpfox::getService('fundraising.mail')->getTypesCode('thankdonor_donor'))
		{
			$aTemplate = Phpfox::getService('fundraising.campaign')->getCampaignThankyouDonorTemplate($iCampaignId);
		}
		else
		{
			$aTemplate = Phpfox::getService('fundraising.mail')->getEmailTemplate($iTemplateType);;
		}

		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iCampaignId);

		$sMessage = Phpfox::getService('fundraising.mail')->parseTemplate($aTemplate['email_template'], $aCampaign, $iDonorId, $iInviterId);

		$sSubject = Phpfox::getService('fundraising.mail')->parseTemplate($aTemplate['email_subject'], $aCampaign, $iDonorId , $iInviterId);

		return array(
			'message' => $sMessage,
			'subject' => $sSubject
		);
		
	}


	/**
	 * parse text for showing on form based on the campaign
	 * it will replace some predefined symbol by the corresponding text
	 * @by minhta
	 * @param string $sToBeParsedText the text to be parsed 
	 * @param array $aCampaign the corresponding campaign
	 * @return
	 */
	public function parseTemplate($sToBeParsedText, $aCampaign, $iDonorId = 0, $iInviterId = 0) {
		//if a id of campaign is passed
		if(!is_array($aCampaign))
		{
			$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($aCampaign);
		}

		$aCampaign['site_name'] = Phpfox::getParam('core.site_title');
		if($iDonorId)
		{
			$aDonorName = Phpfox::getService('fundraising.user')->getDonorNameById($iDonorId);
			if(!$aDonorName)
			{
				return false;
			}

			if($aDonorName['is_guest'])
			{
				$aCampaign['donor_name'] = $aDonorName['guest_full_name'];
			}
			else
			{
				$aCampaign['donor_name'] = $aDonorName['full_name'];
			}
		}

		if($iInviterId)
		{
			$aUser = Phpfox::getService('user')->getUser($iInviterId);
			$aCampaign['inviter_name'] = $aUser['full_name'];
		}

		if(!$aCampaign['financial_goal'])
		{
			$aCampaign['financial_goal'] = _p('unlimited_upper');
			
		}

		$oDate = Phpfox::getLib('date');
		$aLink = Phpfox::getLib('url')->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']);
		$sLink = $aLink;
		$aCampaign['campaign_url'] = $sLink;

		$aCampaign['start_time'] = $oDate->convertTime($aCampaign['start_time']);
		

		
		if(!$aCampaign['end_time'])
		{
			$aCampaign['end_time'] = _p('unlimited_time_upper');
		}
		else
		{
			$aCampaign['end_time'] = $oDate->convertTime($aCampaign['end_time']);
		}


		//the trick here ot send html email along with description 
		$aCampaign['description'] = $aCampaign['description_parsed'];


		$aBeReplaced = array();
		$aReplace = array();

		//setup replace and be replaced array
		foreach($this->_aReplace as $sBeReplaced => $sReplace)
		{
			if(isset($aCampaign[$sReplace]))
			{
				$aBeReplaced[] = $sBeReplaced;
				$aReplace[] = $aCampaign[$sReplace];
			}
		}
		if($this->_bIsHavingCloseReason)
		{
			$aBeReplaced[] = '[admin_reason]';
			$aReplace[] = $this->_sCloseReason;
		}

		$sParsedText = str_replace($aBeReplaced, $aReplace, $sToBeParsedText);
		return $sParsedText;
	}
}

?>
