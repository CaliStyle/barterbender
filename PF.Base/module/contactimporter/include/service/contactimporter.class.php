<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs.class.php');

defined('CONTACTIMPORTER_CENTRALIZE_URL') or define('CONTACTIMPORTER_CENTRALIZE_URL', 'https://openid.younetid.com/v3/contact/index.php');

class Contactimporter_Service_Contactimporter extends Younet_Service
{
	/**
	 * maxium send out invitation
	 */
	const SEND_INVITAION_LIMIT = 60;

	/** Have a quick fix for fb using api* */
	static public $_aAllowProvider = array(
		'yahoo' => 'email',
		'gmail' => 'email',
		'hotmail' => 'email',
		'facebook' => 'social',
		'facebook_' => 'social',
		'twitter' => 'social',
		'linkedin' => 'social',
		'csv' => 'email',
		'typingmanual' => 'email',
	);

	public function getGrantedPermissionOfToken($sProvider, $sAccessToken)
	{
		$oProvider = Phpfox::getService('contactimporter') -> getProvider($sProvider);

		$oApi = $oProvider -> getApi();

		$aPermissions = array();
		$aPermissions['error'] = FALSE;
		try
		{
			$response = $oApi -> api("/me/permissions");
			$aPermissions['data'] = $response['data'][0];
		}
		catch (Exception $e)
		{
			$aPermissions['error'] = TRUE;
		}

		return $aPermissions;

	}

	public function checkFacebookXmppPermission($sProvider)
	{
		$oProvider = Phpfox::getService('contactimporter') -> getProvider($sProvider);
		$oApi = $oProvider -> getApi();
		$sAccessToken = $oApi -> getAccessToken();

		$result = Phpfox::getService('contactimporter') -> checkPermissionOfAccessToken('xmpp_login', $sProvider, $sAccessToken);
		if ($result == 0 || $result == 2)
		{
			return false;
		}
		elseif ($result == 1)
		{
			return true;
		}
	}

