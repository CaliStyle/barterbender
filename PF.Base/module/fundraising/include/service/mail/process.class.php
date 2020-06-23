<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Mail_Process extends Phpfox_Service 
{
	


    /**
     * @by datlv
     * @TODO: will split later update and insert
     * @param $aVals
     */

    public function addEmailTemplate($aVals) {

        $aRow = $this->database()->select('*')->from(Phpfox::getT('fundraising_email_template'))->where('type=' . $aVals['type_id'])->execute('getSlaveRow');

        if($aRow) {
            $aUpdate = array(
                'email_subject' => $aVals['email_subject'],
                'email_template' => $aVals['email_template'],
            );

            $this->database()->update(Phpfox::getT('fundraising_email_template'), $aUpdate, 'type=' . $aVals['type_id']);
        } else {
            $aInsert = array(
                'type' => $aVals['type_id'],
                'email_subject' => $aVals['email_subject'],
                'email_template' => $aVals['email_template'],
            );

            $iId = $this->database()->insert(Phpfox::getT('fundraising_email_template'), $aInsert);
        }
    }


		/**
	 * in case of sending email to user of this site, we only need user id to send them
	 * @by minhta
	 * @param
	 * @return true if sending successfully
	 */
	public function sendEmailTo($iTemplateType = 0, $iCampaignId = 0, $aReceivers =array(), $aCustomEmail = array(), $iDonorId = 0)
	{
		if(!$aReceivers || !$iCampaignId)
		{
			return false;
		}

		if(!is_array($aReceivers))
		{
			$aReceivers = array($aReceivers);
		}

		$aEmailMessage = array(
			'message' => '',
			'subject' => ''
		);

		if($aCustomEmail)
		{
			$aEmailMessage['message'] = Phpfox::getService('fundraising.mail')->parseTemplate($aCustomEmail['message'], $iCampaignId, $iDonorId);
			$aEmailMessage['subject'] = Phpfox::getService('fundraising.mail')->parseTemplate($aCustomEmail['subject'], $iCampaignId, $iDonorId);
		}
		else
		{
			
			$aEmailMessage = Phpfox::getService('fundraising.mail')->getEmailMessageFromTemplate($iTemplateType, $iCampaignId, $iDonorId);
		}


		$aVal = array(
			'email_message' =>Phpfox::getLib('parse.input')->prepare($aEmailMessage['message']),
			'email_subject' => $aEmailMessage['subject'],
			'campaign_id' => $iCampaignId,
			'receivers' => serialize($aReceivers),
			'is_sent' => 0
		);
		Phpfox::getService('fundraising.mail.process')->saveEmailToQueue($aVal);
		
		Phpfox::getService('fundraising.mail.send')->sendEmailsInQueue();

		return true;

	}

	public function saveEmailToQueue($aVal)
	{
		$aInsert = $aVal;
		$aInsert['time_stamp'] = PHPFOX_TIME;
		$this->database()->insert(Phpfox::getT('fundraising_email_queue'), $aInsert);
	}

	public function sendEmailToOwner($iTemplateType = '', $iCampaignId = 0, $iDonorId = 0)
	{
		if(!$iCampaignId)
		{
			return false;
		}

		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);

		if(!$aCampaign)
		{
			return false;
		}

		Phpfox::getService('fundraising.mail.process')->sendEmailTo($iTemplateType, $iCampaignId, (int) $aCampaign['user_id'], $aCustomMessage = array(), $iDonorId);

		return true;
	}

	public function sendEmailToAllDonors($iTemplateType = 0, $iCampaignId = 0, $aCustomMessage = array())
	{
		if(!$iCampaignId)
		{
			return false;
		}

		//we get all donor, we should paging here because we can have a lot of donor
		$aEmails = Phpfox::getService('fundraising.user')->getEmailsOfGuestDonors($iCampaignId);
		foreach($aEmails as $aEmail)
		{
			Phpfox::getService('fundraising.mail.process')->sendEmailTo($iTemplateType, $iCampaignId, $aEmail['email_address'], $aCustomMessage, $aEmail['donor_id']);
		}

		$aIds = Phpfox::getService('fundraising.user')->getIdsOfUserDonors($iCampaignId);
		foreach($aIds as $aId)
		{
			Phpfox::getService('fundraising.mail.process')->sendEmailTo($iTemplateType, $iCampaignId, $aId['user_id'],  $aCustomMessage, $aId['donor_id']);
		}
		
		return true;
	}
	

}

?>