<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

require_once 'email_abstract.class.php';

class Contactimporter_Service_Provider_Typingmanual extends Contactimporter_Service_Provider_Email_Abstract
{
	protected $_name = 'typingmanual';

	public function getContacts($iPage = 1, $iLimit = 50)
	{
		$aInviteLists = $aJoineds = array();
		$iCnt = 0;
		$aErrors = array();
		
		if (isset($_POST['typing_emails']) && $_POST['typing_emails'])
		{
			$aEmails = $_POST['typing_emails'];
			$aEmails = explode(',', $aEmails);
			list($aEmails, $aInvalid, $aJoineds) = Phpfox::getService('invite') -> getValid($aEmails, Phpfox::getUserId());
			foreach ($aEmails as $sEmail)
			{
				$aInviteLists[] = array(
					'name' => $sEmail,
					'email' => $sEmail
				);
			}
			$iCnt =  count($aInviteLists);
			$aInviteLists = Phpfox::getService('contactimporter') -> processEmailRows($aInviteLists);
		}
		
		if(count($aInviteLists) <= 0)
		{
			$aErrors['contacts'] = _p('there_is_not_email_valid_or_invited');
		}
		
		return array(
			'iCnt'=>$iCnt,
			'aInviteLists' => $aInviteLists,
			'aJoineds' => $aJoineds,
			'aInvalid' => $aInvalid,
			'aErrors' => $aErrors
		);
	}

}