	/**
	 * 0 is false, 1 is true, 2 is having error, we should have another action for ex: send mail
	 */
	public function checkPermissionOfAccessToken($sPermission, $sProvider, $sAccessToken)
	{
		$aPermissions = Phpfox::getService('contactimporter') -> getGrantedPermissionOfToken($sProvider, $sAccessToken);

		if ($aPermissions['error'])
		{
			return 2;
		}

		if (array_key_exists($sPermission, $aPermissions['data']))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public function checkInviteIdExist($sInviteId, $iUserId, $sProvider)
	{
		if (!$iUserId)
		{
			return false;
		}
		$sCriteria = '';
		switch($sProvider)
		{
			case 'facebook' :
			case 'twitter' :
			case 'linkedin' :
				$sCriteria = "email like '%{$sProvider} ({$sInviteId})%' and user_id= '{$iUserId}'";

				break;
			default :
				$sCriteria = "email = '{$sInviteId}' and user_id= '{$iUserId}'";
		}
		// check id exist in phpfox_invite table
		$aRow = $this -> database() -> select('invite_id') -> from(Phpfox::getT('invite')) -> where($sCriteria) -> execute('getSlaveRow');

		if ($aRow)
		{
			return true;
		}
		else
		{
			// check id contactimporter_queue
			$sCriteria = "provider = '{$sProvider}' and user_id= '{$iUserId}'";
			$aRows = $this -> database() -> select('queue_id') -> from(Phpfox::getT('contactimporter_queue')) -> where($sCriteria) -> execute('getSlaveRows');
			
			foreach($aRows as $aQueueId)
			{
				$iQueueId = $aQueueId['queue_id'];
				
				$aRow = $this -> database() -> select('id') 
				-> from(Phpfox::getT('contactimporter_invitation_queue_list')) 
				-> where("queue_id = '{$iQueueId}' AND provider = '{$sProvider}' AND friend_id = '{$sInviteId}' ") 
				-> execute('getSlaveRow');
				if ($aRow)
				{
					return true;
				}
			}
		}
		
		return false;

	}

	public function getTotalSentInvitationsOfQueueInAPeriod($iQueueId, $iPeriod, $sProvider = '')
	{
		$sWhere = 'queue_id = ' . $iQueueId .' AND time_stamp > ' . (PHPFOX_TIME - (int)$iPeriod) . ' ';
		if ($iQueueId > 0)
		{
			$aQueue = Phpfox::getService('contactimporter') -> getQueueById($iQueueId);
			$sProvider = $aQueue['provider'];
		}
		
		if(isset($sProvider) && !empty($sProvider))
		{
			switch ($sProvider) {
				
				case 'facebook':
					$oProvider = Phpfox::getService('contactimporter') -> getProvider($sProvider);
					$aProfile = $oProvider->getProfile('me', true);
					$sWhere = ' uid = \'' . $aProfile['identity'] . '\'' . ' AND time_stamp > ' . (PHPFOX_TIME - $iPeriod);
					break;
				
				case 'twitter':
					$oProvider = Phpfox::getService('contactimporter') -> getProvider($sProvider);
					$oApi = $oProvider -> getApi();
					$sAccessToken = $oApi -> getAccessToken();

					$sWhere = ' access_token = \'' . $sAccessToken . '\'' . ' AND time_stamp > ' . (PHPFOX_TIME - $iPeriod);
					break;
				
				case 'linkedin':
					$oProvider = Phpfox::getService('contactimporter') -> getProvider($sProvider);
					$oApi = $oProvider -> getApi();
					$aAccessToken = $oApi -> getAccessToken();
					$sAccessToken = $aAccessToken['oauth_token'];

					$sWhere = ' access_token = \'' . $sAccessToken . '\'' . ' AND time_stamp > ' . (PHPFOX_TIME - $iPeriod);
					break;
				
				default:
					$sWhere = ' provider = \'' . $sProvider . '\'' . ' AND time_stamp > ' . (PHPFOX_TIME - $iPeriod);
					break;
			}
		}
		
		$iCnt = $this -> database() -> select('COUNT(id)') -> from(Phpfox::getT('contactimporter_invitation_queue_list')) -> where($sWhere) -> execute('getSlaveField');
		return $iCnt;
	}

	public function getTotalSentInvitationsInAPeriod($iPeriod)
	{
		$iCnt = $this -> database() -> select('COUNT(id)') -> from(Phpfox::getT('contactimporter_invitation_queue_list')) -> where(' time_stamp > ' . (PHPFOX_TIME - $iPeriod)) -> execute('getSlaveField');

		return $iCnt;
	}

	/**
	 *
	 * @return array('number' => number, 'period' => period by second)
	 */
	public function getQuota($sProvider)
	{
		if (in_array($sProvider, array(
			'facebook',
			'twitter',
			'linkedin'
		)))
		{
			switch($sProvider)
			{
				case 'facebook' :
					$iNumber = 20;
					$iPeriod = 60 * 60 * 24;
					break;
				case 'twitter' :
					$iNumber = 250;
					$iPeriod = 60 * 60 * 24;
					break;
				case 'linkedin' :
					$iNumber = 10;
					$iPeriod = 60 * 60 * 24;
					break;
			}
			$settings = phpfox::getService('socialbridge.providers') -> getProvider($sProvider);
			if (!empty($settings['params']['maxInvite']))
			{
				$iNumber = $settings['params']['maxInvite'];
			}
		}
		else
		{
			$iNumber = (int)Phpfox::getParam('contactimporter.cron_send_invite');

			$iPeriod = 60 * 60;
		}
		
		return array(
			'number' => $iNumber,
			'period' => $iPeriod
		);
	}

	public function getTotalSuccessedInvitationOfQueue($iQueueId)
	{
		$iCnt = $this -> database() -> select('COUNT(id)') -> from(Phpfox::getT('contactimporter_invitation_queue_list')) -> where('queue_id = ' . $iQueueId . ' AND is_sent = 1 AND is_failed = 0') -> execute('getSlaveField');

		return $iCnt;

	}

	public function getTotalFailedInvitationOfQueue($iQueueId)
	{
		$iCnt = $this -> database() -> select('COUNT(id)') -> from(Phpfox::getT('contactimporter_invitation_queue_list')) -> where('queue_id = ' . $iQueueId . ' AND is_sent = 1 AND is_failed = 1') -> execute('getSlaveField');

		return $iCnt;

	}

	public function getQueueById($iQueueId)
	{
		static $aQueues = array();

		if (isset($aQueues[$iQueueId]))
		{
			return $aQueues[$iQueueId];
		}
		$aQueues[$iQueueId] = $this -> database() -> select('*') -> from(Phpfox::getT('contactimporter_queue')) -> where('queue_id = ' . $iQueueId) -> execute('getSlaveRow');

		return $aQueues[$iQueueId];
	}

	public function getMaxSentInvitation()
	{

	}

	public function getFriendsFromQueue($iQueueId, $iLimit = 10)
	{
		$aRows = $this -> database() -> select('friend_id') -> from(Phpfox::getT('contactimporter_invitation_queue_list')) -> where('queue_id = ' . $iQueueId . ' AND is_sent = 0 ') -> limit(0, $iLimit) -> execute('getSlaveRows');

		$aIds = array();
		foreach ($aRows as $aRow)
		{
			$aIds[] = $aRow['friend_id'];
		}

		return $aIds;
	}

	public function getNumberOfRemainingInvitationInPeriod($sProvider)
	{
		$aQuota = Phpfox::getService('contactimporter') -> getQuota($sProvider);
		$iSentInivationsInQuotaPeriod = Phpfox::getService('contactimporter') -> getTotalSentInvitationsOfQueueInAPeriod($iQueueId = 0, $aQuota['period'], $sProvider);

		$iRemainInvitationInQuotaPeriod = $aQuota['number'] - $iSentInivationsInQuotaPeriod;

		return $iRemainInvitationInQuotaPeriod;
	}

	/**
	 *
	 * @param type $sProvider
	 * @param type $sFriendIds = current queued friend id
	 * @return boolean
	 */
	public function getSendIdsFromQueueForProvider($aQueue)
	{
		$iTotal = $aQueue['total'];
		$iSuccess = $aQueue['success'];
		$iFail = $aQueue['fail'];
		$iQueueId = $aQueue['queue_id'];
		$sProvider = $aQueue['provider'];

		Phpfox::getService('contactimporter.process') -> setCurrentApi($sProvider, $aQueue);

		$iMaxSent = (int)Phpfox::getParam('contactimporter.cron_send_invite');

		if ($iMaxSent + $iSuccess + $iFail >= $iTotal)
		{
			$iMaxSent = $iTotal - $iSuccess - $iFail;
		}
		$aQuota = Phpfox::getService('contactimporter') -> getQuota($sProvider);
		$iSentInivationsInQuotaPeriod = Phpfox::getService('contactimporter') -> getTotalSentInvitationsOfQueueInAPeriod($iQueueId, $aQuota['period']);
		
		$iRemainInvitationInQuotaPeriod = $aQuota['number'] - $iSentInivationsInQuotaPeriod;
		if ($iMaxSent > $iRemainInvitationInQuotaPeriod)
		{
			$iMaxSent = $iRemainInvitationInQuotaPeriod;
		}
		if ($iMaxSent <= 0)
		{
			return array();
		}

		$aFriendIds = Phpfox::getService('contactimporter') -> getFriendsFromQueue($iQueueId, $iMaxSent);
		if (count($aFriendIds) == 0)
		{
			Phpfox::getService('contactimporter.process') -> updateQueueData($iQueueId);
			$aFriendIds = Phpfox::getService('contactimporter') -> getFriendsFromQueue($iQueueId, $iMaxSent);
		}

		return $aFriendIds;

	}

	/**
	 * return a row which will be used to get invitation list to send
	 * @return false if there's no row, or a Row of pending queue in database
	 */
	public function getQueueToSend()
	{
		$sServerId = Phpfox::getLib('request') -> getServer('PHPFOX_SERVER_ID');

		//Query the queue
		$aConds[] = 'AND status = "pending" ';
		$aConds[] = 'AND (server_id IS NULL OR server_id = "' . $sServerId . '")';

		$aRow = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('contactimporter_queue')) -> where($aConds) -> limit(1) -> order('time_stamp ASC') -> execute('getRow');

		if (!$aRow)
		{
			return false;
		}

		if ($aRow['server_id'] == null || $aRow['server_id'] == '')
		{
			$aRow['server_id'] = $sServerId;
			$this -> database() -> update(Phpfox::getT('contactimporter_queue'), array('server_id' => $sServerId), "queue_id = " . $aRow['queue_id']);
		}

		return $aRow;
	}

	/**
	 * set current login invite user id to
	 * @param int $iUserId
	 * @return TRUE
	 */
	public function setInviteUserId($iUserId)
	{
		$_SESSION['contactimporter']['user_id'] = $iUserId;
		return TRUE;
	}

