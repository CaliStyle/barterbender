<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

require_once dirname(dirname(dirname(__FILE__))) . '/component/controller/openinviter/openinviter.php';

require_once 'email_abstract.class.php';

class Contactimporter_Service_Provider_OpenInviter extends Contactimporter_Service_Provider_Email_Abstract
{

	protected $_api = NULL;

	protected $_internal = NULL;

	function getApi()
	{
		if (null == $this -> _api)
		{
			$openinviter = new openinviter();
			$inPlugin = $openinviter -> getPlugins(true);
			$openinviter -> startPlugin($this -> getName(), false);
			$this -> _internal = $openinviter -> getInternalError();
			$this -> _api = $openinviter;
		}
		return $this -> _api;

	}

	function getInternal()
	{
		return $this -> _internal;
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
		$internal = $this -> getInternal();
		$openinviter = $this -> getApi();

		$oi_session_id = FALSE;

		if (isset($_SESSION['oi_session_id']))
		{
			$oi_session_id = $_SESSION['oi_session_id'];
		}

		if ($internal)
		{
			$aErrors[] = $internal;
			return FALSE;
		}
		else
		if ($oi_session_id)
		{
			$aUser = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('user')) -> where('user_id = ' . $iUserId) -> execute('getRow');

			$sHost = $_SERVER['HTTP_HOST'];

			$aMessage = array(
				'subject' => $user['full_name'] . _p('is_inviting_you_to') . $sHost,
				'body' => "\n\r " . _p('is_inviting_you_to') . $sHost . "\n\r " . _p('to_join_please_follow_the_link') . " \n\r" . $sLink . "\n\r " . _p('message') . ": \n\r" . $sMessage . "\n\r",
				'attachment' => "\n\r" . _p('attached_message') . ": \n\r" . $sMessage
			);

			if (is_string($sRecipient))
			{
				$sRecipient = array($sRecipient);
			}

			$sendMessage = $openinviter -> sendMessage($oi_session_id, $aMessage, $sRecipient);

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function getContacts($iPage = 1, $iLimit = 50)
	{
		$sProvider = $this -> getName();

		$oContactImporter = Phpfox::getService('contactimporter');

		$iUserId = intval(Phpfox::getUserId());

		$aErrors = $aRows = $aMails = $aInviteList = $aInviteds = array();

		$email_box = isset($_POST['email_box']) ? $_POST['email_box'] : null;

		$password_box = isset($_POST['password_box']) ? $_POST['password_box'] : null;

		$openinviter = $this -> getApi();

		$internal = $this -> getInternal();

		$oi_session_id = $openinviter -> plugin -> getSessionID();

		$_SESSION['oi_session_id'] = $oi_session_id;

		if ($internal != null)
		{
			$aErrors['inviter'] = $internal;
		}
		else
		if (empty($email_box) || empty($password_box))
		{
			$errors['login'] = _p('login_failed_please_check_the_email_and_password_you_have_provided_and_try_again_later');
		}
		else
		if (!$openinviter -> login($email_box, $password_box))
		{
			$internal = $openinviter -> getInternalError();
			$errors['login'] = $internal ? $internal : _p('login_failed_please_check_the_email_and_password_you_have_provided_and_try_again_later');
		}

		$aContacts = $openinviter -> getMyContacts();
		$aInviteds = $oContactImporter -> getInviteds();
		$iCountInvited = 0;

		if (false === $aContacts)
		{
			$aErrors['contacts'] = _p('login_failed_please_check_the_email_and_password_you_have_provided_and_try_again_later');
		}

		foreach ($aContacts as $iKey => $aContacts)
		{
			if (!in_array($iKey, $aInviteds))
			{
				$aInviteList[$iKey] = $aContacts;
			}
			else
			{
				$iCountInvited++;
			}
		}

		$aInviteList = $oContactImporter -> displayContacts($aInviteList);

		//var_dump($aInviteList);exit;

		$iCnt = count($aInviteList);

		return array(
			'oi_session_id' => $oi_session_id,
			'iCnt' => $iCnt,
			'provider_box' => $sProvider,
			'aInviteLists' => $aInviteList,
			'aJoineds' => $aInviteds,
			'aErrors' => $aErrors,
			'sLinkPrev' => '',
			'sLinkNext' => '',
		);
	}

	public function generateResult($message, $bResult)
	{
		return array(
			'result' => $bResult,
			'message' => $message
		);
	}

}
