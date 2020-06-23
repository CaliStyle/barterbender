<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Mail_Send extends Phpfox_Service 
{

	 /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fundraising_email_queue');
    }
	
	public function sendEmailsInQueue($iPage = 0, $iPageSize = 500, $iCampaignId = 0)
	{
		$sCond = 'is_sent = 0 ';
		if($iCampaignId)
		{
			$sCond .= 'AND campaign_id = ' . $iCampaignId;
		}

		$aEmails = $this->database()->select('*')
				->from($this->_sTable)
				->limit($iPage, $iPageSize)
				->where($sCond)
				->execute('getSlaveRows');

		foreach($aEmails as $aEmail)
		{
			$bIsSent = Phpfox::getService('fundraising.mail.send')->send($aEmail['email_subject'], $aEmail['email_message'], unserialize($aEmail['receivers']));

			if($bIsSent)
			{
				$this->database()->update($this->_sTable, array('is_sent' => 1), 'id = ' . $aEmail['id']);
			}
		}
	}


	public function send($sSubject, $sMessage, $aReceivers)
	{
		if(!Phpfox::getService('fundraising.mail.phpfoxmail')->to($aReceivers)		
				->subject($sSubject)
				->message($sMessage)
				->send())
		{
			return false;
		}

		return true;
	}

}

?>