	/**
	 * GET INVITER USER ID
	 * default she/he is viewer or logged in before
	 * @return int|0
	 */
	public function getInviteUserId()
	{
		if (isset($_SESSION['contactimporter']['user_id']) && $_SESSION['contactimporter']['user_id'])
		{
			return $_SESSION['contactimporter']['user_id'];
		}

		return 0;
	}

	/**
	 * add user to none invited list if new users has not invite any friends to our networks.
	 * @param int $iUserId
	 * @return TRUE
	 */
	public function setUserHasInvited($iUserId, $iUsed = 0)
	{
		$sTable = Phpfox::getT('contactimporter_invited');

		$aRow = $this -> database() -> select('*') -> from($sTable) -> where('user_id=' . $iUserId) -> execute('getSlaveRow');

		if ($aRow)
		{
			$this -> database() -> update($sTable, array('is_used' => $iUsed), 'user_id=' . $iUserId);
		}
		else
		{
			$this -> database() -> insert($sTable, array(
				'user_id' => $iUserId,
				'is_used' => $iUsed
			));
		}
		return TRUE;
	}

	/**
	 * for new members, force use social invite to invite at least on friend to be valid users.
	 * if member success added before where installed our module, return 1
	 * return FALSE if new member has not invited any friends
	 * @return TRUE|FALSE
	 */
	public function checkUserHasInvited($iUserId)
	{
		if (!$iUserId)
		{
			return TRUE;
		}

		$sTable = Phpfox::getT('contactimporter_invited');

		$aRow = $this -> database() -> select('*') -> from($sTable) -> where('user_id=' . $iUserId) -> execute('getSlaveRow');

		if (!$aRow)
		{
			return TRUE;
		}

		if ($aRow && $aRow['is_used'])
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * get total send out invitation by specific user id
	 * @param int $iUserId Optional
	 * @return int
	 */
	public function getUserTotalInvited($iUserId = NULL)
	{

		if (NULL == $iUserId)
		{
			$iUserId = Phpfox::getUserId();
		}

		if (!$iUserId)
		{
			return 0;
		}

		$aRow = $this -> database() -> select('sum(emails + socials) as number_invitation') -> from(phpfox::getT('contactimporter_statistics'), 'pi') -> where('user_id=' . $iUserId) -> execute('getSlaveRow');

		if ($aRow)
		{
			return $aRow['number_invitation'];
		}

		return 0;
	}

	/**
	 * @return string
	 */
	public function getCentralizeUrl()
	{
		return CONTACTIMPORTER_CENTRALIZE_URL;
	}

	/**
	 * @param string $sProvider
	 * @return string available values email, social, openinviter
	 */
	public function getPluginType($sProvider)
	{
		return self::$_aAllowProvider[$sProvider];
	}

	/**
	 * get provider object
	 * @param string $sProvider
	 * @return Object
	 */
	public function getProvider($sProvider)
	{
		switch($sProvider)
		{
			case 'facebook' :
			case 'facebook_' :
				return Phpfox::getService('socialbridge.provider.facebook');
			case 'twitter' :
				return Phpfox::getService('socialbridge.provider.twitter');
				break;
			case 'linkedin' :
				return Phpfox::getService('socialbridge.provider.linkedin');
			case 'yahoo' :
				return Phpfox::getService('contactimporter.provider.yahoo');
			case 'gmail' :
			case 'google' :
				return Phpfox::getService('contactimporter.provider.gmail');
			case 'hotmail' :
				return Phpfox::getService('contactimporter.provider.hotmail');
				break;
			case 'csv' :
				return Phpfox::getService('contactimporter.provider.csv');
			case 'typingmanual' :
				return Phpfox::getService('contactimporter.provider.typingmanual');
		}
		return NULL;
	}

	/**
	 * update activity point
	 */
	public function updateActivityPoint($iUserId, $method = '+', $total = 1)
	{
		$iUserId = intval($iUserId);

		if (!$iUserId)
		{
			return;
		}

		if(!in_array($method,['+', '-'])) {
		    return;
        }

		if(empty($total)) {
		    $total = 1;
        }

        $table = Phpfox::getT('user_activity');
		$userActivity = db()->select('activity_total, activity_invite')
                    ->from($table)
                    ->where('user_id = '. (int)$iUserId)
                    ->execute('getSlaveRow');

		if (!empty($userActivity))
		{
		    $total = (int)$total;

            if(Phpfox::isAppActive('Core_Activity_Points')) {
                Phpfox::getService('activitypoint.process')->updatePoints($iUserId, 'contactimporter_sentinvitations', $method, $total);
            }

            $activityTotal = (int)$userActivity['activity_total'];
            $activityInvite = (int)$userActivity['activity_invite'];

            if($method == '+') {
                $activityTotal += $total;
                $activityInvite += $total;
            }
            else {
                if($activityTotal >= $total) {
                    $activityTotal -= $total;
                } else {
                    $activityTotal = 0;
                }

                if($activityInvite >= $total) {
                    $activityInvite -= $total;
                } else {
                    $activityInvite = 0;
                }
            }

            db()->update(Phpfox::getT('user_activity'),[
                'activity_total' => $activityTotal,
                'activity_invite' => $activityInvite
            ],'user_id = '. (int)$iUserId);
		}
	}

	public function addSocialJoined($iUserId, $sCookieEmail, $sCookieEmailFrom)
	{
		$sTable = Phpfox::getT('contactimporter_social_joined');

		$inviterId = $this -> database() -> select('user_id') -> from(Phpfox::getT('invite')) -> where('invite_id = ' . (int)$sCookieEmail) -> execute('getField');

		$sPatt = '/\(.*\)/';
		
		preg_match($sPatt, $sCookieEmailFrom, $arr);
		if (count($arr))
		{
			$sId = str_replace(')', '', str_replace('(', '', $arr[0]));
			$aVals = array(
				'user_id' => (int)$iUserId,
				'social_user_id' => $sId,
				'inviter' => $inviterId
			);
			$this -> database() -> insert($sTable, $aVals);
			phpfox::getService('contactimporter') -> updateStatistic($inviterId, 0, -1);
		}
		else
		{
			$aVals = array(
				'user_id' => (int)$iUserId,
				'social_user_id' => $sCookieEmailFrom,
				'inviter' => $inviterId
			);
			$this -> database() -> insert($sTable, $aVals);
			phpfox::getService('contactimporter') -> updateStatistic($inviterId, -1, 0);
		}

		if ($inviterId)
		{
			$iPointsInvite = Phpfox::getService('contactimporter.process') -> getPointSetting($inviterId, 'points_invite');
			$sql = "UPDATE `" . Phpfox::getT('user_group_setting') . "` SET `default_admin` = " . $iPointsInvite . ", `default_user` = " . $iPointsInvite . ", `default_staff` = " . $iPointsInvite . " WHERE `name` = \"points_invite\" AND `module_id` = \"invite\"";
			Phpfox::getLib('phpfox.database') -> query($sql);
			$this -> cache() -> remove('user_group_setting', 'substr');
		}
		
		$this->updateContactimporterActivityPoint($inviterId);
	}

	public function updateContactimporterActivityPoint($userId)
	{
		$sTable = Phpfox::getT('user_activity');

		$aUser = $this -> database() -> select('*') -> from($sTable) -> where('user_id=' . (int)$userId) -> execute('getRow');

		if (!empty($aUser))
		{
		    Phpfox::getService('user.activity')->update($userId, 'contactimporter');
		}
	}
	
	private function getUserParam($sParam, $iUserId) {

		$iGroupId = $this->getUserBy('user_group_id', $iUserId);

		return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sParam);

	}
	
