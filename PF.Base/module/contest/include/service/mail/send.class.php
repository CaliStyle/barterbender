<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Mail_Send extends Phpfox_Service 
{


	public function send($sSubject, $sMessage, $aReceivers)
	{
		if(!Phpfox::getService('contest.mail.phpfoxmail')->to($aReceivers)		
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