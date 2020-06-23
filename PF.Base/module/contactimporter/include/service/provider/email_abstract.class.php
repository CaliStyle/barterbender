<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Service_Provider_Email_Abstract
{

	/**
	 * set email service
	 * @param string $sName
	 * @return Contactimporter_Service_Email
	 */
	public function setName($sName)
	{
		$this -> _name = strtolower($sName);
		return $this;
	}

	/**
	 * get email service name
	 * @return string
	 */
	public function getName()
	{
		return $this -> _name;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		if (isset($_REQUEST['contact']))
		{
			return $_SESSION['contactimporter'][$this -> _name] = @json_decode(urldecode($_REQUEST['contact']));
		}
		
		if (isset($_SESSION['contactimporter'][$this -> _name]) && $_SESSION['contactimporter'][$this -> _name])
		{
			return $_SESSION['contactimporter'][$this -> _name];
		}
		return array();
	}

	/**
	 * @param array $data
	 * @return Contactimporter_Service_Email
	 */
	public function setData($data)
	{
		$_SESSION['contactimporter'][$this -> _name] = $data;
		return $this;
	}

	/**
	 * clear saved data
	 * @return Contactimporter_Service_Email
	 */
	public function clearData()
	{
		if (isset($_SESSION['contactimporter'][$this -> _name]))
		{
			unset($_SESSION['contactimporter'][$this -> _name]);
		}
		return $this;
	}

	/**
	 * get list of rows
	 * alias to getContacts
	 * @param int $iPage
	 * @param int $iLimit
	 * @return array
	 */
	public function getFriends($iPage = 1, $iLimit = 50)
	{
		return $this -> getContacts($iPage, $iLimit);
	}

	/**
	 * get contacts
	 * @param int $iPage
	 * @param int $iLimit
	 * @return array
	 */
	public function getContacts($iPage = 1, $iLimit = 50)
	{
		$iCnt = 0;
		$aErrors = $aRows = $aMails = $aInvalids = $aJoineds = $aInviteList = array();

		$iOffset = ($iPage - 1) * $iLimit;
		$aRows = $aMails = $aInviteList = array();
		
		unset($_SESSION['yncontactimporter_fullValue'][$this->_name]);
		unset($_SESSION['yncontactimporter'][$this->_name]);
		
		$aContacts = $this -> getData();
		
		if ($aContacts && count($aContacts) > 0)
		{
			foreach ($aContacts as $i => $aContact)
			{
				
				if (!in_array($aContact -> email, $aMails) && !Phpfox::getService('contactimporter')->checkInviteIdExist($aContact -> email, Phpfox::getUserId(), $this->_name))
				{
					$aRows[$i]['name'] = $aContact -> name;
					$aRows[$i]['email'] = $aContact -> email;
					$aMails[] = $aRows[$i]['email'];
					
				}
			}
			
			$aJoineds = Phpfox::getService('contactimporter') -> checkSocialJoined($aMails);
			
			list($aMails, $aInvalid, $aJoined) = Phpfox::getService('invite') -> getValid($aMails, Phpfox::getUserId());
			
			$aInviteList = $aErrors = $aInvalids = array();
			
			if(count($aRows) == 0)
			{
				$aErrors['contacts'] = _p('there_is_not_contact_in_your_account');
			}
			
			$iCountInvited = 0;
			foreach ($aRows as $aRow)
			{
				if (in_array($aRow['email'], $aMails))
				{
					$aInviteList[] = $aRow;
					$_SESSION['yncontactimporter_fullValue'][$this->_name][] = array('id' => $aRow['email'], 'name' => $aRow['name']);
					$_SESSION['yncontactimporter'][$this->_name][] = $aRow['email'];
				}
				else
				{
					$iCountInvited++;
					
				}
			}
			$iCnt = count($aInviteList);
			if ($iCnt == 0)
			{
				$aErrors['contacts'] = _p('you_have_sent_the_invitations_to_all_of_your_friends');
			}

			$aInviteList = Phpfox::getService('contactimporter') -> processEmailRows($aInviteList);
		}
		else
		{
			$aErrors['contacts'] = _p('there_is_not_contact_in_your_account');
		}
		
		
		return array(
			'iInvited' => 0,
			'iCnt' => $iCnt,
			'aInviteLists' => $aInviteList,
			'aJoineds' => $aJoineds,
			'aInvalids' => $aInvalids,
			'sLinkNext' => '',
			'sLinkPrev' => '',
			'aErrors' => $aErrors,
		);
	}

	public function prepareDisplayContacts($contacts)
	{
		$aMails = array();
		foreach ($contacts as $email => $name)
		{
			$aMails[] = $email;
		}

		Phpfox_Error::skip(true);

		$iUserId = intval(Phpfox::getUserId());

		list($aMails, $aInvalid, $aCacheUsers) = Phpfox::getService('invite') -> getValid($aMails, $iUserId);

		$invite_list = array();
		$in_lst = '';

		foreach ($contacts as $email => $name)
		{
			if (in_array($email, $aMails))
			{
				$char = strtoupper(substr($email, 0, 1));
				if (is_numeric($char))
				{
					$invite_list_sort[chr($start)][] = array(
						'email' => $email,
						'name' => $name
					);
				}
				else
				{
					$invite_c = ord($char);
					for ($start = ord('A'); $start <= ord('Z'); $start++)
					{
						if ($invite_c == $start)
						{
							if (!empty($name))
							{
								$invite_list_sort[chr($start)][] = array(
									'email' => $email,
									'name' => $name
								);
								$in_lst .= ',' . $email;
								break;
							}
							else
							{
								$invite_list_sort[chr($start)][] = array(
									'email' => $email,
									'name' => $email
								);
								$in_lst .= ',' . $email;
								break;
							}
						}
						else
						{
							if (!isset($invite_list_sort[chr($start)]))
								$invite_list_sort[chr($start)] = array();
						}
					}
				}
			}
		}

		for ($start = ord('A'); $start <= ord('Z'); $start++)
		{
			if (!isset($invite_list_sort[chr($start)]))
				$invite_list_sort[chr($start)] = array();
		}

		ksort($invite_list_sort);

		return array(
			count($invite_list_sort),
			$invite_list_sort,
			$aMails,
			$aInvalid,
			$aCacheUsers
		);
	}

	/**
	 * @param int $iUserId
	 * @param string $sRecipient
	 * @param string $sSubject
	 * @param string $sMessage
	 * @param string $sLink
	 * @return true|false
	 * @throws Exception
	 */
	public function sendInvitation($iUserId, $sRecipient, $sSubject, $sMessage, $sLink)
	{
		$aUser = Phpfox::getService('user')->getUser($iUserId);

		$sMessage = str_replace("{full_user_name}",Phpfox::getUserBy('full_name'),$sMessage);
		
		$oMail = Phpfox::getLib('mail');

		$sSubject = array(
			'invite.full_name_invites_you_to_site_title',
			array(
				'full_name' => $aUser['full_name'],
				'site_title' => Phpfox::getParam('core.site_title')
			)
		);

		$sBody = array(
			'invite.full_name_invites_you_to_site_title_link',
			array(
				'full_name' => $aUser['full_name'],
				'site_title' => Phpfox::getParam('core.site_title') . '<br/>' . $sMessage,
				'link' => $sLink
			)
		);

		$bResult = $oMail -> to($sRecipient) -> subject($sSubject) -> message($sBody) -> send();
		return $this->generateResult('', $bResult);
		
	}


	public function generateResult($message, $bResult)
	{
		return array(
			'result' => $bResult,
			'message' => $message
		);
	}

}