	private function getUserBy($sVar, $iUserId ) {

		$result = $this->_getUserInfo($iUserId);
		if (isset($result[$sVar]))
		{
			return $result[$sVar];
		}

		return false;
	}
	
	private function _getUserInfo($iUserId) {
		$aRow = $this->database()->select('u.*')
			->from(Phpfox::getT('user'), 'u')
			->where('u.user_id = ' . $iUserId)
			->execute('getRow');
		if(!$aRow) {
			return false;
		}

		$aRow['age'] = Phpfox::getService('user')->age(isset($aRow['birthday']) ? $aRow['birthday'] : '');
		$aRow['location'] = $aRow['country_iso']; 
		$aRow['language'] = $aRow['language_id']; 
		return $aRow;		
	}

	public function getInviteds()
	{
		$iUserId = Phpfox::getUserId();

		if (!$iUserId)
		{
			return array();
		}

		$aInvitations = $this -> database() -> select('*') -> from(Phpfox::getT('invite')) -> where('user_id=' . $iUserId) -> execute('getSlaveRows');

		$aInviteds = array();

		foreach ($aInvitations as $aInvitation)
		{
			$sPatt = '/\(.*\)/';
			preg_match($sPatt, $aInvitation['email'], $arr);
			if (count($arr))
			{
				$sId = str_replace(')', '', str_replace('(', '', $arr[0]));
				$aInviteds[] = $sId;
			}
		}

		$aJoineds = $this -> database() -> select('social_user_id') -> from(Phpfox::getT('contactimporter_social_joined')) -> where('inviter = ' . Phpfox::getUserId()) -> execute('getSlaveRows');

		if (is_array($aJoineds) && count($aJoineds))
		{
			foreach ($aJoineds as $aJoined)
			{
				$aInviteds[] = $aJoined['social_user_id'];
			}
		}

		return $aInviteds;
	}

	public function checkSocialJoined($aMails)
	{
		$aRows = $this -> database() -> select('social_user_id') -> from(Phpfox::getT('contactimporter_social_joined')) -> execute('getSlaveRows');
		if (!count($aRows) || empty($aMails))
		{
			return array();
		}

		$sDbCheck = '';
		foreach ($aMails as $sMail)
		{
			$sMail = trim($sMail);
			$sDbCheck .= '\'' . $this -> database() -> escape($sMail) . '\',';
		}
		$sDbCheck = rtrim($sDbCheck, ',');

		$aJoined = array();
		$aUsers = $this -> database() -> select(Phpfox::getUserField() . ', u.email, f.user_id,f.social_user_id') -> from(Phpfox::getT('user'), 'u') -> leftJoin(Phpfox::getT('contactimporter_social_joined'), 'f', 'f.user_id = u.user_id') -> where('f.social_user_id IN(' . $sDbCheck . ')') -> execute('getSlaveRows');

		foreach ($aUsers as $aUser)
		{
			$aJoined[strtolower($aUser['email'])] = $aUser;
			$aMails = array_diff($aMails, (array)$aUser['social_user_id']);
		}

		return $aJoined;
	}

	public function getAllowProviders()
	{
		$aRows = $this -> database() -> select('*') -> from(Phpfox::getT('contactimporter_providers'), 'cp') -> where('(type = "email" AND enable = 1 AND default_domain !="" ) OR (type = "social" AND enable = 1)') -> order('cp.order_providers ASC') -> execute('getSlaveRows');
		foreach ($aRows as $i => $aRow)
		{
			$aRows[$i]['submit_url'] = Phpfox::getLib('url') -> makeUrl('contactimporter.' . $aRow['name']);
		}
		$aRows = $this -> allowProvider($aRows);
		return $aRows;
	}

	public function allowProvider($provider_lists)
	{
		if (count($provider_lists))
		{
			foreach ($provider_lists as $key => $value)
			{
				//get total invitations
				$sProvicer = $value['name'];
				if ($sProvicer == 'facebook_')
					$sProvicer = 'facebook';
				$provider_lists[$key]['iTotalInvitations'] = Phpfox::getService('contactimporter.contact') -> getProviderTotalInvitations($sProvicer);

				if (!isset(self::$_aAllowProvider[$value['name']]) || (in_array($sProvicer, array('facebook','twitter')) && !Phpfox::getService('socialbridge') -> getProvider($sProvicer)))
				{
					unset($provider_lists[$key]);
				}
			}
		}
		return $provider_lists;
	}

    /**
     * @param $user_id
     * @param $emails
     * @param $social
     * @param string $provider
     * @return bool
     */
	public function updateStatistic($user_id, $emails, $social, $provider = '')
	{
		if (!$user_id)
			return false;
		$statistic = phpfox::getLib('phpfox.database') -> select('*') -> from(phpfox::getT('contactimporter_statistics')) -> where('user_id = ' . (int)$user_id) -> execute('getRow');
		if ($statistic != null)
		{
			$iEmail = ($statistic['emails'] + $emails) < 0 ? 0 : $statistic['emails'] + $emails;
			$iSocial = ($statistic['socials'] + $social) < 0 ? 0 : $statistic['socials'] + $social;
			phpfox::getLib('phpfox.database') -> update(phpfox::getT('contactimporter_statistics'), array(
				'emails' => $iEmail,
				'socials' => $iSocial
			), 'statictis_id = ' . $statistic['statictis_id']);
		}
		else
		{
			Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('contactimporter_statistics'), array(
				'user_id' => (int)$user_id,
				'emails' => $emails,
				'socials' => $social
			));
		}
		if ($provider)
		{
			$iTotal = $emails;
			if ($social > 0)
			{
				$iTotal = $social;
			}
			$contactStatistic = phpfox::getLib('phpfox.database') -> select('*') -> from(phpfox::getT('contactimporter_contact')) -> where('provider = "' . $provider . '" AND user_id = ' . (int)$user_id) -> execute('getRow');
			if ($contactStatistic != null)
			{
				$total = ($contactStatistic['total'] + $iTotal) < 0 ? 0 : $contactStatistic['total'] + $iTotal;
				Phpfox::getLib('phpfox.database') -> update(Phpfox::getT('contactimporter_contact'), array('total' => $total), 'contact_id = ' . $contactStatistic['contact_id']);
			}
			else
			{
				$total = $iTotal < 0 ? 0 : $iTotal;
				Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('contactimporter_contact'), array(
					'user_id' => (int)$user_id,
					'provider' => $provider,
					'total' => $total
				));
			}
		}
	}

	public function getAllEmailInvitations($aCond = array(), $sSort = 'invite_id ASC', $iPage = '', $sLimit = '', $bCount = true)
	{
		$emails = array();
		if ($bCount)
		{
			$iCnt = Phpfox::getLib('phpfox.database') -> select('COUNT(pi.invite_id)') -> from(phpfox::getT('invite'), 'pi') -> leftJoin(phpfox::getT('user'), 'pu', 'pu.user_id = pi.user_id') -> where($aCond) -> execute('getSlaveField');
		}

		if ($iCnt)
		{
			$emails = phpfox::getLib('phpfox.database') -> select('pu.user_name,pi.user_id,pu.full_name,pu.email as inviter_email,pi.email as receive_email,pi.invite_id,pi.is_resend, pi.invited_name, pi.email as email') -> from(phpfox::getT('invite'), 'pi') -> leftJoin(phpfox::getT('user'), 'pu', 'pu.user_id = pi.user_id') -> where($aCond) -> limit($iPage, $sLimit, $iCnt) -> execute('getRows');
		}
		if (is_array($emails))
		{
			$oMail = Phpfox::getLib('mail');
			foreach ($emails as $iKey => $aPost)
			{
				if ($oMail -> checkEmail($aPost['receive_email']))
				{
					$emails[$iKey]['canResendMail'] = true;
				}
				else
				{
					$emails[$iKey]['canResendMail'] = false;
				}

				if (Phpfox::isModule('socialbridge'))
				{
					// in case of the invited name has not been inserted yet
					if (!$aPost['invited_name'])
					{
						if ($sFacebookId = Phpfox::getService('contactimporter') -> getFacebookIdFromPattern($aPost['email']))
						{
							$aProfile = Phpfox::getService('socialbridge.provider.facebook') -> getProfile($sFacebookId, $bIsGetNew = true);
							if ($aProfile)
							{
								$emails[$iKey]['invited_name'] = $aProfile['full_name'];
								$this -> database() -> update(Phpfox::getT('invite'), array('invited_name' => $aProfile['full_name']), 'invite_id = ' . $aPost['invite_id']);
							}
						}
					}
				}
			}
		}

		if (!$bCount)
		{
			return $emails;
		}

		return array(
			$iCnt,
			$emails
		);
	}

	public function getFacebookIdFromPattern($sPattern)
	{

		if (strstr(strtolower($sPattern), 'facebook'))
		{
			$pattern = '/\d+/';
			preg_match($pattern, $sPattern, $aMatches);

			if (count($aMatches))
			{
				return $aMatches[0];
			}
		}
		return false;
	}

	public function get($iUser, $iPage, $iPageSize)
	{
		$iCnt = $this -> database() -> select("COUNT(*)") -> from(Phpfox::getT('invite'), 'i') -> where('i.user_id = ' . (int)$iUser) -> execute('getSlaveField');

		$aInvites = $this -> database() -> select('*') -> from(Phpfox::getT('invite')) -> where('user_id = ' . (int)$iUser) -> order('invite_id DESC') -> limit($iPage, $iPageSize, $iCnt) -> execute('getSlaveRows');

		$iTotal = ($iPage > 1 ? (($iPageSize * $iPage) - $iPageSize) : 0);
		$oMail = Phpfox::getLib('mail');
		foreach ($aInvites as $iKey => $aPost)
		{
			$iTotal++;
			$aInvites[$iKey]['count'] = $iTotal;
			if ($oMail -> checkEmail($aPost['email']))
			{
				$aInvites[$iKey]['canResendMail'] = true;
			}
			else
			{
				$aInvites[$iKey]['canResendMail'] = false;
			}

			if (Phpfox::isModule('socialbridge'))
			{
				// in case of the invited name hasnot been inserted yet
				if (!$aPost['invited_name'])
				{
					if ($sFacebookId = Phpfox::getService('contactimporter') -> getFacebookIdFromPattern($aPost['email']))
					{
						$aProfile = Phpfox::getService('socialbridge.provider.facebook') -> getProfile($sFacebookId, $bIsGetNew = true);
						if ($aProfile)
						{
							$aInvites[$iKey]['invited_name'] = $aProfile['full_name'];
							$this -> database() -> update(Phpfox::getT('invite'), array('invited_name' => $aProfile['full_name']), 'invite_id = ' . $aPost['invite_id']);
						}
					}
				}
			}
		}
		if ($iCnt <= 0)
		{
			$statistic = phpfox::getLib('phpfox.database') -> select('*') -> from(phpfox::getT('contactimporter_statistics')) -> where('user_id = ' . $iUser) -> execute('getRow');

			if ($statistic != null)
			{
				phpfox::getLib('phpfox.database') -> update(phpfox::getT('contactimporter_statistics'), array(
					'emails' => 0,
					'socials' => 0
				), 'statictis_id = ' . $statistic['statictis_id']);
			}
		}
		return array(
			$iCnt,
			$aInvites
		);
	}

	public function getMaxInvitation()
	{
		$iUserId = intval(Phpfox::getUserId());

		if (!$iUserId)
		{
			return self::SEND_INVITAION_LIMIT;
		}

		$iGroupId = Phpfox::getLib('phpfox.database') -> select('user_group_id') -> from(Phpfox::getT('user')) -> where('user_id = ' . $iUserId) -> execute('getSlaveField');

		$iGroupId = intval($iGroupId);

		$iMax = Phpfox::getLib('phpfox.database') -> select('number_invitation') -> from(Phpfox::getT('contactimporter_max_invitations'), 'm') -> where('m.id_user_group = ' . $iGroupId) -> execute('getSlaveField');

		if (!$iMax)
			return self::SEND_INVITAION_LIMIT;

		return $iMax;
	}

	public function getStatistics()
	{
		$aStatistics = phpfox::getLib('phpfox.database') -> select('*') -> from(phpfox::getT('contactimporter_statistics')) -> where('user_id = ' . phpfox::getUserId()) -> execute('getRow');

		$aRows = Phpfox::getLib('phpfox.database') -> select('total,success,fail') -> from(Phpfox::getT('contactimporter_queue')) -> where('total > 0 AND user_id =' . Phpfox::getUserId()) -> execute('getRows');

		$iRemain = 0;
		if (!empty($aRows))
		{
			foreach ($aRows as $aRow)
			{
				$iRemain += $aRow['total'] - $aRow['success'] - $aRow['fail'];
			}
		}

		if ($iRemain < 0)
		{
			$iRemain = 0;
		}
		$aStatistics['remain'] = $iRemain;
		return $aStatistics;
	}

	public function getIconSize()
	{
		$sCacheId = $this -> cache() -> set('contactimporter_setting_icon_size');
		if (!($icon_size = $this -> cache() -> get($sCacheId, 60 * 60)))
		{
			$icon_size = Phpfox::getLib('database') -> select('param_values') -> from(Phpfox::getT('contactimporter_settings')) -> where('settings_type="icon_size"') -> execute('getRow');
			$icon_size = isset($icon_size['param_values']) && $icon_size['param_values'] ? $icon_size['param_values'] : 30;
			$this -> cache() -> save($sCacheId, $icon_size);
		}
		return $icon_size;
	}

	public function getTopProviders()
	{
		$sCacheId = $this -> cache() -> set('contactimporter_providers');
		if (!($providers = $this -> cache() -> get($sCacheId, 60 * 60)))
		{
			$number_provider_display = phpfox::getLib('database') -> select('param_values') -> from(phpfox::getT('contactimporter_settings')) -> where('settings_type="number_provider_display"') -> execute('getRow');
			$number_limit = isset($number_provider_display['param_values']) && $number_provider_display['param_values'] >= 0 ? $number_provider_display['param_values'] : 100;
			if ($number_limit == 0)
				return array();
			$providers = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('contactimporter_providers')) -> where('enable = 1') -> order('order_providers ASC') -> execute('getSlaveRows');
			$providers = $this -> allowProvider($providers);
			$providers = array_slice($providers, 0, $number_limit);
			$this -> cache() -> save($sCacheId, $providers);
		}
		return $providers;
	}

	public function removeCache()
	{
		$this -> cache() -> remove('contactimporter_setting_icon_size');
		$this -> cache() -> remove('contactimporter_providers');
	}

	public function getProviders($aConds = array())
	{
		$con = array();
		foreach ($aConds as $c)
		{
			if (strpos($c, 'LIKE'))
			{
				if (!strpos($c, 'All') && $c != '2')
				{
					$c = str_replace('disable', '0', $c);
					$c = str_replace('enable', '1', $c);
					$c = str_replace('active', 'enable', $c);
					if (count($con) == 0)
						$con[] = $c;
					else
						$con[] = ' AND ' . $c;
				}
			}
		}
		//SKIP PROVIDERS
		$con[] = "AND name NOT IN('famiva','fdcareer')";
		(($sPlugin = Phpfox_Plugin::get('contactimporter.component_service_contactimporter_getProviders__start')) ? eval($sPlugin) : false);
		$providers = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('contactimporter_providers'), 'cp') -> where($con) -> order('cp.order_providers ASC') -> execute('getSlaveRows');
		$providers = $this -> allowProvider($providers);
		(($sPlugin = Phpfox_Plugin::get('contactimporter.component_service_contactimporter_getProviders__end')) ? eval($sPlugin) : false);
		return $providers;
	}

	function validateEmail($email)
	{
		$pattern = "/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/";
		return (bool) preg_match($pattern, $email);
	}

	public function addContactImporter($aVals)
	{
		$sqlInsert = "insert IGNORE  into " . phpfox::getT('contactimporter') . "(contactimporter_id,user_id,provider,contactimporter_user_id,time_stamp) values (null," . $aVals['user_id'] . "," . "'" . $aVals['provider'] . "','" . $aVals['contactimporter_user_id'] . "'," . $aVals['time_stamp'] . ")";
		$insert = phpfox::getLib('database') -> query($sqlInsert);
	}

	public function addUnsubscribe($aVals)
	{
		$sqlInsert = "insert IGNORE  into " . phpfox::getT('contactimporter_unsubscribe') . "(`unsubscribe_id`,`email`,`time_stamp`) values (null,'" . $aVals['email'] . "','" . $aVals['time_stamp'] . "')";
		$insert = phpfox::getLib('database') -> query($sqlInsert);
	}

	public function getEmailUnsubscribe()
	{
		$aEmails = $this -> database() -> select('email') -> from(phpfox::getT('contactimporter_unsubscribe'), 'e') -> where(1) -> execute('getRows');
		$emails = array();
		foreach ($aEmails as $key => $value)
		{
			$emails[$key] = $value['email'];
		}
		return $emails;
	}

	public function getUserInvite($iPage = 0, $iLimit = 0)
	{
		$aCond = array();
		$email_invited = $this -> database() -> select('email') -> from(phpfox::getT('user'), 'u') -> where('u.user_id=' . phpfox::getUserId()) -> execute('getSlaveField');
		$aCond[] = "inv.email = '" . $email_invited . "'";

		$aRows = $this -> database() -> select('DISTINCT u.*') -> from(phpfox::getT('user'), 'u') -> join(phpfox::getT('invite'), 'inv', 'u.user_id = inv.user_id') -> where($aCond) -> execute('getSlaveRows');
		$iCnt = count($aRows);
		return array(
			$iCnt,
			$aRows
		);
	}

	public function subscribe($email)
	{
		$this -> database() -> delete(phpfox::getT('contactimporter_unsubscribe'), "email ='" . $email . "'");
		$aRow = $this -> database() -> select('email') -> from(phpfox::getT('contactimporter_unsubscribe')) -> where("email ='" . $email . "'") -> execute('getRow');

		if (count($aRow) > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
		return false;
	}

	public function getUsersInvite($email_invited)
	{
		$aCond = array();
		$aCond[] = "inv.email = '" . $email_invited . "'";
		$aRows = $this -> database() -> select('DISTINCT u.*') -> from(phpfox::getT('user'), 'u') -> join(phpfox::getT('invite'), 'inv', 'u.user_id = inv.user_id') -> where($aCond) -> execute('getSlaveRows');
		$iCnt = count($aRows);
		return array(
			$iCnt,
			$aRows
		);
	}

	public function displayEmailContacts($contacts)
	{
		$aMails = array();
		foreach ($contacts as $email => $name)
		{
			$aMails[] = $email;
		}
		Phpfox_Error::skip(true);
		list($aMails, $aInvalid, $aCacheUsers) = Phpfox::getService('invite') -> getValid($aMails, Phpfox::getUserId());
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
			$invite_list_sort,
			$aMails,
			$aInvalid,
			$aCacheUsers
		);
	}

	public function displayContacts($contacts)
	{
		$contact_list = array();

		foreach ($contacts as $key => $email)
		{
			if (is_array($email))
			{
				$char = strtoupper(substr($email['name'], 0, 1));
				if (is_numeric($char))
				{
					$contact_list['Z'][] = array(
						'key' => $key,
						'name' => $email['name'],
						'pic' => $email['pic']
					);
				}
				else
				{
					$social_c = ord($char);
					for ($start = ord('A'); $start <= ord('Z'); $start++)
					{
						if ($social_c == $start)
						{
							if (is_array($email))
							{
								$contact_list[chr($start)][] = array(
									'key' => $key,
									'name' => $email['name'],
									'pic' => $email['pic']
								);
								break;
							}
							else
							{
								$contact_list[chr($start)][] = array(
									'key' => $key,
									'name' => $email,
									'pic' => ''
								);
								break;
							}
						}
						else
						{
							if (!isset($contact_list[chr($start)]))
								$contact_list[chr($start)] = array();
						}
					}
				}
			}
			else
			{
				$char = strtoupper(substr($email, 0, 1));
				if (is_numeric($char))
				{
					$contact_list['Z'][] = array(
						'key' => $key,
						'name' => $email,
						'pic' => $email['pic']
					);
				}
				else
				{
					$social_c = ord($char);
					for ($start = ord('A'); $start <= ord('Z'); $start++)
					{
						if ($social_c == $start)
						{
							if (is_array($email))
							{
								$contact_list[chr($start)][] = array(
									'key' => $key,
									'name' => $email,
									'pic' => $email['pic']
								);
								break;
							}
							else
							{
								$contact_list[chr($start)][] = array(
									'key' => $key,
									'name' => $email,
									'pic' => ''
								);
								break;
							}
						}
						else
						{
							if (!isset($contact_list[chr($start)]))
								$contact_list[chr($start)] = array();
						}
					}
				}
			}
		}
		for ($start = ord('A'); $start <= ord('Z'); $start++)
		{
			if (!isset($contact_list[chr($start)]))
				$contact_list[chr($start)] = array();
		}
		ksort($contact_list);
		return $contact_list;
	}

	public function updateOrderProviders($orderProviders, $name)
	{
		$this -> database() -> update(phpfox::getT('contactimporter_providers'), array('order_providers' => $orderProviders), "name='" . $name . "'");
		phpfox::getService('contactimporter') -> removeCache();
	}

	static public function convert_vi_to_en($str)
	{
		$str1 = $str;

		$str = preg_replace("/[^A-Za-z\+]/", "", $str1);

		if (empty($str))
		{
			$str = preg_replace("/[^A-Za-z0-9\+]/", "", $str1);
		}
		$str = str_replace("quot", "", $str);
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
		$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
		$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
		$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
		$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
		$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
		$str = preg_replace("/(Đ)/", 'D', $str);

		return $str;
	}

	/**
	 * process list of $aRows
	 */
	static public function processEmailRows($aRows)
	{
		$aResults = array();

		foreach ($aRows as $i => $aRow)
		{
			$name = $aRow['name'];
			$email = $aRow['email'];

			if (trim($name) == '')
			{
				$name = $email;
			}

			if (empty($email))
			{
				continue;
			}

			$name1 = self::convert_vi_to_en(trim($name));

			if (function_exists('mb_strtoupper'))
			{
				$char = mb_strtoupper(mb_substr($name1, 0, 1, "UTF-8"), "UTF-8");
			}
			else
			{
				$char = strtoupper(substr($name1, 0, 1));
			}
			if (!preg_match("/[A-Za-z]/", $char))
			{
				$aResults['Z'][] = array(
					'email' => $email,
					'name' => $name
				);
			}
			else
			{
				for ($start = ord('A'); $start <= ord('Z'); $start++)
				{
					if (ord($char) == $start)
					{
						$aResults[chr($start)][] = array(
							'email' => $email,
							'name' => $name
						);
						break;
					}
					else
					{
						if (!isset($aResults[chr($start)]))
						{
							$aResults[chr($start)] = array();
						}
					}
				}
			}
		}
		ksort($aResults);
		return $aResults;
	}

	static public function processSocialRows($aRows, $sProvider = NULL)
	{
		$aResults = array();
		foreach ($aRows as $i => $aRow)
		{

			$id = $aRow['id'];

			if (empty($id))
				continue;

			if (isset($aRow['profile_image_url']))
			{
				$aRows[$i]['pic'] = $aRow['pic'] = $aRow['profile_image_url'];
			}
			$name = $aRow['name'];
			$name1 = self::convert_vi_to_en(trim($name));
			$pic = $aRow['pic'];
			if (function_exists('mb_strtoupper'))
			{
				$char = mb_strtoupper(mb_substr($name1, 0, 1, "UTF-8"), "UTF-8");
			}
			else
			{
				$char = strtoupper(substr($name1, 0, 1));
			}
			if (!preg_match("/[A-Za-z]/", $char))
			{
				$aResults['Z'][] = array(
					'name' => $name,
					'id' => $id,
					'pic' => $pic
				);
			}
			else
			{
				for ($start = ord('A'); $start <= ord('Z'); $start++)
				{
					if (ord($char) == $start)
					{
						$aResults[chr($start)][] = array(
							'name' => $name,
							'id' => $id,
							'pic' => $pic
						);
						break;
					}
					else
					{
						if (!isset($aResults[chr($start)]))
						{
							$aResults[chr($start)] = array();
						}
					}
				}
			}
		}
		ksort($aResults);
		return $aResults;
	}

	public function getAdminStatistics($sWhere = '1 = 1', $sSort = 'date ASC', $iPage = '', $sLimit = '', $bCount = true)
	{
		$staistics = array();
		if ($bCount)
		{
			$aDate = Phpfox::getLib('phpfox.database') -> select('st.date') -> from(phpfox::getT('contactimporter_admin_statistics'), 'st') -> where($sWhere) -> group('st.date') -> execute('getRows');
			$iCnt = count($aDate);
		}
		if ($iCnt)
		{
			$staistics = phpfox::getLib('phpfox.database') -> select('st.date, SUM(st.total) as total') -> from(phpfox::getT('contactimporter_admin_statistics'), 'st') -> group('st.date') -> where($sWhere) -> limit($iPage, $sLimit, $iCnt) -> execute('getRows');
		}
		if (!$bCount)
		{
			return $staistics;
		}

		return array(
			$iCnt,
			$staistics
		);
	}
    /*
	 * $params is array
	 * iUser, 
	 * iPage, 
	 * iPageSize,
	 * sType
	 */
	public function getQueue($params = array())
	{
		$sType = '(';
		foreach (self::$_aAllowProvider as $provide => $type)
		{
			if($params['sType'] == $type)
			{
				$sType .= "'".$provide."',";
			}
		}
		$sType = substr($sType, 0, strlen($sType)-1).')';
		
		$iCnt = $this -> database() 
			-> select("COUNT(*)") 
			-> from(Phpfox::getT('contactimporter_queue'), 'q') 
			-> leftjoin(Phpfox::getT("contactimporter_invitation_queue_list"), "iql", "iql.queue_id = q.queue_id AND iql.friend_id != ''") 
			-> where("q.user_id = ".(int)$params['iUser']." AND iql.is_sent = 0 AND iql.provider  IN ".$sType) 
			-> execute('getSlaveField');

		$aQueues = $this -> database() 
			-> select('*') 
			-> from(Phpfox::getT('contactimporter_queue'), 'q') 
			-> leftjoin(Phpfox::getT("contactimporter_invitation_queue_list"), "iql", "iql.queue_id = q.queue_id AND iql.friend_id != ''") 
			-> where("q.user_id = ".(int)$params['iUser']." AND iql.is_sent = 0 AND iql.provider  IN ".$sType) 
			-> order('iql.id ASC') 
			-> limit($params['iPage'], $params['iPageSize'], $iCnt) 
			-> execute('getSlaveRows');

		$iTotal = ($params['iPage'] > 1 ? (($params['iPageSize'] * $params['iPage']) - $params['iPageSize']) : 0);
		$oMail = Phpfox::getLib('mail');
		
		foreach ($aQueues as $iKey => $aQueue)
		{
			$iTotal++;
			
			if(!isset($aQueue['json']))
			{
				$arrFriend_ids = unserialize($aQueue['friend_ids']);
				$arrNames = unserialize($aQueue['jsons']);
				
				$keyTemp =  array_keys($arrFriend_ids, $aQueue['friend_id']);
				if(isset($keyTemp[0]) && isset($arrNames[$keyTemp[0]])) {
					$aUpdate = array(
						'json' => serialize($arrNames[$keyTemp[0]])
					);
					$this->database()->update(Phpfox::getT('contactimporter_invitation_queue_list'), $aUpdate, 'id = ' . $aQueue['id']);

					if (isset($arrNames[$keyTemp[0]]) && isset($arrNames[$keyTemp[0]]['name'])) {
						$aQueues[$iKey]['name'] = $arrNames[$keyTemp[0]]['name'];
					}
					if (isset($arrNames[$keyTemp[0]]) && isset($arrNames[$keyTemp[0]]['pic'])) {
						$aQueues[$iKey]['pic'] = $arrNames[$keyTemp[0]]['pic'];
					}
				}
				
			}
			else{
				$arrTemp = unserialize($aQueue['json']);
				$aQueues[$iKey]['name'] = $arrTemp['name'];
				if(isset($arrTemp['pic']))				
					$aQueues[$iKey]['pic'] = $arrTemp['pic'];
				else {
					$aQueues[$iKey]['pic'] = '';
				}
			}
			
			$aQueues[$iKey]['count'] = $iTotal;
			if ($oMail -> checkEmail($aQueue['friend_id']))
			{
				$aQueues[$iKey]['canResendMail'] = true;
			}
			else
			{
				$aQueues[$iKey]['canResendMail'] = false;
			}
		}

		if ($iCnt <= 0)
		{
			$statistic = phpfox::getLib('phpfox.database') -> select('*') -> from(phpfox::getT('contactimporter_statistics')) -> where('user_id = ' . $params['iUser']) -> execute('getRow');

			if ($statistic != null)
			{
				phpfox::getLib('phpfox.database') -> update(phpfox::getT('contactimporter_statistics'), array(
					'emails' => 0,
					'socials' => 0
				), 'statictis_id = ' . $statistic['statictis_id']);
			}
		}
		return array(
			$iCnt,
			$aQueues
		);
	}  

	/*
	 * iQueueId, 
	 */
	public function updateQueue($params = array())
	{
		
		if(!$params['iQueueId'])
		{
			return false;
		}
		
		$aQueues = $this -> database() 
			-> select('*') 
			-> from(Phpfox::getT('contactimporter_queue'), 'q') 
			-> where("q.queue_id = ".$params['iQueueId']) 
			-> execute('getSlaveRow');
		
		$friendIds = unserialize ($aQueues['friend_ids']);
		$jsons = unserialize ($aQueues['jsons']);
		
		
		if(in_array($params['friendId'], $friendIds))
		{
			$key = array_keys($friendIds,$params['friendId']);
			unset($friendIds[$key[0]]);
			unset($jsons[$key[0]]);
		}
		$aUpdate = array(
			'total' => $aQueues['total'] -1 ,
			'friend_ids' => serialize($friendIds),
			'jsons' => serialize($jsons) 
		);			
		if($aUpdate['total'])		
		{
			$this->database()->update(Phpfox::getT('contactimporter_queue'), $aUpdate, 'queue_id = '.$params['iQueueId']);
		}	
		else {
			$this -> database() -> delete(Phpfox::getT('contactimporter_queue'), 'queue_id = ' . (int)$params['iQueueId']);
			
		}
		
	}   

	public function buildSectionMenu()
    {
        $aSectionMenu = [
            _p('invite_friends') => '',
            _p('queue_emails') => 'contactimporter.queueemails',
            _p('queue_messages') => 'contactimporter.queuemes',
            _p('pending_invitations') => 'contactimporter.invitations',
        ];
        if (Phpfox::getUserParam('contactimporter.export_contact')) {
            $aSectionMenu[_p('export_contacts')] = 'contactimporter.export';
        }
        Phpfox::getLib('template')->buildSectionMenu('contactimporter', $aSectionMenu);
    }
}
